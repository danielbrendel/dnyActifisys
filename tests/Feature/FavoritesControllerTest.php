<?php

/*
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->post('/login', [
            'email' => env('TEST_USEREMAIL'),
            'password' => env('TEST_USERPW')
        ]);
    }

    public function testAdd()
    {
        $response = $this->get('/user/' . env('TEST_USERID2') . '/fav/add');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    /**
     * @depends testAdd
     */
    public function testFetch()
    {
        $response = $this->get('/favorites/fetch');

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals(200, $content->code);
    }

    /**
     * @depends testFetch
     */
    public function testRemove()
    {
        $response = $this->get('/user/' . env('TEST_USERID2') . '/fav/remove');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
}
