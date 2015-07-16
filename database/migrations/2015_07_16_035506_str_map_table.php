<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StrMapTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::dropIfExists( 'str_map' );
        
        Schema::create( 'str_map', function(Blueprint $table)
                                   {
                                        $table->string( 'name'  );
                                        $table->string( 'key'   )->unique();
                                        $table->string( 'value' );
                       
                                        $table->unsignedInteger( 'count' )
                                              ->default(0);
                                   });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists( 'str_map' );
	}

}
