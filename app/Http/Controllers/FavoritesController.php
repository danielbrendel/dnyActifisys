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
use Illuminate\Http\Request;
use App\FavoritesModel;
use Illuminate\Support\Facades\Cache;

class FavoritesController extends Controller
{
    /**
     * Add favorite
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        try {
            $entityId = request('entityId');
            $entType = request('entType');

            $result = FavoritesModel::add(auth()->id(), $entityId, $entType);

            if ($result->type === 'ENT_HASHTAG') {
                $hashtag = TagsModel::where('id', '=', $result->entityId)->first();
                $result->avatar = TagsModel::getTopImage($hashtag->tag);
                $result->total_posts = Cache::remember('tag_stats_posts_' . $hashtag->tag, 3600 * 24, function () use ($hashtag) {
                    return PostModel::where('hashtags', 'LIKE', '%' . $hashtag->tag . ' %')->count();
                });
                $result->short_name = AppModel::getShortExpression($hashtag->tag);
            } else if ($result->type === 'ENT_USER') {
                $user = User::get($result->entityId);
                $result->avatar = $user->avatar;
                $result->total_posts = User::getStats($result->entityId)->posts;
                $result->short_name = AppModel::getShortExpression($user->username);
            }

            return response()->json(array('code' => 200, 'fav' => $result, 'msg' => __('app.favorite_added')));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Remove favorite
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove()
    {
        try {
            $entityId = request('entityId');
            $entType = request('entType');

            FavoritesModel::remove(auth()->id(), $entityId, $entType);

            return response()->json(array('code' => 200, 'msg' => __('app.favorite_removed')));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }
}
