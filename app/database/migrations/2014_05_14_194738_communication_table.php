<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CommunicationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('communications', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->bigInteger('sites_id')->index();                
                    $table->bigInteger('users_id')->index();
                    $table->string('sender',128);
                    $table->string('sender_phone',64);
                    $table->string('sender_email',255);
                    $table->longText('message');
                    $table->longText('recipients');
                    $table->string('theme',255);
                    $table->string('type',64);
                    $table->text('url');
                    $table->integer('elements_id')->index();
                    $table->string('elements_type',255)->index();
                    $table->boolean('email_notify')->default(false);
                    $table->bigInteger('views');
                    $table->boolean('intranet')->default(false);
                    $table->boolean('hidden')->default(false);
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
		Schema::drop('communications');
	}

}
