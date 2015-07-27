<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // ............................................................. reports

        Schema::dropIfExists( 'reports' );
        
        Schema::create( 'reports',
                       function(Blueprint $table)
                       {
                            $table->increments( 'id' );
                       
                            $table->string( 'model'  );
                            $table->string( 'driver' )->nullable();
                            $table->string( 'providers');
           
                            // subject (who about?)
                            $table->unsignedInteger('uid');
                            $table->string( 'name');

                            // according to who?
                            $table->enum( 'layer', ['model','self','others'] );
                           
                            // information
                            $table->string( 'heading'      );
                            $table->string( 'sub_heading'  );
                            $table->string( 'type'         );

                            $table->string( 'info_type'    );
                            $table->string( 'info'         );
                       
                            // information description
                            $table->string( 'desc_type'    )->nullable;
                            $table->string( 'desc'         )->nullable;

                            // feedback regading the information
                            $table->string( 'feedback_type')->nullable;
                            $table->string( 'feedback'     )->nullable;

                            // followup actions, now what?
                            $table->string( 'actions_type' )->nullable;
                            $table->string( 'actions'      )->nullable;

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
        Schema::dropIfExists( 'reports' );
	}
}
