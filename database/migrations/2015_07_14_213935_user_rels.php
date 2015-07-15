<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserRels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('users', function(Blueprint $table)
                {
                $table->string('signatures', 2048)->after('emails')
                                                  ->nullable();

                $table->string('providers')->after('slogan')
                                           ->nullable();
                      
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('users', function(Blueprint $table)
                {
                      if (Schema::hasColumn('users', 'providers')){
                                  $table->dropColumn('providers');
                      }
                      
                      if (Schema::hasColumn('users', 'signatures')){
                                  $table->dropColumn('signatures');
                      }
                });
	}
}
