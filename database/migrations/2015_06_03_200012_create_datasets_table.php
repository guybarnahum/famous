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
                       
                       $table->string( 'provider' )->unique()
                                                   ->index ();
                       
                       $table->string( 'api_key'    );
                       $table->string( 'api_secret' );

                       $table->string( 'driver' ); // code to handle the dataset api
                       $table->string( 'oath_callback_uri' );
                       
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
