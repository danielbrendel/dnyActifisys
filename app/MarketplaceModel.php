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
 * Class MarketplaceModel
 *
 * Interface to market place handler
 */
class MarketplaceModel extends Model
{
    /**
     * Add marketplace advert
     * 
     * @param $userId
     * @param $categoryId
     * @param $banner
     * @param $title
     * @param $description
     * @return void
     * @throws \Exception
     */
    public static function addAdvert($userId, $categoryId, $banner, $title, $description)
    {
        try {
            $item = new MarketplaceModel;
            $item->userId = $userId;
            $item->categoryId = $categoryId;
            $item->title = $title;
            $item->description = $description;
            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Fetch marketplace adverts
     * 
     * @param $paginate
     * @param $category
     * @return mixed
     * @throws \Exception
     */
    public static function fetch($paginate = null, $category = 0)
    {
        try {
            $query = MarketplaceModel::where('active', '=', true);

            if ($category != 0) {
                $query->where('categoryId', '=', $category);
            }

            if ($paginate !== null) {
                $query->where('id', '<', $paginate);
            }

            return $query->orderBy('id', 'desc')->limit(env('APP_MARKETPLACEPACKLIMIT'))->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
