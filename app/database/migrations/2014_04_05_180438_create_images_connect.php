<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesConnect extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::create('images_connect', function($table) {
                    $table->bigIncrements('id');
                    $table->bigInteger('images_id')->index();
                    $table->bigInteger('elements_id')->index();
                    $table->string('elements_type',255)->index();
                    $table->timestamps();
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
		Schema::drop('images_connect');
	}

}
