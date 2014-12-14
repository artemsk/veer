<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages_pivot', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('parent_id')->index();
                    $table->bigInteger('child_id')->index();
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
		Schema::drop('pages_pivot');
	}

}
