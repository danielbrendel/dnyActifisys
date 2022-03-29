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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaptchaModelTest extends TestCase
{
    public function testCreateSum()
    {
        try {
            $result = CaptchaModel::createSum('TestCase');
            $this->assertIsArray($result);
            $this->assertCount(2, $result);

            return $result;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @depends testCreateSum
     */
    public function testQuerySum($created)
    {
        try {
            $result = CaptchaModel::querySum('TestCase');
            $this->assertEquals($result, $created[0] + $created[1]);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
