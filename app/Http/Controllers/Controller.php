<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use App\User;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Add language middleware here
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            \App::setLocale(env('APP_LANG', 'en'));

            return $next($request);
        });
    }

    /**
     * Validate authentication
     *
     * @throws Exception
     */
    protected function validateAuth()
    {
        if (Auth::guest()) {
            throw new Exception(__('app.not_logged_in'), 403);
        }

        $user = User::getByAuthId();
        if ((!$user) || ($user->deactivated)) {
            throw new Exception(__('app.user_not_found_or_deactivated'), 403);
        }
    }
}
