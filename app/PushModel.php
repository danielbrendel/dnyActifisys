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

use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Class PushModel
 *
 * Represents the push interface
 */
class PushModel extends Model
{
    /**
     * Validate notification type
     *
     * @param $type
     * @throws \Exception
     */
    private static function validatePushType($type)
    {
        try {
            $types = array('PUSH_PARTICIPATED', 'PUSH_CREATED', 'PUSH_COMMENTED', 'PUSH_FAVORITED', 'PUSH_MESSAGED', 'PUSH_CANCELED');
            if (!in_array($type, $types)) {
                throw new \Exception('Invalid notification type: ' . $type);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add a notification to the list
     *
     * @param $shortMsg
     * @param $longMsg
     * @param $type
     * @param int $userId The user ID
     * @return void
     * @throws \Exception
     */
    public static function addNotification($shortMsg, $longMsg, $type, $userId)
    {
        try {
            static::validatePushType($type);

            $entry = new PushModel();
            $entry->type = $type;
            $entry->shortMsg = $shortMsg;
            $entry->longMsg = $longMsg;
            $entry->seen = false;
            $entry->userId = $userId;
            $entry->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all unseen notifications and mark them as seen
     *
     * @param int $userId The ID of the user
     * @param bool $markAsSeen
     * @return mixed Items or null if non exist
     * @throws \Exception
     */
    public static function getUnseenNotifications($userId, $markAsSeen = true)
    {
        try {
            $items = PushModel::where('userId', '=', $userId)->where('seen', '=', false)->get();

            if ($markAsSeen) {
                foreach ($items as $item) {
                    $item->seen = true;
                    $item->save();
                }
            }

            return $items;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get notifications of user
     *
     * @param $userId
     * @param $limit
     * @param null $paginate
     * @return mixed
     * @throws \Exception
     */
    public static function getNotifications($userId, $limit, $paginate = null)
    {
        try {
            $rowset = PushModel::where('userId', '=', $userId)->where('seen', '=', true);

            if ($paginate !== null) {
                $rowset->where('id', '<', $paginate);
            }

            return $rowset->orderBy('id', 'desc')->limit($limit)->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Mark unseen notifications as seen
     *
     * @param $userId
     * @throws \Exception
     */
    public static function markSeen($userId)
    {
        try {
            $items = PushModel::where('userId', '=', $userId)->where('seen', '=', false)->get();

            foreach ($items as $item) {
                $item->seen = true;
                $item->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
