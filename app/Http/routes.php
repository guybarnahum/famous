<?php

/*
|-------------------------------------------------------------------------------
| Application Routes
|-------------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/'   , 'HomeController@index');
Route::get('home', 'HomeController@index');
    
// ........................................................... login/{provider?}
//
// Social logins
// Used to login for socialite providers, see config/services.php
//    <a href="/login/twitter">Login in with Twitter</a>
//

Route::get('login/{provider?}'      , 'Auth\AuthController@autorizeWithProvider'  );
Route::get('callback/{provider?}'   , 'Auth\AuthController@handleProviderCallback');

Route::get('logout_p/{provider?}'   , 'Auth\AuthController@logoutFromProvider'    );
Route::get('logout'                 , 'Auth\AuthController@logout'    );

// ........................................................................ ajax
// POST ajax api calls

Route::post('accounts/{uid?}/{provider?}'
                                    ,'HomeController@getUserAccounts'      );
Route::post('accounts/{uid?}'       ,'HomeController@getUserAccountsByUid' );
Route::post('accounts'              ,'HomeController@getActiveUserAccounts');

if ( true ){ // Enable GET for debugging only..
    Route::get('accounts/{uid?}/{provider?}'
                ,'HomeController@getUserAccounts'      );
    Route::get('accounts/{uid?}'       ,'HomeController@getUserAccountsByUid' );
    Route::get('accounts'              ,'HomeController@getActiveUserAccounts');
}

Route::post('facts/{uid?}/{provider?}', 'HomeController@getUserFacts'    );
Route::post('facts/{uid?}'          , 'HomeController@getUserFactsByUid' );
Route::post('facts'                 , 'HomeController@getActiveUserFacts');

if ( false ){ // Enable GET for debugging only..
    Route::get('facts/{uid?}/{provider?}', 'HomeController@getUserFacts' );
    Route::get('facts/{uid?}'       , 'HomeController@getUserFactsByUid' );
    Route::get('facts'              , 'HomeController@getActiveUserFacts');
}
    
Route::post('gen_facts/{uid?}/{provider?}'
                                    ,'HomeController@generateUserFacts'      );
Route::post('gen_facts/{uid?}'      ,'HomeController@generateUserFactsByUid' );
Route::post('gen_facts'             ,'HomeController@generateActiveUserFacts');

if (false){ // Enable GET for debugging only..
    Route::get('gen_facts/{uid?}/{provider?}'
                                    ,'HomeController@generateUserFacts'      );
    Route::get('gen_facts/{uid?}'   ,'HomeController@generateUserFactsByUid' );
    Route::get('gen_facts'          ,'HomeController@generateActiveUserFacts');
}

Route::post('user/{uid}'            , 'HomeController@getUserInfo'       );
Route::post('user'                  , 'HomeController@getActiveUserInfo' );
Route::get ('user/{uid}'            , 'HomeController@getUserInfo'       );
Route::get ('user'                  , 'HomeController@getActiveUserInfo' );

// ................................................................ api/callback

// Disable our pesky \Debugbar for these api/callback routes..
Route::filter( 'nodebugbar', function(){ \Debugbar::disable();});
Route::when  ( 'api/callback/*', 'nodebugbar');
Route::when  ( 'api/callback'  , 'nodebugbar');

Route::get ('api/callback'             , 'api\CallbackController@index');
Route::get ('api/callback/{namespace?}', 'api\CallbackController@show' );
Route::post('api/callback'             , 'api\CallbackController@index');
Route::post('api/callback/{namespace?}', 'api\CallbackController@show' );

// ................................................................. controllers

Route::controllers([
                   //	'auth'     => 'Auth\AuthController',
                   //	'password' => 'Auth\PasswordController',
                   ]);
