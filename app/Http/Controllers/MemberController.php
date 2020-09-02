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

use App\ActivityModel;
use App\AppModel;
use App\CaptchaModel;
use App\FavoritesModel;
use App\IgnoreModel;
use App\ParticipantModel;
use App\ReportModel;
use App\User;
use App\VerifyModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MemberController extends Controller
{
    /**
     * Show user profile
     *
     * @param string $slugOrId Either slug or ID
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($slugOrId)
    {
        try {
            $this->validateAuth();

            $user = User::getBySlug($slugOrId);
            if (!$user) {
                $user = User::get($slugOrId);
                if (!$user) {
                    throw new \Exception(__('app.user_not_found_or_locked'));
                }
            }

            if (IgnoreModel::hasIgnored($user->id, auth()->id())) {
                throw new \Exception(__('app.user_not_found_or_locked'));
            }

            $user->activities = ActivityModel::where('owner', '=', $user->id)->count();
            $user->age = Carbon::parse($user->birthday)->age;
            if ($user->gender === 1) {
                $user->genderText = __('app.gender_male');
            } else if ($user->gender === 2) {
                $user->genderText = __('app.gender_female');
            } else if ($user->gender === 3) {
                $user->genderText = __('app.gender_diverse');
            } else {
                $user->genderText = __('app.gender_unspecified');
            }
            $user->ignored = IgnoreModel::hasIgnored(auth()->id(), $user->id);
            $user->hasFavorited = FavoritesModel::hasUserFavorited(auth()->id(), $user->id, 'ENT_USER');
            $user->verified = VerifyModel::getState($user->id) === VerifyModel::STATE_VERIFIED;

            if ($user->id === auth()->id()) {
                $interestedIn = ParticipantModel::where('participant', '=', $user->id)->where('type', '=', ParticipantModel::PARTICIPANT_POTENTIAL)->get()->toArray();
                foreach ($interestedIn as &$potential) {
                    $potential['activityData'] = ActivityModel::getActivity($potential['activity']);
                }

                $participatingIn = ParticipantModel::where('participant', '=', $user->id)->where('type', '=', ParticipantModel::PARTICIPANT_ACTUAL)->get()->toArray();
                foreach ($participatingIn as &$participating) {
                    $participating['activityData'] = ActivityModel::getActivity($participating['activity']);
                }

                $user->potential = array_values($interestedIn);
                $user->actual = array_values($participatingIn);
            }

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

    /**
     * Show settings
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function viewSettings()
    {
        try {
            $this->validateAuth();

            $self = User::get(auth()->id());
            $self->state = VerifyModel::getState(auth()->id());

            return view('member.settings', [
                'captchadata' => CaptchaModel::createSum(session()->getId()),
                'self' => $self
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Save settings
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveSettings()
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'name' => 'required|min:3|max:55',
                'birthday' => 'required|date',
                'gender' => 'required|numeric',
                'location' => 'nullable',
                'bio' => 'nullable'
            ]);
			
			if (!isset($attr['location'])) {
				$attr['location'] = '';
			}
			
			if (!isset($attr['bio'])) {
				$attr['bio'] = '';
			}

            User::saveSettings($attr);

            $av = request()->file('avatar');
            if ($av != null) {
                $tmpName = md5(random_bytes(55));

                $av->move(base_path() . '/public/gfx/avatars', $tmpName . '.' . $av->getClientOriginalExtension());

                list($width, $height) = getimagesize(base_path() . '/public/gfx/avatars/' . $tmpName . '.' . $av->getClientOriginalExtension());

                $avimg = imagecreatetruecolor(64, 64);
                if (!$avimg)
                    throw new \Exception('imagecreatetruecolor() failed');

                $srcimage = null;
                $newname =  md5_file(base_path() . '/public/gfx/avatars/' . $tmpName . '.' . $av->getClientOriginalExtension()) . '.' . $av->getClientOriginalExtension();
                switch (AppModel::getImageType(base_path() . '/public/gfx/avatars/' . $tmpName . '.' . $av->getClientOriginalExtension())) {
                    case IMAGETYPE_PNG:
                        $srcimage = imagecreatefrompng(base_path() . '/public/gfx/avatars/' . $tmpName . '.' . $av->getClientOriginalExtension());
                        imagecopyresampled($avimg, $srcimage, 0, 0, 0, 0, 64, 64, $width, $height);
                        imagepng($avimg, base_path() . '/public/gfx/avatars/' . $newname);
                        break;
                    case IMAGETYPE_JPEG:
                        $srcimage = imagecreatefromjpeg(base_path() . '/public/gfx/avatars/' . $tmpName . '.' . $av->getClientOriginalExtension());
                        imagecopyresampled($avimg, $srcimage, 0, 0, 0, 0, 64, 64, $width, $height);
                        imagejpeg($avimg, base_path() . '/public/gfx/avatars/' . $newname);
                        break;
                    default:
                        return back()->with('error', __('app.settings_avatar_invalid_image_type'));
                        break;
                }

                unlink(base_path() . '/public/gfx/avatars/' . $tmpName . '.' . $av->getClientOriginalExtension());

                $user = User::get(auth()->id());
                $user->avatar = $newname;
                $user->save();
            }

            return back()->with('flash.success', __('app.settings_saved'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Save password
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function savePassword()
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'password' => 'required',
                'password_confirmation' => 'required'
            ]);

            User::savePassword($attr);

            return back()->with('flash.success', __('app.password_saved'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function saveEMail()
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'email' => 'required|email'
            ]);

            User::saveEMail($attr);

            return back()->with('flash.success', __('app.email_saved'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Save notification flags
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function saveNotifications()
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'newsletter' => 'nullable|numeric',
                'email_on_message' => 'nullable|numeric',
                'email_on_participated' => 'nullable|numeric',
                'email_on_fav_created' => 'nullable|numeric',
                'email_on_comment' => 'nullable|numeric',
                'email_on_act_canceled' => 'nullable|numeric'
            ]);

            if (!isset($attr['newsletter'])) {
                $attr['newsletter'] = 0;
            }

            if (!isset($attr['email_on_message'])) {
                $attr['email_on_message'] = 0;
            }

            if (!isset($attr['email_on_participated'])) {
                $attr['email_on_participated'] = 0;
            }

            if (!isset($attr['email_on_fav_created'])) {
                $attr['email_on_fav_created'] = 0;
            }

            if (!isset($attr['email_on_comment'])) {
                $attr['email_on_comment'] = 0;
            }

            if (!isset($attr['email_on_act_canceled'])) {
                $attr['email_on_act_canceled'] = 0;
            }

            User::saveNotifications($attr);

            return back()->with('flash.success', __('app.notifications_saved'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Apply for account verification
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyAccount()
    {
        try {
            $this->validateAuth();

            $attr = request()->validate([
                'idcard_front' => 'required|file',
                'idcard_back' => 'required|file',
                'confirmation' => 'required|numeric'
            ]);

            VerifyModel::addVerifyAccount(auth()->id(), $attr);

            return back()->with('success', __('app.verify_account_ok'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete user
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteAccount()
    {
        try {
            $attr = request()->validate([
                'captcha' => 'required|numeric',
            ]);

            if ($attr['captcha'] !== CaptchaModel::querySum(session()->getId())) {
                throw new Exception(__('app.invalid_captcha'));
            }

            User::deleteUser();

            return redirect('/logout')->with('flash.success', __('app.account_deleted'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
