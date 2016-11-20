<?php 


Route::get('view-logs', ['as' => 'view-logs', 'uses' => 'ViewLogController@getLogList']);

Route::get('view-logs/{filename}', ['as' => 'view-log-detail', 'uses' => 'ViewLogController@getLog']);

Route::get('view-logs/clear/{filename}', ['as' => 'log-clear', 'uses' => 'ViewLogController@clearLog']);

Route::get('view-sql-logs', ['as' => 'view-sql-logs', 'uses' => 'ViewLogController@getSqlLogList']);

Route::get('view-sql-logs/{filename}', ['as' => 'view-sql-log-detail', 'uses' => 'ViewLogController@getSqlLog']);

Route::get('view-sql-logs/clear/{filename}', ['as' => 'sql-log-clear', 'uses' => 'ViewLogController@clearSqlLog']);