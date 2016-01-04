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
            $table->bigInteger('attributes_id')->nullable()->index();
            $table->bigInteger('elements_id')->nullable()->index();
            $table->string('elements_type', 255)->default('')->index();
            $table->longText('product_new_price');
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
