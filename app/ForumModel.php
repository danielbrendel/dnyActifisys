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

/**
 * Class ForumModel
 *
 * Interface to forum
 */
class ForumModel extends Model
{
    /**
     * Create new forum
     *
     * @param $name
     * @param $description
     * @return int
     * @throws Exception
     */
    public static function add($name, $description)
    {
        try {
            $item = new ForumModel;
            $item->name = $name;
            $item->description = $description;
            $item->locked = false;
            $item->save();

            return $item->id;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit existing forum
     *
     * @param $id
     * @param $name
     * @param $description
     * @return void
     * @throws Exception
     */
    public static function edit($id, $name, $description)
    {
        try {
            $item = ForumModel::where('locked', '=', false)->where('id', '=', $id)->first();
            if (!$item) {
                throw new Exception('Item not found: ' . $id);
            }

            $item->name = $name;
            $item->description = $description;
            $item->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Lock/Unlock forum
     *
     * @param $id
     * @param $locked
     * @return void
     * @throws Exception
     */
    public static function lock($id, $locked = true)
    {
        try {
            $item = ForumModel::where('locked', '=', false)->where('id', '=', $id)->first();
            if (!$item) {
                throw new Exception('Item not found: ' . $id);
            }

            $item->locked = $locked;
            $item->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove forum and its content
     *
     * @param $id
     * @return void
     * @throws Exception
     */
    public static function remove($id)
    {
        try {
            $forum = ForumModel::where('id', '=', $id)->first();
            if ($forum) {
                $threads = ForumThreadModel::where('forumId', '=', $forum->id)->get();
                foreach ($threads as $thread) {
                    $posts = ForumPostModel::where('threadId', '=', $thread->id)->get();
                    foreach ($posts as $post) {
                        $post->delete();
                    }

                    $thread->delete();
                }

                $forum->delete();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Query forum list
     *
     * @param $paginate
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public static function queryList($paginate, $name)
    {
        try {
            $query = ForumModel::where('locked', '=', false);

            if ($paginate !== null) {
                $query->where('id', '>', $paginate);
            }

            if ((is_string($name)) && (strlen($name) > 0)) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . trim(strtolower($name)) . '%']);
            }

            $collection = $query->limit(env('APP_FORUMPACKLIMIT'))->get();

            foreach ($collection as &$item) {
                $item->lastUser = null;

                $lastThread = ForumThreadModel::where('locked', '=', false)->where('forumId', '=', $item->id)->max('id');
                if ($lastThread) {
                    $lastPost = ForumPostModel::where('locked', '=', false)->where('threadId', '=', $lastThread)->max('id');
                    if ($lastPost) {
                        $lastPost = ForumPostModel::where('id', '=', $lastPost)->first();

                        $item->lastUser = User::get($lastPost->userId);
                        $item->lastUser->diffForHumans = $lastPost->created_at->diffForHumans();
                        $item->lastUser->threadId = $lastThread;
                    }
                }
            }

            return $collection->toArray();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
