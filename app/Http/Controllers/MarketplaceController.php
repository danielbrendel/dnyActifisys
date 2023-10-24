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
use App\CaptchaModel;
use App\MarketCategoryModel;
use App\MarketplaceModel;
use App\ReportModel;
use App\AppModel;
use App\User;

class MarketplaceController extends Controller
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
            if (!env('APP_ENABLEMARKETPLACE')) {
                return redirect('/');
            }

            return $next($request);
        });
    }

    /**
     * View marketplace index
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws Exception
     */
    public function index()
    {
        return view('marketplace.index', [
            'captchadata' => CaptchaModel::createSum(session()->getId()),
            'categories' => MarketCategoryModel::getAll()
        ]);
    }

    /**
     * Create an advert
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'category' => 'required|numeric',
                'title' => 'required',
                'description' => 'required',
                'link' => 'required'
            ]);

            MarketplaceModel::addAdvert(auth()->id(), $attr['category'], 'banner', $attr['title'], $attr['description'], $attr['link']);
        
            return back()->with('flash.success', __('app.marketplace_advert_created'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Edit an advert
     * 
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'category' => 'required|numeric',
                'title' => 'required',
                'description' => 'required',
                'link' => 'required'
            ]);

            MarketplaceModel::editAdvert($id, auth()->id(), $attr['category'], 'banner', $attr['title'], $attr['description'], $attr['link']);
        
            return back()->with('flash.success', __('app.marketplace_advert_edited'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete an advert
     * 
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        try {
            $this->validateAuth();

            $advert = MarketplaceModel::where('id', '=', $id)->where('userId', '=', auth()->id())->first();
            if (!$advert) {
                throw new \Exception('Marketplace item not found: ' . $id); 
            }

            $advert->delete();

            return back()->with('flash.success', __('app.marketplace_advert_deleted'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Report an advert
     * 
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function report($id)
    {
        try {
            $this->validateAuth();

            $advert = MarketplaceModel::where('id', '=', $id)->where('userId', '<>', auth()->id())->first();
            if (!$advert) {
               throw new \Exception('Marketplace item not found: ' . $id); 
            }

            ReportModel::addReport(auth()->id(), $advert->id, 'ENT_MARKETITEM');

            return back()->with('flash.success', __('app.marketplace_advert_reported'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get list of adverts
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        try {
            $paginate = request('paginate', null);
            $category = request('category', 0);

            $data = MarketplaceModel::fetch($paginate, $category)->toArray();
            foreach ($data as $key => &$item) {
                $item['user'] = User::where('id', '=', $item['userId'])->first();
                if (($item['user'] === null) || ($item['user']->deactivated)) {
                    unset($data[$key]);
                }
                $item['description'] = AppModel::translateLinks($item['description']);
            }

            return response()->json(array('code' => 200, 'data' => array_values($data)));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }
}
