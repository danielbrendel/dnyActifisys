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
use App\InstallerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstallerController extends Controller
{
    /**
     * View installer wizard
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewInstall()
    {
        if (!file_exists(base_path() . '/do_install')) {
            exit('Indicator file not found');
        }

        return view('home.install');
    }

    /**
     * Perform installation
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function install()
    {
        try {
            $attr = request()->validate([
                'project' => 'required',
                'name' => 'required',
                'email' => 'required|email',
                'dbhost' => 'required',
                'dbuser' => 'required',
                'dbport' => 'required|numeric',
                'database' => 'required',
                'dbpassword' => 'nullable',
                'smtphost' => 'required',
                'smtpuser' => 'required',
                'smtppassword' => 'required',
                'smtpfromaddress' => 'required|email'
            ]);

            if (!isset($attr['dbpassword'])) {
                $attr['dbpassword'] = '';
            }

            if (!isset($attr['ga'])) {
                $attr['ga'] = '';
            }

            $attr['password'] = AppModel::getRandomPassword(10);

            InstallerModel::install($attr);

            Auth::attempt([
               'email' => $attr['email'],
               'password' => $attr['password']
            ]);

            return redirect('/maintainer')->with('success', __('app.product_installed', ['password' => $attr['password']]));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
