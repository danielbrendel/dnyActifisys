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

use App\ActivitiesHaveImages;
use App\ActivityModel;
use App\CaptchaModel;
use App\IgnoreModel;
use App\MailerModel;
use App\ParticipantModel;
use App\PushModel;
use App\ReportModel;
use App\ThreadModel;
use App\User;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
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
                'time_of_activity' => 'required|date_format:H:i',
                'location' => 'required',
                'limit' => 'nullable|numeric',
                'only_gender' => 'nullable|numeric'
            ]);

            if (!isset($attr['limit'])) {
                $attr['limit'] = 0;
            }

            if (!isset($attr['only_gender'])) {
                $attr['only_gender'] = 0;
            }

            $id = ActivityModel::createActivity(auth()->id(), $attr);

            return redirect('/activity/' . $id)->with('flash.success', __('app.activity_created'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Edit activity
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit()
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'activityId' => 'required|numeric',
                'title' => 'required|max:100',
                'description' => 'required',
                'date_of_activity' => 'required|date',
                'time_of_activity' => 'required|date_format:H:i',
                'location' => 'required',
                'limit' => 'nullable|numeric',
                'only_gender' => 'nullable|numeric'
            ]);

            if (!isset($attr['limit'])) {
                $attr['limit'] = 0;
            }

            if (!isset($attr['only_gender'])) {
                $attr['only_gender'] = 0;
            }

            $id = ActivityModel::updateActivity($attr);

            return back()->with('flash.success', __('app.activity_edited'));
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
            if ((!$activity) || ($activity->locked)) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            $activity->user = User::get($activity->owner);

            if (IgnoreModel::hasIgnored($activity->owner, auth()->id())) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            $activity->actualParticipants = ParticipantModel::getActualParticipants($activity->id);
            $activity->potentialParticipants = ParticipantModel::getPotentialParticipants($activity->id);
            $activity->selfParticipated = ParticipantModel::has(auth()->id(), $activity->id, ParticipantModel::PARTICIPANT_ACTUAL);
            $activity->selfInterested = ParticipantModel::has(auth()->id(), $activity->id, ParticipantModel::PARTICIPANT_POTENTIAL);
            $activity->images = ActivitiesHaveImages::getForActivity($activity->id);

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
     * Fetch activity package
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch()
    {
        try {
            $paginate = request('paginate', null);
            if ($paginate === 'null') {
                $paginate = null;
            }

            $city = request('city', null);
            if ($city === '_all') {
                $city = null;
            }

            $dateFrom = request('date_from', null);
            if ($dateFrom === '_default') {
                $dateFrom = null;
            }

            $dateTill = request('date_till', null);
            if ($dateTill === '_default') {
                $dateTill = null;
            }

            $data = ActivityModel::fetchActivities($city, $paginate, $dateFrom, $dateTill)->toArray();
            foreach ($data as $key => &$item) {
                $item['user'] = User::get($item['owner']);

                if (IgnoreModel::hasIgnored($item['owner'], auth()->id())) {
                    unset($data[$key]);
                    continue;
                }

                $item['participants'] = ParticipantModel::where('activity', '=', $item['id'])->where('type', '=', ParticipantModel::PARTICIPANT_ACTUAL)->count();
                $item['messages'] = ThreadModel::where('activityId', '=', $item['id'])->count();
                $item['diffForHumans'] = Carbon::createFromDate($item['date_of_activity'])->diffForHumans();
            }

            return response()->json(array('code' => 200, 'data' => array_values($data), 'last' => count($data) === 0));
        } catch (Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
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

            $activity = ActivityModel::getActivity($id);
            if (IgnoreModel::hasIgnored($activity->owner, auth()->id())) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

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
     * Fetch user activities
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchUserActivities($id)
    {
        try {
            $data = ActivityModel::fetchUserActivities($id);
            foreach ($data as &$item) {
                $item->diffForHumans = $item->date_of_activity->diffForHumans();
                $item->participants = ParticipantModel::where('activity', '=', $item->id)->where('type', '=', ParticipantModel::PARTICIPANT_ACTUAL)->count();
                $item->messages = ThreadModel::where('activityId', '=', $item->id)->count();
            }

            return response()->json(array('code' => 200, 'data' => $data));
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

            $activity = ActivityModel::getActivity($id);

            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            if (IgnoreModel::hasIgnored($activity->owner, auth()->id())) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            ThreadModel::add(auth()->id(), $id, $attr['message']);

            $user = User::get(auth()->id());
            $owner = User::get($activity->owner);
            PushModel::addNotification(__('app.user_commented_short'), __('app.user_commented_long', ['name' => $owner->name, 'sender' => $user->name, 'message' => $attr['message']]), 'PUSH_COMMENTED', $activity->owner);

            if (($owner) && ($owner->email_on_comment)) {
                $html = view('mail.user_commented', ['name' => $owner->name, 'sender' => $user->name, 'message' => $attr['message'], 'activityId' => $activity->id])->render();
                MailerModel::sendMail($user->email, __('app.mail_user_commented_title'), $html);
            }

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

            $parentPost = ThreadModel::where('id', '=', $parentId)->first();
            if (!$parentPost) {
                throw new Exception(__('app.parent_post_not_found'));
            }

            $activity = ActivityModel::getActivity($parentPost->activityId);

            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            if (IgnoreModel::hasIgnored($activity->owner, auth()->id())) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            $reply = ThreadModel::reply(auth()->id(), $parentId, $attr['text']);

            return response()->json(array('code' => 200, 'comment' => $reply));
        } catch (Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Add as participant
     *
     * @param $activityId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function participantAdd($activityId)
    {
        try {
            $this->validateAuth();

            $activity = ActivityModel::getActivity($activityId);

            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            if ($activity->canceled) {
                throw new Exception(__('app.activity_canceled'));
            }

            if ((new DateTime('now')) > (new DateTime($activity->date_of_activity))) {
                throw new Exception(__('app.activity_expired'));
            }

            if (IgnoreModel::hasIgnored($activity->owner, auth()->id())) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            $user = User::get(auth()->id());

            if ($activity->only_gender !== 0) {
                if ($user->gender !== $activity->only_gender) {
                    throw new Exception(__('app.your_gender_excluded'));
                }
            }

            ParticipantModel::add(auth()->id(), $activityId, ParticipantModel::PARTICIPANT_ACTUAL);

            PushModel::addNotification(__('app.user_participated_short'), __('app.user_participated_long', ['name' => $user->name]), 'PUSH_PARTICIPATED', $activity->owner);

            $owner = User::get($activity->owner);
            if (($owner) && ($owner->email_on_participated)) {
                $htmlCode = view('mail.user_participated', ['name' => $owner->name, 'participant' => $user, 'activity' => $activity])->render();
                MailerModel::sendMail($owner->email, __('app.mail_user_participated'), $htmlCode);
            }

            return back()->with('flash.success', __('app.added_as_participant'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove as participant
     *
     * @param $activityId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function participantRemove($activityId)
    {
        try {
            $this->validateAuth();

            $activity = ActivityModel::getActivity($activityId);
            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            ParticipantModel::remove(auth()->id(), $activityId, ParticipantModel::PARTICIPANT_ACTUAL);

            return back()->with('flash.success', __('app.removed_as_participant'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Add as potential participant
     *
     * @param $activityId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function potentialAdd($activityId)
    {
        try {
            $this->validateAuth();

            $activity = ActivityModel::getActivity($activityId);

            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            if ($activity->canceled) {
                throw new Exception(__('app.activity_canceled'));
            }

            if ((new DateTime('now')) > (new DateTime($activity->date_of_activity))) {
                throw new Exception(__('app.activity_expired'));
            }

            if (IgnoreModel::hasIgnored($activity->owner, auth()->id())) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            ParticipantModel::add(auth()->id(), $activityId, ParticipantModel::PARTICIPANT_POTENTIAL);

            return back()->with('flash.success', __('app.added_as_potential'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove as potential participant
     *
     * @param $activityId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function potentialRemove($activityId)
    {
        try {
            $this->validateAuth();

            $activity = ActivityModel::getActivity($activityId);
            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            ParticipantModel::remove(auth()->id(), $activityId, ParticipantModel::PARTICIPANT_POTENTIAL);

            return back()->with('flash.success', __('app.removed_as_potential'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Lock activity
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lockActivity($id)
    {
        try {
            $this->validateAuth();

            $user = User::get(auth()->id());

            $activity = ActivityModel::getActivity($id);
            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            if (($user->admin) || ($user->id === $activity->owner)) {
                ActivityModel::lockActivity($id);

                return redirect('/')->with('flash.success', __('app.activity_locked'));
            } else {
                return back()->with('flash.error', __('app.insufficient_permissions'));
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel activity
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelActivity($id)
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
               'reason' => 'nullable'
            ]);

            if (!isset($attr['reason'])) {
                $attr['reason'] = '';
            }

            $user = User::get(auth()->id());

            $activity = ActivityModel::getActivity($id);
            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            if (($user->admin) || ($user->id === $activity->owner)) {
                ActivityModel::cancelActivity($id, $attr['reason']);

                return back()->with('flash.success', __('app.activity_canceled'));
            } else {
                return back()->with('flash.error', __('app.insufficient_permissions'));
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Report activity
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reportActivity($id)
    {
        try {
            $this->validateAuth();

            $user = User::get(auth()->id());

            $activity = ActivityModel::getActivity($id);
            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            ReportModel::addReport(auth()->id(), $activity->id, 'ENT_ACTIVITY');

            return back()->with('flash.success', __('app.activity_reported'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Lock comment
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lockComment($id)
    {
        try {
            $this->validateAuth();

            $user = User::get(auth()->id());

            $cmt = ThreadModel::where('id', '=', $id)->first();
            if (!$cmt) {
                throw new Exception(__('app.comment_not_found'));
            }

            if (!(($user->admin) || ($user->id === $cmt->userId))) {
                throw new Exception(__('app.insufficient_permissions'));
            }

            $cmt->locked = true;
            $cmt->save();

            return back()->with('flash.success', __('app.comment_locked'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Report comment
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reportComment($id)
    {
        try {
            $this->validateAuth();

            $user = User::get(auth()->id());

            $cmt = ThreadModel::where('id', '=', $id)->first();
            if (!$cmt) {
                throw new Exception(__('app.comment_not_found'));
            }

            ReportModel::addReport(auth()->id(), $cmt->id, 'ENT_COMMENT');

            return back()->with('flash.success', __('app.comment_reported'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Edit a comment
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editComment($id)
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
               'text' => 'required|max:4096'
            ]);

            $user = User::get(auth()->id());

            $cmt = ThreadModel::where('id', '=', $id)->first();
            if (!$cmt) {
                throw new Exception(__('app.comment_not_found'));
            }

            if (!(($user->admin) || ($user->id === $cmt->userId))) {
                throw new Exception(__('app.insufficient_permissions'));
            }

            $cmt->text = $attr['text'];
            $cmt->save();

            return back()->with('flash.success', __('app.comment_edited'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Perform file upload process
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadFile($id)
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
               'image' => 'required|file',
               'name' => 'nullable'
            ]);

            if (!isset($attr['name'])) {
                $attr['name'] = '';
            }

            $activity = ActivityModel::getActivity($id);

            if ((!$activity) || ($activity->canceled)) {
                throw new Exception(__('app.activity_not_found_or_canceled'));
            }

            $user = User::get(auth()->id());

            ActivitiesHaveImages::addFile($id, $attr['name']);

            return back()->with('flash.success', __('app.file_uploaded'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete an existing file
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFile($id)
    {
        try {
            ActivitiesHaveImages::deleteFile($id);

            return back()->with('flash.success', __('app.file_deleted'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
