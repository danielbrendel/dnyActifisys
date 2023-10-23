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

use App\AppModel;
use App\CaptchaModel;
use App\CategoryModel;
use App\LocationModel;
use App\FaqModel;
use App\MailerModel;
use App\PostModel;
use App\ReportModel;
use App\TagsModel;
use App\ThemeModel;
use App\ThreadModel;
use App\ForumModel;
use App\User;
use App\AnnouncementsModel;
use App\VerifyModel;
use App\ViewCountModel;
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
        parent::__construct();

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
          'comments' => ReportModel::getReportPack('ENT_COMMENT'),
          'forum_posts' => ReportModel::getReportPack('ENT_FORUMPOST'),
          'market_items' => ReportModel::getReportPack('ENT_MARKETITEM'),
          'gallery_items' => ReportModel::getReportPack('ENT_GALLERYITEM')
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
            'categories' => CategoryModel::all(),
            'locations' => LocationModel::all(),
            'forums' => ForumModel::all()
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
     * Save about settings
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function about()
    {
        try {
            $attr = request()->validate([
               'headline_top' => 'required',
               'headline_sub' => 'required',
               'about' => 'required'
            ]);

            AppModel::saveSetting('headline_top', $attr['headline_top']);
            AppModel::saveSetting('headline_sub', $attr['headline_sub']);
            AppModel::saveSetting('about', $attr['about']);

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

            $user = null;

            if ((!is_numeric($ident)) && (is_string($ident)) && (strlen($ident) > 0)) {
                if (filter_var($ident, FILTER_VALIDATE_EMAIL)) {
                    $user = User::getByEmail($ident);
                } else {
                    $user = User::findByName($ident);
                    if (count($user) > 0) {
                        $user = $user[0];
                    }
                }
            } else {
                $user = User::get($ident);
            }

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

            AppModel::initiateNewsletter($attr['subject'], $attr['content']);

            return back()->with('flash.success', __('app.newsletter_in_progress'));
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
     * Save logo
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveLogo()
    {
        try {
            $attr = request()->validate([
               'logo' => 'required|file'
            ]);

            $av = request()->file('logo');
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
                $newname =  'logo.' . $av->getClientOriginalExtension();
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

     /**
     * Add new location
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addLocation()
    {
        try {
            $attr = request()->validate([
                'name' => 'required'
            ]);

            LocationModel::add($attr['name']);

            return back()->with('flash.success', __('app.location_added'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Edit existing location
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editLocation($id)
    {
        try {
            $attr = request()->validate([
                'name' => 'required'
            ]);

            LocationModel::edit($id, $attr['name']);

            return back()->with('flash.success', __('app.location_edited'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Set active status of a location
     *
     * @param $id
     * @param $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function locationActiveStatus($id, $status)
    {
        try {
            LocationModel::setActiveStatus($id, (bool)$status);

            return back()->with('flash.success', __('app.location_status_changed'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Create an announcement
     * 
     * @return mixed
     */
    public function createAnnouncement()
    {
        try {
            $attr = request()->validate([
                'title' => 'required',
                'content' => 'required',
                'until' => 'required|date'
            ]);

            AnnouncementsModel::add($attr['title'], $attr['content'], date('Y-m-d 23:59:59', strtotime($attr['until'])));

            return back()->with('flash.success', __('app.announcement_created'));
        } catch (\Exception $e) {
            return back()->with('flash.error', $e->getMessage());
        }
    }

    /**
     * Create forum
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createForum()
    {
        try {
            $attr = request()->validate([
                'name' => 'required',
                'description' => 'required'
            ]);

            ForumModel::add($attr['name'], $attr['description']);

            return back()->with('flash.success', __('app.forum_created'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Edit forum
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editForum()
    {
        try {
            $attr = request()->validate([
                'id' => 'required|numeric',
                'name' => 'required',
                'description' => 'required'
            ]);

            ForumModel::edit($attr['id'], $attr['name'], $attr['description']);

            return back()->with('flash.success', __('app.forum_edited'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Lock forum
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lockForum($id)
    {
        try {
            ForumModel::lock($id);

            return back()->with('flash.success', __('app.forum_locked'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove forum
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeForum($id)
    {
        try {
            ForumModel::remove($id);

            return back()->with('flash.success', __('app.forum_removed'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * View visits page
     * 
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function visits()
    {
        $start = date('Y-m-d', strtotime('-30 days'));
        $end = date('Y-m-d', strtotime('-1 day'));

        $predefined_dates = [
            __('app.last_week') => date('Y-m-d', strtotime('-7 days')),
			__('app.last_two_weeks') => date('Y-m-d', strtotime('-14 days')),
			__('app.last_month') => date('Y-m-d', strtotime('-1 month')),
			__('app.last_three_months') => date('Y-m-d', strtotime('-3 months')),
			__('app.last_year') => date('Y-m-d', strtotime('-1 year')),
        ];

        $online_count = ViewCountModel::getOnlineCount();

        return view('maintainer.visits', [
            'start' => $start,
            'end' => $end,
            'predefined_dates' => $predefined_dates,
            'online_count' => $online_count,
            'captchadata' => CaptchaModel::createSum(session()->getId())
        ]);
    }

    /**
     * Query visits according to given data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryVisits()
    {
        try {
            $start = request('start', date('Y-m-d', strtotime('-30 days')));
            $end = request('end', date('Y-m-d', strtotime('-1 day')));

            $visits = ViewCountModel::getVisitsPerDay($start, $end);
            $dayDiff = (new \DateTime($end))->diff((new \DateTime($start)))->format('%a');

            $visits_data = [];
            $visits_total = 0;

            foreach ($visits as $item) {
                $visits_total += $item->count;

                $visits_data[] = [
                    'date' => $item->created_at,
                    'count' => $item->count
                ];
            }

            return response()->json(array('code' => 200, 'data' => [
                'visits' => $visits_data,
                'visits_total' => $visits_total,
                'start' => $start,
                'end' => $end,
                'day_diff' => (int)$dayDiff
            ]));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }

    /**
     * Query visitor online count
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryOnlineCount()
    {
        try {
            $online_count = ViewCountModel::getOnlineCount();

            return response()->json(array('code' => 200, 'count' => $online_count));
        } catch (\Exception $e) {
            return response()->json(array('code' => 500, 'msg' => $e->getMessage()));
        }
    }
}
