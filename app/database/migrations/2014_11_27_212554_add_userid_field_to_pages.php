<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUseridFieldToPages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pages', function(Blueprint $table) {
                   $table->bigInteger('users_id')->index()->nullable()->after('url');
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pages', function($table) {
                   $table->dropColumn('users_id');
                });
	}

}

// @reminder: author of page can be attribute pair: author->who