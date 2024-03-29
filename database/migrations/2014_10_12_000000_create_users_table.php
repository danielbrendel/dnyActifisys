<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('slug', 1024);
            $table->string('avatar')->default('default.png');
            $table->string('password_reset')->nullable();
            $table->string('account_confirm');
            $table->string('device_token', 1024)->default(''); //Only for mobile devices
            $table->boolean('deactivated')->default(false);
            $table->dateTime('birthday')->useCurrent();
            $table->integer('gender')->default(0); //0 = unspecified, 1 = male, 2 = female, 3 = diverse
            $table->string('bio', 1024)->default('');
            $table->string('location')->default('');
            $table->boolean('admin')->default(false);
            $table->boolean('maintainer')->default(false);
            $table->boolean('pro')->default(false);
            $table->boolean('public_profile')->default(false);
            $table->boolean('allow_messages')->default(true);
            $table->boolean('newsletter')->default(true);
            $table->string('newsletter_token')->default('');
            $table->boolean('email_on_message')->default(true);
            $table->boolean('email_on_fav_created')->default(true);
            $table->boolean('email_on_participated')->default(true);
            $table->boolean('email_on_comment')->default(true);
            $table->boolean('email_on_act_canceled')->default(true);
            $table->boolean('email_on_act_upcoming')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
