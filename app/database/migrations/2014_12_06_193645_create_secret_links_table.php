<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSecretLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('secrets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('secret',64)->index();
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
        Schema::drop('secrets');
	}

}
