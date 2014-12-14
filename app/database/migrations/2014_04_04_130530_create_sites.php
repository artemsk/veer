<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSites extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sites', function($table) {
                    $table->bigIncrements('id')->index();
                    $table->longText('url');
                    $table->bigInteger('parent_id')->default('0')->index();
                    $table->integer('manual_sort')->default('0');
                    $table->tinyInteger('redirect_on')->default('0');
                    $table->longText('redirect_url')->default('');
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
		Schema::drop('sites');                
	}

}
