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

Route::get('/'   , 'WelcomeController@index');
Route::get('home', 'HomeController@index');
Route::get('user', 'UserController@index' );
    
// ........................................................... login/{provider?}
//
// Used to login for third party service, see config/services.php
// The provider parameter is the dataset (eg: Twitter),
// so the view would have a link that looks like this:
//
//    <a href="/login/twitter">Login in with Twitter</a>
//

Route::get('login/{provider?}', 'Auth\AuthController@login');

// ................................................................. controllers

Route::controllers([
	'auth'     => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
