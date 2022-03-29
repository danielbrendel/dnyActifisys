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
use Illuminate\Support\Facades\Cache;

class UniqueViewsModel extends Model
{
    /**
     * Add hashed IP address token as viewer for given item and return view count
     *
     * @param $id
     * @return int
     * @throws Exception
     */
    public static function viewForItem($id)
    {
        try {
            $count = 0;
            $token = md5(request()->ip());

            $item = static::where('activity', '=', $id)->where('token', '=', $token)->first();
            if (!$item) {
                $item = new self();
                $item->activity = $id;
                $item->token = $token;
                $item->save();
            }

            $count = Cache::remember('view_for_activity_' . $id, 60 * 15, function() use ($id) {
                return static::where('activity', '=', $id)->count();
            });

            return $count;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
