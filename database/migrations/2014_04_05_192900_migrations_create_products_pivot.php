<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreateProductsPivot extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_pivot', function($table) {
            $table->bigIncrements('id')->index();
            $table->bigInteger('parent_id')->nullable()->index();
            $table->bigInteger('child_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products_pivot');
    }

}
