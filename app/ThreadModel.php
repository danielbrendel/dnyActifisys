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

/**
 * Class ThreadModel
 *
 * Interface to activity comment threads
 */
class ThreadModel extends Model
{
    const MAX_PREVIEW_MSG = 25;

    /**
     * Add thread comment
     *
     * @param $userId
     * @param $postId
     * @param $text
     * @return mixed
     * @throws \Exception
     */
    public static function add($userId, $activityId, $text)
    {
        try {
            $act = ActivityModel::getActivity($activityId);
            if (!$act) {
                throw new \Exception('Activity not found: ' . $activityId);
            }

            $thread = new ThreadModel();
            $thread->userId = $userId;
            $thread->activityId = $activityId;
            $thread->text = htmlspecialchars($text);
            $thread->save();

            $user = User::get($act->userId);
            if (($user) && ($userId !== $act->userId)) {
                PushModel::addNotification(__('app.user_posted_comment_short', ['name' => $user->name]), __('app.user_posted_comment', ['name' => $user->name, 'profile' => url('/user/' . $user->id), 'msg' => ((strlen($text) > self::MAX_PREVIEW_MSG) ? substr($text, 0, self::MAX_PREVIEW_MSG) . '...' : $text), 'item' => url('/p/' . $postId . '?c=' . $thread->id . '#' . $thread->id)]), 'PUSH_COMMENTED', $user->id);
            }

            /*$mentionedNames = AppModel::getMentionList($text);
            foreach ($mentionedNames as $name) {
                $curUser = User::getByUsername($name);
                if ($curUser) {
                    PushModel::addNotification(__('app.user_mentioned_short', ['name' => $user->name]), __('app.user_mentioned', ['name' => $user->name, 'item' => url('/p/' . $post->id . '#' . $thread->id)]), 'PUSH_MENTIONED', $curUser->id);
                }
            }*/

            return $thread->id;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove thread entry
     *
     * @param $threadId
     * @throws \Exception
     */
    public static function remove($threadId, $userId = null)
    {
        try {
            if ($userId === null) {
                $userId = auth()->id();
            }

            $thread = ThreadModel::where('id', '=', $threadId)->where('userId', '=', $userId)->first();
            if ($thread) {
                $thread->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit thread entry
     *
     * @param $threadId
     * @param $newText
     * @param null $userId
     * @throws \Exception
     */
    public static function edit($threadId, $newText, $userId = null)
    {
        try {
            if ($userId === null) {
                $userId = auth()->id();
            }

            $thread = ThreadModel::where('id', '=', $threadId)->where('userId', '=', $userId)->first();
            if ($thread) {
                $thread->text = $newText;
                $thread->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get thread posts from activity
     * @param $id
     * @param null $paginate
     * @param bool $special
     * @return mixed
     * @throws \Exception
     */
    public static function getFromActivity($id, $paginate = null, $special = false)
    {
        try {
            $threads = ThreadModel::where('activityId', '=', $id)->where('locked', '=', false)->where('parentId', '=', 0);
            if ($paginate !== null) {
                $initialPaginate = $paginate;

                if ($special === false) {
                    for ($i = 0; $i < env('APP_THREADPACKLIMIT'); $i++) {
                        $coll = static::getFromActivity($id, $paginate, true);
                        $paginate = (count($coll) > 0) ? $coll[count($coll)-1]->id : 0;
                    }
                }

                if ($special) {
                    $threads->where('id', '<', $paginate)->orderBy('id', 'desc');
                } else {
                    $threads->where('id', '>=', $paginate)->where('id', '<', $initialPaginate)->orderBy('id', 'asc');
                }
            } else {
                $threads->orderBy('id', 'desc');
            }

            return $threads->limit((($special) ? 1 : env('APP_THREADPACKLIMIT')))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get sub thread count
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public static function getSubCount($id)
    {
        try {
            return ThreadModel::where('parentId', '=', $id)->where('locked', '=', false)->count();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get sub thread posts
     *
     * @param $id
     * @param null $paginate
     * @return mixed
     * @throws \Exception
     */
    public static function fetchSubThread($id, $paginate = null)
    {
        try {
            $threads = ThreadModel::where('parentId', '=', $id)->where('locked', '=', false);
            if ($paginate !== null) {
                $threads->where('id', '>', $paginate);
            }

            return $threads->orderBy('id', 'asc')->limit(env('APP_THREADPACKLIMIT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add thread reply message
     *
     * @param $userId
     * @param $parentId
     * @param $text
     * @return mixed
     * @throws \Exception
     */
    public static function reply($userId, $parentId, $text)
    {
        try {
            $parent = ThreadModel::where('id', '=', $parentId)->where('locked', '=', false)->where('parentId', '=', 0)->first();
            if (!$parent) {
                throw new \Exception('Parent item not found for ' . $parentId);
            }

            $thread = new ThreadModel();
            $thread->userId = $userId;
            $thread->activityId = $parent->activityId;
            $thread->parentId = $parentId;
            $thread->text = htmlspecialchars($text);
            $thread->save();

            $user = User::get($userId);
            if (($user) && ($userId !== $parent->userId)) {
                PushModel::addNotification(__('app.user_replied_comment_short', ['name' => $user->name]), __('app.user_replied_comment', ['name' => $user->name, 'profile' => url('/user/' . $user->slug),'msg' => ((strlen($text) > self::MAX_PREVIEW_MSG) ? substr($text, 0, self::MAX_PREVIEW_MSG) . '...' : $text), 'item' => url('/activity/' . $parent->activityId . '#' . $thread->id)]), 'PUSH_COMMENTED', $parent->userId);
            }

            return $thread;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
