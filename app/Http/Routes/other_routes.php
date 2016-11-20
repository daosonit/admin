<?php

Route::get('/profile', ['as'   => 'profile.index',
                        'uses' => 'Profile\ProfileController@showProfile']);

Route::put('/profile', array('as'   => 'profile.edit',
                             'uses' => 'Profile\ProfileController@updateProfile'));

Route::post('/profile/change-password', array('as'   => 'profile.change_password',
                                              'uses' => 'Profile\ProfileController@changePassword'));

Route::get('/', 'HomeController@index');

Route::get('dashboard', ['as' => 'dashboard', 'uses' => 'HomeController@getDashboard']);
Route::get('home', ['as' => 'home', 'uses' => 'HomeController@index']);


Route::get('/access-forbidden', 'ErrorsController@getAccessForbidden');





