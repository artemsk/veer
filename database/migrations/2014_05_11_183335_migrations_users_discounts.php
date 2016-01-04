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
            $table->bigInteger('sites_id')->nullable()->index();
            $table->string('secret_code', 128);
            $table->decimal('discount', 5, 2)->nullable();
            $table->tinyInteger('expires')->nullable();
            $table->timestamp('expiration_day')->nullable();
            $table->tinyInteger('expiration_times')->nullable();
            $table->string('status', 10)->index();
            $table->bigInteger('users_id')->nullable()->index();
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
