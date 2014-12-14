<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DownloadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('downloads', function ($table) {
                   $table->bigIncrements('id');
                   $table->tinyInteger('original');
                   $table->string('fname',255);
                   $table->string('secret',255);
                   $table->tinyInteger('expires');
                   $table->timestamp('expiration_day');
                   $table->tinyInteger('expiration_times');
                   $table->bigInteger('downloads')->default(0);
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
		Schema::drop('downloads');
	}

}
