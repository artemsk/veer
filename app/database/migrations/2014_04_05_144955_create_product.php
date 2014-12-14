<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProduct extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function($table) {
                   $table->bigIncrements('id');
                   $table->tinyInteger('grp')->default(0)->index();
                   $table->string('status',5)->index();
                   $table->bigInteger('qty');
                   $table->bigInteger('weight');
                   $table->text('title');
                   $table->longText('descr');
                   $table->string('production_code',155);
                   $table->longText('grp_ids');
                   $table->timestamp('to_show');
                   $table->decimal('currency',15,2);
                   $table->decimal('price',15,2);
                   $table->decimal('price_sales',15,2);
                   $table->decimal('price_opt',15,2);
                   $table->decimal('price_base',15,2); // закупочная цена
                   $table->timestamp('price_sales_on');
                   $table->timestamp('price_sales_off');
                   $table->decimal('score',15,2);
                   $table->tinyInteger('star')->default(0)->index();
                   $table->bigInteger('ordered');
                   $table->bigInteger('viewed');
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
		Schema::drop('products');
	}

}
