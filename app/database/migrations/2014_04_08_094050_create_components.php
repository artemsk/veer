<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComponents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('components', function ($table) {
                   $table->bigIncrements('id');
                   $table->string('route_name',255)->index();
                   $table->string('components_type',24)->index();
                   $table->string('components_src',255)->index();
                   $table->bigInteger('sites_id')->index();
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
		Schema::drop('components');
	}

}
