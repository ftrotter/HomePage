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

//uncomment this to enable authentication.
//remember that you need to do this again in ending.web.php
//Route::middleware([\CareSet\CareSetJWTAuthClient\Middleware\JWTClientMiddleware::class])->group(function () {

Route::get('/', function () {
    return view('welcome');
});

//First we do the "triples" which give the links types.. https://en.wikipedia.org/wiki/Triplestore
Route::get('genericLinkerForm/{durc_type_left}/{durc_type_right}/{durc_type_link}','GenericTripleLinker@linkForm');
// this saves the triples form POSTs
Route::post('genericLinkerSave/{durc_type_left}/{durc_type_right}/{durc_type_link}','GenericTripleLinker@linkSaver');
// this will always show the SQL to build to triples table, as well as the SQL to generate a Zermelo Graph report from the triple... even if the database already exists..
Route::get('genericLinkerSQL/{durc_type_left}/{durc_type_right}/{durc_type_link}','GenericTripleLinker@showSQLView');

//this section is the same as triples... but handles relationships with just two sides...
Route::get('genericLinkerForm/{durc_type_left}/{durc_type_right}','GenericLinker@linkForm');
Route::post('genericLinkerSave/{durc_type_left}/{durc_type_right}','GenericLinker@linkSaver');
Route::get('genericLinkerSQL/{durc_type_left}/{durc_type_right}','GenericLinker@showSQLView');




