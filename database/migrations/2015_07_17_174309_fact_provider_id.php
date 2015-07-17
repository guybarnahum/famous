<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FactProviderId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('facts', function(Blueprint $table)
                      {
                            if (!Schema::hasColumn('facts', 'fct_provider_id')){
                                $table->string('fct_provider_id')
                                      ->after('fct_name')
                                      ->nullable();
                            }
                      });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('facts', function(Blueprint $table)
                      {
                            if (Schema::hasColumn('facts', 'fct_provider_id')){
                                $table->dropColumn( 'fct_provider_id' );
                            }
                      });
	}

}
