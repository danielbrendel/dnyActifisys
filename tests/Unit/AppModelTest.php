<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature\Models;

use App\AppModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppModelTest extends TestCase
{
    public function testGetCookieConsentText()
    {
        $result = AppModel::getCookieConsentText();
        $this->assertIsString($result);
    }

    public function testGetAboutContent()
    {
        $result = AppModel::getAboutContent();
        $this->assertIsString($result);
    }

    public function testGetTermsOfService()
    {
        $result = AppModel::getTermsOfService();
        $this->assertIsString($result);
    }

    public function testGetImprint()
    {
        $result = AppModel::getImprint();
        $this->assertIsString($result);
    }

    public function testGetRegInfo()
    {
        $result = AppModel::getRegInfo();
        $this->assertIsString($result);
    }

    public function testGetImageType()
    {
        $result = AppModel::getImageType(public_path() . '/gfx/avatars/default.png');
        $this->assertEquals(IMAGETYPE_PNG, $result);

        $result = AppModel::getImageType('does not exist');
        $this->assertEquals(null, $result);
    }

}
