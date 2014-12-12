<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTextUrls extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('products', function ($table) {
                   $table->string('url',255)->after('id')->default('')->index();
                });
                
		Schema::table('pages', function ($table) {
                   $table->string('url',255)->after('id')->default('')->index();
                });                
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('products', function ($table) {
                   $table->dropColumn('url');
                });
                
		Schema::table('pages', function ($table) {
                   $table->dropColumn('url');
                });
	}

}
