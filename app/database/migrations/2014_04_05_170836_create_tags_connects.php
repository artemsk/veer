<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsConnects extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::create('tags_connect', function($table) {
                    $table->bigIncrements('id');
                    $table->bigInteger('tags_id')->index();
                    $table->bigInteger('elements_id')->index();
                    $table->string('elements_type',255)->index();
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
		Schema::drop('tags_connect');
	}

}
