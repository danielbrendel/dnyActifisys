<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

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
use App\VerifyModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyModelTest extends TestCase
{
    public function testVerifyStatus()
    {
        VerifyModel::verifyStatus(env('TEST_USERID'), VerifyModel::STATE_INPROGRESS);
        $status = VerifyModel::getState(env('TEST_USERID'));
        $this->assertEquals(VerifyModel::STATE_INPROGRESS, $status);

        VerifyModel::verifyStatus(env('TEST_USERID'), VerifyModel::STATE_VERIFIED);
        $status = VerifyModel::getState(env('TEST_USERID'));
        $this->assertEquals(VerifyModel::STATE_VERIFIED, $status);

        VerifyModel::verifyStatus(env('TEST_USERID'), VerifyModel::STATE_DECLINED);
        $status = VerifyModel::getState(env('TEST_USERID'));
        $this->assertEquals(VerifyModel::STATE_DECLINED, $status);
    }
}
