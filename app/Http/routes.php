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

Route::get('login/{provider?}'      , 'Auth\AuthController@autorizeProvider'  );
Route::get('callback/{provider?}'   , 'Auth\AuthController@handleProviderCallback');

Route::get('logoutProvider/{provider?}'
                                    , 'Auth\AuthController@logoutProvider'  );
Route::get('logout'                 , 'Auth\AuthController@logout'          );

// ........................................................................ ajax
// POST ajax api calls
//
// what   : user | accounts | facts | insights | reports
// uid    : me is session active user
//
// filter : user | accounts | facts filter by provider
//                              (facebook, linkedin, google, twitter)
//          insights and reports by system and type respectivly.
//
Route::post('get/{what}/{uid?}/{filter?}', 'HomeController@get'      );
Route::post('get/{what}/{uid?}'          , 'HomeController@getByUid' );
Route::post('get/{what}'                 , 'HomeController@getActive');

if ( false ){ // Enable GET for debugging only..
Route::get('get/{what}/{uid?}/{filter?}'
                                    , 'HomeController@get'        );
Route::get('get/{what}/{uid?}'      , 'HomeController@getByUid'   );
Route::get('get/{what}'             , 'HomeController@getActive'  );
}

Route::post('mine/{what}/{uid?}/{filter?}'
                                    , 'HomeController@mine'       );
Route::post('mine/{what}/{uid?}'    , 'HomeController@mineoByUid' );
Route::post('mine/{what}'           , 'HomeController@mineActive' );

if ( false ){ // Enable GET for debugging only..
Route::get('mine/{what}/{uid?}/{filter?}'
                                    , 'HomeController@mine'      );
Route::get('mine/{what}/{uid?}'     , 'HomeController@mineByUid' );
Route::get('mine/{what}'            , 'HomeController@mineActive');
}

if ( false ){ // Enable GET for debugging only..
    Route::get( 'widget/{which}'    , 'HomeController@widget'    );
}

Route::post( 'widget/{which}'       , 'HomeController@widget'    );
   
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
