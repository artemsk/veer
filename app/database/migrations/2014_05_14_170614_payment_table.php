<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("orders_payment",function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('sites_id')->index();
                    $table->string('name',96)->index();
                    $table->boolean('enable')->default(true)->index();
                    $table->string('type',10)->index();
                    $table->string('paying_time',12)->index();
                    $table->decimal('commission',12,2)->default(0);
                    $table->boolean('discount_enable')->default(false)->index();
                    $table->longText('discount_conditions');
                    $table->decimal('discount_price',5,2);
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
		Schema::drop('orders_payment');
	}

}
