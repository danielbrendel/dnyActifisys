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
use Illuminate\Support\Facades\Cache;

/**
 * Class FavoritesModel
 *
 * Interface to favorites
 */
class FavoritesModel extends Model
{
    const MAX_SHORT_NAME = 15;

    /**
     * Validate entity type
     *
     * @param $entType
     * @throws \Exception
     */
    public static function validateEntityType($entType)
    {
        try {
            $types = array('ENT_USER');

            if (!in_array($entType, $types)) {
                throw new \Exception('Invalid entity type: ' . $entType);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Add new favorite entry
     *
     * @param $userId
     * @param $entityId
     * @param $entType
     * @return FavoritesModel
     * @throws \Exception
     */
    public static function add($userId, $entityId, $entType)
    {
        try {
            static::validateEntityType($entType);

            $exists = FavoritesModel::where('userId', '=', $userId)->where('entityId', '=', $entityId)->where('type', '=', $entType)->count();
            if (!$exists) {
                $entry = new FavoritesModel();
                $entry->userId = $userId;
                $entry->entityId = $entityId;
                $entry->type = $entType;
                $entry->save();

                if ($entType === 'ENT_USER') {
                    $user = User::get($userId);
                    if ($user) {
                        PushModel::addNotification(__('app.added_to_favorites_short'), __('app.added_to_favorites', ['name' => $user->username]), 'PUSH_FAVORITED', $entityId);
                    }
                }

                return $entry;
            }

            return $exists;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove favorite
     *
     * @param $userId
     * @param $entityId
     * @param $entType
     * @throws \Exception
     */
    public static function remove($userId, $entityId, $entType)
    {
        try {
            static::validateEntityType($entType);

            $exists = FavoritesModel::where('userId', '=', $userId)->where('entityId', '=', $entityId)->where('type', '=', $entType)->first();
            if ($exists) {
                $exists->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if user as favorited a specific entity
     *
     * @param $userId
     * @param $entityId
     * @param $entType
     * @return bool
     * @throws \Exception
     */
    public static function hasUserFavorited($userId, $entityId, $entType)
    {
        try {
            $exists = FavoritesModel::where('userId', '=', $userId)->where('entityId', '=', $entityId)->where('type', '=', $entType)->count();

            return $exists > 0;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get favorites of user
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public static function getForUser($id)
    {
        try {
            return FavoritesModel::where('userId', '=', $id)->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get favorites with details
     *
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public static function getDetailedForUser($id)
    {
        try {
            $favorites = FavoritesModel::getForUser($id);
            foreach ($favorites as &$favorite) {
                if ($favorite->type === 'ENT_USER') {
                    $user = User::get($favorite->entityId);
                    $favorite->name = $user->name;
                    $favorite->short_name = AppModel::getShortExpression($favorite->name);
                    $favorite->avatar = $user->avatar;
                    $favorite->diffForHumans = $favorite->created_at->diffForHumans();
                    $favorite->activityCount = ActivityModel::where('canceled', '=', false)->where('locked', '=', false)->where('owner', '=', $user->id)->where('date_of_activity', '>', date('Y-m-d H:i:s'))->count();
                }
            }

            return $favorites;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
