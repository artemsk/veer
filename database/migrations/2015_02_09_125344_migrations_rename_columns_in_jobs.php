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
        });

        Schema::table('jobs', function($table) {
            $table->renameColumn('scheduled_at', 'available_at');
        });
        
		Schema::table('jobs', function($table) {		
			$table->string('queue')->nullable();
			$table->tinyInteger('reserved')->nullable()->unsigned();
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
        });

        Schema::table('jobs', function($table) {
            $table->renameColumn('available_at', 'scheduled_at');
        });

        Schema::table('jobs', function($table) {
			$table->dropColumn('queue');
        });

        Schema::table('jobs', function($table) {
			$table->dropColumn('reserved');
        });
        
		Schema::table('jobs', function($table) {
			$table->dropColumn('reserved_at');
		});
	}

}
