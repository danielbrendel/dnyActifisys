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

use App\AppModel;
use App\CaptchaModel;
use App\IgnoreModel;
use App\MessageModel;
use App\TagsModel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MessageController extends Controller
{
    /**
     * View message list
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        return view('message.list', [
            'user' => User::getByAuthId(),
			'cookie_consent' => AppModel::getCookieConsentText(),
            'captchadata' => CaptchaModel::createSum(session()->getId())
        ]);
    }

    /**
     * Fetch list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchList()
    {
        try {
            $paginate = request('paginate', null);
            $direction = request('direction', null);

            $data = MessageModel::fetch(auth()->id(), env('APP_MESSAGEPACKLIMIT'), $paginate, $direction);
            foreach ($data as &$item) {
                if ($item->lm->senderId === auth()->id()) {
                    $item->lm->user = User::get($item->lm->userId);
                } else {
                    $item->lm->user = User::get($item->lm->senderId);
                }

                $item->lm->sender = User::get($item->lm->senderId);

                $item->lm->diffForHumans = $item->lm->created_at->diffForHumans();
            }

            return response()->json(array('code' => 200, 'data' => $data));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Show message thread
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $msg = MessageModel::getMessageThread($id);
            if (!$msg) {
                return back()->with('error', __('app.message_not_found'));
            }

            $msg->user = User::get($msg->userId);
            $msg->sender = User::get($msg->senderId);

            if ($msg->senderId == auth()->id()) {
                $msg->message_partner = $msg->user;
            } else {
                $msg->message_partner = $msg->sender;
            }

            return view('message.show', [
                'msg' => $msg,
				'cookie_consent' => AppModel::getCookieConsentText(),
                'captchadata' => CaptchaModel::createSum(session()->getId())
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Query message pack
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function query()
    {
        try {
            $ident = request('id');
            $paginate = request('paginate');

            $data = MessageModel::queryThreadPack($ident, env('APP_MESSAGETHREADPACK', 30), $paginate);

            return response()->json(array('code' => 200, 'data' => $data));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * View message creation form
     *
     * @return mixed
     * @throws \Exception
     */
    public function create()
    {
        $userId = request('userId');
        $user = User::get($userId);

        if (!$user) {
            return back()->with('flash.error', __('app.user_not_found_or_locked'));
        }

        if (IgnoreModel::hasIgnored($userId, auth()->id())) {
            return back()->with('flash.error', __('app.user_not_found_or_locked'));
        }

        return view('message.create', [
            'user' => $user,
			'cookie_consent' => AppModel::getCookieConsentText(),
            'captchadata' => CaptchaModel::createSum(session()->getId())
        ]);
    }

    /**
     * Send message
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function send()
    {
        try {
            $attr = request()->validate([
               'user' => 'required|numeric',
               'subject' => 'nullable',
               'text' => 'required'
            ]);

            if (!isset($attr['subject'])) {
                $attr['subject'] = '';
            }

            $sender = User::getByAuthId();
            if (!$sender) {
                throw new \Exception('Not logged in');
            }

            $receiver = User::get($attr['user']);

            if (!$receiver) {
                throw new \Exception(__('app.user_not_found_or_locked'));
            }

            if (IgnoreModel::hasIgnored($receiver->id, auth()->id())) {
                return back()->with('flash.error', __('app.user_not_found_or_locked'));
            }

            $id = MessageModel::add($receiver->id, $sender->id, $attr['subject'], $attr['text']);

            return redirect('/messages/show/' . $id)->with('flash.success', __('app.message_sent'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
