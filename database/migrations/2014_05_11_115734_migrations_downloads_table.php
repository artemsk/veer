<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsDownloadsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downloads', function ($table) {
            $table->bigIncrements('id');
            $table->tinyInteger('original')->default(0);
            $table->string('fname', 255);
            $table->string('secret', 255);
            $table->tinyInteger('expires')->nullable();
            $table->timestamp('expiration_day')->nullable();
            $table->tinyInteger('expiration_times')->nullable();
            $table->bigInteger('downloads')->default(0);
            $table->bigInteger('elements_id')->default(0)->index();
            $table->string('elements_type', 255)->default('')->index();
            $table->nullableTimestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('downloads');
    }

}
