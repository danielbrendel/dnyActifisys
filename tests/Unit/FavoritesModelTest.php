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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesModelTest extends TestCase
{
    public function testValidateEntityType()
    {
        FavoritesModel::validateEntityType('ENT_USER');
        $this->addToAssertionCount(1);

        $this->expectExceptionMessage('Invalid entity type: ENT_INVALID');
        FavoritesModel::validateEntityType('ENT_INVALID');
    }

    public function testAdd()
    {
        try {
            FavoritesModel::add(env('TEST_USERID'), env('TEST_USERID2'), 'ENT_USER');
            $result = FavoritesModel::where('userId', '=', env('TEST_USERID'))->where('entityId', '=', env('TEST_USERID2'))->count();
            $this->assertEquals($result, 1);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @depends testAdd
     */
    public function testHasUserFavorited()
    {
        try {
            $result = FavoritesModel::hasUserFavorited(env('TEST_USERID'), env('TEST_USERID2'), 'ENT_USER');
            $this->assertTrue($result);

            $result = FavoritesModel::hasUserFavorited(env('TEST_USERID_NONEXISTENT'), env('TEST_ENTITY_HASHTAG'), 'ENT_HASHTAG');
            $this->assertFalse($result);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @depends testHasUserFavorited
     */
    public function testGetForUser()
    {
        try {
            $result = FavoritesModel::getForUser(env('TEST_USERID'));
            $this->assertIsObject($result);
            $this->assertTrue(count($result) > 0);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @depends testGetForUser
     */
    public function testRemove()
    {
        try {
            FavoritesModel::remove(env('TEST_USERID'), env('TEST_USERID2'), 'ENT_USER');
            $result = FavoritesModel::where('userId', '=', env('TEST_USERID'))->where('entityId', '=', env('TEST_USERID2'))->count();
            $this->assertEquals($result, 0);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
