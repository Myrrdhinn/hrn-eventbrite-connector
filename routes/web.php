<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Default Index page (there's nothing there atm :) )
Route::get('/', function () {
    return view('welcome');
});

//Eventbrite webhook receiver
Route::any('/request', 'RequestController@index'); 

//Salesforce Autentication
Route::get('/authenticate', function()
{
    $loginURL = 'https://login.salesforce.com';

    return Forrest::authenticate($loginURL);
});

//Callback for the Salesforce Autentication
Route::get('/callback', function()
{
    Forrest::callback();

    return Redirect::to('/');
});

//Check the token from Autentication
Route::get('token', function(){ dd(Cache::get('forrest_token')); });


//WIP Sales UI, currently not used
Route::get('/list', 'ListController@index'); 

//tester.. display stuff from database
Route::any('/test', 'TestController@index'); 

//Basic authentication routes for laravel
Auth::routes();

//sf test route (for query testing
Route::get('/sftest', 'Salesforce@index'); 

//Default Laravel.. login route.. we don't need this atm
//Route::get('/home', 'HomeController@index')->name('home'); 





