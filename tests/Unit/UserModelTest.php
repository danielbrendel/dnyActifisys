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
use App\FavoritesModel;
use App\CaptchaModel;
use App\FaqModel;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function testGet()
    {
        try {
            $result = User::get(env('TEST_USERID'));
            $this->assertIsObject($result);
            $this->assertEquals(env('TEST_USERID'), $result->id);
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    public function testIsAdmin()
    {
        try {
            $result = User::isAdmin(env('TEST_USERID'));
            $this->assertTrue($result);
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    public function testIsMaintainer()
    {
        try {
            $result = User::isMaintainer(env('TEST_USERID'));
            $this->assertTrue($result);
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    public function testGetByEmail()
    {
        try {
            $result = User::getByEmail(env('TEST_USEREMAIL'));
            $this->assertEquals(env('TEST_USEREMAIL'), $result->email);
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    public function testRegister()
    {
        $this->markTestSkipped();

        try {
            $_SESSION['PHPSESSID'] = 'TestCase';

            $captcha = CaptchaModel::createSum($_SESSION['PHPSESSID']);

            $attr = array(
                'name' => md5(random_bytes(55)),
                'email' => md5(random_bytes(55)) . '@domain.tld',
                'password' => 'password',
                'password_confirmation' => 'password',
                'captcha' => $captcha[0] + $captcha[1]
            );

            User::register($attr);

            return $attr['name'];
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    /**
     * @depends testRegister
     */
    public function testConfirm($name)
    {
        try {
            $hash = User::where('name', '=', $name)->first()->account_confirm;

            User::confirm($hash);

            $value = User::where('name', '=', $name)->where('account_confirm', '=', '_confirmed')->count();
            $this->assertEquals(1, $value);
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }
}
