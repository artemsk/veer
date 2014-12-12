<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeFieldToModulesTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders_shipping', function ($table) {
                   $table->string('func_name',128)->after('address')->default('');
                });
                
		Schema::table('orders_payment', function ($table) {
                   $table->string('func_name',128)->after('discount_price')->default('');
                });                
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('orders_shipping', function ($table) {
                   $table->dropColumn('func_name');
                });
                
                Schema::table('orders_payment', function ($table) {
                   $table->dropColumn('func_name');
                });
	}

}
