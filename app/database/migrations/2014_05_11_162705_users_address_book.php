<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersAddressBook extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_book', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('users_id')->index();
                    $table->tinyInteger('office_address')->default(0);                    
                    $table->string('name',255);
                    $table->string('country',128);
                    $table->string('region',255);
                    $table->string('city',128);
                    $table->string('postcode',64);
                    $table->longText('address');
                    $table->string('nearby_station',128);
                    $table->string('b_inn',255);
                    $table->string('b_account',255);
                    $table->string('b_bank',255);
                    $table->string('b_corr',255);
                    $table->string('b_bik',255);
                    $table->longText('b_others');
                    $table->tinyInteger('primary')->default(0)->index();                    
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
		Schema::drop('users_book');
	}

}
