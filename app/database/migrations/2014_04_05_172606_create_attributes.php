<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attributes', function($table) {
                    $table->bigIncrements('id');
                    $table->string('type',10)->index();
                    $table->text('name');
                    $table->longText('val');
                    $table->longText('descr');                   
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
		Schema::drop('attributes');
	}

}
