<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ShippingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("orders_shipping",function($table) {
                    $table->bigIncrements('id')->index();
                    $table->string('name',96)->index();
                    $table->boolean('enable')->default(true)->index();
                    $table->string('delivery_type',24)->index();
                    $table->string('payment_type',24)->index();
                    $table->decimal('price',12,2);
                    $table->longText('discount_conditions');
                    $table->decimal('discount_price',5,2);
                    $table->longText('address');
                    $table->longText('other_options');                    
                    $table->integer('manual_order')->index();
                    $table->nullableTimestamps();  
                    $table->softDeletes();                
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders_shipping');
	}

}
