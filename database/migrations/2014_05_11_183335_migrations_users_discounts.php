<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsUsersDiscounts extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_discounts', function($table) {
            $table->bigIncrements('id')->index();
            $table->bigInteger('sites_id')->default(0)->index();
            $table->string('secret_code', 128);
            $table->decimal('discount', 5, 2)->nullable();
            $table->tinyInteger('expires')->default(0);
            $table->timestamp('expiration_day')->nullable();
            $table->tinyInteger('expiration_times')->nullable();
            $table->string('status', 10)->index();
            $table->bigInteger('users_id')->default(0)->index();
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
        Schema::drop('users_discounts');
    }

}
