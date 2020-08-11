<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
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
        'title', 'description', 'date_of_activity', 'location', 'limit'
    ];

    protected $dates = [
        'date_of_activity', 'created_at', 'updated_at'
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

            $dtActivity = new DateTime($attr['date_of_activity']);
            $dtNow = new DateTime();
            if ($dtActivity < $dtNow) {
                throw new Exception(__('app.date_is_in_past'));
            }

            $taglist = static::getTagList($attr['description']);

            if (($attr['only_verified'] == true) && (VerifyModel::getState($owner) != VerifyModel::STATE_VERIFIED)) {
                throw new Exception(__('app.only_for_verified_users'));
            }

            $item = new ActivityModel();
            $item->owner = $owner;
            $item->title = htmlspecialchars($attr['title']);
            $item->description = htmlspecialchars($attr['description']);
            $item->tags = (count($taglist) > 0) ? implode(' ', $taglist) . ' ' : '';
            $item->date_of_activity = date('Y-m-d H:i:s', strtotime($attr['date_of_activity'] . ' ' . $attr['time_of_activity']));
            $item->category = $attr['category'];
            $item->location = htmlspecialchars(strtolower(trim($attr['location'])));
            $item->limit = $attr['limit'];
            $item->only_gender = $attr['only_gender'];
            $item->only_verified = $attr['only_verified'];
            $item->save();

            $item->slug = Str::slug(strval($item->id) . ' ' . $item->title, '-');
            $item->save();

            ParticipantModel::add($owner, $item->id, ParticipantModel::PARTICIPANT_ACTUAL);

            $favs = FavoritesModel::where('entityId', '=', $owner)->where('type', '=', 'ENT_USER')->get();
            foreach ($favs as $fav) {
                PushModel::addNotification(__('app.activity_created_short'), __('app.activity_created_long', ['name' => $user->name, 'profile' => url('/user/' . $user->id), 'title' => $attr['title'], 'item' => url('/activity/' . $item->id)]), 'PUSH_CREATED', $fav->userId);

                $favUser = User::get($fav->userId);
                if (($favUser) && ($favUser->email_on_fav_created)) {
                    $html = view('mail.fav_created', ['name' => $favUser->name, 'creator' => $user->name, 'activity' => $item])->render();
                    MailerModel::sendMail($user->email, __('app.activity_created'), $html);
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

            $dtActivity = new DateTime($attr['date_of_activity']);
            $dtNow = new DateTime();
            if ($dtActivity < $dtNow) {
                throw new Exception(__('app.date_is_in_past'));
            }

            if (($attr['only_verified'] == true) && (VerifyModel::getState($user->id) != VerifyModel::STATE_VERIFIED)) {
                throw new Exception(__('app.only_for_verified_users'));
            }

            $taglist = static::getTagList($attr['description']);

            $item->title = htmlspecialchars($attr['title']);
            $item->description = htmlspecialchars($attr['description']);
            $item->slug = Str::slug(strval($item->id) . ' ' . $item->title, '-');
            $item->tags = (count($taglist) > 0) ? implode(' ', $taglist) . ' ' : '';
            $item->date_of_activity = date('Y-m-d H:i:s', strtotime($attr['date_of_activity'] . ' ' . $attr['time_of_activity']));
            $item->category = $attr['category'];
            $item->location = htmlspecialchars(strtolower(trim($attr['location'])));
            $item->limit = $attr['limit'];
            $item->only_gender = $attr['only_gender'];
            $item->only_verified = $attr['only_verified'];
            $item->save();
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
     * @param null $city
     * @param null $paginate
     * @param null $dateFrom
     * @param null $dateTill
     * @param null $tag
     * @param null $category
     * @return mixed
     * @throws Exception
     */
    public static function fetchActivities($city = null, $paginate = null, $dateFrom = null, $dateTill = null, $tag = null, $category = null)
    {
        try {
            $activities = ActivityModel::where('date_of_activity', '>=', date('Y-m-d H:i:s'))->where('locked', '=', false)->where('canceled', '=', false);

            if ($city !== null) {
                $activities->where('location', 'like', '%' . strtolower($city) . '%');
            }

            if ($paginate !== null) {
                $activities->where('date_of_activity', '>', date('Y-m-d H:i:s', strtotime($paginate)));
            }

            if ($dateFrom !== null) {
                $asDate = date('Y-m-d H:i:s', strtotime($dateFrom));
                if ((new DateTime($asDate) < (new DateTime('now')))) {
                    throw new Exception(__('app.date_from_smaller_than_now'));
                }

                $activities->where('date_of_activity', '>=', $asDate);
            }

            if ($dateTill !== null) {
                $asDate = date('Y-m-d H:i:s', strtotime($dateTill));
                if ((new DateTime($asDate) < (new DateTime('now')))) {
                    throw new Exception(__('app.date_till_smaller_than_now'));
                }

                if ($dateFrom !== null) {
                    $asDate2 = date('Y-m-d H:i:s', strtotime($dateFrom));
                    if ((new DateTime($asDate) < (new DateTime($asDate2)))) {
                        throw new Exception(__('app.till_date_must_not_be_less_than_from_date'));
                    }
                }

                $activities->where('date_of_activity', '<=', $asDate);
            }

            if ($tag !== null) {
                $activities->where('tags', 'LIKE', '%' . $tag . ' %');
            }

            if ($category !== null) {
                $activities->where('category', '=', $category);
            }

            return $activities->orderBy('date_of_activity', 'asc')->limit(env('APP_ACTIVITYPACKLIMIT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Fetch user activities
     *
     * @param $userId
     * @return mixed
     * @throws Exception
     */
    public static function fetchUserActivities($userId)
    {
        try {
            return ActivityModel::where('date_of_activity', '>=', date('Y-m-d H:i:s'))
                ->where('locked', '=', false)
                ->where('canceled', '=', false)
                ->where('owner', '=', $userId)
                ->get();
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

                    PushModel::addNotification(__('app.activity_canceled'), __('app.activity_canceled_long', ['title' => $activity->title, 'item' => url('/activity/' . $activity->id), 'owner' => $owner->name, 'profile' => url('/user/' . $owner->id)]), 'PUSH_CANCELED', $userData->id);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
