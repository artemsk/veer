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
                    $table->bigInteger('users_id')->index();
                    $table->string('sess_id',128)->index();
                    $table->string('description',255);
                    $table->bigInteger('logons_count')->default(0);
                    $table->timestamp('last_logon');
                    $table->timestamp('last_active');
                    $table->longText('ips');
                    $table->longText('access_parameters');
                    $table->longText('sites_watch');
                    $table->boolean('banned')->default(false)->index();
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
		Schema::drop('users_admin');
	}

}
