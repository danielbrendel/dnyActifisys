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
     * @param $link
     * @return void
     * @throws \Exception
     */
    public static function addAdvert($userId, $categoryId, $banner, $title, $description, $link)
    {
        try {
            $catexists = MarketCategoryModel::where('id', '=', $categoryId)->count();
            if ($catexists === 0) {
                throw new \Exception('Category not found');
            }

            $item = new MarketplaceModel;
            $item->userId = $userId;
            $item->categoryId = $categoryId;
            $item->title = $title;
            $item->description = $description;
            $item->link = $link;

            $img = request()->file($banner);
            if ($img != null) {
                $imgName = md5(random_bytes(55));

                $img->move(base_path() . '/public/gfx/market', $imgName . '.' . $img->getClientOriginalExtension());

                $item->banner = $imgName . '.' . $img->getClientOriginalExtension();
            } else {
                throw new \Exception('Banner image is invalid');
            }

            $item->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit marketplace advert
     * 
     * @param $id
     * @param $userId
     * @param $categoryId
     * @param $banner
     * @param $title
     * @param $description
     * @param $link
     * @return void
     * @throws \Exception
     */
    public static function editAdvert($id, $userId, $categoryId, $banner, $title, $description, $link)
    {
        try {
            $item = MarketplaceModel::where('id', '=', $id)->where('active', '=', true)->where('userId', '=', $userId)->first();
            if (!$item) {
                throw new \Exception('Failed to obtain item');
            }

            $catexists = MarketCategoryModel::where('id', '=', $categoryId)->count();
            if ($catexists === 0) {
                throw new \Exception('Category not found');
            }

            $item->categoryId = $categoryId;
            $item->title = $title;
            $item->description = $description;
            $item->link = $link;

            $img = request()->file($banner);
            if ($img != null) {
                $imgName = md5(random_bytes(55));

                $img->move(base_path() . '/public/gfx/market', $imgName . '.' . $img->getClientOriginalExtension());

                $item->banner = $imgName . '.' . $img->getClientOriginalExtension();
            }

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
