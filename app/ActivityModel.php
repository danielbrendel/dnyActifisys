<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class ActivityModel
 *
 * Interface to activities
 */
class ActivityModel extends Model
{
    protected $fillable = [
        'title', 'description', 'date_of_activity_from', 'date_of_activity_till', 'location', 'limit'
    ];

    protected $casts = [
        'date_of_activity_from' => 'datetime', 
        'date_of_activity_till' => 'datetime', 
        'created_at' => 'datetime', 
        'updated_at' => 'datetime'
    ];

    /**
     * Get tag list from description
     *
     * @param $description
     * @return array
     * @throws Exception
     */
    private static function getTagList($description)
    {
        try {
            $taglist = array();
            $inTag = false;
            $curTag = '';
            for ($i = 0; $i < strlen($description); $i++) {
                if ($description[$i] === '#') {
                    $inTag = true;
                    continue;
                }

                if ($inTag === true) {
                    if (!ctype_alnum($description[$i])) {
                        $taglist[] = $curTag;
                        $curTag = '';
                        $inTag = false;
                        continue;
                    }

                    $curTag .= $description[$i];
                }
            }
            if ($inTag === true) {
                $taglist[] = $curTag;
            }

            return $taglist;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Replace umlauts
     * 
     * @param $str
     * @return string
     * @throws Exception
     */
    private static function replaceUmlauts($str)
    {
        try {
            $str = str_replace('ü', 'ue', $str);
            $str = str_replace('ö', 'oe', $str);
            $str = str_replace('ä', 'ae', $str);

            return $str;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Create an activity
     *
     * @param $owner
     * @param array $attr
     * @return mixed
     * @throws Exception
     */
    public static function createActivity($owner, array $attr)
    {
        try {
            $user = User::get($owner);
            if ((!$user) || ($user->deactivated)) {
                throw new Exception(__('app.user_not_existing_or_deactivated'));
            }

            $dtActivity = new DateTime($attr['date_of_activity_from']);
            $dtNow = new DateTime();
            if ($dtActivity < $dtNow) {
                throw new Exception(__('app.date_is_in_past'));
            }

            $taglist = static::getTagList(static::replaceUmlauts($attr['description']));

            if (($attr['only_verified'] == true) && (VerifyModel::getState($owner) != VerifyModel::STATE_VERIFIED)) {
                throw new Exception(__('app.only_for_verified_users'));
            }

            $item = new ActivityModel();
            $item->owner = $owner;
            $item->title = htmlspecialchars($attr['title']);
            $item->description = htmlspecialchars($attr['description']);
            $item->tags = (count($taglist) > 0) ? implode(' ', $taglist) . ' ' : '';
            $item->date_of_activity_from = date('Y-m-d H:i:s', strtotime($attr['date_of_activity_from'] . ' ' . $attr['time_of_activity']));
            $item->date_of_activity_till = date('Y-m-d H:i:s', strtotime($attr['date_of_activity_till'] . ' ' . $attr['time_of_activity']));
            $item->category = $attr['category'];
            $item->location = htmlspecialchars(strtolower(trim($attr['location'])));
            $item->limit = $attr['limit'];
            $item->only_gender = $attr['only_gender'];
            $item->only_verified = $attr['only_verified'];
            $item->slug = '';
            $item->save();

            $item->slug = Str::slug(strval($item->id) . ' ' . $item->title, '-');
            $item->save();

            $date_count = ActivityModel::getSameDateCount($item->date_of_activity_till, 'till');
            if ($date_count > 0) {
                $date_fix = strtotime($item->date_of_activity_till) + $date_count;
                $item->date_of_activity_till = date('Y-m-d H:i:s', $date_fix);
                $item->save();
            }

            if ($attr['add_participant']) {
                ParticipantModel::add($owner, $item->id, ParticipantModel::PARTICIPANT_ACTUAL);
            }

            $favs = FavoritesModel::where('entityId', '=', $owner)->where('type', '=', 'ENT_USER')->get();
            foreach ($favs as $fav) {
                PushModel::addNotification(__('app.activity_created_short'), __('app.activity_created_long', ['name' => $user->name, 'profile' => url('/user/' . $user->id), 'title' => $attr['title'], 'item' => url('/activity/' . $item->id)]), 'PUSH_CREATED', $fav->userId);

                $favUser = User::get($fav->userId);
                if (($favUser) && ($favUser->email_on_fav_created)) {
                    $activity_display_date = ((Carbon::createFromDate($item->date_of_activity_from)->format(__('app.display_date_format')) == Carbon::createFromDate($item->date_of_activity_till)->format(__('app.display_date_format'))) ? Carbon::createFromDate($item->date_of_activity_from)->format(__('app.display_date_format')) . ' ' . Carbon::createFromDate($item->date_of_activity_from)->format(__('app.display_time_format')) : Carbon::createFromDate($item->date_of_activity_from)->format(__('app.display_date_format')) . ' - ' . Carbon::createFromDate($item->date_of_activity_till)->format(__('app.display_date_format')) . ' ' . Carbon::createFromDate($item->date_of_activity_from)->format(__('app.display_time_format')));
                    $html = view('mail.fav_created', ['name' => $favUser->name, 'creator' => $user->name, 'activity' => $item, 'activity_display_date' => $activity_display_date])->render();
                    MailerModel::sendMail($favUser->email, __('app.activity_created'), $html);
                }
            }

            return $item->id;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit activity
     *
     * @param $owner
     * @param array $attr
     * @throws Exception
     */
    public static function updateActivity(array $attr)
    {
        try {
            $user = User::get(auth()->id());
            $item = ActivityModel::getActivity($attr['activityId']);

            if ((!$user) || ($user->deactivated)) {
                throw new Exception(__('app.user_not_existing_or_deactivated'));
            }

            if ((!$user->admin) && ($user->id !== $item->owner)) {
                throw new Exception(__('app.insufficient_permissions'));
            }

            $dtActivity = new DateTime($attr['date_of_activity_from']);
            $dtNow = new DateTime();
            if ($dtActivity < $dtNow) {
                throw new Exception(__('app.date_is_in_past'));
            }

            if (($attr['only_verified'] == true) && (VerifyModel::getState($user->id) != VerifyModel::STATE_VERIFIED)) {
                throw new Exception(__('app.only_for_verified_users'));
            }

            $otherDate = false;
            if (strtotime($item->date_of_activity_till) !== strtotime($attr['date_of_activity_till'] . ' ' . $attr['time_of_activity'])) {
                $otherDate = true;
            }

            $taglist = static::getTagList(static::replaceUmlauts($attr['description']));

            $item->title = htmlspecialchars($attr['title']);
            $item->description = htmlspecialchars($attr['description']);
            $item->slug = Str::slug(strval($item->id) . ' ' . $item->title, '-');
            $item->tags = (count($taglist) > 0) ? implode(' ', $taglist) . ' ' : '';
            $item->date_of_activity_from = date('Y-m-d H:i:s', strtotime($attr['date_of_activity_from'] . ' ' . $attr['time_of_activity']));
            $item->date_of_activity_till = date('Y-m-d H:i:s', strtotime($attr['date_of_activity_till'] . ' ' . $attr['time_of_activity']));
            $item->category = $attr['category'];
            $item->location = htmlspecialchars(strtolower(trim($attr['location'])));
            $item->limit = $attr['limit'];
            $item->only_gender = $attr['only_gender'];
            $item->only_verified = $attr['only_verified'];
            $item->save();

            if ($otherDate) {
                $date_count = ActivityModel::getSameDateCount($item->date_of_activity_till, 'till');
                if ($date_count > 0) {
                    $date_fix = strtotime($item->date_of_activity_till) + $date_count;
                    $item->date_of_activity_till = date('Y-m-d H:i:s', $date_fix);
                    $item->save();
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get activity by ID
     *
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public static function getActivity($id)
    {
        try {
            return ActivityModel::where('id', '=', $id)->where('locked', '=', false)->first();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get activity by slug
     *
     * @param $slug
     * @return mixed
     * @throws Exception
     */
    public static function getActivityBySlug($slug)
    {
        try {
            return ActivityModel::where('slug', '=', $slug)->where('locked', '=', false)->first();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Fetch activity package
     *
     * @param null $location
     * @param null $paginate
     * @param null $dateFrom
     * @param null $dateTill
     * @param null $tag
     * @param null $category
     * @param null $text
     * @return mixed
     * @throws Exception
     */
    public static function fetchActivities($location = null, $paginate = null, $dateFrom = null, $dateTill = null, $tag = null, $category = null, $text = null)
    {
        try {
            $activities = ActivityModel::where(function($query){
                $query->where('date_of_activity_from', '>=', date('Y-m-d H:i:s'))
                ->orWhere('date_of_activity_till', '>=', date('Y-m-d H:i:s', strtotime('+' . env('APP_ACTIVITYRUNTIME', 60) . ' minutes')));
            })->where('locked', '=', false)->where('canceled', '=', false);

            if ($location !== null) {
                $activities->where('location', 'like', '%' . strtolower($location) . '%');
            }

            if ($paginate !== null) {
                $activities->where('date_of_activity_till', '>', date('Y-m-d H:i:s', strtotime($paginate)));
            }

            if ($dateFrom !== null) {
                $asDate = date('Y-m-d 23:59:59', strtotime($dateFrom));
                if ((new DateTime($asDate) < (new DateTime('now')))) {
                    throw new Exception(__('app.date_from_smaller_than_now'));
                }

                $asDate = date('Y-m-d H:i:s', strtotime($dateFrom));

                $activities->where('date_of_activity_till', '>=', $asDate);
            }

            if ($dateTill !== null) {
                $asDate = date('Y-m-d 23:59:59', strtotime($dateTill));
                if ((new DateTime($asDate) < (new DateTime('now')))) {
                    throw new Exception(__('app.date_till_smaller_than_now'));
                }

                if ($dateFrom !== null) {
                    $asDate2 = date('Y-m-d H:i:s', strtotime($dateFrom));
                    if ((new DateTime($asDate) < (new DateTime($asDate2)))) {
                        throw new Exception(__('app.till_date_must_not_be_less_than_from_date'));
                    }
                }

                $activities->where('date_of_activity_till', '<=', $asDate);
            }

            if ($tag !== null) {
                $activities->where('tags', 'LIKE', '%' . $tag . ' %');
            }

            if ($category !== null) {
                $activities->where('category', '=', $category);
            }

            if ($text !== null) {
                $activities->where(function($query) use ($text) {
                    $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($text) . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($text) . '%'])
                    ->orWhereRaw('LOWER(location) LIKE ?', ['%' . strtolower($text) . '%']);
                });
            }

            return $activities->orderBy('date_of_activity_from', 'asc')->limit(env('APP_ACTIVITYPACKLIMIT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Fetch activity package of past activities
     *
     * @param null $location
     * @param null $paginate
     * @param null $dateFrom
     * @param null $dateTill
     * @param null $tag
     * @param null $category
     * @param null $text
     * @return mixed
     * @throws Exception
     */
    public static function fetchPastActivities($location = null, $paginate = null, $dateFrom = null, $dateTill = null, $tag = null, $category = null, $text = null)
    {
        try {
            $activities = ActivityModel::where(function($query){
                $query->where('date_of_activity_from', '<=', date('Y-m-d H:i:s'))
                ->orWhere('date_of_activity_till', '<=', date('Y-m-d H:i:s', strtotime('+' . env('APP_ACTIVITYRUNTIME', 60) . ' minutes')));
            })->where('locked', '=', false);

            if ($location !== null) {
                $activities->where('location', 'like', '%' . strtolower($location) . '%');
            }

            if ($paginate !== null) {
                $activities->where('date_of_activity_till', '<', date('Y-m-d H:i:s', strtotime($paginate)));
            }

            if ($dateFrom !== null) {
                $asDate = date('Y-m-d 23:59:59', strtotime($dateFrom));
                if ((new DateTime($asDate) < (new DateTime('now')))) {
                    throw new Exception(__('app.date_from_smaller_than_now'));
                }

                $asDate = date('Y-m-d H:i:s', strtotime($dateFrom));

                $activities->where('date_of_activity_till', '>=', $asDate);
            }

            if ($dateTill !== null) {
                $asDate = date('Y-m-d 23:59:59', strtotime($dateTill));
                if ((new DateTime($asDate) < (new DateTime('now')))) {
                    throw new Exception(__('app.date_till_smaller_than_now'));
                }

                if ($dateFrom !== null) {
                    $asDate2 = date('Y-m-d H:i:s', strtotime($dateFrom));
                    if ((new DateTime($asDate) < (new DateTime($asDate2)))) {
                        throw new Exception(__('app.till_date_must_not_be_less_than_from_date'));
                    }
                }

                $activities->where('date_of_activity_till', '<=', $asDate);
            }

            if ($tag !== null) {
                $activities->where('tags', 'LIKE', '%' . $tag . ' %');
            }

            if ($category !== null) {
                $activities->where('category', '=', $category);
            }

            if ($text !== null) {
                $activities->where(function($query) use ($text) {
                    $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($text) . '%'])
                    ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($text) . '%'])
                    ->orWhereRaw('LOWER(location) LIKE ?', ['%' . strtolower($text) . '%']);
                });
            }

            return $activities->orderBy('date_of_activity_from', 'desc')->limit(env('APP_ACTIVITYPACKLIMIT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Fetch user activities
     *
     * @param $userId
     * @param $type
     * @param $paginate
     * @return mixed
     * @throws Exception
     */
    public static function fetchUserActivities($userId, $type, $paginate = null)
    {
        try {
            $query = ActivityModel::where('locked', '=', false)
                ->where('owner', '=', $userId);

            if ($type === 'running') {
                $query->where('date_of_activity_till', '>=', date('Y-m-d H:i:s'));
            } else if ($type === 'past') {
                $query->where('date_of_activity_till', '<', date('Y-m-d H:i:s'));
            } else {
                throw new \Exception('Invalid query type: ' . $type);
            }

            if ($paginate !== null) {
                if ($type === 'running') {
                    $query->where('date_of_activity_till', '>', date('Y-m-d H:i:s', strtotime($paginate)));
                } else if ($type === 'past') {
                    $query->where('date_of_activity_till', '<', date('Y-m-d H:i:s', strtotime($paginate)));
                }
            }

            if ($type === 'running') {
                $query->orderBy('date_of_activity_from', 'asc');
            } else if ($type === 'past') {
                $query->orderBy('date_of_activity_from', 'desc');
            }

            return $query->limit(env('APP_ACTIVITYPACKLIMIT'))->get();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get count of activities with the exact same date
     * 
     * @param $date
     * @param $which
     * @return int
     * @throws Exception
     */
    public static function getSameDateCount($date, $which)
    {
        try {
            $query = ActivityModel::where('locked', '=', false);

            $date = date('Y-m-d H:i', strtotime($date));

            if ($which === 'from') {
                $query->where('date_of_activity_from', '>=', date('Y-m-d H:i:s'))->whereRaw("DATE_FORMAT(date_of_activity_from, '%Y-%m-%d %H:%i') = ?", [$date]);
            } else if ($which === 'till') {
                $query->where('date_of_activity_till', '>=', date('Y-m-d H:i:s'))->whereRaw("DATE_FORMAT(date_of_activity_till, '%Y-%m-%d %H:%i') = ?", [$date]);
            } else {
                throw new Exception('Unknown identifier: ' . $which);
            }

            return $query->count();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Query user participations
     * 
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    public static function queryUserParticipations($userId)
    {
        try {
            $query = DB::select('SELECT * FROM activity_models WHERE (id IN (SELECT activity FROM participant_models WHERE participant = ?) OR owner = ?) AND date_of_activity_till >= ? ORDER BY date_of_activity_from ASC', [$userId, $userId, date('Y-m-d H:i:s')]);
            return $query;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Lock activity
     *
     * @param $id
     * @throws Exception
     */
    public static function lockActivity($id)
    {
        try {
            $activity = static::getActivity($id);
            if ($activity) {
                $activity->locked = true;
                $activity->save();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Cancel activity
     *
     * @param $id
     * @param string $reason
     * @throws Exception
     */
    public static function cancelActivity($id, $reason = '')
    {
        try {
            $activity = static::getActivity($id);
            if ($activity) {
                if ($activity->canceled) {
                    return;
                }

                $activity->canceled = true;
                $activity->cancelReason = $reason;
                $activity->save();

                $owner = User::get($activity->owner);

                $participants = ParticipantModel::getActualParticipants($id);
                foreach ($participants as $participant) {
                    $userData = User::get($participant->participant);
                    if (($userData) && ($userData->email_on_act_canceled)) {
                        $html = view('mail.act_canceled', ['name' => $userData->name, 'activity' => $activity, 'owner' => $owner])->render();
                        MailerModel::sendMail($userData->email, __('app.activity_canceled'), $html);
                    }

                    PushModel::addNotification(__('app.activity_canceled'), __('app.activity_canceled_long', ['title' => $activity->title, 'item' => url('/activity/' . $activity->id), 'name' => $owner->name, 'profile' => url('/user/' . $owner->id)]), 'PUSH_CANCELED', $userData->id);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Perform reminder job
     * 
     * @return array
     * @throws \Exception
     */
    public static function reminderJob()
    {
        try {
            $result = [];

            $tomorrow = date('Y-m-d', strtotime('+1 day'));

            $activities = ActivityModel::where('locked', '=', false)->where('canceled', '=', false)->whereRaw('DATE(date_of_activity_from) = ?', [$tomorrow])->get();
            foreach ($activities as $activity) {
                $participants = ParticipantModel::where('activity', '=', $activity->id)->where('type', '=', ParticipantModel::PARTICIPANT_ACTUAL)->get();
                foreach ($participants as $participant) {
                    $users = User::where('id', '=', $participant->participant)->where('deactivated', '=', false)->where('account_confirm', '=', '_confirmed')->get();
                    foreach ($users as $user) {
                        $owner = User::where('id', '=', $activity->owner)->first();

                        if ($user->email_on_act_upcoming) {
                            $html = view('mail.act_upcoming', ['name' => $user->name, 'activity' => $activity, 'owner' => $owner])->render();
                            MailerModel::sendMail($user->email, __('app.activity_upcoming'), $html);
                        }

                        PushModel::addNotification(__('app.activity_upcoming'), __('app.activity_upcoming_long', ['title' => $activity->title, 'item' => url('/activity/' . $activity->id), 'name' => $owner->name, 'profile' => url('/user/' . $owner->id)]), 'PUSH_UPCOMING', $user->id);
                    
                        $result[] = array('user' => $user->email, 'activity' => $activity->slug);
                    }
                }
            }

            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
