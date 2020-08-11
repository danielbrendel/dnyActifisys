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

use App\CaptchaModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->post('/login', [
            'email' => env('TEST_USEREMAIL'),
            'password' => env('TEST_USERPW')
        ]);
    }

    public function testIndex()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testImprint()
    {
        $response = $this->get('/imprint');

        $response->assertStatus(200);
        $response->assertViewIs('home.imprint');
    }

    public function testTos()
    {
        $response = $this->get('/tos');

        $response->assertStatus(200);
        $response->assertViewIs('home.tos');
    }

    public function testFaq()
    {
        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertViewIs('home.faq');
    }

    public function testNews()
    {
        $response = $this->get('/news');

        $response->assertStatus(200);
        $response->assertViewIs('home.news');
    }

    public function testViewContact()
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
        $response->assertViewIs('home.contact');
    }

    public function testContact()
    {
        $response = $this->post('/contact', [
            'name' => md5(random_bytes(55)),
            'email' => md5(random_bytes(55)) . '@domain.tld',
            'subject' => md5(random_bytes(55)),
            'content' => md5(random_bytes(55)),
            'captcha' => CaptchaModel::querySum(session()->getId())
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    public function testLogout()
    {
        $response = $this->get('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function testRegister()
    {
        $name = md5(random_bytes(55));
        $password = md5(random_bytes(55));

        $response = $this->post('/register', [
            'name' => $name,
            'email' => $name . '@domain.tld',
            'password' => $password,
            'password_confirmation' => $password,
            'captcha' => CaptchaModel::querySum(session()->getId())
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
    }
}
