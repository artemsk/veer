<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreateConfiguration extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('configuration', function($table) {
            $table->bigIncrements('id');
            $table->bigInteger('sites_id')->default(0)->index();
            $table->string('conf_key', 255)->index();
            $table->longText('conf_val');
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
		Schema::drop('configuration');
	}

}
