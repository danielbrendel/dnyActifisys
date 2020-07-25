<?php

/*
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

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
     * @return RedirectResponse
     */
    public function add()
    {
        try {
            $entityId = request('entityId');

            $result = FavoritesModel::add(auth()->id(), $entityId, 'ENT_USER');

            $user = User::get($result->entityId);
            $result->avatar = $user->avatar;
            $result->total_posts = User::getStats($result->entityId)->posts;
            $result->short_name = AppModel::getShortExpression($user->username);

            return back()->with('flash.success', __('app.favorite_added'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Remove favorite
     * @return RedirectResponse
     */
    public function remove()
    {
        try {
            $entityId = request('entityId');

            FavoritesModel::remove(auth()->id(), $entityId, 'ENT_USER');

            return back()->with('flash.success', __('app.favorite_removed'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }
}
