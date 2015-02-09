<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsRenameColumnsInJobs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('jobs', function($table) {
			$table->renameColumn('times', 'attempts');
			$table->renameColumn('scheduled_at', 'available_at');
			$table->string('queue');
			$table->tinyInteger('reserved')->unsigned();
			$table->unsignedInteger('reserved_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('jobs', function($table) {
			$table->renameColumn('attempts', 'times');
			$table->renameColumn('available_at', 'scheduled_at');
			$table->dropColumn('queue');
			$table->dropColumn('reserved');
			$table->dropColumn('reserved_at');
		});
	}

}
