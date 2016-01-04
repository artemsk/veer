<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationsCreatePagesPivot extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages_pivot', function($table) {
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
        Schema::drop('pages_pivot');
    }

}
