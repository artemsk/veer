<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StatusTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create("orders_status",function($table) {
                    $table->bigIncrements('id');
                    $table->string('name',128);
                    $table->integer('manual_order')->index();
                    $table->string('color',10);
                    $table->boolean('flag_first')->default(false)->index();
                    $table->boolean('flag_unreg')->default(false)->index();
                    $table->boolean('flag_error')->default(false)->index();
                    $table->boolean('flag_payment')->default(false)->index();
                    $table->boolean('flag_delivery')->default(false)->index();
                    $table->boolean('flag_close')->default(false)->index();
                    $table->boolean('secret')->default(false)->index();
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
		Schema::drop('orders_status');
	}

}
