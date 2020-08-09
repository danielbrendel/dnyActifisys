<?php

/*
    ComAct (dnyComAct) developed by Daniel Brendel

    (C) 2019 - 2020 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_models', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description');
            $table->string('tags', 1024)->default('');
            $table->dateTime('date_of_activity');
            $table->string('location');
            $table->integer('limit')->unsigned()->default(0); //0 = unlimited, >0 = limited
            $table->integer('only_gender')->unsigned()->default(0); //0 = all, 1 = only male, 2 = only female, 3 = only diverse
            $table->integer('owner');
            $table->integer('category')->default(0);
            $table->boolean('locked')->default(false);
            $table->boolean('canceled')->default(false);
            $table->string('cancelReason')->default('');
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
        Schema::dropIfExists('activity_models');
    }
}
