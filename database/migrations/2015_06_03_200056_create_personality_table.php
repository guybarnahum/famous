<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonalityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // .................................................. personlity (types)
        
        // personlity type
        
        Schema::dropIfExists( 'personality_types' );
        
        Schema::create( 'personality_types', function(Blueprint $table)
                       {
                       // id is our primary index -
                       // used in other tables as a foreign key 'pt_id'
                       
                       $table->increments('id');
                       
                       $table->string( 'sys'    );
                       $table->string( 'name'   )->unique();
                       $table->string( 'display')->unique();
                       $table->string( 'desc'   );
                       
                       $table->timestamps();
                       });
        
        // ................................................ personlity (entries)
        
        Schema::dropIfExists( 'personality_entries' );

        Schema::create( 'personality_entries', function(Blueprint $table)
                       {
                       $table->increments('id');
                       
                       $table->unsignedInteger('uid');
                       $table->foreign('uid')->references('id')
                                             ->on('users');
                       
                       $table->unsignedInteger('ptid');
                       $table->foreign('ptid')->references('id')
                                              ->on('personality_types');
                       
                       $table->string( 'name' )->nullable();
                       $table->integer( 'value' );
                       $table->integer( 'error' );
                       
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
        Schema::dropIfExists( 'personality_entries' );
        Schema::dropIfExists( 'personality_types' );
	}

}
