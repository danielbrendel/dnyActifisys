<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use App\AppModel;
use App\PostModel;
use App\TagsModel;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\FavoritesModel;
use Illuminate\Support\Facades\Cache;

class FavoritesController extends Controller
{
    /**
     * Add favorite
     *
     * @param $id
     * @return RedirectResponse
     */
    public function add($id)
    {
        try {
            $result = FavoritesModel::add(auth()->id(), $id, 'ENT_USER');

            return back()->with('flash.success', __('app.favorite_added'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Remove favorite
     *
     * @param $id
     * @return RedirectResponse
     */
    public function remove($id)
    {
        try {
            FavoritesModel::remove(auth()->id(), $id, 'ENT_USER');

            return back()->with('flash.success', __('app.favorite_removed'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Fetch favorites
     *
     * @return JsonResponse
     */
    public function fetch()
    {
        try {
            $data = FavoritesModel::getDetailedForUser(auth()->id());

            return response()->json(array('code' => 200, 'data' => $data));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }
}
