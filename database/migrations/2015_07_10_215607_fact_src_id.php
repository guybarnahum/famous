<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FactSrcId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('facts', function(Blueprint $table)
                            {
                                if (!Schema::hasColumn('facts', 'src_id_type')){
                      
                                    $table->string('src_id_type')->after('src_id')
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
        if (Schema::hasColumn('facts', 'src_id_type')){
            Schema::table('facts', function(Blueprint $table)
                      {
                            $table->dropColumn('src_id_type');
                      });
        }
    }
}
