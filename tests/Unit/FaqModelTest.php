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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqModelTest extends TestCase
{
    public function testGetAll()
    {
        try {
            $result = FaqModel::getAll();
            $this->assertIsObject($result);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
