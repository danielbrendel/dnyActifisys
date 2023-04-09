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
 * Class ForumThreadModel
 *
 * Interface to forum thread
 */
class ForumThreadModel extends Model
{
    /**
     * Add new forum thread
     * 
     * @param $ownerId
     * @param $forumId
     * @param $title
     * @param $initialMessage
	 * @param $sticky
     * @return int
     * @throws Exception
     */
    public static function add($ownerId, $forumId, $title, $initialMessage, $sticky = false)
    {
        try {
            $item = new ForumThreadModel;
            $item->ownerId = $ownerId;
            $item->forumId = $forumId;
            $item->title = $title;
            $item->sticky = $sticky;
            $item->save();

            ForumPostModel::add($item->id, $ownerId, $initialMessage);

            return $item->id;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit forum thread
     * 
     * @param $threadId
     * @param $title
     * @param $sticky
     * @param $locked
     * @return void
     * @throws Exception
     */
    public static function edit($threadId, $title, $sticky, $locked)
    {
        try {
            $item = ForumThreadModel::where('id', '=', $threadId)->first();
            if (!$item) {
                throw new Exception('Item not found: ' . $threadId);
            }

            $item->title = $title;
            $item->sticky = $sticky;
            $item->locked = $locked;
            $item->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get list of threads
     * 
     * @param $forumId
     * @param $paginate
     * @param $searchPhrase
     * @return mixed
     * @throws Exception
     */
    public static function list($forumId, $paginate = null, $searchPhrase = null)
    {
        try {
            $query = ForumThreadModel::where('sticky', '=', false)->where('forumId', '=', $forumId);

            if ($paginate !== null) {
                $query->where('updated_at', '<', $paginate);
            }

            if ($searchPhrase !== null) {
                $query->where('title', 'LIKE', '%' . $searchPhrase . '%');
            }

            $collection = $query->orderBy('updated_at', 'desc')->limit(env('APP_FORUMPACKLIMIT'))->get();

            foreach ($collection as &$item) {
                $postId = ForumPostModel::where('threadId', '=', $item->id)->max('id');
                $postData = ForumPostModel::where('id', '=', $postId)->first();
                if ($postData == null) { dd($item->id);}
                $item->user = User::where('id', '=', $postData->userId)->first();
                $item->user->diffForHumans = $postData->created_at->diffForHumans();
            }

            return $collection->toArray();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get sticky threads of a forum
     * 
     * @param $forumId
     * @return mixed
     * @throws Exception
     */
    public static function getStickies($forumId)
    {
        try {
            $stickies = ForumThreadModel::where('forumId', '=', $forumId)->where('sticky', '=', true)->get();
            
            foreach ($stickies as &$sticky) {
                $postId = ForumPostModel::where('threadId', '=', $sticky->id)->max('id');
                $postData = ForumPostModel::where('id', '=', $postId)->first();
                $sticky->user = User::where('id', '=', $postData->userId)->first();
                $sticky->user->diffForHumans = $postData->created_at->diffForHumans();
            }

            return $stickies;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
