<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        // ........................................................ fact (types)
        
        // fact type
        Schema::dropIfExists( 'fact_types' );
        
        Schema::create( 'fact_types', function(Blueprint $table)
                       {
                       // id is our primary index -
                       // used in other tables as a foreign key 'fct_id'
                       
                       $table->increments('id');
                       
                       $table->string( 'name' );
                       $table->string( 'statement_fmt');
                       $table->string( 'question_fmt' );
                       $table->string( 'desc' );
                       
                       $val_types = array( 'bool', 'string', 'num', 'time' );
                       $table->enum( 'val_type', $val_types );
                       
                       $table->timestamps();
                       });
        
       
        // ...................................................... fact (entries)
        // A naive representation of claims or facts
        // Needs rearchitecting. So far it covers:
        //
        // [A] is friend of B according to [C]
        // [A] was born in 1969 according to [C]
        // [A] believes in evolution according to [A]
        // [A] liked [B]'s post on Facebook according to [truth]
        // [A] is a good manager according to [truth]
        //
        Schema::dropIfExists( 'facts' );
        
        Schema::create( 'facts', function(Blueprint $table)
                       {
                       $table->increments('id');
                       
                       // subject (who)
                       $table->unsignedInteger('uid');
                       $table->foreign('uid')->references('id')
                                             ->on('users')
                                             ->onDelete('cascade')
                                             ->onUpdate('cascade');

                       // object (to who)(null is n/a)
                       $table->unsignedInteger('obj_id')
                             ->nullable();
                       
                       $table->foreign('obj_id')->references('id')
                                                ->on('users')
                                                ->onDelete('cascade')
                                                ->onUpdate('cascade');

                       $table->string( 'obj_provider_id');
                       $table->string( 'obj_id_type');
                       $table->string( 'obj_name'   );

                       // source (accroding to) (null is truth)
                       $table->unsignedInteger('src_id')
                             ->nullable();
                       
                       $table->foreign('src_id')->references('id')
                                                ->on('users')
                                                ->onDelete('cascade')
                                                ->onUpdate('cascade');

                       // source dataset account (accroding to)(null is unknown)
                       $table->unsignedInteger('act_id')
                             ->nullable();
                       
                       $table->foreign('act_id')->references('id')
                                                ->on('accounts')
                                                ->onDelete('cascade')
                                                ->onUpdate('cascade');
                       
                       $table->unsignedInteger('fct_id')
                             ->nullable();
                       
                       $table->foreign('fct_id')->references('id')
                                                ->on('fact_types')
                                                ->onDelete('cascade')
                                                ->onUpdate('cascade');

                       $table->string('fct_name');
                       
                       // source has no knowledge or refuse to comment
                       $table->boolean( 'refuse'    )->default( false );
                       $table->boolean( 'dont_know' )->default( false );

                       // FIXME: Make sure this cached value has the same
                       // range of 'val_type' in 'fact_types' table above!
                       $val_types = array( 'bool', 'string', 'num', 'time' );
                       $table->enum( 'val_type', $val_types );

                       $table->string( 'value'  )->default('');
                       $table->string( 'error'  )->default('');
                       
                       // 'score' is the opinion on fact by source
                       // (source could be truth)
                       // -100 .. 0 .. 100 scale from oppostion to neutral
                       // to support for fact
                       
                       $table->integer( 'score'      );
                       $table->integer( 'confidence' );
                       
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
        Schema::dropIfExists( 'facts' );
        Schema::dropIfExists( 'fact_types' );
	}
    
}
