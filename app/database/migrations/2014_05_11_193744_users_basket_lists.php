<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersBasketLists extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_lists', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('sites_id')->index();
                    $table->bigInteger('users_id')->index(); 
                    $table->string('session_id',128);
                    $table->string('name',255);
                    $table->bigInteger('elements_id')->index();
                    $table->string('elements_type',255)->index();
                    $table->bigInteger('quantity');         
                    $table->nullableTimestamps();           
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_lists');
	}

}
