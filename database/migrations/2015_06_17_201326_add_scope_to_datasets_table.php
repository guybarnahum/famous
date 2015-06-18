<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScopeToDatasetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
        Schema::table('datasets', function(Blueprint $table)
                      {
                      $table->string('scope')->after('oath_callback_uri')
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
        Schema::table('accounts', function(Blueprint $table)
                      {
                      $table->dropColumn('scope');
                      });
	}

}
