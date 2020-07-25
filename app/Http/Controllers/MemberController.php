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
use App\FavoritesModel;
use App\IgnoreModel;
use App\ReportModel;
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
            $user->hasFavorited = FavoritesModel::hasUserFavorited(auth()->id(), $id, 'ENT_USER');

            return view('member.profile', [
               'captchadata' => CaptchaModel::createSum(session()->getId()),
               'user' => $user
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Lock a user
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lock($id)
    {
        try {
            $this->validateAuth();

            $self = User::get(auth()->id());

            if ((!$self->admin) || (!$self->maintainer)) {
                throw new \Exception(__('app.insufficient_permissions'));
            }

            $target = User::get($id);
            if (!$target) {
                throw new \Exception(__('app.user_not_found_or_locked'));
            }

            $target->locked = true;
            $target->save();

            return back()->with('flash.success', __('app.user_locked'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Report a user
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function report($id)
    {
        try {
            $this->validateAuth();

            $target = User::get($id);
            if (!$target) {
                throw new \Exception(__('app.user_not_found_or_locked'));
            }

            ReportModel::addReport(auth()->id(), $target->id, 'ENT_USER');

            return back()->with('flash.success', __('app.user_reported'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Add to ignore list
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ignoreAdd($id)
    {
        try {
            $this->validateAuth();

            $target = User::get($id);
            if (!$target) {
                throw new \Exception(__('app.user_not_found_or_locked'));
            }

            IgnoreModel::add(auth()->id(), $id);

            return back()->with('flash.success', __('app.user_ignored'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Remove from ignore list
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ignoreRemove($id)
    {
        try {
            $this->validateAuth();

            $target = User::get($id);
            if (!$target) {
                throw new \Exception(__('app.user_not_found_or_locked'));
            }

            IgnoreModel::remove(auth()->id(), $id);

            return back()->with('flash.success', __('app.user_not_ignored'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }
}
