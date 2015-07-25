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
                       $table->string( 'emails'  );
                       $table->string( 'password', 60);
                       
                       $table->string( 'name'    );
                       $table->string( 'slogan'  );
                       
                       $table->longText( 'personality_desc')->nullable();
                       $table->longText( 'skill_desc')->nullable();
                       
                       $table->integer( 'bio_id'   )->nullable();
                       $table->string ( 'bio_url'  )->nullable();// auto generated and structured (xml?)
                       $table->string ( 'wiki_url' )->nullable();// wikipedia style entry
                       
                       // major state
                       $table->boolean( 'opt_out' )->default(false);
                       
                       // cached photo
                       $table->string ( 'pri_photo_large'  )->nullable();
                       $table->string ( 'pri_photo_medium' )->nullable();
                       $table->string ( 'pri_photo_small'  )->nullable();
                       
                       $table->rememberToken();
                       $table->timestamps();
                       });

        // ............................................................ accounts
        
        Schema::dropIfExists( 'accounts' );

        Schema::create('accounts', function(Blueprint $table)
                       {
                       $table->increments('id');

                       // owner id in our system
                       $table->unsignedInteger('uid');
                       $table->foreign('uid')->references('id')
                                             ->on('users')
                                             ->onDelete('cascade')
                                             ->onUpdate('cascade');
                       
                       // dataset provider
                       $table->string ('provider');


                       // dataset provider access
                       $table->string('provider_uid' );
                       $table->string('access_token', 2048 )->nullable();
                       
                       // provider user info
                       $table->string('avatar'  )->nullable();
                       $table->string('email'   )->nullable();
                       $table->string('username')->nullable();
                       $table->string('name'    )->nullable();

                       // accoutn state
                       $account_states = array( 'pending'   ,
                                                'active'    ,
                                                'logout'    ,
                                                'suspended' ,
                                                'disabled'  );

                       $table->enum( 'state'         , $account_states );
                       $table->enum( 'provider_state', $account_states );
                       
                       $table->timestamps();
                       });
        
        // .............................................................. photos
        
        Schema::dropIfExists( 'photos' );

        Schema::create('photos', function(Blueprint $table)
                       {
                        $table->increments('id');
                       
                        // photo owner
                        $table->unsignedInteger('uid');
                        $table->foreign('uid')->references('id')
                                              ->on('users')
                                              ->onDelete('cascade')
                                              ->onUpdate('cascade');

                        // photo account
                        $table->unsignedInteger('src_id');
                        $table->foreign('src_id')->references('id')
                                                 ->on('accounts')
                                                 ->onDelete('cascade')
                                                 ->onUpdate('cascade');
                       
                        // info
                        $table->string( 'url' ); // url should be static / safe
                       
                        // we keep photos as 512, 256, 128, 64 pix
                        // other services are just large, medium, small
                       
                        $photo_sizes = array( 'large', 'medium', 'small',
                                              '512', '256', '128', '32' );

                        $table->enum  ( 'size'    , $photo_sizes    );
                        $table->string( 'dataset' );
                       
                        $table->string( 'hash' )->nullable();
                        // state and permission
                       
                        $photo_states =
                        array( 'pending', 'active', 'suspended', 'disabled' );
                       
                        $table->enum  ( 'state' , $photo_states );

                        // TODO: TBD how to control permission of photos
                        // in profile. User may chose to display or hide photos
                        // for various profile display modes
                        $table->integer( 'permission' );
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
        Schema::dropIfExists( 'photos'   );
        Schema::dropIfExists( 'accounts' );
        Schema::dropIfExists( 'users'    );
	}
}
