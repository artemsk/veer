<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDownloadableFieldToPrds extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::table('products', function ($table) {
                   $table->tinyInteger('download')->after('star')->default(0)->index();
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
                   $table->dropColumn('download');
                });
	}

}
