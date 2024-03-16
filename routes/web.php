<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Verifikator\SklOldController;


Route::get('/', function () {
	return redirect()->route('login');
});

Route::get('/v2/register', function () {
	return view('v2register');
});

Route::get('/home', function () {
	if (session('status')) {
		return redirect()->route('admin.home')->with('status', session('status'));
	}
	return redirect()->route('admin.home');
});


Auth::routes(['register' => true]); // menghidupkan registration

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
	// landing
	Route::get('/', 'HomeController@index')->name('home');
});

Route::group(['prefix' => 'test', 'as' => 'test.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
	Route::get('sample/{id}', 'TestController@index')->name('sample');
	Route::get('files', 'ListFileController@index')->name('files');
	Route::delete('files', 'ListFileController@destroy')->name('files.delete');
});
