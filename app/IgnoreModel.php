<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class IgnoreModel
 *
 * Interface to ignore function
 */
class IgnoreModel extends Model
{
    /**
     * Add to ignore list
     *
     * @param $userId
     * @param $targetId
     * @throws \Exception
     */
    public static function add($userId, $targetId)
    {
        try {
            if (User::isAdmin($targetId)) {
                return;
            }

            $exists = IgnoreModel::where('userId', '=', $userId)->where('targetId', '=', $targetId)->count();
            if ($exists === 0) {
                $item = new IgnoreModel();
                $item->userId = $userId;
                $item->targetId = $targetId;
                $item->save();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove from ignore list
     *
     * @param $userId
     * @param $targetId
     * @throws \Exception
     */
    public static function remove($userId, $targetId)
    {
        try {
            $exists = IgnoreModel::where('userId', '=', $userId)->where('targetId', '=', $targetId)->first();
            if ($exists) {
                $exists->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Check if a user has ignored another user
     *
     * @param $userId
     * @param $targetId
     * @return bool
     * @throws \Exception
     */
    public static function hasIgnored($userId, $targetId)
    {
        try {
            return $exists = IgnoreModel::where('userId', '=', $userId)->where('targetId', '=', $targetId)->count() > 0;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
