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
use App\PushModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushModelTest extends TestCase
{
    public function testAddNotification()
    {
        try {
            $shortMsg = md5(random_bytes(55));
            $longMsg = md5(random_bytes(55));

            PushModel::addNotification($shortMsg, $longMsg, 'PUSH_COMMENTED', env('TEST_USERID'));

            $result = PushModel::where('shortMsg', '=', $shortMsg)->where('longMsg', '=', $longMsg)->where('type', '=', 'PUSH_COMMENTED')->where('userId', '=', env('TEST_USERID'))->count();
            $this->assertEquals(1, $result);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testGetUnseenNotifications()
    {
        try {
            $result = PushModel::getUnseenNotifications(env('TEST_USERID'));
            $this->assertTrue(count($result) > 0);
            foreach ($result as $item) {
                $this->assertIsObject($item);
                $this->assertTrue(isset($item->shortMsg));
            }
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testGetNotifications()
    {
        try {
            $result = PushModel::getNotifications(env('TEST_USERID'), env('TEST_FETCHLIMIT'));
            $this->assertTrue(count($result) > 0);
            foreach ($result as $item) {
                $this->assertIsObject($item);
                $this->assertTrue(isset($item->shortMsg));
            }
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
