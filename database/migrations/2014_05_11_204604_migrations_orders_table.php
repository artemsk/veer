<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsOrdersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function($table) {
            $table->bigIncrements('id')->index();
            $table->string('hash', 72)->index();
            $table->bigInteger('sites_id')->nullable()->index();
            $table->integer('cluster')->nullable()->index();
            $table->bigInteger('cluster_oid')->nullable()->index();
            $table->string('type', 10)->index();
            $table->bigInteger('users_id')->index()->nullable();
            $table->string('user_type', 128);
            $table->string('name', 255);
            $table->string('email', 255)->index();
            $table->string('phone', 128)->index();
            $table->string('delivery_method', 128)->index();
            $table->integer('delivery_method_id')->nullable()->index();
            $table->bigInteger('userbook_id')->nullable();
            $table->string('country', 96);
            $table->string('city', 96)->index();
            $table->longText('address');
            $table->timestamp('delivery_plan')->nullable();
            $table->timestamp('delivery_real')->nullable();
            $table->boolean('delivery_hold')->nullable()->index();
            $table->decimal('delivery_price', 12, 2)->nullable();
            $table->boolean('delivery_free')->nullable()->index();
            $table->decimal('content_price', 12, 2)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('used_discount', 12, 2)->nullable();
            $table->bigInteger('userdiscount_id')->nullable();
            $table->boolean('free')->nullable()->index();
            $table->string('payment_method', 128)->index();
            $table->integer('payment_method_id')->nullable()->index();
            $table->boolean('payment_hold')->nullable()->index();
            $table->boolean('payment_done')->nullable();
            $table->string('status_id')->index();
            $table->boolean('close')->nullable()->index();
            $table->timestamp('close_time')->nullable();
            $table->decimal('scores', 5, 2)->nullable();
            $table->boolean('hidden')->default(false)->index();
            $table->boolean('pin')->default(false)->index();
            $table->boolean('archive')->default(false)->index();
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
        Schema::drop('orders');
    }

}
