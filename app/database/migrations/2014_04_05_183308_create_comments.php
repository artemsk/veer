<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComments extends Migration {

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
                    $table->bigInteger('customers_id')->index();
                    $table->longText('txt');
                    $table->tinyInteger('rate')->index();
                    $table->tinyInteger('vote_y')->index();
                    $table->tinyInteger('vote_n')->index();
                    $table->tinyInteger('hidden')->default(0);
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
		Schema::drop('comments');
	}

}
