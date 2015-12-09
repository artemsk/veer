<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsPaymentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("orders_payment", function($table) {
            $table->bigIncrements('id')->index();
            $table->bigInteger('sites_id')->default(0)->index();
            $table->string('name', 96)->index();
            $table->boolean('enable')->default(true)->index();
            $table->string('type', 10)->index();
            $table->string('paying_time', 12)->index();
            $table->decimal('commission', 12, 2)->default(0);
            $table->boolean('discount_enable')->default(false)->index();
            $table->longText('discount_conditions');
            $table->decimal('discount_price', 5, 2)->nullable();
            $table->longText('other_options');
            $table->integer('manual_order')->default(0)->index();
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
        Schema::drop('orders_payment');
    }

}
