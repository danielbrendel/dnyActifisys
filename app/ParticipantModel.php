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

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ParticipantModel
 *
 * Interface to participants
 */
class ParticipantModel extends Model
{
    const PARTICIPANT_ACTUAL = 1;
    const PARTICIPANT_POTENTIAL = 2;

    /**
     * Add participant or potential participant
     * @param $user
     * @param $activity
     * @param $type
     * @throws Exception
     */
    public static function add($user, $activity, $type)
    {
        try {
            if ($type === self::PARTICIPANT_ACTUAL) {
                $count = ParticipantModel::where('activity', '=', $activity)->where('type', '=', $type)->count();
                $act = ActivityModel::getActivity($activity);
                if (($act->limit > 0) && ($count >= $act->limit)) {
                    throw new Exception(__('app.activity_limit_reached'));
                }
            }

            $exists = ParticipantModel::where('participant', '=', $user)->where('activity', '=', $activity)->where('type', '=', $type)->first();
            if (!$exists) {
                $item = new ParticipantModel();
                $item->participant = $user;
                $item->activity = $activity;
                $item->type = $type;
                $item->save();

                if ($type == self::PARTICIPANT_ACTUAL) {
                    static::remove($user, $activity, self::PARTICIPANT_POTENTIAL);
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove participant or potential participant
     *
     * @param $user
     * @param $activity
     * @param $type
     * @throws Exception
     */
    public static function remove($user, $activity, $type)
    {
        try {
            $item = ParticipantModel::where('participant', '=', $user)->where('activity', '=', $activity)->where('type', '=', $type)->first();
            if ($item) {
                $item->delete();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if a user is an actual participant or potentially interested
     *
     * @param $user
     * @param $activity
     * @param $type
     * @return bool
     * @throws Exception
     */
    public static function has($user, $activity, $type)
    {
        try {
            return ParticipantModel::where('participant', '=', $user)->where('activity', '=', $activity)->where('type', '=', $type)->count() > 0;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get actual participants
     *
     * @param $activity
     * @return mixed
     * @throws Exception
     */
    public static function getActualParticipants($activity)
    {
        try {
            return ParticipantModel::where('activity', '=', $activity)->where('type', '=', self::PARTICIPANT_ACTUAL)->get();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get potential participants
     *
     * @param $activity
     * @return mixed
     * @throws Exception
     */
    public static function getPotentialParticipants($activity)
    {
        try {
            return ParticipantModel::where('activity', '=', $activity)->where('type', '=', self::PARTICIPANT_POTENTIAL)->get();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
