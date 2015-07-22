<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PersonalityGroup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('personality_types',
              function(Blueprint $table)
              {
                    if (!Schema::hasColumn('personality_types', 'group')){
              
                           $table->string('group')->after('name')
                                                     ->default('');
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
        if (Schema::hasColumn('personality_types', 'group')){
            
               Schema::table('personality_types',
                             function(Blueprint $table)
                             {
                                $table->dropColumn('group');
                             });
        }
    }

}
