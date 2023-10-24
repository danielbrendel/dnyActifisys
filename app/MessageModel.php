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

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
     * @return int
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

            $channel = static::select('channel')->where('userId', '=', $userId)->where('senderId', '=', $senderId)->first();
            if (!$channel) {
                $channel = static::select('channel')->where('senderId', '=', $userId)->where('userId', '=', $senderId)->first();
                if (!$channel) {
                    $channel = md5(strval($userId) . strval($senderId) . random_bytes(55));
                } else {
                    $channel = $channel->channel;
                }
            } else {
                $channel = $channel->channel;
            }

            $list = MessageListModel::where('channel', '=', $channel)->first();
            if (!$list) {
                $list = new MessageListModel();
                $list->channel = $channel;
                $list->user1 = $userId;
                $list->user2 = $senderId;
                $list->save();
            } else {
                $list->touch();
            }

            $msg = new MessageModel();
            $msg->userId = $userId;
            $msg->senderId = $senderId;
            $msg->channel = $channel;
            $msg->subject = htmlspecialchars($subject);
            $msg->message = \Purifier::clean($message);
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
     * Post image to user
     * 
     * @param $userId
     * @param $senderId
     * @param $subject
     * @return int
     * @throws \Exception
     */
    public static function image($userId, $senderId, $subject)
    {
        try {
            $att = request()->file('image');
            if ($att != null) {
                if ($att->getSize() > env('APP_MAXUPLOADSIZE')) {
                    throw new Exception(__('app.post_upload_size_exceeded'));
                }

                $fname = uniqid('', true) . md5(random_bytes(55));
                $fext = $att->getClientOriginalExtension();

                $att->move(public_path() . '/gfx/uploads/', $fname . '.' . $fext);

                $baseFile = public_path() . '/gfx/uploads/' . $fname;
                $fullFile = $baseFile . '.' . $fext;

                if (ImageModel::isValidImage(public_path() . '/gfx/uploads/' . $fname . '.' . $fext)) {
                    if (!ImageModel::createThumbFile($fullFile, ImageModel::getImageType($fext, $baseFile), $baseFile, $fext)) {
                        throw new \Exception('createThumbFile failed', 500);
                    }
                } else {
                    unlink(public_path() . '/gfx/uploads/' . $fname . '.' . $fext);
                    throw new \Exception(__('app.image_invalid_file_type'));
                }

                return MessageModel::add($userId, $senderId, $subject, '<a href="' . asset('/gfx/uploads/' . $fname . '_thumb.' . $fext) . '"><img src="' . asset('/gfx/uploads/' . $fname . '_thumb.' . $fext) . '" alt="image"/></a>');
            }

            return 0;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Fetch message pack
     *
     * @param $userId
     * @param $limit
     * @param null $paginate
     * @param null $direction
     * @return mixed
     * @throws Exception
     */
    public static function fetch($userId, $limit, $paginate = null, $direction = null)
    {
        try {
            $channels = MessageListModel::where(function($channels) use ($userId) {
                $channels->where('user1', '=', $userId)
                    ->orWhere('user2', '=', $userId);
            });
            
            if ($paginate !== null) {
                $channels->where('updated_at', '<', $paginate);
            }

            $channels = $channels->orderBy('updated_at', 'desc')->limit($limit)->get();

            foreach ($channels as &$item) {
                $item->lm = static::where('channel', '=', $item->channel)->orderBy('id', 'desc')->first();
                if ($item->lm) {
                    if ($item->lm->senderId === auth()->id()) {
                        if (!$item->lm->seen) {
                            $item->lm->seen = true;
                        }
                    }
                }
            }

            return $channels;
        } catch (Exception $e) {
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

            if (($msg->userId !== auth()->id()) && ($msg->senderId !== auth()->id())) {
                throw new \Exception('Access denied');
            }

            return $msg;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get thread pack
     * 
     * @param $ident
     * @param $limit
     * @param $paginate
     * @return array
     * @throws \Exception
     */
    public static function queryThreadPack($ident, $limit, $paginate = null)
    {
        try {
            $query = static::where('channel', '=', $ident)->where(function($query){
                $query->where('userId', '=', auth()->id())->orWhere('senderId', '=', auth()->id());
            });

            if ($paginate !== null) {
                $query->where('id', '<', $paginate);
            }

            $items = $query->orderBy('id', 'desc')->limit($limit)->get();

            foreach ($items as &$item) {
                if ($item->senderId !== auth()->id()) {
                    $item->seen = true;
                    $item->save();
                }
            }

            $items = $items->toArray();

            foreach ($items as &$item) {
                $item['sender'] = User::get($item['senderId'])->toArray();
                $item['receiver'] = User::get($item['userId'])->toArray();
                $item['diffForHumans'] = Carbon::parse($item['created_at'])->diffForHumans();
                //$item['message'] = AppModel::translateLinks($item['message']);
            }

            return $items;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get chat with partner
     * 
     * @param $self
     * @param $partner
     * @return mixed
     * @throws \Exception
     */
    public static function getChatWithUser($self, $partner)
    {
        try {
            $query = static::where(function($query) use($self, $partner) {
                $query->where('userId', '=', $self)
                    ->where('senderId', '=', $partner);
            })->orWhere(function($query) use ($self, $partner) {
                $query->where('userId', '=', $partner)
                    ->where('senderId', '=', $self);
            });

            return $query->orderBy('id', 'desc')->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get amount of unread messages
     *
     * @param $userId
     * @return int
     * @throws \Exception
     */
    public static function unreadCount($userId)
    {
        try {
            return static::where('userId', '=', $userId)->where('seen', '=', false)->count();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
