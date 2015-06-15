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

Route::get('login/{provider?}'    , 'Auth\AuthController@autorizeWithProvider'  );
Route::get('callback/{provider?}' , 'Auth\AuthController@handleProviderCallback');

Route::get('logout/{provider?}'   , 'Auth\AuthController@logoutFromProvider'    );
Route::get('logout'               , 'Auth\AuthController@logout'    );

// ................................................................. controllers

Route::controllers([
//	'auth'     => 'Auth\AuthController',
//	'password' => 'Auth\PasswordController',
]);
