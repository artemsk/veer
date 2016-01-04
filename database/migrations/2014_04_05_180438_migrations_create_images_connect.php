<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreateImagesConnect extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images_connect', function($table) {
            $table->bigIncrements('id');
            $table->bigInteger('images_id')->nullable()->index();
            $table->bigInteger('elements_id')->nullable()->index();
            $table->string('elements_type', 255)->default('')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('images_connect');
    }

}
