<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2021 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cookie_consent', 512);
            $table->string('home_bg', 512);
            $table->float('home_bg_alpha')->default(0.5);
            $table->text('about');
            $table->text('imprint');
            $table->text('tos');
            $table->string('reg_info');
            $table->text('project_name_formatted');
            $table->string('default_theme')->default('_default');
            $table->string('headline_top');
            $table->string('headline_sub');
            $table->text('head_code')->default('');
            $table->text('adcode')->default('');
            $table->string('newsletter_token')->nullable();
            $table->string('newsletter_subject')->nullable();
            $table->text('newsletter_content')->nullable();
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
        Schema::dropIfExists('app_settings');
    }
}
