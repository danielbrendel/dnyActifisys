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
use App\CaptchaModel;
use App\MarketCategoryModel;
use App\MarketplaceModel;
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
        $this->middleware(function($request, $next) {
            if (!env('APP_ENABLEMARKETPLACE')) {
                return redirect('/');
            }

            return $next($request);
        });
    }

    /**
     * View forum index
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
            }

            return response()->json(array('code' => 200, 'data' => array_values($data)));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }
}
