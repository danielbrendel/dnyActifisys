<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

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
use App\ReportModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportModelTest extends TestCase
{
    public function testAddReport()
    {
        try {
            ReportModel::addReport(env('TEST_USERID'), env('TEST_USERID2'), 'ENT_USER');

            $result = ReportModel::where('userId', '=', env('TEST_USERID'))->where('entityId', '=', env('TEST_USERID2'))->where('type', '=', 'ENT_USER')->first();
            $this->assertIsObject($result);
            $this->assertTrue(isset($result->entityId));
            $result->delete();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
