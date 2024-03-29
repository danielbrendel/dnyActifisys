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

use App\ActivitiesHaveImages;
use App\ActivityModel;
use App\AppModel;
use App\CaptchaModel;
use App\CategoryModel;
use App\IgnoreModel;
use App\MailerModel;
use App\ParticipantModel;
use App\PushModel;
use App\ReportModel;
use App\ThreadModel;
use App\User;
use App\VerifyModel;
use App\LocationModel;
use App\UniqueViewsModel;
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
        $attr = request()->validate([
            'title' => 'required|max:100',
            'description' => 'required',
            'date_of_activity_from' => 'required|date',
            'date_of_activity_till' => 'required|date',
            'time_of_activity' => 'required|date_format:H:i',
            'category' => 'required|numeric',
            'location' => 'required',
            'add_participant' => 'nullable|numeric',
            'limit' => 'nullable|numeric',
            'only_gender' => 'nullable|numeric',
            'only_verified' => 'nullable|numeric'
        ]);
        
        try {
            $this->validateAuth();

            if (!isset($attr['add_participant'])) {
                $attr['add_participant'] = 0;
            }

            if (!isset($attr['limit'])) {
                $attr['limit'] = 0;
            }

            if (!isset($attr['only_gender'])) {
                $attr['only_gender'] = 0;
            }

            if (!isset($attr['only_verified'])) {
                $attr['only_verified'] = 0;
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
        $attr = request()->validate([
            'activityId' => 'required|numeric',
            'title' => 'required|max:100',
            'description' => 'required',
            'date_of_activity_from' => 'required|date',
            'date_of_activity_till' => 'required|date',
            'time_of_activity' => 'required|date_format:H:i',
            'category' => 'required|numeric',
            'location' => 'required',
            'limit' => 'nullable|numeric',
            'only_gender' => 'nullable|numeric',
            'only_verified' => 'nullable|numeric'
        ]);

        try {
            $this->validateAuth();

            if (!isset($attr['limit'])) {
                $attr['limit'] = 0;
            }

            if (!isset($attr['only_gender'])) {
                $attr['only_gender'] = 0;
            }

            if (!isset($attr['only_verified'])) {
                $attr['only_verified'] = 0;
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
     * @param string $slugOrId Either slug or ID
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($slugOrId)
    {
        try {
            $activity = ActivityModel::getActivityBySlug($slugOrId);
            if (!$activity) {
                $activity = ActivityModel::getActivity($slugOrId);
                if ((!$activity) || ($activity->locked)) {
                    throw new Exception(__('app.activity_not_found_or_locked'));
                }
            }

            $activity->user = User::get($activity->owner);

            if ((IgnoreModel::hasIgnored($activity->owner, auth()->id())) || (IgnoreModel::hasIgnored(auth()->id(), $activity->owner))) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            if ($activity->only_verified == true) {
                if ((Auth::guest()) || (VerifyModel::getState(auth()->id()) != VerifyModel::STATE_VERIFIED)) {
                    throw new Exception(__('app.activity_verified_only'));
                }
            }

            $activity->user->verified = VerifyModel::getState($activity->user->id) === VerifyModel::STATE_VERIFIED;

            $activity->actualParticipants = ParticipantModel::getActualParticipants($activity->id);
            $activity->potentialParticipants = ParticipantModel::getPotentialParticipants($activity->id);
            $activity->selfParticipated = ParticipantModel::has(auth()->id(), $activity->id, ParticipantModel::PARTICIPANT_ACTUAL);
            $activity->selfInterested = ParticipantModel::has(auth()->id(), $activity->id, ParticipantModel::PARTICIPANT_POTENTIAL);
            $activity->images = ActivitiesHaveImages::getForActivity($activity->id);
            $activity->categoryData = CategoryModel::where('id', '=', $activity->category)->first();
			$activity->date_of_activity_from_display = Carbon::createFromDate($activity->date_of_activity_from)->format(__('app.date_format_display'));
            $activity->date_of_activity_till_display = Carbon::createFromDate($activity->date_of_activity_till)->format(__('app.date_format_display'));
            $activity->startTime = Carbon::createFromDate($activity->date_of_activity_from)->format(__('app.time_format_display'));
            $activity->view_count = AppModel::countAsString(UniqueViewsModel::viewForItem($activity->id));

            foreach ($activity->actualParticipants as &$item) {
                $item->user = User::get($item->participant);
            }

            foreach ($activity->potentialParticipants as &$item) {
                $item->user = User::get($item->participant);
            }

            $additional_meta = [
                'og:title' => $activity->title,
                'og:description' => preg_replace('/\s+/', ' ', substr($activity->description, 0, 100)) . '...',
                'og:url' => url('/activity/' . $activity->slug),
            ];

            return view('activity.show', [
                'activity' => $activity,
                '_meta_description' => env('APP_PROJECTNAME') . ' - ' . $activity->title,
                'additional_meta' => $additional_meta,
                'captchadata' => CaptchaModel::createSum(session()->getId())
            ]);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
	
	/**
     * Refresh activity page
     *
	 * @param $slugOrId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
	public function refresh($slugOrId)
	{
		try {
			$activity = ActivityModel::getActivityBySlug($slugOrId);
            if (!$activity) {
                $activity = ActivityModel::getActivity($slugOrId);
                if ((!$activity) || ($activity->locked)) {
                    throw new Exception(__('app.activity_not_found_or_locked'));
                }
            }
			
			return redirect('/activity/' . $activity->slug . '#title');
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

            $location = request('location', null);
            if ($location === '_all') {
                $location = null;
            }

            $dateFrom = request('date_from', null);
            if ($dateFrom === '_default') {
                $dateFrom = null;
            }

            $dateTill = request('date_till', null);
            if ($dateTill === '_default') {
                $dateTill = null;
            }

            $tag = request('tag', null);
            if ($tag === '') {
                $tag = null;
            }

            $category = request('category', null);
            if ($category == 0) {
                $category = null;
            }

            $text = request('text', null);
            if ($text === '') {
                $text = null;
            }

            $data = ActivityModel::fetchActivities($location, $paginate, $dateFrom, $dateTill, $tag, $category, $text)->toArray();
            foreach ($data as $key => &$item) {
                $item['_type'] = 'activity';

                $item['user'] = User::get($item['owner']);

                if ((IgnoreModel::hasIgnored($item['owner'], auth()->id())) || (IgnoreModel::hasIgnored(auth()->id(), $item['owner']))) {
                    unset($data[$key]);
                    continue;
                }

                if (($item['only_verified'] == true) && (VerifyModel::getState(auth()->id()) != VerifyModel::STATE_VERIFIED)) {
                    unset($data[$key]);
                    continue;
                }

                $item['user']->verified = VerifyModel::getState($item['user']->id) === VerifyModel::STATE_VERIFIED;

                $item['participants'] = ParticipantModel::where('activity', '=', $item['id'])->where('type', '=', ParticipantModel::PARTICIPANT_ACTUAL)->count();
                $item['messages'] = ThreadModel::where('activityId', '=', $item['id'])->count();
                $item['date_of_activity_from'] = Carbon::createFromDate($item['date_of_activity_from']);
                $item['date_of_activity_from_display'] = $item['date_of_activity_from']->format(__('app.date_format_display'));
                $item['date_of_activity_till'] = Carbon::createFromDate($item['date_of_activity_till']);
                $item['date_of_activity_till_display'] = $item['date_of_activity_till']->format(__('app.date_format_display'));
                $item['date_of_activity_time'] = $item['date_of_activity_from']->format(__('app.time_format_display'));
                $item['diffForHumans'] = $item['date_of_activity_from']->diffForHumans();
                $item['date_of_activity_from_formatted'] = $item['date_of_activity_from']->format(__('app.date_format'));
                $item['view_count'] = AppModel::countAsString(UniqueViewsModel::viewForItem($item['id']));
                $item['categoryData'] = CategoryModel::where('id', '=', $item['category'])->first();

                $item['running'] = false;
                
                if (((new DateTime($item['date_of_activity_from'])) < (new DateTime('now'))) && ((new DateTime($item['date_of_activity_till']))->modify('+' . env('APP_ACTIVITYRUNTIME', 60) . ' minutes') >= (new DateTime('now')))) {
                    $item['running'] = true;
                }
            }

            $data = array_values($data);

            if (!User::hasProMode(auth()->id())) {
                $adcode = AppModel::getAdCode();
                if ((strlen($adcode) > 0) && (count($data) > 0)) {
                    $aditem = array();
                    $aditem['_type'] = 'ad';
                    $aditem['code'] = $adcode;
                    $aditem['tags'] = '';
                    $aditem['category'] = 0;
                    $aditem['date_of_activity_from'] = $data[count($data)-1]['date_of_activity_from'];
                    $data[] = $aditem;
                }
            }

            return response()->json(array('code' => 200, 'data' => $data, 'last' => count($data) === 0));
        } catch (Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Fetch past activity package
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchPast()
    {
        try {
            $paginate = request('paginate', null);
            if ($paginate === 'null') {
                $paginate = null;
            }

            $location = request('location', null);
            if ($location === '_all') {
                $location = null;
            }

            $dateFrom = request('date_from', null);
            if ($dateFrom === '_default') {
                $dateFrom = null;
            }

            $dateTill = request('date_till', null);
            if ($dateTill === '_default') {
                $dateTill = null;
            }

            $tag = request('tag', null);
            if ($tag === '') {
                $tag = null;
            }

            $category = request('category', null);
            if ($category == 0) {
                $category = null;
            }

            $text = request('text', null);
            if ($text === '') {
                $text = null;
            }

            $data = ActivityModel::fetchPastActivities($location, $paginate, $dateFrom, $dateTill, $tag, $category, $text)->toArray();
            foreach ($data as $key => &$item) {
                $item['_type'] = 'activity';

                $item['user'] = User::get($item['owner']);

                if ((IgnoreModel::hasIgnored($item['owner'], auth()->id())) || (IgnoreModel::hasIgnored(auth()->id(), $item['owner']))) {
                    unset($data[$key]);
                    continue;
                }

                if (($item['only_verified'] == true) && (VerifyModel::getState(auth()->id()) != VerifyModel::STATE_VERIFIED)) {
                    unset($data[$key]);
                    continue;
                }

                $item['user']->verified = VerifyModel::getState($item['user']->id) === VerifyModel::STATE_VERIFIED;

                $item['participants'] = ParticipantModel::where('activity', '=', $item['id'])->where('type', '=', ParticipantModel::PARTICIPANT_ACTUAL)->count();
                $item['messages'] = ThreadModel::where('activityId', '=', $item['id'])->count();
                $item['date_of_activity_from'] = Carbon::createFromDate($item['date_of_activity_from']);
                $item['date_of_activity_from_display'] = $item['date_of_activity_from']->format(__('app.date_format_display'));
                $item['date_of_activity_till'] = Carbon::createFromDate($item['date_of_activity_till']);
                $item['date_of_activity_till_display'] = $item['date_of_activity_till']->format(__('app.date_format_display'));
                $item['date_of_activity_time'] = $item['date_of_activity_from']->format(__('app.time_format_display'));
                $item['diffForHumans'] = $item['date_of_activity_from']->diffForHumans();
                $item['date_of_activity_from_formatted'] = $item['date_of_activity_from']->format(__('app.date_format'));
                $item['view_count'] = AppModel::countAsString(UniqueViewsModel::viewForItem($item['id']));
                $item['categoryData'] = CategoryModel::where('id', '=', $item['category'])->first();

                $item['running'] = false;
                
                if (((new DateTime($item['date_of_activity_from'])) < (new DateTime('now'))) && ((new DateTime($item['date_of_activity_till']))->modify('+' . env('APP_ACTIVITYRUNTIME', 60) . ' minutes') >= (new DateTime('now')))) {
                    $item['running'] = true;
                }
            }

            $data = array_values($data);

            if (!User::hasProMode(auth()->id())) {
                $adcode = AppModel::getAdCode();
                if ((strlen($adcode) > 0) && (count($data) > 0)) {
                    $aditem = array();
                    $aditem['_type'] = 'ad';
                    $aditem['code'] = $adcode;
                    $aditem['tags'] = '';
                    $aditem['category'] = 0;
                    $aditem['date_of_activity_from'] = $data[count($data)-1]['date_of_activity_from'];
                    $data[] = $aditem;
                }
            }

            return response()->json(array('code' => 200, 'data' => $data, 'last' => count($data) === 0));
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

            if (($activity->only_verified == true) && (VerifyModel::getState(auth()->id()) != VerifyModel::STATE_VERIFIED)) {
                throw new Exception(__('app.activity_verified_only'));
            }

            $threads = ThreadModel::getFromActivity($id, $paginate);
            if ($paginate !== null) {
                $threads = $threads->sortByDesc('id');
            }
            $threads = $threads->toArray();
            foreach ($threads as &$thread) {
                $thread['user'] = User::get($thread['userId']);
                $thread['user']->verified = VerifyModel::getState($thread['user']->id) === VerifyModel::STATE_VERIFIED;
                $thread['adminOrOwner'] = User::isAdmin(auth()->id()) || ($thread['userId'] === auth()->id());
                $thread['diffForHumans'] = Carbon::createFromDate($thread['created_at'])->diffForHumans();
                $thread['created_at'] = date(__('app.date_format'), strtotime($thread['created_at']));
                $thread['subCount'] = ThreadModel::getSubCount($thread['id']);
                $thread['text'] = AppModel::translateLinks($thread['text']);
            }

            return response()->json(array('code' => 200, 'data' => array_values($threads), 'last' => (count($threads) === 0)));
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

            $threads = ThreadModel::fetchSubThread($parentId, $paginate)->toArray();
            foreach ($threads as &$thread) {
                $thread['user'] = User::get($thread['userId']);
                $thread['user']->verified = VerifyModel::getState($thread['user']->id) === VerifyModel::STATE_VERIFIED;
                $thread['adminOrOwner'] = User::isAdmin(auth()->id()) || ($thread['userId'] === auth()->id());
                $thread['diffForHumans'] = Carbon::createFromDate($thread['created_at'])->diffForHumans();
                $thread['created_at'] = date(__('app.date_format'), strtotime($thread['created_at']));
            }

            return response()->json(array('code' => 200, 'data' => array_values($threads), 'last' => (count($threads) === 0)));
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
            if (IgnoreModel::hasIgnored($id, auth()->id())) {
                throw new Exception(__('app.user_not_found_or_locked'));
            }

            $type = request('type', 'running');
            $paginate = request('paginate', null);

            $data = ActivityModel::fetchUserActivities($id, $type, $paginate)->toArray();
            foreach ($data as $key => &$item) {
                if (($item['only_verified'] == true) && (VerifyModel::getState(auth()->id()) != VerifyModel::STATE_VERIFIED)) {
                    unset($data[$key]);
                    continue;
                }

                $item['_type'] = 'activity';
                $item['diffForHumans'] = Carbon::createFromDate($item['date_of_activity_from'])->diffForHumans();
                $item['date_of_activity_from'] = Carbon::createFromDate($item['date_of_activity_from']);
                $item['date_of_activity_from_display'] = Carbon::createFromDate($item['date_of_activity_from'])->format(__('app.date_format_display'));
                $item['date_of_activity_till'] = Carbon::createFromDate($item['date_of_activity_till']);
                $item['date_of_activity_till_display'] = $item['date_of_activity_till']->format(__('app.date_format_display'));
                $item['date_of_activity_time'] = $item['date_of_activity_from']->format(__('app.time_format_display'));
                $item['participants'] = ParticipantModel::where('activity', '=', $item['id'])->where('type', '=', ParticipantModel::PARTICIPANT_ACTUAL)->count();
                $item['messages'] = ThreadModel::where('activityId', '=', $item['id'])->count();
                $item['categoryData'] = CategoryModel::where('id', '=', $item['category'])->first();
                $item['view_count'] = AppModel::countAsString(UniqueViewsModel::viewForItem($item['id']));
            }

            $running_activity_count = ActivityModel::where('owner', '=', $id)->where('date_of_activity_till', '>=', date('Y-m-d H:i:s'))->count();
            $past_activity_count = ActivityModel::where('owner', '=', $id)->where('date_of_activity_till', '<', date('Y-m-d H:i:s'))->count();
            $total_activity_count = $running_activity_count + $past_activity_count;

            return response()->json(array('code' => 200, 'data' => array_values($data), 'running' => $running_activity_count, 'past' => $past_activity_count, 'total' => $total_activity_count));
        } catch (Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Fetch user participations
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchUserParticipations($userId)
    {
        try {
            $data = ActivityModel::queryUserParticipations($userId);

            foreach ($data as $key => &$item) {
                $item = (array)$item;

                $item['diffForHumans'] = Carbon::createFromDate($item['date_of_activity_from'])->diffForHumans();
                $item['date_of_activity_from_display'] = Carbon::createFromDate($item['date_of_activity_from'])->format(__('app.date_format_display'));
                $item['date_of_activity_till_display'] = Carbon::createFromDate($item['date_of_activity_till'])->format(__('app.date_format_display'));
                $item['date_of_activity_time'] = Carbon::createFromDate($item['date_of_activity_from'])->format(__('app.time_format_display'));
                $item['participants'] = ParticipantModel::where('activity', '=', $item['id'])->where('type', '=', ParticipantModel::PARTICIPANT_ACTUAL)->count();
                $item['messages'] = ThreadModel::where('activityId', '=', $item['id'])->count();
                $item['categoryData'] = CategoryModel::where('id', '=', $item['category'])->first();
                $item['view_count'] = AppModel::countAsString(UniqueViewsModel::viewForItem($item['id']));
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

            if (($activity->only_verified == true) && (VerifyModel::getState(auth()->id()) != VerifyModel::STATE_VERIFIED)) {
                throw new Exception(__('app.activity_verified_only'));
            }

            ThreadModel::add(auth()->id(), $id, $attr['message']);

            $user = User::get(auth()->id());
            $owner = User::get($activity->owner);

            if (($user) && (auth()->id() !== $activity->owner)) {
                PushModel::addNotification(__('app.user_commented_short'), __('app.user_commented_long', ['profile' => url('/user/' . $user->slug), 'name' => $owner->name, 'sender' => $user->name, 'message' => $attr['message'], 'item' => url('/activity/' . $id)]), 'PUSH_COMMENTED', $activity->owner);
            }

            if (($owner) && ($owner->email_on_comment) && (auth()->id() !== $activity->owner)) {
                $html = view('mail.user_commented', ['name' => $owner->name, 'sender' => $user->name, 'message' => $attr['message'], 'activityId' => $activity->id])->render();
                MailerModel::sendMail($owner->email, __('app.mail_user_commented_title'), $html);
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

            if (($activity->only_verified == true) && (VerifyModel::getState(auth()->id()) != VerifyModel::STATE_VERIFIED)) {
                throw new Exception(__('app.activity_verified_only'));
            }

            $user = User::get(auth()->id());

            if ($activity->only_gender !== 0) {
                if ($user->gender !== $activity->only_gender) {
                    throw new Exception(__('app.your_gender_excluded'));
                }
            }

            ParticipantModel::add(auth()->id(), $activityId, ParticipantModel::PARTICIPANT_ACTUAL);

            PushModel::addNotification(__('app.user_participated_short'), __('app.user_participated_long', ['name' => $user->name, 'profile' => url('/user/' . $user->id), 'item' => url('/activity/' . $activity->id)]), 'PUSH_PARTICIPATED', $activity->owner);

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

            if (($activity->only_verified == true) && (VerifyModel::getState(auth()->id()) != VerifyModel::STATE_VERIFIED)) {
                throw new Exception(__('app.activity_verified_only'));
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
            //$this->validateAuth();

            $reporter = auth()->id();

            $user = User::get(auth()->id());
            if (!$user) {
                $reporter = 0;
            }

            $activity = ActivityModel::getActivity($id);
            if (!$activity) {
                throw new Exception(__('app.activity_not_found_or_locked'));
            }

            ReportModel::addReport($reporter, $activity->id, 'ENT_ACTIVITY');

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

    /**
     * Query location by term
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryLocation()
    {
        try {
            $term = request('term', '');
            $items = array();

            if (strlen($term) >= 2) {
                $items = LocationModel::queryByTerm($term);
            }
            
            return response()->json(array('code' => 200, 'data' => $items));
        } catch (Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Perform reminder cronjob task
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function reminderJob($password)
    {
        try {
            if ($password !== env('APP_CRONPW')) {
                return response()->json(array('code' => 403));
            }

            $curDate = date('Y-m-d');
            $lastExec = AppModel::getSettings()->reminder_last_execution;
            if (date('Y-m-d', strtotime($lastExec)) == $curDate) {
                throw new \Exception('Reminder job has already been executed today');
            }

            $data = ActivityModel::reminderJob();

            AppModel::saveSetting('reminder_last_execution', $curDate);

            return response()->json(array('code' => 200, 'data' => $data));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }
}
