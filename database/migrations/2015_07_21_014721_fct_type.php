<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FctType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('facts',
                      function(Blueprint $table)
                      {
                            if (!Schema::hasColumn('facts', 'fct_type')){
                      
                                   $table->string('fct_type')->after('fct_name')
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
        if (Schema::hasColumn('facts', 'fct_type')){
            Schema::table('facts', function(Blueprint $table)
                      {
                            $table->dropColumn('fct_type');
                      });
        }
	}

}
