<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeFieldInUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table) {
                    $table->dropColumn('type');
                    $table->integer('roles_id')->after('password')->default(0);                    
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table) {
                    $table->dropColumn('roles_id');
                    $table->string('type',15)->after('password');  
                });
	}

}
