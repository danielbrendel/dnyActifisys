<?php

/*
    ComAct (dnyComAct) developed by Daniel Brendel

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

            $item = new ActivityModel();
            $item->owner = $owner;
            $item->title = $attr['title'];
            $item->description = $attr['description'];
            $item->date_of_activity = date('Y-m-d H:i:s', strtotime($attr['date_of_activity'] . ' ' . $attr['time_of_activity']));
            $item->location = strtolower(trim($attr['location']));
            $item->limit = $attr['limit'];
            $item->save();

            ParticipantModel::add($owner, $item->id, ParticipantModel::PARTICIPANT_ACTUAL);

            $favs = FavoritesModel::where('entityId', '=', $owner)->where('type', '=', 'ENT_USER')->get();
            foreach ($favs as $fav) {
                PushModel::addNotification(__('app.activity_created_short'), __('app.activity_created_long', ['name' => $user->name, 'title' => $attr['title']]), 'PUSH_CREATED', $fav->userId);

                $favUser = User::get($fav->userId);
                if (($favUser) && ($favUser->email_on_fav_created)) {
                    $html = view('mail.fav_created', ['name' => $favUser->name, 'creator' => $user->id, 'title' => $attr['title'], 'description' => $attr['description']])->render();
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

            $item->title = $attr['title'];
            $item->description = $attr['description'];
            $item->date_of_activity = date('Y-m-d H:i:s', strtotime($attr['date_of_activity'] . ' ' . $attr['time_of_activity']));
            $item->location = strtolower(trim($attr['location']));
            $item->limit = $attr['limit'];
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
     * Fetch activity package
     *
     * @param $id
     * @param null $city
     * @param null $paginate
     * @return mixed
     * @throws Exception
     */
    public static function fetchActivities($city = null, $paginate = null)
    {
        try {
            $activities = ActivityModel::where('date_of_activity', '>=', date('Y-m-d H:i:s'))->where('locked', '=', false)->where('canceled', '=', false);

            if ($city !== null) {
                $activities->where('location', 'like', '%' . strtolower($city) . '%');
            }

            if ($paginate !== null) {
                $activities->where('date_of_activity', '>', date('Y-m-d H:i:s', strtotime($paginate)));
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

                    PushModel::addNotification(__('app.activity_canceled'), __('app.activity_canceled_long', ['title' => $activity->title, 'owner' => $owner->name]), 'PUSH_CANCELED', $userData->id);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}