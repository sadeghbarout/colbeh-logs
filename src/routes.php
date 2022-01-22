<?php
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Colbeh\Logs\Controllers'], function () {
//	Route::get('/', ['as' => 'bmi_path', 'uses' => 'ConstController@index']);
	Route::get('log-viewer', "LogViewController@index");

	Route::get('log-viewer', "LogViewController@index");
	Route::get('log-viewer/file', "LogViewController@filesList");
	Route::delete('log-viewer/delete', "LogViewController@destroy");
	Route::get('log-viewer/show/{path1}/{path2?}/{path3?}', "LogViewController@show");
	Route::get('log-viewer/show-ajax', "LogViewController@showAjax");
});