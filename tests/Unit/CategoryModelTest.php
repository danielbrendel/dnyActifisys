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
use App\CategoryModel;
use App\FavoritesModel;
use App\CaptchaModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    public function testEdit()
    {
        $attr = [
            'name' => md5(random_bytes(55)),
            'description' => md5(random_bytes(55))
        ];

        CategoryModel::edit(env('TEST_CATEGORYID'), $attr);

        $data = CategoryModel::where('id', '=', env('TEST_CATEGORYID'))->first();
        $this->assertEquals($attr['name'], $data->name);
        $this->assertEquals($attr['description'], $data->description);
    }

    public function testSetInactiveStatus()
    {
        CategoryModel::setInactiveStatus(env('TEST_CATEGORYID'), true);
        $item = CategoryModel::where('id', '=', env('TEST_CATEGORYID'))->first();
        $this->assertEquals(1, $item->inactive);

        CategoryModel::setInactiveStatus(env('TEST_CATEGORYID'), false);
        $item = CategoryModel::where('id', '=', env('TEST_CATEGORYID'))->first();
        $this->assertEquals(0, $item->inactive);
    }

    public function testFetch()
    {
        $result = CategoryModel::fetch();
        foreach($result as $item) {
            $this->assertTrue(isset($item->name));
            $this->assertTrue(isset($item->description));
            $this->assertTrue(isset($item->image));
            $this->assertTrue(isset($item->inactive));
        }
    }
}
