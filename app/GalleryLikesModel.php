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
 * Class GalleryLikesModel
 * 
 * Interface to gallery item likes management
 */
class GalleryLikesModel extends Model
{
    /**
     * Add like for item
     * 
     * @param $itemId
     * @param $userId
     * @return void
     * @throws \Exception
     */
    public static function addLike($itemId, $userId)
    {
        try {
            $exists = GalleryLikesModel::where('galleryId', '=', $itemId)->where('userId', '=', $userId)->count();
            if ($exists == 0) {
                $item = new GalleryLikesModel;
                $item->galleryId = $itemId;
                $item->userId = $userId;
                $item->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove like for item
     * 
     * @param $itemId
     * @param $userId
     * @return void
     * @throws \Exception
     */
    public static function removeLike($itemId, $userId)
    {
        try {
            $item = GalleryLikesModel::where('galleryId', '=', $itemId)->where('userId', '=', $userId)->first();
            if ($item) {
                $item->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove all likes of an item
     * 
     * @param $itemId
     * @return void
     * @throws \Exception
     */
    public static function removeForItem($itemId)
    {
        $items = GalleryLikesModel::where('galleryId', '=', $itemId)->get();
        foreach ($items as $item) {
            $item->delete();
        }
    }

    /**
     * Get likes of item
     * 
     * @param $itemId
     * @return int
     * @throws \Exception
     */
    public static function getForItem($itemId)
    {
        try {
            $result = GalleryLikesModel::where('galleryId', '=', $itemId)->count();
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Indicate if logged in user has liked the given item
     * 
     * @param $itemId
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public static function hasUserLiked($itemId, $userId = null)
    {
        try {
            if (($userId === null) && (\Auth::guest())) {
                return false;
            }

            if ($userId === null) {
                $userId = auth()->id();
            }

            return GalleryLikesModel::where('galleryId', '=', $itemId)->where('userId', '=', $userId)->count() > 0;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
