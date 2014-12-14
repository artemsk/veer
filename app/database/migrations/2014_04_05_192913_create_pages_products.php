<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesProducts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages_products', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('pages_id')->index();
                    $table->bigInteger('products_id')->index();
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
		Schema::drop('pages_products');
	}

}
