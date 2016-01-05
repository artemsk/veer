<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreateAttributesConnect extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributes_connect', function($table) {
            $table->bigIncrements('id');
            $table->bigInteger('attributes_id')->default(0)->index();
            $table->bigInteger('elements_id')->default(0)->index();
            $table->string('elements_type', 255)->default('')->index();
            $table->longText('product_new_price')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attributes_connect');
    }

}
