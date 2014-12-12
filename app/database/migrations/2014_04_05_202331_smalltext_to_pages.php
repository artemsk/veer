<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SmalltextToPages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pages', function($table) {
                    $table->longText('small_txt')->after('title')->default('');
                    $table->tinyInteger('show_small')->after('small_txt')->default(1);
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
                    $table->dropColumn('small_txt','show_small');
                });
	}

}
