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
use App\ParticipantModel;
use App\ThreadModel;
use App\User;
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

    /**
     * Show activity details
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $activity = ActivityModel::getActivity($id);

            $activity->user = User::get($activity->owner);
            $activity->actualParticipants = ParticipantModel::getActualParticipants($activity->id);
            $activity->potentialParticipants = ParticipantModel::getPotentialParticipants($activity->id);
            $activity->selfParticipated = ParticipantModel::has(auth()->id(), $activity->id, ParticipantModel::PARTICIPANT_ACTUAL);
            $activity->selfInterested = ParticipantModel::has(auth()->id(), $activity->id, ParticipantModel::PARTICIPANT_POTENTIAL);

            foreach ($activity->actualParticipants as &$item) {
                $item->user = User::get($item->participant);
            }

            foreach ($activity->potentialParticipants as &$item) {
                $item->user = User::get($item->participant);
            }

            return view('activity.show', [
                'activity' => $activity,
                'captchadata' => CaptchaModel::createSum(session()->getId())
            ]);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Fetch thread comment pack
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchThread($id)
    {
        try {
            $paginate = request('paginate', null);

            $threads = ThreadModel::getFromActivity($id, $paginate);
            foreach ($threads as &$thread) {
                $thread->user = User::get($thread->userId);
                $thread->adminOrOwner = User::isAdmin(auth()->id()) || ($thread->userId === auth()->id());
                $thread->diffForHumans = $thread->created_at->diffForHumans();
                $thread->subCount = ThreadModel::getSubCount($thread->id);
            }

            return response()->json(array('code' => 200, 'data' => $threads, 'last' => (count($threads) === 0)));
        } catch (Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Fetch sub thread comment pack
     *
     * @param $parentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchSubThread($parentId)
    {
        try {
            $paginate = request('paginate', null);

            $threads = ThreadModel::fetchSubThread($parentId, $paginate);
            foreach ($threads as &$thread) {
                $thread->user = User::get($thread->userId);
                $thread->adminOrOwner = User::isAdmin(auth()->id()) || ($thread->userId === auth()->id());
                $thread->diffForHumans = $thread->created_at->diffForHumans();
            }

            return response()->json(array('code' => 200, 'data' => $threads, 'last' => (count($threads) === 0)));
        } catch (Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Add post to activity thread
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addThread($id)
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
               'message' => 'required|max:4096'
            ]);

            ThreadModel::add(auth()->id(), $id, $attr['message']);

            return redirect('/activity/' . $id . '#thread')->with('flash.success', __('app.comment_added'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reply to thread
     *
     * @param $parentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function replyThread($parentId)
    {
        try {
            $attr = request()->validate([
                'text' => 'required|max:4096'
            ]);

            $reply = ThreadModel::reply(auth()->id(), $parentId, $attr['text']);

            return response()->json(array('code' => 200, 'comment' => $reply));
        } catch (Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }
}
