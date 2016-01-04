<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCustomersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function($table) {
            $table->bigIncrements('id')->index();
            $table->bigInteger('sites_id')->nullable()->index();
            $table->string('email', 255)->index();
            $table->string('password', 128);
            $table->string('type', 15)->index();
            $table->string('gender', 1)->index()->default('');
            $table->string('firstname', 128)->default('');
            $table->string('lastname', 128)->default('');
            $table->timestamp('birth')->nullable();
            $table->string('phone', 128)->index()->default(false);
            $table->bigInteger('logons_count')->nullable();
            $table->bigInteger('orders_count')->nullable();
            $table->tinyInteger('newsletter')->nullable();
            $table->tinyInteger('banned')->nullable();
            $table->tinyInteger('restrict_orders')->nullable();
            $table->rememberToken();
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
