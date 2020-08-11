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

class MessageControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->post('/login', [
            'email' => env('TEST_USEREMAIL'),
            'password' => env('TEST_USERPW')
        ]);
    }

    public function testList()
    {
        $response = $this->get('/messages');

        $response->assertStatus(200);
        $response->assertViewIs('message.list');
    }

    public function testFetchList()
    {
        $response = $this->get('/messages/list');

        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals(200, $content->code);
        $this->assertTrue(isset($content->data));
        $this->assertTrue(isset($content->min));
        $this->assertTrue(isset($content->max));
    }

    public function testShow()
    {
        $response = $this->get('/messages/show/' . env('TEST_MESSAGEID'));

        $response->assertStatus(200);
        $response->assertViewIs('message.show');
    }

    public function testSend()
    {
        $subject = md5(random_bytes(55));
        $text = md5(random_bytes(55));

        $response = $this->post('/messages/send', [
            'userId' => env('TEST_USERID2'),
            'subject' => $subject,
            'text' => $text
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
    }
}
