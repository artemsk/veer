<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteLastActiveFieldFromUseradmin extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users_admin', function($table) {
                   $table->dropColumn('last_active'); 
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users_admin', function($table) {
                   $table->timestamp('last_active')->after('last_logon')->nullable(); 
                });		
	}

}
