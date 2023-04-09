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

use Illuminate\Database\Eloquent\Model;

/**
 * Class ForumPostModel
 *
 * Interface to forum postings
 */
class ForumPostModel extends Model
{
    /**
     * Add forum thread posting
     * 
     * @param $threadId
     * @param $userId
     * @param $message
     * @return void
     * @throws Exception
     */
    public static function add($threadId, $userId, $message)
    {
        try {
            $thread = ForumThreadModel::where('id', '=', $threadId)->first();
            if ((!$thread) || ($thread->locked)) {
                throw new Exception(__('app.thread_not_found_or_locked'));
            }

            $thread->touch();

            $item = new ForumPostModel;
            $item->threadId = $threadId;
            $item->userId = $userId;
            $item->message = $message;
            $item->save();

            if ($thread->ownerId !== $userId) {
                $user = User::get($userId);
                PushModel::addNotification(__('app.forum_reply_short'), __('app.forum_reply_long', ['name' => $user->name, 'url' => url('/forum/thread/' . $threadId . '/show'), 'thread' => $thread->title]), 'PUSH_FORUMREPLY', $thread->ownerId);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit forum post
     * 
     * @param $postId
     * @param $message
     * @return void
     * @throws Exception
     */
    public static function edit($postId, $message)
    {
        try {
            $post = ForumPostModel::where('id', '=', $postId)->first();
            if (!$post) {
                throw new Exception('Post not found: ' . $postId);
            }

            $user = User::getByAuthId();
            if (!(($user) && (($post->userId === $user->id) || (($user->maintainer) || (!$user->admin))))) {
                throw new Exception(__('app.insufficient_permissions'));
            }

            $post->message = $message;
            $post->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get postings of a thread
     * 
     * @param $threadId
     * @param $paginate
     * @return mixed
     * @throws Exception
     */
    public static function getPosts($threadId, $paginate = null)
    {
        try {
            $query = ForumPostModel::where('threadId', '=', $threadId);

            if ($paginate !== null) {
                $query->where('id', '>', $paginate);
            }

            $collection = $query->limit(env('APP_FORUMPACKLIMIT'))->get();

            foreach ($collection as &$item) {
                $item->user = User::get($item->userId);
                $item->diffForHumans = $item->created_at->diffForHumans();

                if ($item->locked) {
                    $item->message = __('app.forum_post_locked');
                }

                $item->updatedAtDiff = $item->updated_at->diffForHumans();
            }

            return $collection->toArray();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Lock forum post
     * 
     * @param $id
     * @return void
     * @throws Exception
     */
    public static function lock($id)
    {
        try {
            $user = User::getByAuthId();
            if ((!$user) || (!$user->maintainer) || (!$user->admin)) {
                throw new Exception(__('app.insufficient_permissions'));
            }

            $post = ForumPostModel::where('id', '=', $id)->first();
            $post->locked = true;
            $post->save();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
