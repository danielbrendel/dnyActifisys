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
            $item->date_of_activity = $attr['date_of_activity'];
            $item->location = $attr['location'];
            $item->limit = $attr['limit'];
            $item->save();

            ParticipantModel::add($owner, $item->id, ParticipantModel::PARTICIPANT_ACTUAL);

            return $item->id;
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
     * @throws Exception
     */
    public static function cancelActivity($id)
    {
        try {
            $activity = static::getActivity($id);
            if ($activity) {
                $activity->canceled = true;
                $activity->save();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
