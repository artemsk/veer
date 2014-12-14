<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('searches', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->text('q');
			$table->bigInteger('times');
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
		Schema::drop('searches');
	}

}
