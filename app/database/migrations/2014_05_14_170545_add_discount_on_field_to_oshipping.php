<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiscountOnFieldToOshipping extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders_shipping', function ($table) {
                   $table->boolean('discount_enable')->after('price')->default(false)->index();
                   $table->bigInteger('sites_id')->after('id')->default(0)->index();
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
                   $table->dropColumn('discount_enable');
                   $table->dropColumn('sites_id');
                });
	}

}
