<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_roles', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('sites_id')->index();
                    $table->string('role',128)->index();
                    $table->string('price_field',64)->index();
                    $table->decimal('discount',4,2)->default(0);
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
		Schema::drop('users_roles');
	}

}
