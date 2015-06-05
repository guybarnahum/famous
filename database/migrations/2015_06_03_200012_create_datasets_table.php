<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatasetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
	{
        // .................................................... datasets (types)
        
        // 'famous', 'facebook', 'linkedin', 'twitter' ,
        // 'qoura', 'stackoverflow', 'github',
        // 'gmail', 'goolgle+'
        
        Schema::dropIfExists( 'datasets' );
        
        Schema::create( 'datasets', function(Blueprint $table)
                       {
                       // id is our primary index -
                       // used in other tables as a foreign key 'ds_id'
                       
                       $table->increments('id');
                       
                       $table->string( 'name'   )->unique();
                       $table->string( 'code'   )->unique();
                       $table->string( 'driver' ); // code to handle the dataset api
                       
                       $table->timestamps();
                       });
    }

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists( 'datasets' );
    }
}
