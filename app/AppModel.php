<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class AppModel
 *
 * General application interface
 */
class AppModel extends Model
{
    const ONE_HOUR = 3600;
    const ONE_DAY = self::ONE_HOUR * 24;
    const MAX_EXPRESSION_LENGTH = 15;
    const COUNT_MILLION = 1000000;
    const COUNT_HUNDREDTHOUSAND = 100000;
    const COUNT_TENTHOUSAND = 10000;
    const COUNT_THOUSAND = 1000;

    /**
     * Get home background
     * @return mixed
     */
    public static function getHomeBackground()
    {
        return DB::table('app_settings')->first()->home_bg;
    }

    /**
     * Get clep background
     * @return mixed
     */
    public static function getClepBackground()
    {
        return DB::table('app_settings')->first()->clep_bg;
    }

    /**
     * Get home background alpha value
     * @return mixed
     */
    public static function getHomeBackgroundAlpha()
    {
        return DB::table('app_settings')->first()->home_bg_alpha;
    }

    /**
     * Get headline top
     * @return mixed
     */
    public static function getHeadlineTop()
    {
        return Cache::remember('headline_top', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->headline_top;
        });
    }

    /**
     * Get headline sub
     * @return mixed
     */
    public static function getHeadlineSub()
    {
        return Cache::remember('headline_sub', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->headline_sub;
        });
    }

    /**
     * Get cookie consent text
     * @return mixed
     */
    public static function getCookieConsentText()
    {
        return Cache::remember('cookie_consent', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->cookie_consent;
        });
    }

    /**
     * Get about content
     * @return mixed
     */
    public static function getAboutContent()
    {
        return Cache::remember('about', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->about;
        });
    }

    /**
     * Get ToS content
     * @return mixed
     */
    public static function getTermsOfService()
    {
        return Cache::remember('tos', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->tos;
        });
    }

    /**
     * Get imprint content
     * @return mixed
     */
    public static function getImprint()
    {
        return Cache::remember('imprint', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->imprint;
        });
    }

    /**
     * Get short registration info
     * @return mixed
     */
    public static function getRegInfo()
    {
        return Cache::remember('reg_info', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->reg_info;
        });
    }

    /**
     * Get market place text
     * @return mixed
     */
    public static function getMarketplaceText()
    {
        return Cache::remember('marketplace_text', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->marketplace_text;
        });
    }

    /**
     * Get gallery text
     * @return mixed
     */
    public static function getGalleryText()
    {
        return Cache::remember('gallery_text', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->gallery_text;
        });
    }

    /**
     * Get formatted project name
     * @return mixed
     */
    public static function getFormattedProjectName()
    {
        return Cache::remember('formatted_project_name', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->project_name_formatted;
        });
    }

    /**
     * Get default theme
     * @return mixed
     */
    public static function getDefaultTheme()
    {
        return Cache::remember('default_theme', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->default_theme;
        });
    }

    /**
     * Get head code
     * @return mixed
     */
    public static function getHeadCode()
    {
        return Cache::remember('head_code', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->head_code;
        });
    }

    /**
     * Get ad code
     * @return mixed
     */
    public static function getAdCode()
    {
        return Cache::remember('ad_code', AppModel::ONE_DAY, function() {
            return DB::table('app_settings')->first()->adcode;
        });
    }

    /**
     * Get image type of file
     *
     * @param $file
     * @return mixed|null
     */
    public static function getImageType($file)
    {
        $imagetypes = array(
            array('png', IMAGETYPE_PNG),
            array('jpg', IMAGETYPE_JPEG),
            array('jpeg', IMAGETYPE_JPEG)
        );

        for ($i = 0; $i < count($imagetypes); $i++) {
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) == $imagetypes[$i][0]) {
                if (exif_imagetype($file) == $imagetypes[$i][1])
                    return $imagetypes[$i][1];
            }
        }

        return null;
    }

    /**
     * Get a list of mentioned users
     *
     * @param $text
     * @return array
     */
    public static function getMentionList($text)
    {
        $inMention = false;
        $terminationChars = array(' ', '.', '!', '\n');
        $curName = '';

        $result = array();

        for ($i = 0; $i < strlen($text); $i++) {
            if ($text[$i] === '@') {
                $inMention = true;
                if (strlen($curName) > 0) {
                    if (!in_array($curName, $result)) {
                        $result[] = $curName;
                    }
                }
                $curName = '';
                continue;
            }

            if ($inMention) {
                if ((in_array($text[$i], $terminationChars)) || ($i === strlen($text) - 1)) {
                    if (!in_array($curName, $result)) {
                        $result[] = $curName;
                    }

                    $curName = '';
                    $inMention = false;
                    continue;
                }

                $curName .= $text[$i];
            }
        }

        return $result;
    }

    /**
     * Get settings
     *
     * @return Model|\Illuminate\Database\Query\Builder|object|null
     * @throws \Exception
     */
    public static function getSettings()
    {
        try {
            return DB::table('app_settings')->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save setting
     *
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public static function saveSetting($key, $value)
    {
        try {
            DB::table('app_settings')->where('id', '=', 1)->update(array($key => $value));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save env content
     *
     * @throws \Exception
     */
    public static function saveEnvironmentConfig()
    {
        try {
            $content = '# Actifisys environment configuration' . PHP_EOL;

            foreach ($_ENV as $key => $value) {
                $type = gettype($value);
                if ($type === 'string') {
                    $content .= $key . '="' . $value . '"' . PHP_EOL;
                } else {
                    if ($type === 'bool') {
                        $content .= $key = '=' . (($value) ? 'true' : 'false') . '' . PHP_EOL;
                    } else {
                        $content .= $key = '=' . $value . '' . PHP_EOL;
                    }
                }
            }

            $entire = file_get_contents(base_path() . '/.env') . PHP_EOL . $content;

            file_put_contents(base_path() . '/.env', $entire);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate a random password
     *
     * @param $length
     * @return string
     * @throws \Exception
     */
    public static function getRandomPassword($length)
    {
        try {
            $chars = 'abcdefghijklmnopqrstuvwxyz1234567890%$!';

            $result = '';

            for ($i = 0; $i < $length; $i++) {
                $result .= $chars[rand(0, strlen($chars) - 1)];
            }

            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get list of available languages
     *
     * @return array
     * @throws \Exception
     */
    public static function getLanguageList()
    {
        try {
            $result = array();
            $files = scandir(base_path() . '/resources/lang');
            foreach ($files as $file) {
                if (($file[0] !== '.') && (is_dir(base_path() . '/resources/lang/' . $file))) {
                    $result[] = $file;
                }
            }

            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get short expression
     *
     * @param $exp
     * @return string
     */
    public static function getShortExpression($exp)
    {
        return (strlen($exp) > self::MAX_EXPRESSION_LENGTH) ?
            substr($exp, 0, self::MAX_EXPRESSION_LENGTH) . '...' :
            $exp;
    }

    /**
     * Lock entity
     * @param $id
     * @param $type
     * @throws \Exception
     */
    public static function lockEntity($id, $type)
    {
        try {
            if ($type === 'ENT_HASHTAG') {
                $item = TagsModel::where('id', '=', $id)->first();
                if ($item) {
                    $item->locked = true;
                    $item->save();
                }
            } else if ($type === 'ENT_USER') {
                $item = User::where('id', '=', $id)->first();
                if ($item) {
                    $item->deactivated = true;
                    $item->save();
                }
            } else if ($type === 'ENT_POST') {
                $item = PostModel::where('id', '=', $id)->first();
                if ($item) {
                    $item->locked = true;
                    $item->save();
                }
            } else if ($type === 'ENT_COMMENT') {
                $item = ThreadModel::where('id', '=', $id)->first();
                if ($item) {
                    $item->locked = true;
                    $item->save();
                }
            } else if ($type === 'ENT_MARKETITEM') {
                $item = MarketplaceModel::where('id', '=', $id)->first();
                if ($item) {
                    $item->active = false;
                    $item->save();
                }
            } else {
                throw new Exception('Invalid type: ' . $type, 500);
            }

            $rows = ReportModel::where('entityId', '=', $id)->where('type', '=', $type)->get();
            foreach ($rows as $row) {
                $row->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete entity
     * @param $id
     * @param $type
     * @throws \Exception
     */
    public static function deleteEntity($id, $type)
    {
        try {
            if ($type === 'ENT_HASHTAG') {
                $item = TagsModel::where('id', '=', $id)->first();
                if ($item) {
                    $item->delete();
                }
            } else if ($type === 'ENT_USER') {
                $item = User::where('id', '=', $id)->first();
                if ($item) {
                    $item->name = '_deleted_' . md5(random_bytes(55));
                    $item->email = md5(random_bytes(55));
                    $item->avatar = 'default.png';
                    $item->password = '';
					$item->deactivated = true;
                    $item->save();
                }
            } else if ($type === 'ENT_COMMENT') {
                $item = ThreadModel::where('id', '=', $id)->first();
                if ($item) {
                    $item->locked = true;
                    $item->text = '';
                    $item->save();
                }
            } else if ($type === 'ENT_MARKETITEM') {
                $item = MarketplaceModel::where('id', '=', $id)->first();
                if ($item) {
                    $item->delete();
                }
            } else {
                throw new Exception('Invalid type: ' . $type, 500);
            }

            $rows = ReportModel::where('entityId', '=', $id)->where('type', '=', $type)->get();
            foreach ($rows as $row) {
                $row->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Set entity safe
     *
     * @param $id
     * @param $type
     * @throws \Exception
     */
    public static function setEntitySafe($id, $type)
    {
        try {
            $rows = ReportModel::where('entityId', '=', $id)->where('type', '=', $type)->get();
            foreach ($rows as $row) {
                $row->delete();
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Generate a string representation for the given count
     *
     * @param $count
     * @return string
     * @throws \Exception
     */
    public static function countAsString($count)
    {
        try {
            if ($count >= self::COUNT_MILLION) {
                return strval(round($count / self::COUNT_MILLION, 1)) . 'M';
            } else if (($count < self::COUNT_MILLION) && ($count >= self::COUNT_HUNDREDTHOUSAND)) {
                return strval(round($count / self::COUNT_THOUSAND, 1)) . 'K';
            } else if (($count < self::COUNT_HUNDREDTHOUSAND) && ($count >= self::COUNT_TENTHOUSAND)) {
                return strval(round($count / self::COUNT_THOUSAND, 1)) . 'K';
            } else if (($count < self::COUNT_TENTHOUSAND) && ($count >= self::COUNT_THOUSAND)) {
                return strval(round($count / self::COUNT_THOUSAND, 1)) . 'K';
            } else {
                return strval($count);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Assume if the remote URL points to an image
     * 
     * @param $url
     * @return bool
     */
    public static function isRemoteImage($url)
    {
        $imagetypes = [
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/gif',
            'image/svg'
        ];

        $headers = get_headers($url, true);

        if (isset($headers['Content-Type'])) {
            $type = strtolower($headers['Content-Type']);
            if (in_array($type, $imagetypes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Translate text URLs into anchor HTML elements
     * 
     * @param $text
     * @return string
     * @throws \Exception
     */
    public static function translateURLs($text)
    {
        try {
            if (strpos($text, 'https://') === 0) {
                $text = '&nbsp;' . $text;
            }

            return str_replace(['<p>', '</p>'], '', preg_replace('"\b(https?://\S+)"', '<a href="$1" class="is-translated-link" target="_blank">$1</a>', $text));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Translate text image links to img HTML elements
     * 
     * @param $text
     * @return string
     * @throws \Exception
     */
    public static function translateImages($text)
    {
        try {
            $dom = new \DOMDocument();
            $dom->loadHtml(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            $xpath = new \DOMXPath($dom);
            foreach ($xpath->query('//text()') as $node) {
                $fixnode = $node->data;
                if (strpos($fixnode, '?')) {
                    $fixnode = substr($fixnode, 0, strpos($fixnode, '?'));
                }
                $replaced = preg_replace('/(https?:\/\/[^ ]+?(?:\.jpg|\.jpeg|\.png|\.gif|\.svg))/', '<img src="$1" alt="$1"/>', $fixnode);
                $frag = $dom->createDocumentFragment();
                $frag->appendXML($replaced);
                $node->parentNode->replaceChild($frag, $node);
            }

            return str_replace(['<p>', '</p>'], '', $dom->saveHtml());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Translate all links of a text
     * 
     * @param $text
     * @param $images
     * @return string
     * @throws \Exception
     */
    public static function translateLinks($text, $images = true)
    {
        try {
            if (!env('APP_ENABLELINKTRANSLATION', false)) {
                return $text;
            }

            $text = static::translateURLs($text);

            if ($images) {
                $text = static::translateImages($text);
            }
            
            return $text;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Save formatted project name
     *
     * @param $code
     * @throws \Exception
     */
    public static function saveFormattedProjectName($code)
    {
        try {
            DB::update('UPDATE app_settings SET project_name_formatted = ? WHERE id = 1', array($code));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Initiate new newsletter sending process
     * 
     * @param $subject
     * @param $content
     * @return void
     * @throws \Exception
     */
    public static function initiateNewsletter($subject, $content)
    {
        try {
            $token = md5($subject . $content . random_bytes(55));

            DB::update('UPDATE app_settings SET newsletter_token = ?, newsletter_subject = ?, newsletter_content = ? WHERE id = 1', array($token, $subject, $content));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Newsletter sending pack job
     * 
     * @return array
     * @throws \Exception
     */
    public static function sendNewsletter()
    {
        try {
            $result = array();

            $settings = DB::table('app_settings')->first();

            if ($settings->newsletter_token !== null) {
                $users = User::where('account_confirm', '=', '_confirmed')->where('deactivated', '=', false)->where('newsletter', '=', true)->where('newsletter_token', '<>', $settings->newsletter_token)->limit(env('APP_NEWSLETTER_COUNT'))->get();
                
                foreach ($users as $user) {
                    $user->newsletter_token = $settings->newsletter_token;
                    $user->save();

                    MailerModel::sendMail($user->email, $settings->newsletter_subject, $settings->newsletter_content);

                    $result[] = array('user' => $user->name . '/' . $user->email);
                }
            }

            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Create a HelpRealm ticket
     *
     * @param $name
     * @param $email
     * @param $subject
     * @param $body
     * @throws \Exception
     */
    public static function createTicket($name, $email, $subject, $body)
    {
        try {
            $postFields = [
                'apitoken' => env('HELPREALM_TOKEN'),
                'subject' => $subject,
                'text' => $body,
                'name' => $name,
                'email' => $email,
                'type' => env('HELPREALM_TICKETTYPEID'),
                'prio' => '1',
                'attachment' => null
            ];

            $ch = curl_init("https://helprealm.io/api/" . env('HELPREALM_WORKSPACE') . '/ticket/create');

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

            $response = curl_exec($ch);
            if(curl_error($ch)) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            curl_close($ch);

            $json = json_decode($response);
            if ($json->code !== 201) {
                throw new Exception('Backend returned error ' . ((isset($json->data->invalid_fields)) ? print_r($json->data->invalid_fields, true) : ''), $json->code);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get a list of sitemap items
     * 
     * @return array
     */
    public static function sitemap()
    {
        $sitemap = [];

        $sitemap[] = url('/');
        $sitemap[] = url('/faq');
        $sitemap[] = url('/imprint');
        $sitemap[] = url('/tos');
        $sitemap[] = url('/forum');

        if (env('APP_ENABLEGALLERY')) {
            $sitemap[] = url('/gallery');
        }

        if (env('APP_ENABLEMARKETPLACE')) {
            $sitemap[] = url('/marketplace');
        }

        if (env('TWITTER_NEWS')) {
            $sitemap[] = url('/news');
        }

        if (env('HELPREALM_WORKSPACE')) {
            $sitemap[] = url('/contact');
        }

        $activities = ActivityModel::where(function($query){
            $query->where('date_of_activity_from', '>=', date('Y-m-d H:i:s'))
            ->orWhere('date_of_activity_till', '>=', date('Y-m-d H:i:s', strtotime('+' . env('APP_ACTIVITYRUNTIME', 60) . ' minutes')));
        })->where('locked', '=', false)->where('canceled', '=', false)->orderBy('date_of_activity_from', 'asc')->get();

        foreach ($activities as $activity) {
            $sitemap[] = url('/activity/' . $activity->slug);
        }

        return $sitemap;
    }
}
