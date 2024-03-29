<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePushModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userId');
            $table->string('shortMsg');
            $table->string('longMsg');
            $table->string('type');
            $table->boolean('seen')->default(false);
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
        Schema::dropIfExists('push_models');
    }
}
