<?php
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Colbeh\Logs\Controllers'], function () {
//	Route::get('/', ['as' => 'bmi_path', 'uses' => 'ConstController@index']);
	Route::get('log-helper', "LogViewController@index");

	Route::get('log-helper', "LogViewController@index");
	Route::get('log-helper/file', "LogViewController@filesList");
	Route::delete('log-helper/delete', "LogViewController@destroy");
	Route::get('log-helper/show/{path1}/{path2?}/{path3?}', "LogViewController@show");
	Route::get('log-helper/show-ajax', "LogViewController@showAjax");
});