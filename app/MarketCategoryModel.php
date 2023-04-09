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
 * Class MarketCategoryModel
 *
 * Interface to market place category handler
 */
class MarketCategoryModel extends Model
{
    /**
     * Get categories
     * 
     * @param $only_active
     * @return mixed
     * @throws \Exception
     */
    public static function getAll($only_active = true)
    {
        try {
            if ($only_active) {
                return MarketCategoryModel::where('active', '=', true)->get();
            } else {
                return MarketCategoryModel::all();
            }
        } catch(\Exception $e) {
            throw $e;
        }
    }
}
