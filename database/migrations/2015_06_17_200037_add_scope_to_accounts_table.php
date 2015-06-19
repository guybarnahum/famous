<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScopeToAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('accounts', function(Blueprint $table)
                      {
                        $table->string('scope_request')->after('expired_at')
                                                       ->nullable();
                        $table->string('scope_granted')->after('expired_at')
                                                       ->nullable();
                        $table->string('scope_denied' )->after('scope_granted')
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
                        $table->dropColumn('scope_request');
                        $table->dropColumn('scope_granted');
                        $table->dropColumn('scope_denied' );
                      });
	}
}
