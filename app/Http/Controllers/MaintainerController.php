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
use App\CategoryModel;
use App\FaqModel;
use App\MailerModel;
use App\PostModel;
use App\ReportModel;
use App\TagsModel;
use App\ThemeModel;
use App\ThreadModel;
use App\User;
use App\VerifyModel;
use Dotenv\Dotenv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MaintainerController extends Controller
{
    /**
     * Validate permissions
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = User::get(auth()->id());
            if ((!$user) || (!$user->maintainer)) {
                abort(403);
            }

            return $next($request);
        });
    }

    /**
     * Show index page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $reports = array(
          'users' => ReportModel::getReportPack('ENT_USER'),
          'activities' => ReportModel::getReportPack('ENT_ACTIVITY'),
          'comments' => ReportModel::getReportPack('ENT_COMMENT')
        );

        foreach ($reports['comments'] as &$cmt) {
            $cmt->postId = ThreadModel::where('id', '=', $cmt->entityId)->first()->postId;
        }

        $themes = array();
        foreach (ThemeModel::getThemes() as $theme) {
            $item = new \stdClass();
            $item->name = $theme;
            $item->content = ThemeModel::getTheme($theme);
            $themes[] = $item;
        }

        $verification_users = VerifyModel::fetchPack();

        return view('maintainer.index', [
            'captchadata' => CaptchaModel::createSum(session()->getId()),
            'user' => User::get(auth()->id()),
            'settings' => AppModel::getSettings(),
            'faqs' => FaqModel::getAll(),
            'themes' => $themes,
            'langs' => AppModel::getLanguageList(),
			'cookie_consent' => AppModel::getCookieConsentText(),
            'reports' => $reports,
            'verification_users' => $verification_users,
            'categories' => CategoryModel::all()
        ]);
    }

    /**
     * Save app database settings
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        try {
            $attr = request()->validate([
               'attribute' => 'required',
               'content' => 'nullable'
            ]);

            if (!isset($attr['content'])) {
                $attr['content'] = '';
            }

            AppModel::saveSetting($attr['attribute'], $attr['content']);

            Artisan::call('cache:clear');

            return back()->with('flash.success', __('app.settings_saved'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Add FAQ item
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addFaq()
    {
        try {
            $attr = request()->validate([
                'question' => 'required',
                'answer' => 'required'
            ]);

            $faq = new FaqModel();
            $faq->question = $attr['question'];
            $faq->answer = $attr['answer'];
            $faq->save();

            Artisan::call('cache:clear');

            return back()->with('flash.success', __('app.faq_saved'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Edit FAQ item
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editFaq()
    {
        try {
            $attr = request()->validate([
                'id' => 'required|numeric',
                'question' => 'required',
                'answer' => 'required'
            ]);

            $faq = FaqModel::where('id', '=', $attr['id'])->first();
            $faq->question = $attr['question'];
            $faq->answer = $attr['answer'];
            $faq->save();

            Artisan::call('cache:clear');

            return back()->with('flash.success', __('app.faq_saved'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Remove FAQ item
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeFaq($id)
    {
        try {
            $faq = FaqModel::where('id', '=', $id)->first();
            $faq->delete();

            Artisan::call('cache:clear');

            return back()->with('flash.success', __('app.faq_removed'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Store env configuration
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function envSave()
    {
        try {
            foreach ($_POST as $key => $value) {
                if (substr($key, 0, 4) === 'ENV_') {
                    $_ENV[substr($key, 4)] = $value;
                }
            }

            AppModel::saveEnvironmentConfig();

            return back()->with('flash.success', __('app.env_saved'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Get user details
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userDetails()
    {
        try {
            $ident = request('ident');

            $user = User::get($ident);
            if (!$user) {
                return response()->json(array('code' => 404, 'msg' => __('app.user_not_found')));
            }

            return response()->json(array('code' => 200, 'data' => $user));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Save user data
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userSave()
    {
        try {
            $attr = request()->validate([
                'id' => 'required|numeric',
                'name' => 'required',
                'email' => 'required|email',
                'deactivated' => 'nullable|numeric',
                'admin' => 'nullable|numeric',
                'maintainer' => 'nullable|numeric'
            ]);

            $user = User::get($attr['id']);
            if (!$user) {
                return back()->with('flash.error', __('app.user_not_found'));
            }

            $user->name = $attr['name'];
            $user->email = $attr['email'];
            $user->deactivated = (isset($attr['deactivated'])) ? (bool)$attr['deactivated'] : false;
            $user->admin = (isset($attr['admin'])) ? (bool)$attr['admin'] : false;
            $user->maintainer = (isset($attr['maintainer'])) ? (bool)$attr['maintainer'] : false;
            if ($user->maintainer === true) {
                $user->admin = true;
            }
            $user->save();

            return back()->with('flash.success', __('app.saved'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Send newsletter
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function newsletter()
    {
        try {
            $attr = request()->validate([
               'subject' => 'required',
               'content' => 'required'
            ]);

            User::sendNewsletter($attr['subject'], $attr['content']);

            return back()->with('flash.success', __('app.newsletter_sent'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Add new theme
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addTheme()
    {
        try {
            $attr = request()->validate([
                'name' => 'required',
                'code' => 'required'
            ]);

            if (pathinfo($attr['name'], PATHINFO_EXTENSION) !== 'css') {
                $attr['name'] .= '.css';
            }

            ThemeModel::addTheme($attr['name'], $attr['code']);

            return back()->with('flash.success', __('app.theme_created'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Set default theme
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setDefaultTheme()
    {
        try {
            $name = request('name');

            AppModel::saveSetting('default_theme', $name);

            Artisan::call('cache:clear');

            return back()->with('flash.success', __('app.theme_default_saved'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Edit theme
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editTheme()
    {
        try {
            $attr = request()->validate([
                'name' => 'required',
                'code' => 'required'
            ]);

            if (pathinfo($attr['name'], PATHINFO_EXTENSION) !== 'css') {
                $attr['name'] .= '.css';
            }

            ThemeModel::editTheme($attr['name'], $attr['code']);

            return back()->with('flash.success', __('app.theme_edited'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    public function deleteTheme()
    {
        try {
            $name = request('name');

            if (pathinfo($name, PATHINFO_EXTENSION) !== 'css') {
                $name .= '.css';
            }

            ThemeModel::deleteTheme($name);

            return back()->with('flash.success', __('app.theme_deleted'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Save favicon
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveFavicon()
    {
        try {
            $attr = request()->validate([
               'favicon' => 'required|file'
            ]);

            $av = request()->file('favicon');
            if ($av != null) {
                if ($av->getClientOriginalExtension() !== 'png') {
                    return back()->with('error', __('app.not_a_png_file'));
                }

                $tmpName = md5(random_bytes(55));

                $av->move(base_path() . '/public/', $tmpName . '.' . $av->getClientOriginalExtension());

                list($width, $height) = getimagesize(base_path() . '/public/' . $tmpName . '.' . $av->getClientOriginalExtension());

                $avimg = imagecreatetruecolor(64, 64);
                if (!$avimg)
                    throw new \Exception('imagecreatetruecolor() failed');

                $srcimage = null;
                $newname =  'favicon.' . $av->getClientOriginalExtension();
                switch (AppModel::getImageType(base_path() . '/public/' . $tmpName . '.' . $av->getClientOriginalExtension())) {
                    case IMAGETYPE_PNG:
                        $srcimage = imagecreatefrompng(base_path() . '/public/' . $tmpName . '.' . $av->getClientOriginalExtension());
                        imagecopyresampled($avimg, $srcimage, 0, 0, 0, 0, 64, 64, $width, $height);
                        imagepng($avimg, base_path() . '/public/' . $newname);
                        break;
                    default:
                        return back()->with('error', __('app.not_a_png_file'));
                        break;
                }

                unlink(base_path() . '/public/' . $tmpName . '.' . $av->getClientOriginalExtension());

                return back()->with('success', __('app.saved'));
            }
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Save background
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveBackground()
    {
        try {
            $attr = request()->validate([
                'headline_top' => 'required',
                'headline_sub' => 'required',
                'bg_alpha' => 'required|numeric'
            ]);

            AppModel::saveSetting('headline_top', $attr['headline_top']);
            AppModel::saveSetting('headline_sub', $attr['headline_sub']);
            AppModel::saveSetting('home_bg_alpha', $attr['bg_alpha']);

            $ba = request()->file('bg');
            if ($ba != null) {
                $newName = md5(random_bytes(55));
                $ba->move(base_path() . '/public/gfx/', $newName . '.' . $ba->getClientOriginalExtension());

                if (AppModel::getImageType(base_path() . '/public/gfx/' . $newName . '.' . $ba->getClientOriginalExtension()) === null) {
                    unlink(base_path() . '/public/gfx/', $newName . '.' . $ba->getClientOriginalExtension());
                    throw new \Exception(__('app.invalid_image_file'));
                }

                AppModel::saveSetting('home_banner', $newName . '.' . $ba->getClientOriginalExtension());

                Artisan::call('cache:clear');

                return back()->with('success', __('app.saved'));
            }
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Lock entity
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lockEntity()
    {
        try {
            $id = request('id');
            $type = request('type');

            AppModel::lockEntity($id, $type);

            return back()->with('flash.success', __('app.entity_locked'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Delete entity
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteEntity()
    {
        try {
            $id = request('id');
            $type = request('type');

            AppModel::deleteEntity($id, $type);

            return back()->with('flash.success', __('app.entity_deleted'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Set entity safe
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setSafeEntity()
    {
        try {
            $id = request('id');
            $type = request('type');

            AppModel::setEntitySafe($id, $type);

            return back()->with('flash.success', __('app.entity_set_safe'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Save formatted project name
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveFormattedProjectName()
    {
        try {
            $attr = request()->validate([
                'code' => 'nullable'
            ]);

            if (!isset($attr['code'])) {
                $attr['code'] = '';
            }

            AppModel::saveFormattedProjectName($attr['code']);

            Artisan::call('cache:clear');

            return back()->with('flash.success', __('app.formatted_project_name_saved'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Approve account
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveAccount($id)
    {
        try {
            $user = User::get($id);

            VerifyModel::verifyStatus($id, VerifyModel::STATE_VERIFIED);

            $html = view('mail.acc_verify', ['name' => $user->name, 'state' => __('app.account_verified'), 'reason' => '-'])->render();
            MailerModel::sendMail($user->email, __('app.mail_acc_verify_title'), $html);

            return back()->with('flash.success', __('app.account_verified'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Decline account
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function declineAccount($id)
    {
        try {
            $user = User::get($id);

            $reason = request('reason', '');

            VerifyModel::verifyStatus($id, VerifyModel::STATE_DECLINED, urldecode($reason));

            $html = view('mail.acc_verify', ['name' => $user->name, 'state' => __('app.account_verification_declined'), 'reason' => urldecode($reason)])->render();
            MailerModel::sendMail($user->email, __('app.mail_acc_verify_title'), $html);

            return back()->with('flash.success', __('app.account_verification_declined'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Add new category
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addCategory()
    {
        try {
            $attr = request()->validate([
                'name' => 'required',
                'description' => 'required',
                'image' => 'required|file'
            ]);

            CategoryModel::add($attr);

            return back()->with('flash.success', __('app.category_added'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Edit existing category
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editCategory($id)
    {
        try {
            $attr = request()->validate([
                'name' => 'required',
                'description' => 'required'
            ]);

            CategoryModel::edit($id, $attr);

            return back()->with('flash.success', __('app.category_edited'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Set inactive status of a category
     *
     * @param $id
     * @param $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function categoryInactiveStatus($id, $status)
    {
        try {
            CategoryModel::setInactiveStatus($id, (bool)$status);

            return back()->with('flash.success', __('app.category_status_changed'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }
}
