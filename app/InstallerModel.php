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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use PDO;
use PDOException;

/**
 * Class InstallerModel
 *
 * Interface to product installation
 */
class InstallerModel extends Model
{
    /**
     * Perform installation process
     *
     * @param $attr
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public static function install($attr)
    {
        try {
            $envcontent = '# Actifisys Environment configuration' . PHP_EOL . PHP_EOL;
            $envcontent .= 'APP_NAME=Actifisys' . PHP_EOL;
            $envcontent .= 'APP_CODENAME=dnyActifisys' . PHP_EOL;
            $envcontent .= 'APP_AUTHOR="Daniel Brendel"' . PHP_EOL;
            $envcontent .= 'APP_CONTACT="dbrendel1988@gmail.com"' . PHP_EOL;
            $envcontent .= 'APP_VERSION="1.0"' . PHP_EOL;
            $envcontent .= 'APP_ENV=local' . PHP_EOL;
            $envcontent .= 'APP_KEY=' . PHP_EOL;
            $envcontent .= 'APP_DEBUG=true' . PHP_EOL;
            $envcontent .= 'APP_URL="' . url('/') . '"' . PHP_EOL;
            $envcontent .= 'APP_PROJECTNAME="' . $attr['project'] . '"' . PHP_EOL;
            $envcontent .= 'APP_DESCRIPTION="The lightweight activity finder system"' . PHP_EOL;
            $envcontent .= 'APP_KEYWORDS="actifisys, dnyactifisys, daniel brendel, activities, appointment, meeting, community, social, finder, opensource, freeware"' . PHP_EOL;
			$envcontent .= 'APP_LANG=en' . PHP_EOL;
            $envcontent .= 'APP_ACTIVITYPACKLIMIT=9' . PHP_EOL;
			$envcontent .= 'APP_THREADPACKLIMIT=15' . PHP_EOL;
            $envcontent .= 'APP_MESSAGEPACKLIMIT=20' . PHP_EOL;
            $envcontent .= 'APP_MESSAGETHREADPACK=30' . PHP_EOL;
            $envcontent .= 'APP_FORUMPACKLIMIT=10' . PHP_EOL;
            $envcontent .= 'APP_ACTIVITYRUNTIME=60' . PHP_EOL;
            $envcontent .= 'APP_ENABLEMARKETPLACE=false' . PHP_EOL;
            $envcontent .= 'APP_MARKETPLACEPACKLIMIT=10' . PHP_EOL;
            $envcontent .= 'APP_ENABLEGALLERY=false' . PHP_EOL;
            $envcontent .= 'APP_GALLERYPACKLIMIT=10' . PHP_EOL;
            $envcontent .= 'APP_GALLERYTHREADPACKLIMIT=10' . PHP_EOL;
            $envcontent .= 'APP_ONLINECOUNTLIMIT=30' . PHP_EOL;
            $envcontent .= 'APP_ONLINECOUNTTIMER=60' . PHP_EOL;
            $envcontent .= 'APP_MAXUPLOADSIZE=5000' . PHP_EOL;
            $envcontent .= 'APP_ABOUTPOSITION=bottom' . PHP_EOL;
            $envcontent .= 'APP_ACCOUNTVERIFICATION=1' . PHP_EOL;
            $envcontent .= 'APP_ENABLESCROLLER=true' . PHP_EOL;
            $envcontent .= 'APP_ENABLELINKTRANSLATION=true' . PHP_EOL;
            $envcontent .= 'APP_NEWSLETTER_COUNT=5' . PHP_EOL;
            $envcontent .= 'APP_CRONPW="' . substr(md5(random_bytes(55)), 10) . '"' . PHP_EOL;
            $envcontent .= 'LOG_CHANNEL=stack' . PHP_EOL;
            $envcontent .= 'DB_CONNECTION=mysql' . PHP_EOL;
            $envcontent .= 'DB_HOST="' . $attr['dbhost'] . '"' . PHP_EOL;
            $envcontent .= 'DB_PORT=' . $attr['dbport'] . PHP_EOL;
            $envcontent .= 'DB_DATABASE="' . $attr['database'] . '"' . PHP_EOL;
            $envcontent .= 'DB_USERNAME="' . $attr['dbuser'] . '"' . PHP_EOL;
            $envcontent .= 'DB_PASSWORD="' . $attr['dbpassword']. '"' . PHP_EOL;
            $envcontent .= 'SMTP_FROMADDRESS="' . $attr['smtpfromaddress'] . '"' . PHP_EOL;
            $envcontent .= 'SMTP_FROMNAME="${APP_PROJECTNAME}"' . PHP_EOL;
            $envcontent .= 'SMTP_HOST="' . $attr['smtphost'] . '"' . PHP_EOL;
            $envcontent .= 'SMTP_USERNAME="' . $attr['smtpuser'] . '"' . PHP_EOL;
            $envcontent .= 'SMTP_PASSWORD="' . $attr['smtppassword'] . '"' . PHP_EOL;
            $envcontent .= 'GA_TOKEN=null' . PHP_EOL;
            $envcontent .= 'TWITTER_NEWS=null' . PHP_EOL;
            $envcontent .= 'STRIPE_ENABLE=false' . PHP_EOL;
            $envcontent .= 'STRIPE_TOKEN_SECRET=""' . PHP_EOL;
            $envcontent .= 'STRIPE_TOKEN_PUBLIC=""' . PHP_EOL;
            $envcontent .= 'STRIPE_CURRENCY="usd"' . PHP_EOL;
            $envcontent .= 'STRIPE_COSTS_VALUE=1000' . PHP_EOL;
            $envcontent .= 'STRIPE_COSTS_LABEL="10.00$"' . PHP_EOL;
			$envcontent .= 'HELPREALM_WORKSPACE=null' . PHP_EOL;
			$envcontent .= 'HELPREALM_TOKEN=' . PHP_EOL;
			$envcontent .= 'HELPREALM_TICKETTYPEID=' . PHP_EOL;
            $envcontent .= 'FIREBASE_ENABLE=false' . PHP_EOL;
            $envcontent .= 'FIREBASE_ENDPOINT="https://fcm.googleapis.com/fcm/send"' . PHP_EOL;
            $envcontent .= 'FIREBASE_KEY=' . PHP_EOL;
            $envcontent .= 'FIREBASE_PROPNAME="notification"' . PHP_EOL;
            $envcontent .= 'BROADCAST_DRIVER=log' . PHP_EOL;
            $envcontent .= 'CACHE_DRIVER=file' . PHP_EOL;
            $envcontent .= 'QUEUE_CONNECTION=sync' . PHP_EOL;
            $envcontent .= 'SESSION_DRIVER=file' . PHP_EOL;
            $envcontent .= 'SESSION_LIFETIME=5760' . PHP_EOL;

            file_put_contents(base_path() . '/.env', $envcontent);

            \Artisan::call('config:clear');
            \Artisan::call('key:generate');

            $dbobj = new PDO('mysql:host=' . $attr['dbhost'], $attr['dbuser'], $attr['dbpassword']);
            $dbobj->exec('CREATE DATABASE IF NOT EXISTS `' . $attr['database'] . '`;');

            Config::set('database.connections.mysql', [
                'host' => $attr['dbhost'],
                'port' => $attr['dbport'],
                'database' => $attr['database'],
                'username' => $attr['dbuser'],
                'password' => $attr['dbpassword'],
                'driver' => 'mysql',
                'url' => env('DATABASE_URL'),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    \PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
                ]) : [],
            ]);

            \DB::reconnect();

            \Artisan::call('migrate:install');
            \Artisan::call('migrate:refresh', array('--path' => 'database/migrations', '--force' => true));

            \DB::insert("INSERT INTO app_settings (cookie_consent, home_bg, clep_bg, about, imprint, tos, reg_info, marketplace_text, gallery_text, project_name_formatted, default_theme, headline_top, headline_sub) VALUES('cookie_consent', 'bg.jpg', 'clep.jpg', 'about', 'imprint', 'tos', 'reg_info', 'Marketplace text', 'Gallery text' '', '_default', 'Headline top', 'Headline sub')");

            $user = new User();
            $user->name = $attr['name'];
            $user->email = $attr['email'];
            $user->password = password_hash($attr['password'], PASSWORD_BCRYPT);
            $user->maintainer = true;
            $user->admin = true;
            $user->account_confirm = '_confirmed';
            $user->avatar = 'default.png';
            $user->bio = 'Project maintainer and administrator';
            $user->gender = 0;
            $user->birthday = date('Y-m-d H:i:s');
            $user->email_on_message = true;
            $user->save();

            unlink(base_path() . '/do_install');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
