<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RealtimeUpdates extends Migration {

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
        
        Schema::dropIfExists( 'realtime_updates' );
        
        Schema::create( 'realtime_updates', function(Blueprint $table)
                       {
                       // id is our primary index -
                       // used in other tables as a foreign key 'ds_id'
                       
                       $table->increments('id');
                       $table->string ( 'provider' );
                       $table->string ( 'object' );
                       $table->boolean( 'active' )->default( true );
                       $table->string ( 'json', 1024 );
                       
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
        Schema::dropIfExists( 'realtime_updates' );
	}

}
