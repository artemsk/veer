<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreateSearchPivot extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches_connect', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('searches_id')->nullable()->index();
            $table->bigInteger('users_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('searches_connect');
    }

}
