<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreateComments extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function($table) {
            $table->bigIncrements('id');
            $table->text('author');
            $table->bigInteger('customers_id')->nullable()->index();
            $table->longText('txt');
            $table->tinyInteger('rate')->defult(0)->index();
            $table->tinyInteger('vote_y')->nullable()->index();
            $table->tinyInteger('vote_n')->nullable()->index();
            $table->tinyInteger('hidden')->nullable();
            $table->bigInteger('elements_id')->nullable()->index();
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
        Schema::drop('comments');
    }

}
