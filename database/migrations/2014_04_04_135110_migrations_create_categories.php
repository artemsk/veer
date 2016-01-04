<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreateCategories extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function($table) {
            $table->bigIncrements('id');
            $table->longText('title');
            $table->longText('description');
            $table->longText('remote_url');
            $table->bigInteger('parent_id')->nullable()->index();
            $table->bigInteger('sites_id')->nullable()->index();
            $table->integer('manual_sort')->nullable()->index();
            $table->bigInteger('views')->nullable();
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
        Schema::drop('categories');
    }

}
