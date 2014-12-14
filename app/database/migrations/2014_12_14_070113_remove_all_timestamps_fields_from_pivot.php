<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAllTimestampsFieldsFromPivot extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('attributes_connect', function($table) {
                   $table->dropColumn('created_at'); 
				   $table->dropColumn('updated_at'); 
				   $table->dropColumn('deleted_at'); 
                });
		Schema::table('categories_connect', function($table) {
                   $table->dropColumn('created_at'); 
				   $table->dropColumn('updated_at'); 
				   $table->dropColumn('deleted_at'); 
                });	
		Schema::table('categories_pivot', function($table) {
                   $table->dropColumn('created_at'); 
				   $table->dropColumn('updated_at'); 
				   $table->dropColumn('deleted_at'); 
                });		
		Schema::table('images_connect', function($table) {
                   $table->dropColumn('created_at'); 
				   $table->dropColumn('updated_at'); 
				   $table->dropColumn('deleted_at'); 
                });	
		Schema::table('pages_pivot', function($table) {
                   $table->dropColumn('created_at'); 
				   $table->dropColumn('updated_at'); 
				   $table->dropColumn('deleted_at'); 
                });	
		Schema::table('pages_products', function($table) {
                   $table->dropColumn('created_at'); 
				   $table->dropColumn('updated_at'); 
				   $table->dropColumn('deleted_at'); 
                });		
		Schema::table('products_pivot', function($table) {
                   $table->dropColumn('created_at'); 
				   $table->dropColumn('updated_at'); 
				   $table->dropColumn('deleted_at'); 
                });		
		Schema::table('searches_connect', function($table) {
                   $table->dropColumn('created_at'); 
				   $table->dropColumn('updated_at'); 
				   $table->dropColumn('deleted_at'); 
                });			
		Schema::table('tags_connect', function($table) {
                   $table->dropColumn('created_at'); 
				   $table->dropColumn('updated_at'); 
				   $table->dropColumn('deleted_at'); 
                });						
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('attributes_connect', function($table) {
                    $table->nullableTimestamps();
                    $table->softDeletes();    
                });
		Schema::table('categories_connect', function($table) {
                    $table->nullableTimestamps();
                    $table->softDeletes();    
                });	
		Schema::table('categories_pivot', function($table) {
                    $table->nullableTimestamps();
                    $table->softDeletes();    
                });			
		Schema::table('images_connect', function($table) {
                    $table->nullableTimestamps();
                    $table->softDeletes();    
                });						
		Schema::table('pages_pivot', function($table) {
                    $table->nullableTimestamps();
                    $table->softDeletes();    
                });		
		Schema::table('pages_products', function($table) {
                    $table->nullableTimestamps();
                    $table->softDeletes();    
                });		
		Schema::table('products_pivot', function($table) {
                    $table->nullableTimestamps();
                    $table->softDeletes();    
                });	
		Schema::table('searches_connect', function($table) {
                    $table->nullableTimestamps();
                    $table->softDeletes();    
                });		
		Schema::table('tags_connect', function($table) {
                    $table->nullableTimestamps();
                    $table->softDeletes();    
                });					
	}

}
