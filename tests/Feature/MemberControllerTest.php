<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->post('/login', [
            'email' => env('TEST_USEREMAIL'),
            'password' => env('TEST_USERPW')
        ]);
    }

    public function testShow()
    {
        $response = $this->get('/user/' . env('TEST_USERID'));

        $response->assertStatus(200);
        $response->assertViewIs('member.profile');
    }

    public function testViewSettings()
    {
        $response = $this->get('/settings');

        $response->assertStatus(200);
        $response->assertViewIs('member.settings');
    }

    public function testSave()
    {
        $bio = md5(random_bytes(55));

        $response = $this->post('/settings', ['bio' => $bio]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function testReport()
    {
        $response = $this->get('/user/' . env('TEST_USERID2') . '/report');

        $response->assertStatus(302);
    }
}
