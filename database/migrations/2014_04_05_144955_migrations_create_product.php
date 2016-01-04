<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreateProduct extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function($table) {
            $table->bigIncrements('id');
            $table->tinyInteger('grp')->nullable()->index();
            $table->string('status', 5)->index();
            $table->bigInteger('qty')->nullable();
            $table->bigInteger('weight')->nullable();
            $table->text('title');
            $table->longText('descr');
            $table->string('production_code', 155);
            $table->longText('grp_ids');
            $table->timestamp('to_show')->nullable();
            $table->decimal('currency', 15, 2)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('price_sales', 15, 2)->nullable();
            $table->decimal('price_opt', 15, 2)->nullable();
            $table->decimal('price_base', 15, 2)->nullable(); // закупочная цена
            $table->timestamp('price_sales_on')->nullable();
            $table->timestamp('price_sales_off')->nullable();
            $table->decimal('score', 15, 2)->nullable();
            $table->tinyInteger('star')->nullable()->index();
            $table->bigInteger('ordered')->nullable();
            $table->bigInteger('viewed')->nullable();
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
        Schema::drop('products');
    }

}
