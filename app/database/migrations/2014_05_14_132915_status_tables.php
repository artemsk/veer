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
                    $table->bigIncrements('id')->index();
                    $table->string('name',128);
                    $table->integer('manual_order')->index();
                    $table->string('color',10);
                    $table->boolean('flag_first')->defaul(false)->index();
                    $table->boolean('flag_unreg')->defaul(false)->index();
                    $table->boolean('flag_error')->defaul(false)->index();
                    $table->boolean('flag_payment')->defaul(false)->index();
                    $table->boolean('flag_delivery')->defaul(false)->index();
                    $table->boolean('flag_close')->defaul(false)->index();
                    $table->boolean('secret')->defaul(false)->index();
                    $table->timestamps();  
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
