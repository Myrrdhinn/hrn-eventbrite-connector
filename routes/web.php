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

Route::get('/', function () {
    return view('welcome');
});

Route::any('/request', 'RequestController@index'); //Eventbrite webhook receiver

Route::any('/test', 'TestController@index'); //tester.. display stuff from database

Route::get('/list', 'ListController@index'); //WIP Sales UI

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home'); //Default Laravel.. will be deleted.. :D

Route::get('/sftest', 'Salesforce@index'); //sf test route

//Salesforce Api Routes

/*Route::get('/authenticate', function()
{
    return Forrest::authenticate();
});*/

Route::get('/authenticate', function()
{
    $loginURL = 'https://test.salesforce.com';

    return Forrest::authenticate($loginURL);
});

Route::get('/callback', function()
{
    Forrest::callback();

    return Redirect::to('/');
});
