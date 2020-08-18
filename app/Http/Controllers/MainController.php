<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App\Http\Controllers;

use App\AppModel;
use App\CaptchaModel;
use App\FaqModel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    private $cookie_consent;

    /**
     * MainController constructor.
     */
    public function __construct()
    {
        $this->cookie_consent = AppModel::getCookieConsentText();
    }

    /**
     * View home index page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $captchaData = CaptchaModel::createSum(session()->getId());

        return view('activity.browse', [
            'captchadata' => $captchaData
        ]);
    }

    /**
     * View faq page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function faq()
    {
        return view('home.faq', ['captchadata' => CaptchaModel::createSum(session()->getId()), 'cookie_consent' => $this->cookie_consent, 'faqs' => FaqModel::getAll()]);
    }

    /**
     * View imprint page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function imprint()
    {
        return view('home.imprint', ['captchadata' => CaptchaModel::createSum(session()->getId()), 'cookie_consent' => $this->cookie_consent, 'imprint_content' => AppModel::getImprint()]);
    }

    /**
     * View news page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function news()
    {
        return view('home.news', ['captchadata' => CaptchaModel::createSum(session()->getId()), 'cookie_consent' => $this->cookie_consent]);
    }

    /**
     * View tos page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tos()
    {
        return view('home.tos', ['captchadata' => CaptchaModel::createSum(session()->getId()), 'cookie_consent' => $this->cookie_consent, 'tos_content' => AppModel::getTermsOfService()]);
    }

    /**
     * View contact page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewContact()
    {
        return view('home.contact', ['captchadata' => CaptchaModel::createSum(session()->getId()), 'cookie_consent' => $this->cookie_consent]);
    }

    /**
     * Process contact request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function contact()
    {
        try {
            $attr = request()->validate([
                'name' => 'required',
                'email' => 'required|email',
                'subject' => 'required',
                'body' => 'required',
                'captcha' => 'required'
            ]);

            if ($attr['captcha'] !== CaptchaModel::querySum(session()->getId())) {
                return back()->with('error', __('app.captcha_invalid'))->withInput();
            }

            AppModel::createTicket($attr['name'], $attr['email'], $attr['subject'], $attr['body']);

            return back()->with('success', __('app.contact_success'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Perform login
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        $attr = request()->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guest()) {
            $user = User::where('email', '=', $attr['email'])->first();
            if ($user !== null) {
                if ($user->account_confirm !== '_confirmed') {
                    return back()->with('error', __('app.account_not_yet_confirmed'));
                }

                if ($user->deactivated) {
                    return back()->with('error', __('app.account_deactivated'));
                }
            }

            if (Auth::attempt([
                'email' => $attr['email'],
                'password' => $attr['password']
            ])) {
                $user = User::get(auth()->id());
                return redirect('/')->with('flash.success', __('app.login_welcome_msg', ['name' => $user->name]));
            } else {
                return back()->with('error', __('app.login_failed'));
            }
        } else {
            return back()->with('error', __('app.login_already_logged_in'));
        }
    }

    /**
     * Perform logout
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        if(Auth::check()) {
            Auth::logout();
            request()->session()->invalidate();

            return  redirect('/')->with('flash.success', __('app.logout_success'));
        } else {
            return  redirect('/')->with('error', __('app.not_logged_in'));
        }
    }

    /**
     * Send email with password recovery link to user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recover()
    {
        $attr = request()->validate([
            'email' => 'required|email'
        ]);

        try {
            User::recover($attr['email']);

            return back()->with('success', __('app.pw_recovery_ok'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reset password
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function reset()
    {
        $attr = request()->validate([
            'password' => 'required',
            'password_confirm' => 'required'
        ]);

        $hash = request('hash');

        try {
            User::reset($attr['password'], $attr['password_confirm'], $hash);

            return redirect('/')->with('success', __('app.password_reset_ok'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * View password reset form
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewReset()
    {
        return view('home.pwreset', [
            'hash' => request('hash', ''),
            'captchadata' => CaptchaModel::createSum(session()->getId()),
            'cookie_consent' => $this->cookie_consent
        ]);
    }

    /**
     * Process registration
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register()
    {
        $attr = request()->validate([
            'name' => 'required|min:3|max:55',
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required',
            'captcha' => 'required|numeric'
        ]);

        try {
            $userId = User::register($attr);

            return back()->with('success', __('app.register_confirm_email', ['link' => url('/resend/' . $userId)]));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Resend account confirmation e-mail
     *
     * @param $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend($userId)
    {
        try {
            User::resend($userId);

            return back()->with('success', __('app.register_confirm_resend', ['link' => url('/resend/' . $userId)]));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Confirm account
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirm()
    {
        $hash = request('hash');

        try {
            User::confirm($hash);

            return redirect('/')->with('success', __('app.register_confirmed_ok'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
