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

use Illuminate\Http\Request;
use App\AppModel;
use App\CaptchaModel;
use App\GalleryModel;
use App\GalleryLikesModel;
use App\GalleryThreadModel;
use App\ReportModel;
use App\IgnoreModel;
use App\PushModel;
use App\User;
use Illuminate\Support\Carbon;

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
            $tag = request('tag', null);

            $data = GalleryModel::fetch($paginate, $tag);
            foreach ($data as $key => &$item) {
                $item->user = User::where('id', '=', $item->userId)->first();
                if ($item->user->locked) {
                    unset($data[$key]);
                    continue;
                }

                $item->tags = explode(' ', $item->tags);
                $item->likes = AppModel::countAsString(GalleryLikesModel::getForItem($item->id));

                $item->hasLiked = GalleryLikesModel::hasUserLiked($item->id);
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

            $item->tags = explode(' ', $item->tags);
            $item->likes = AppModel::countAsString(GalleryLikesModel::getForItem($item->id));
            $item->hasLiked = GalleryLikesModel::hasUserLiked($item->id);

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
                'location' => 'required',
                'tags' => 'nullable'
            ]);

            if (!isset($attr['tags'])) {
                $attr['tags'] = '';
            }

            GalleryModel::addItem($attr['title'], $attr['location'], $attr['tags'], auth()->id());

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

    /**
     * Add gallery item comment
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addThread()
    {
        try {
            $this->validateAuth();

            $user = User::getByAuthId();

            $attr = request()->validate([
                'message' => 'required',
                'item' => 'required|numeric'
            ]);

            $item = GalleryModel::findItem($attr['item']);
            if ((!$item) || ($item->locked)) {
                throw new \Exception('Item not found or locked');
            }

            GalleryThreadModel::addThread($attr['message'], $attr['item']);

            if (!$user->id !== $item->userId) {
                PushModel::addNotification(__('app.user_gallery_item_commented_short'), __('app.user_gallery_item_commented_long', ['profile' => url('/user/' . $user->slug), 'name' => $user->name, 'item' => url('/gallery/item/' . $item->id)]), 'PUSH_COMMENTED', $item->userId);
            }

            return back()->with('flash.success', __('app.gallery_thread_added'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Fetch gallery item thread
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchThread($id)
    {
        try {
            $paginate = request('paginate', null);

            $data = GalleryThreadModel::fetch($id, $paginate)->toArray();

            foreach ($data as $key => &$value) {
                $user = User::get($value['userId']);

                if ((!$user) || ($user->deactivated)) {
                    unset($data[$key]);
                    continue;
                }

                if (!\Auth::guest()) {
                    if (IgnoreModel::hasIgnored(auth()->id(), $user->id)) {
                        unset($data[$key]);
                        continue;
                    }
                }

                $value['user'] = $user;
                $value['diffForHumans'] = Carbon::createFromDate($value['created_at'])->diffForHumans();
                $value['adminOrOwner'] =  (User::isAdmin(auth()->id())) || ($value['userId'] === auth()->id());
            }

            return response()->json(array('code' => 200, 'data' => array_values($data)));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Report gallery thread item
     * 
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reportThread($id)
    {
        try {
            $this->validateAuth();

            ReportModel::addReport(auth()->id(), $id, 'ENT_GALLERYTHREAD');

            return back()->with('flash.success', __('app.gallery_thread_item_reported'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Lock gallery thread item
     * 
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function lockThread($id)
    {
        try {
            $this->validateAuth();

            GalleryThreadModel::lock($id);

            return response()->json(array('code' => 200, 'msg' => __('app.gallery_thread_item_locked')));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Edit gallery thread item
     * 
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editThread($id)
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'text' => 'required'
            ]);

            GalleryThreadModel::edit($id, $attr['text']);

            return back()->with('flash.success', __('app.gallery_thread_item_edited'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }
}
