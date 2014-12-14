<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdminUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_admin', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('users_id')->index()->default(false);
                    $table->string('sess_id',128)->index()->default('');
                    $table->string('description',255)->default('');
                    $table->bigInteger('logons_count')->default(0);
                    $table->timestamp('last_logon')->nullable();
                    $table->timestamp('last_active')->nullable();
                    $table->longText('ips')->default('');
                    $table->longText('access_parameters')->default('');
                    $table->longText('sites_watch')->default('');
                    $table->boolean('banned')->default(false)->index();
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
		Schema::drop('users_admin');
	}

}
