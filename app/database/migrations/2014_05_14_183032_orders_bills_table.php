<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersBillsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders_bills', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('orders_id')->index();                
                    $table->bigInteger('users_id')->index();
                    $table->bigInteger('status_id')->index();
                    $table->string('payment_method',128)->index();
                    $table->integer('payment_method_id')->nullable()->index();
                    $table->string('link',128);
                    $table->longText('content');  
                    $table->decimal('price',12,2);
                    $table->boolean('sent')->default(false);
                    $table->boolean('viewed')->default(false);
                    $table->boolean('paid')->default(false);
                    $table->boolean('canceled')->default(false);
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
		Schema::drop('orders_bills');
	}

}
