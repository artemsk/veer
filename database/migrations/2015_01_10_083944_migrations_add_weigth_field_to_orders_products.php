<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsAddWeigthFieldToOrdersProducts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders_products', function(Blueprint $table) {
            $table->bigInteger('weight')->after('price')->default(0);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('orders_products', function($table) {
            $table->dropColumn('weight');
        });
	}

}
