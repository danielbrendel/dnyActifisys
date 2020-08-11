<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ThemeModel
 *
 * Interface to themes
 */
class ThemeModel extends Model
{
    /**
     * Add theme
     *
     * @param $name
     * @param $code
     * @throws \Exception
     */
    public static function addTheme($name, $code)
    {
        try {
            if (!is_dir(public_path() . '/css/themes')) {
                mkdir(public_path() . '/css/themes');
            }

            if (file_exists(public_path() . '/css/themes/' . $name)) {
                throw new \Exception('Theme with the given name does already exist');
            }

            file_put_contents(public_path() . '/css/themes/' . $name, $code);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Edit theme
     *
     * @param $name
     * @param $code
     * @throws \Exception
     */
    public static function editTheme($name, $code)
    {
        try {
            file_put_contents(public_path() . '/css/themes/' . $name, $code);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete a theme
     *
     * @param $name
     * @throws \Exception
     */
    public static function deleteTheme($name)
    {
        try {
            unlink(public_path() . '/css/themes/' . $name);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get all existing themes
     *
     * @return array
     * @throws Exception
     */
    public static function getThemes()
    {
        try {
            $result = array();

            if (is_dir(public_path() . '/css/themes')) {
                $files = scandir(public_path() . '/css/themes');
                foreach ($files as $file) {
                    if (($file[0] === '.') || (pathinfo($file, PATHINFO_EXTENSION) !== 'css')) {
                        continue;
                    }

                    $result[] = $file;
                }
            }

            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get theme content
     *
     * @param $name
     * @return false|string
     * @throws Exception
     */
    public static function getTheme($name)
    {
        try {
            if (!file_exists(public_path() . '/css/themes/' . $name)) {
                throw new Exception('Theme not found: ' . $name);
            }

            return file_get_contents(public_path() . '/css/themes/' . $name);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Determine which theme to include
     *
     * @return string
     */
    public static function getThemeToInclude()
    {
        try {
            if ((!isset($_COOKIE['theme'])) || (((isset($_COOKIE['theme'])) && ($_COOKIE['theme'] !== '_default') && (!file_exists(public_path() . '/css/themes/' . $_COOKIE['theme']))))) {
                if ((AppModel::getDefaultTheme() === '_default') || (!file_exists(public_path() . '/css/themes/' . AppModel::getDefaultTheme()))) {
                    return asset('css/app.css');
                } else {
                    return asset('css/themes/' . AppModel::getDefaultTheme());
                }
            } else {
                if ($_COOKIE['theme'] === '_default') {
                    return asset('css/app.css');
                } else {
                    return asset('css/themes/' . $_COOKIE['theme']);
                }
            }
        } catch (Exception $e) {
            return asset('css/app.css');
        }
    }
}
