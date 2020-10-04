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

//The Generic linker works to make a lightweight tripestore out of DURC objects. Specifically object <-- tag --> object
//this will show the 'add new triples' form, or if the triples table is not yet built, show the SQL to build it.
Route::get('genericLinkerForm/{durc_type_left}/{durc_type_right}/{durc_type_link}','GenericLinker@linkForm');
// this saves the triples form POSTs
Route::post('genericLinkerSave/{durc_type_left}/{durc_type_right}/{durc_type_link}','GenericLinker@linkSaver');
// this will always show the SQL to build to triples table, as well as the SQL to generate a Zermelo Graph report from the triple... even if the database already exists..
Route::get('genericLinkerSQL/{durc_type_left}/{durc_type_right}/{durc_type_link}','GenericLinker@showSQLView');


Route::middleware([\CareSet\CareSetJWTAuthClient\Middleware\JWTClientMiddleware::class])->group(function () {

	Route::get('/test',function() {

		return 'You have authentication working on this server';

	});

});
