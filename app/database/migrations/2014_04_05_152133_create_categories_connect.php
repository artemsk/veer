<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesConnect extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories_connect', function($table) {
                    $table->bigIncrements('id');
                    $table->bigInteger('categories_id')->index();
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
		Schema::drop('categories_connect');
	}

}
