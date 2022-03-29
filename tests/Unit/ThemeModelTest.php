<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\Models;

use App\AppModel;
use App\FavoritesModel;
use App\CaptchaModel;
use App\FaqModel;
use App\ReportModel;
use App\ThemeModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeModelTest extends TestCase
{
    public function testAddTheme()
    {
        $cssFile = md5(random_bytes(55)) . '.css';
        $code = 'body { background-color: rgb(0, 0, 0); }';

        ThemeModel::addTheme($cssFile, $code);

        $this->assertTrue(file_exists(public_path() . '/css/themes/' . $cssFile));
        $this->assertEquals($code, file_get_contents(public_path() . '/css/themes/' . $cssFile));

        return $cssFile;
    }

    /**
     * @param $cssFile
     * @throws \Exception
     * @depends testAddTheme
     */
    public function testEditTheme($cssFile)
    {
        $newCode = 'body { background-color: rgb(255, 255, 255); }';
        ThemeModel::editTheme($cssFile, $newCode);
        $this->assertEquals($newCode, file_get_contents(public_path() . '/css/themes/' . $cssFile));

        return $cssFile;
    }

    /**
     * @param $cssFile
     * @return mixed
     * @throws \Exception
     * @depends testEditTheme
     */
    public function testGetTheme($cssFile)
    {
        $code = ThemeModel::getTheme($cssFile);
        $this->assertIsString($code);

        return $cssFile;
    }

    /**
     * @param $cssFile
     * @depends testGetTheme
     */
    public function testDeleteTheme($cssFile)
    {
        ThemeModel::deleteTheme($cssFile);
        $this->assertFalse(file_exists(public_path() . '/css/themes/' . $cssFile));
    }

    public function testGetThemes()
    {
        $themes = ThemeModel::getThemes();

        $this->assertIsArray($themes);

        foreach ($themes as $theme) {
            $this->assertTrue(file_exists(public_path() . '/css/themes/' . $theme));
        }
    }

    public function testGetThemeToInclude()
    {
        $theme = ThemeModel::getThemeToInclude();
        $this->assertEquals(asset('css/app.css'), $theme);
    }
}
