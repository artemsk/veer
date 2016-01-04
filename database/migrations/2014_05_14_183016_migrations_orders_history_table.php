<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsOrdersHistoryTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_history', function($table) {
            $table->bigIncrements('id')->index();
            $table->bigInteger('orders_id')->default(0)->index();
            $table->bigInteger('status_id')->nullable();
            $table->string('name', 128);
            $table->longText('comments');
            $table->boolean('to_customer')->default(false);
            $table->longText('order_cache');
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
        Schema::drop('orders_history');
    }

}
