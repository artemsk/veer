<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsAddPublicFieldCommunications extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('communications', function(Blueprint $table) {
            $table->boolean('public')->default(1)->after('views')->index();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('communications', function($table) {
            $table->dropColumn('username');
        });
	}

}
