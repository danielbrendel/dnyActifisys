<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2022 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

namespace Tests\Feature;

use App\ActivityModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->post('/login', [
            'email' => env('TEST_USEREMAIL'),
            'password' => env('TEST_USERPW')
        ]);
    }

   public function testCreate()
   {
       $attr = [
           'title' => md5(random_bytes(55)),
           'description' => md5(random_bytes(55)),
           'location' => 'Testlocation',
           'limit' => '0',
           'date_of_activity' => '15.12.2100',
           'time_of_activity' => '00:51'
       ];

       $response = $this->post('/activity/create', $attr);

       $response->assertStatus(302);

       return $attr['title'];
   }

    /**
     * @depends testCreate
     */
   public function testEdit($title)
   {
       $item = ActivityModel::where('title', '=', $title)->first();

       $attr = [
           'activityId' => $item->id,
           'title' => md5(random_bytes(55)),
           'description' => md5(random_bytes(55)),
           'location' => 'Testlocation',
           'limit' => '0',
           'date_of_activity' => '15.12.2105',
           'time_of_activity' => '00:32'
       ];

       $response = $this->post('/activity/edit', $attr);

       $response->assertStatus(302);
   }

   public function testShow()
   {
       $response = $this->get('/activity/' . env('TEST_ACTIVITYID'));
       $response->assertStatus(200);
       $response->assertViewIs('activity.show');
   }

   public function testFetch()
   {
       $response = $this->get('/activity/fetch');
       $response->assertStatus(200);
       $content = json_decode($response->getContent());
       $this->assertEquals(200, $content->code);
       $this->assertTrue(isset($content->data));
   }

   public function testFetchThread()
   {
       $response = $this->get('/activity/' . env('TEST_ACTIVITYID') . '/thread');
       $response->assertStatus(200);
       $content = json_decode($response->getContent());
       $this->assertEquals(200, $content->code);
       $this->assertTrue(isset($content->data));
   }

    public function testFetchSubThread()
    {
        $response = $this->get('/thread/' . env('TEST_THREADID') . '/sub');
        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals(200, $content->code);
        $this->assertTrue(isset($content->data));
    }

    public function testFetchUserActivities()
    {
        $response = $this->get('/activity/user/' . env('TEST_USERID'));
        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEquals(200, $content->code);
        $this->assertTrue(isset($content->data));
    }

    public function testAddThread()
    {
        $response = $this->post('/activity/' . env('TEST_ACTIVITYID') . '/thread/add', [
           'message' => md5(random_bytes(55))
        ]);

        $response->assertStatus(302);
    }

    public function testReplyThread()
    {
        $response = $this->post('/thread/' . env('TEST_THREADID') . '/reply', [
            'message' => md5(random_bytes(55))
        ]);

        $response->assertStatus(200);
    }

    public function testParticipantAdd()
    {
        $response = $this->get('/activity/' . env('TEST_ACTIVITYID') . '/participant/add');
        $response->assertStatus(302);
    }

    /**
     * @depends testParticipantAdd
     */
    public function testParticipantRemove()
    {
        $response = $this->get('/activity/' . env('TEST_ACTIVITYID') . '/participant/remove');
        $response->assertStatus(302);
    }

    public function testPotentialAdd()
    {
        $response = $this->get('/activity/' . env('TEST_ACTIVITYID') . '/interested/add');
        $response->assertStatus(302);
    }

    /**
     * @depends testPotentialAdd
     */
    public function testPotentialRemove()
    {
        $response = $this->get('/activity/' . env('TEST_ACTIVITYID') . '/interested/remove');
        $response->assertStatus(302);
    }

    public function testReportActivity()
    {
        $response = $this->get('/activity/' . env('TEST_ACTIVITYID') . '/report');
        $response->assertStatus(302);
    }

    public function testCancelActivity()
    {
        $response = $this->post('/activity/' . env('TEST_ACTIVITYID') . '/cancel', [
            'reason' => md5(random_bytes(55))
        ]);
        $response->assertStatus(302);
    }

    public function testLockActivity()
    {
        $response = $this->get('/activity/' . env('TEST_ACTIVITYID') . '/lock');
        $response->assertStatus(302);
    }

    public function testEditComment()
    {
        $response = $this->post('/comment/' . env('TEST_THREADID') . '/edit', [
           'message' => md5(random_bytes(55))
        ]);
        $response->assertStatus(302);
    }

    public function testReportComment()
    {
        $response = $this->get('/comment/' . env('TEST_THREADID') . '/report');
        $response->assertStatus(302);
    }

    public function testLockComment()
    {
        $response = $this->get('/comment/' . env('TEST_THREADID') . '/lock');
        $response->assertStatus(302);
    }
}
