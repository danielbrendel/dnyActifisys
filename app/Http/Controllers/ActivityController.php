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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Validate authentication
     *
     * @throws Exception
     */
    private function validateAuth()
    {
        if (Auth::guest()) {
            throw new Exception(__('app.not_logged_in'), 403);
        }
    }

    /**
     * Create an activity
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create()
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'title' => 'required|max:100',
                'description' => 'required',
                'date_of_activity' => 'required|date',
                'location' => 'required',
                'limit' => 'nullable|numeric'
            ]);

            if (!isset($attr['limit'])) {
                $attr['limit'] = 0;
            }

            $id = ActivityModel::createActivity(auth()->id(), $attr);

            return redirect('/activity/' . $id)->with('flash.success', __('app.activity_created'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $activity = ActivityModel::getActivity($id);

            return view('activity.show', [
                'activity' => $activity
            ]);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
