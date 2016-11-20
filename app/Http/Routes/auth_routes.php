<?php


// Route::get('/register', array('as' => 'register', 'uses' => 'Auth\AuthController@getRegister'));
// Route::post('/register', array('as' => 'register', 'uses' => 'Auth\AuthController@postRegister'));
Route::get('/login', array('as' => 'login', 'uses' => 'Auth\AuthController@getLogin'));
Route::post('/login', array('as' => 'login', 'uses' => 'Auth\AuthController@postLogin'));
Route::get('/logout', array('as' => 'login', 'uses' => 'Auth\AuthController@getLogout'));