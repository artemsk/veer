<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttributeFieldToLists extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users_lists', function(Blueprint $table) {
                   $table->longtext('attributes')->after('quantity')->default('');
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users_lists', function($table) {
                   $table->dropColumn('attributes');
                });
	}

}
