<?php

/*
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

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
use App\PostModel;
use App\ThreadModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadModelTest extends TestCase
{
    public function testAdd()
    {
        try {
            $text = md5(random_bytes(55));

            ThreadModel::add(env('TEST_USERID'), env('TEST_ACTIVITYID'), $text);

            $result = ThreadModel::where('userId', '=', env('TEST_USERID'))->where('activityId', '=', env('TEST_ACTIVITYID'))->where('text', '=', $text)->first();
            $this->assertIsObject($result);
            $this->assertEquals($text, $result->text);

            return $result->id;
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    /**
     * @depends testAdd
     */
    public function testEdit($id)
    {
        try {
            $text = md5(random_bytes(55));

            ThreadModel::edit($id, $text, env('TEST_USERID'));

            $result = ThreadModel::where('id', '=', $id)->first();
            $this->assertEquals($text, $result->text);

            return $id;
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    /**
     * @depends testEdit
     */
    public function testGetFromActivity($id)
    {
        try {
            $result = ThreadModel::getFromActivity(env('TEST_ACTIVITYID'), env('TEST_FETCHLIMIT'));
            $this->assertIsObject($result);
            foreach ($result as $item) {
                $this->assertIsObject($item);
                $this->assertTrue(isset($item->text));
            }

            return $id;
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }

    /**
     * @param $id
     * @depends testGetFromActivity
     */
    public function testRemove($id)
    {
        try {
            ThreadModel::remove($id, env('TEST_USERID'));

            $result = ThreadModel::where('id', '=', $id)->count();
            $this->assertEquals(0, $result);
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }
}
