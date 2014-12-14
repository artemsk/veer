<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders_products', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('orders_id')->index();
                    $table->boolean('product')->default(1)->index();                    
                    $table->bigInteger('products_id')->nullable();
                    $table->text('name');
                    $table->decimal('original_price',12,2)->nullable();
                    $table->decimal('price_per_one',12,2)->nullable();
                    $table->bigInteger('quantity')->nullable();
                    $table->decimal('price',12,2)->nullable();
                    $table->longText('attributes');
                    $table->longText('comments');                   
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
		Schema::drop('orders_products');
	}

}
