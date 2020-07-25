<?php

/*
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Exception;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get user object
     *
     * @param int $id The ID of the user
     * @return mixed
     */
    public static function get($id)
    {
        $user = User::where('id', '=', $id)->first();

        return $user;
    }

    /**
     * Return admin flag value
     * @param $id
     * @return bool
     */
    public static function isAdmin($id)
    {
        $user = static::get($id);

        if (($user) && ($user->admin)) {
            return true;
        }

        return false;
    }

    /**
     * Get user object by email
     *
     * @param string $email The E-Mail address
     * @return mixed
     */
    public static function getByEmail($email)
    {
        $user = User::where('email', '=', $email)->first();

        return $user;
    }

    /**
     * Get user object by authentication ID
     *
     * @return mixed
     */
    public static function getByAuthId()
    {
        if (Auth::guest()) {
            return null;
        }

        return User::where('id', '=', auth()->id())->first();
    }

    /**
     * Perform registration
     *
     * @param $attr
     * @throws Exception
     */
    public static function register($attr)
    {
        try {
            if (!Auth::guest()) {
                throw new Exception(__('app.register_already_signed_in'));
            }

            if ($attr['password'] !== $attr['password_confirmation']) {
                throw new Exception(__('app.register_password_mismatch'));
            }

            $sum = CaptchaModel::querySum(session()->getId());
            if ($attr['captcha'] !== $sum) {
                throw new Exception(__('app.register_captcha_invalid'));
            }

            if (User::getByEmail($attr['email'])) {
                throw new Exception(__('app.register_email_in_use'));
            }

            $user = new User();
            $user->name = $attr['name'];
            $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
            $user->email = $attr['email'];
            $user->avatar = 'default.png';
            $user->account_confirm = md5($attr['email'] . $attr['name'] . random_bytes(55));
            $user->save();

            $html = view('mail.registered', ['name' => $user->name, 'hash' => $user->account_confirm])->render();
            MailerModel::sendMail($user->email, __('app.mail_subject_register'), $html);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Confirm account
     *
     * @param $hash
     * @throws Exception
     */
    public static function confirm($hash)
    {
        try {
            $user = User::where('account_confirm', '=', $hash)->first();
            if ($user === null) {
                throw new Exception(__('app.register_confirm_token_not_found'));
            }

            $user->account_confirm = '_confirmed';
            $user->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Initialize password recovery
     *
     * @param $email
     * @throws Exception
     */
    public static function recover($email)
    {
        try {
            $user = User::getByEmail($email);
            if (!$user) {
                throw new Exception(__('app.email_not_found'));
            }

            $user->password_reset = md5($user->email . date('c') . uniqid('', true));
            $user->save();

            $htmlCode = view('mail.pwreset', ['username' => $user->username, 'hash' => $user->password_reset])->render();
            MailerModel::sendMail($user->email, __('app.mail_password_reset_subject'), $htmlCode);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Perform password reset
     *
     * @param $password
     * @param $password_confirm
     * @param $hash
     * @throws Exception
     */
    public static function reset($password, $password_confirm, $hash)
    {
        try {
            if ($password != $password_confirm) {
                throw new Exception(__('app.password_mismatch'));
            }

            $user = User::where('password_reset', '=', $hash)->first();
            if (!$user) {
                throw new Exception(__('app.hash_not_found'));
            }

            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->password_reset = '';
            $user->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Save user settings
     *
     * @param $attr
     * @param null $id
     * @throws Exception
     */
    public static function saveSettings($attr, $id = null)
    {
        try {
            if ($id === null) {
                $id = auth()->id();
            }

            $user = User::get($id);
            $user->name = $attr['name'];
            $user->birthday = $attr['birthday'];
            $user->gender = $attr['gender'];
            if (($user->gender < 0) || ($user->gender > 3)) {
                $user->gender = 0;
            }
            $user->location = $attr['location'];
            $user->bio = $attr['bio'];
            $user->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Save password
     *
     * @param $attr
     * @param null $id
     * @throws \Throwable
     */
    public static function savePassword($attr, $id = null)
    {
        try {
            if ($id === null) {
                $id = auth()->id();
            }

            if ($attr['password'] !== $attr['password_confirmation']) {
                throw new Exception(__('app.password_mismatch'));
            }

            $user = User::get($id);
            $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
            $user->save();

            $html = view('mail.pw_changed', ['name' => $user->name])->render();
            MailerModel::sendMail($user->email, __('app.password_changed'), $html);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Save E-Mail
     *
     * @param $attr
     * @param null $id
     * @throws \Throwable
     */
    public static function saveEMail($attr, $id = null)
    {
        try {
            if ($id === null) {
                $id = auth()->id();
            }

            $user = User::get($id);
            $oldMail = $user->email;
            $user->email = $attr['email'];
            $user->save();

            $html = view('mail.email_changed', ['name' => $user->name, 'email' => $attr['email']])->render();
            MailerModel::sendMail($user->email, __('app.email_changed'), $html);
            MailerModel::sendMail($oldMail, __('app.email_changed'), $html);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Save notification flags
     *
     * @param $attr
     * @param null $id
     * @throws Exception
     */
    public static function saveNotifications($attr, $id = null)
    {
        try {
            if ($id === null) {
                $id = auth()->id();
            }

            $user = User::get($id);
            $user->newsletter = $attr['newsletter'];
            $user->email_on_message = $attr['email_on_message'];
            $user->email_on_participated = $attr['email_on_participated'];
            $user->email_on_fav_created = $attr['email_on_fav_created'];
            $user->save();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete a user
     *
     * @param null $id
     * @throws Exception
     */
    public static function deleteUser($id = null)
    {
        try {
            if ($id === null) {
                $id = auth()->id();
            }

            $user = User::get($id);
            $user->name = 'deleted ' . md5(random_bytes(55));
            $user->email = md5(random_bytes(55));
            $user->password = '';
            $user->deactivated = true;
            $user->save();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
