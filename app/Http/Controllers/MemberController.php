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

use App\ActivityModel;
use App\CaptchaModel;
use App\IgnoreModel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MemberController extends Controller
{
    /**
     * Show user profile
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $this->validateAuth();

            $user = User::get($id);
            if (!$user) {
                throw new \Exception(__('app.user_not_found_or_locked'));
            }

            if (IgnoreModel::hasIgnored($id, auth()->id())) {
                throw new \Exception(__('app.user_not_found_or_locked'));
            }

            $user->activities = ActivityModel::where('owner', '=', $user->id)->count();
            $user->age = Carbon::parse($user->birthday)->age;
            if ($user->gender === 1) {
                $user->genderText = __('app.gender_male');
            } else if ($user->gender === 2) {
                $user->genderText = __('app.gender_female');
            } else {
                $user->genderText = __('app.gender_diverse');
            }
            $user->ignored = IgnoreModel::hasIgnored(auth()->id(), $id);

            return view('member.profile', [
               'captchadata' => CaptchaModel::createSum(session()->getId()),
               'user' => $user
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
