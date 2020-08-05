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

use Illuminate\Database\Eloquent\Model;

/**
 * Class MessageModel
 *
 * Interface to private messages
 */
class MessageModel extends Model
{
    /**
     * Add message
     *
     * @param $userId
     * @param $subject
     * @param $message
     * @return int|mixed
     * @throws \Exception
     */
    public static function add($userId, $senderId, $subject, $message)
    {
        try {
            $user = User::get($userId);
            if (!$user) {
                throw new \Exception('User not found: ' . $userId);
            }

            $sender = User::get($senderId);
            if (!$sender) {
                throw new \Exception('Sender not found: ' . $senderId);
            }

            if (IgnoreModel::hasIgnored($userId, $senderId)) {
                throw new \Exception(__('app.user_no_messages'));
            }

            $msg = new MessageModel();
            $msg->userId = $userId;
            $msg->senderId = $senderId;
            $msg->subject = $subject;
            $msg->message = $message;
            $msg->save();

            PushModel::addNotification(__('app.new_message_short', ['name' => $sender->name]), __('app.new_message', ['name' => $sender->name, 'subject' => $subject, 'profile' => url('/user/' . $sender->id)]), 'PUSH_MESSAGED', $userId);

            if ($user->email_on_message) {
                $html = view('mail.message', ['msgid' => $msg->id, 'name' => $user->name, 'sender' => $sender->name, 'message' => $message])->render();
                MailerModel::sendMail($user->email, __('app.message_received'), $html);
            }

            return $msg->id;
        } catch (\Exception $e) {
            throw $e;
        }

        return 0;
    }

    /**
     * Fetch message pack
     *
     * @param $userId
     * @param $limit
     * @param null $paginate
     * @return mixed
     * @throws \Exception
     */
    public static function fetch($userId, $limit, $paginate = null)
    {
        try {
            $rowset = MessageModel::where('senderId', '=', $userId);

            if ($paginate !== null) {
                $rowset->where('id', '<', $paginate);
            }

            return $rowset->orderBy('id', 'desc')->limit($limit)->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get message thread
     *
     * @param $msgId
     * @return array
     * @throws \Exception
     */
    public static function getMessageThread($msgId)
    {
        try {
            $msg = MessageModel::where('id', '=', $msgId)->first();
            if (!$msg) {
                throw new Exception('Message not found: ' . $msgId);
            }

            $msg->seen = true;
            $msg->save();

            $previous = MessageModel::where(function($query) use ($msg) {
                $query->where('userId', '=', $msg->userId)
                    ->where('senderId', '=', $msg->senderId)
                    ->where('id', '<>', $msg->id);
            })->orWhere(function($query) use ($msg) {
                $query->where('userId', '=', $msg->senderId)
                    ->where('senderId', '=', $msg->userId);
            })->orderBy('created_at', 'desc')->get();
            foreach ($previous as $item) {
                if (!$item->seen) {
                    $item->seen = true;
                    $item->save();
                }
            }

            return array(
              'msg' => $msg,
              'previous' => $previous
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
