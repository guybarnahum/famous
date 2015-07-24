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
                    if (!Schema::hasColumn('personality_types', 'value_units')){
              
                        $table->string('value_units')->after('name')
                                                     ->default('');

                        $table->string('error_units')->after('value_units')
                                                     ->default('');
                      
                        $table->string('group')->after('error_units')
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
                             function(Blueprint $table){
                                $table->dropColumn('group');
                             });
        }
        
        if (Schema::hasColumn('personality_types', 'error_units')){
            
            Schema::table('personality_types',
                          function(Blueprint $table){
                          $table->dropColumn('error_units');
                          });
        }
        
        if (Schema::hasColumn('personality_types', 'value_units')){
            
            Schema::table('personality_types',
                          function(Blueprint $table){
                          $table->dropColumn('value_units');
                          });
        }
    }

}
