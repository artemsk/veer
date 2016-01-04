<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreatePage extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function($table) {
            $table->bigIncrements('id');
            $table->text('title');
            $table->longText('txt');
            $table->tinyInteger('show_comments')->default(1);
            $table->tinyInteger('show_title')->default(1);
            $table->tinyInteger('show_date')->default(1);
            $table->tinyInteger('in_list')->default(1)->index();
            $table->tinyInteger('in_last')->default(1)->index();
            $table->tinyInteger('in_news')->default(1)->index();
            $table->bigInteger('manual_order')->nullable()->index();
            $table->bigInteger('views')->nullable();
            $table->tinyInteger('hidden')->nullable()->index();
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
        Schema::drop('pages');
    }

}
