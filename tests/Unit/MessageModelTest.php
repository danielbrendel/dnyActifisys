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
use App\MessageModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageModelTest extends TestCase
{
    public function testAdd()
    {
        try {
            $subject = md5(random_bytes(55));
            $message = md5(random_bytes(55));

            MessageModel::add(env('TEST_USERID'), env('TEST_USERID'), $subject, $message);
            MessageModel::add(env('TEST_USERID'), env('TEST_USERID'), $subject, $message);

            $result = MessageModel::where('userId', '=', env('TEST_USERID'))->where('senderId', '=', env('TEST_USERID'))->where('subject', '=', $subject)->where('message', '=', $message)->first();
            $this->assertIsObject($result);
            $this->assertTrue(isset($result->message));

            return $result->id;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testFetch()
    {
        try {
            $result = MessageModel::fetch(env('TEST_USERID'), env('TEST_FETCHLIMIT'), null);
            $this->assertIsObject($result);
            $this->assertTrue(count($result) > 0);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @depends testAdd
     */
    public function testGetMessageThread($id)
    {
        try {
            $result = MessageModel::getMessageThread($id);
            $this->assertIsArray($result);
            $this->assertTrue(isset($result['msg']));
            $this->assertTrue(isset($result['previous']));

            $this->assertTrue(isset($result['msg']->message));

            foreach ($result['previous'] as $item) {
                $this->assertIsObject($item);
                $this->assertTrue(isset($item->message));
            }
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
