<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsUsersBasketLists extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_lists', function($table) {
            $table->bigIncrements('id')->index();
            $table->bigInteger('sites_id')->default(0)->index();
            $table->bigInteger('users_id')->default(0)->index();
            $table->string('session_id', 128);
            $table->string('name', 255);
            $table->bigInteger('elements_id')->default(0)->index();
            $table->string('elements_type', 255)->default('')->index();
            $table->bigInteger('quantity')->default(0);
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users_lists');
    }

}
