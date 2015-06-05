<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
            
class CreateUsersTable extends Migration {
    
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	
    // user
    // uid    : # primary index
    // bio_id :
    // personality_desc :
    // skill_desc       :
    // interest_desc    :
    // confidence       :     0..100
    // complete         : -1, 0..100

    public function up()
	{
        // ................................................................ user
        
        Schema::dropIfExists( 'users' );
        
        Schema::create('users', function(Blueprint $table)
                       {
                       // id is our primary index -
                       // used in other tables as a foreign key 'uid'
                       $table->increments('id');
                       
                       $table->string( 'email'   )->unique();
                       $table->string( 'password', 60);
                       
                       $table->string( 'name'    );
                       $table->string( 'slogan'  );
                       $table->string( 'contact' ); # need to be multiple
                       
                       $table->longText( 'personality_desc');
                       $table->longText( 'skill_desc');
                       
                       $table->integer( 'bio_id'  );
                       $table->string( 'bio_url'  );// auto generated and structured (xml?)
                       $table->string( 'wiki_url' );// wikipedia style entry
                       
                       // major state
                       $table->boolean( 'opt_out' );
                       
                       // cached photo
                       $table->string ( 'pri_photo_large' );
                       $table->string ( 'pri_photo_medium' );
                       $table->string ( 'pri_photo_small' );
                       
                       $table->rememberToken();
                       $table->timestamps();
                       });

        // ............................................................ accounts
        
        Schema::dropIfExists( 'accounts' );

        Schema::create('accounts', function(Blueprint $table)
                       {
                       $table->increments('id');

                       // account owner id
                       $table->unsignedInteger('uid');
                       $table->foreign('uid')->references('id')
                                             ->on('users');
                       // dataset id
                       $table->unsignedInteger('ds_id');
                       $table->foreign('ds_id')->references('id')
                                               ->on('datasets');
                       
                       // dataset access & state
                       $table->string('token');
                       $table->string('auth');
                       
                       $account_states =
                       array( 'pending', 'active', 'suspended', 'disabled' );

                       $table->enum( 'state'   , $account_states ); // in 'fm' system
                       $table->enum( 'ds_state', $account_states ); // in 'dataset'
                       });
        
        // .............................................................. photos
        
        Schema::dropIfExists( 'photos' );

        Schema::create('photos', function(Blueprint $table)
                       {
                        $table->increments('id');
                       
                        // photo owner
                        $table->unsignedInteger('uid');
                        $table->foreign('uid')->references('id')
                                              ->on('users');
                        // photo account
                        $table->unsignedInteger('src_id');
                        $table->foreign('src_id')->references('id')
                                                 ->on('accounts');
                       
                        // info
                        $table->string( 'url' ); // url should be static / safe
                       
                        // we keep photos as 512, 256, 128, 64 pix
                        // other services are just large, medium, small
                       
                        $photo_sizes = array( 'large', 'medium', 'small',
                                              '512', '256', '128', '32' );

                        $table->enum  ( 'size'    , $photo_sizes    );
                        $table->string( 'dataset' );
                       
                        // state and permission
                       
                        $photo_states =
                        array( 'pending', 'active', 'suspended', 'disabled' );
                       
                        $table->enum  ( 'state' , $photo_states );

                        // TODO: TBD how to control permission of photos
                        // in profile. User may chose to display or hide photos
                        // for various profile display modes
                        $table->integer( 'permission' );
                        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists( 'photos'   );
        Schema::dropIfExists( 'accounts' );
        Schema::dropIfExists( 'users'    );
	}
}
