<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

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

            if (env('FIREBASE_ENABLE', false)) {
                $user = User::get($userId);
                if (($user) && (isset($user->device_token)) && (is_string($user->device_token)) && (strlen($user->device_token) > 0)) {
                    static::sendCloudNotification($shortMsg, $longMsg, $user->device_token);
                }
            }
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

    /**
     * Send cloud notification to Google Firebase
     * 
     * @param $title
     * @param $body
     * @param $device_token
     * @return void
     * @throws \Exception
     */
    private static function sendCloudNotification($title, $body, $device_token)
    {
        try {
            $curl = curl_init();

            $headers = [
                'Content-Type: application/json',
                'Authorization: key=' . env('FIREBASE_KEY')
            ];

            $data = [
                'to' => $device_token,
                'data' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => asset('logo.png')
                ]
            ];

            curl_setopt($curl, CURLOPT_URL, env('FIREBASE_ENDPOINT'));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

            $result = curl_exec($curl);
            $result_data = json_decode($result);
            if ((!isset($result_data->success)) || (!$result_data->success)) {
                //throw new \Exception('Failed to deliver Firebase cloud message');
            }

            curl_close($curl);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
