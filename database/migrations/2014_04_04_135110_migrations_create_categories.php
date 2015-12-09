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
            $table->bigInteger('parent_id')->default(0)->index();
            $table->bigInteger('sites_id')->default(0)->index();
            $table->integer('manual_sort')->default(0)->index();
            $table->bigInteger('views')->default(0);
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
