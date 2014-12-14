<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('sites_id')->index();
                    $table->string('email',255)->index();
                    $table->string('password',128);
                    $table->string('type',15)->index();
                    $table->string('gender',1)->index()->default('');
                    $table->string('firstname',128)->default('');
                    $table->string('lastname',128)->default('');
                    $table->timestamp('birth')->nullable();
                    $table->string('phone',128)->index()->default(false);
                    $table->bigInteger('logons_count')->default(0);
                    $table->bigInteger('orders_count')->default(0);
                    $table->tinyInteger('newsletter')->default(0);
                    $table->tinyInteger('banned')->default(0);
                    $table->tinyInteger('restrict_orders')->default(0);
                    $table->string('remember_token',100)->nullable();
                    $table->nullableTimestamps();
                    $table->softDeletes();                    
                });
                
                Schema::table('comments', function($table) {
                    $table->renameColumn('customers_id', 'users_id');
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
                
                Schema::table('comments', function($table) {
                    $table->renameColumn('users_id', 'customers_id');
                });
	}

}
