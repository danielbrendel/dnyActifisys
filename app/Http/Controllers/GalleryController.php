<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AppModel;
use App\CaptchaModel;
use App\GalleryModel;
use App\GalleryLikesModel;
use App\ReportModel;
use App\User;

class GalleryController extends Controller
{
    /**
     * Construct object
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware(function($request, $next) {
            if (!env('APP_ENABLEGALLERY')) {
                return redirect('/');
            }

            return $next($request);
        });
    }

    /**
     * View gallery index
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws Exception
     */
    public function index()
    {
        return view('gallery.index', [
            'captchadata' => CaptchaModel::createSum(session()->getId())
        ]);
    }

    /**
     * Fetch gallery items
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch()
    {
        try {
            $paginate = request('paginate', null);

            $data = GalleryModel::fetch($paginate);
            foreach ($data as $key => &$item) {
                $item->user = User::where('id', '=', $item->userId)->first();
                if ($item->user->locked) {
                    unset($data[$key]);
                    continue;
                }

                $item->likes = AppModel::countAsString(GalleryLikesModel::getForItem($item->id));
            }

            return response()->json(array('code' => 200, 'data' => $data));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * View specific gallery item
     * 
     * @param $slug
     * @return mixed
     */
    public function view($slug)
    {
        try {
            $item = GalleryModel::findItem($slug);
            if (!$item) {
                throw new \Exception('Item not found: ' . $slug);
            }

            $item->user = User::where('id', '=', $item->userId)->first();
            if ($item->user->locked) {
                throw new \Exception('Item belongs to locked user');
            }

            $item->likes = AppModel::countAsString(GalleryLikesModel::getForItem($item->id));

            return view('gallery.item', [
                'item' => $item,
                'user' => User::getByAuthId(),
                'captchadata' => CaptchaModel::createSum(session()->getId())
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Add a gallery item
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add()
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'title' => 'required',
                'location' => 'required'
            ]);

            GalleryModel::addItem($attr['title'], $attr['location'], auth()->id());

            return back()->with('flash.success', __('app.gallery_item_added'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle like of a gallery item
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function like($id)
    {
        try {
            $this->validateAuth();

            $action = '';

            $item = GalleryModel::where('id', '=', $id)->first();
            if ($item) {
                $current = GalleryLikesModel::where('galleryId', '=', $id)->where('userId', '=', auth()->id())->count();
                if ($current) {
                    GalleryLikesModel::removeLike($id, auth()->id());
                    $action = 'unliked';
                } else {
                    GalleryLikesModel::addLike($id, auth()->id());
                    $action = 'liked';
                }
            }

            return response()->json(array('code' => 200, 'action' => $action));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Remove a gallery item
     * 
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove($id)
    {
        try {
            $this->validateAuth();

            $user = User::getByAuthId();
            $item = GalleryModel::where('id', '=', $id)->first();

            if (!$item) {
                throw new \Exception('Item not found: ' . $id);
            }

            if ((($user->admin) || ($user->maintainer)) || ($item->userId == $user->id)) {
                GalleryModel::removeItem($item->id);
            } else {
                throw new \Exception('Insufficient permissions');
            }

            return back()->with('flash.success', __('app.gallery_item_deleted'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Report a gallery item
     * 
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function report($id)
    {
        try {
            $this->validateAuth();

            $item = GalleryModel::where('id', '=', $id)->where('userId', '<>', auth()->id())->first();
            if (!$item) {
               throw new \Exception('Gallery item not found: ' . $id); 
            }

            ReportModel::addReport(auth()->id(), $item->id, 'ENT_GALLERYITEM');

            return back()->with('flash.success', __('app.gallery_item_reported'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
