<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsChangeTypeFieldInUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('type');
        });

        Schema::table('users', function($table) {
            $table->integer('roles_id')->after('password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('roles_id');
        });

        Schema::table('users', function($table) {
            $table->string('type', 15)->nullable()->after('password');
        });
    }

}
