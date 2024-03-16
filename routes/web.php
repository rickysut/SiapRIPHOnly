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

//route untuk Pelaku usaha
Route::group(['prefix' => 'importir', 'as' => 'importir.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
});

Route::group(['prefix' => 'verification', 'as' => 'verification.', 'namespace' => 'Verifikator', 'middleware' => ['auth']], function () {

	//verifikasi data lokasi tanam
	Route::get('{noIjin}/lokasitanam', 'LokasiTanamController@index')->name('lokasitanam');

	Route::get('{noIjin}/lokasitanam/{lokasiId}', 'LokasiTanamController@listLokasibyPetani')->name('listLokasibyPetani');
	Route::get('{id}/summary', 'VerifSklController@dataCheck')->name('data.summary');

	//new verifikasi tanam
	Route::get('tanam', 'VerifTanamController@index')->name('tanam');
	Route::group(['prefix' => 'tanam', 'as' => 'tanam.'], function () {
		Route::get('{id}/check', 'VerifTanamController@check')->name('check');
		// Route::get('{noIjin}/daftar_lokasi_tanam', 'LokasiTanamController@daftarTanam')->name('daftarTanam');
		Route::put('{id}/storeCheck', 'VerifTanamController@storeCheck')->name('storeCheck');
		Route::get('{id}/show', 'VerifTanamController@show')->name('show');
		Route::get('{id}/showlocation', 'LokasiTanamController@showLocation')->name('showLocation');
		Route::post('{id}/checkBerkas', 'VerifTanamController@checkBerkas')->name('checkBerkas');
		Route::get('{noIjin}/poktan/{poktan_id}/check', 'VerifTanamController@verifPks')->name('check.pks');
		Route::put('pks/{id}/store', 'VerifTanamController@verifPksStore')->name('check.pks.store');
		Route::put('{id}/checkPksSelesai', 'VerifTanamController@checkPksSelesai')->name('checkPksSelesai');
		// Route::get('{noIjin}/lokasi/{anggota_id}', 'VerifTanamController@lokasicheck')->name('lokasicheck');
	});

	//new verifikasi produksi
	Route::get('produksi', 'VerifProduksiController@index')->name('produksi');
	Route::group(['prefix' => 'produksi', 'as' => 'produksi.'], function () {
		Route::get('{id}/check', 'VerifProduksiController@check')->name('check');
		Route::post('{id}/storeCheck', 'VerifProduksiController@storeCheck')->name('storeCheck');
		Route::get('{id}/show', 'VerifProduksiController@show')->name('show');
		Route::post('{id}/checkBerkas', 'VerifProduksiController@checkBerkas')->name('checkBerkas');
		Route::get('{noIjin}/poktan/{poktan_id}/check', 'VerifProduksiController@verifPks')->name('check.pks');
		Route::put('pks/{id}/store', 'VerifProduksiController@verifPksStore')->name('check.pks.store');
		Route::post('{id}/checkPksSelesai', 'VerifProduksiController@checkPksSelesai')->name('checkPksSelesai');
		Route::get('{id}/showlocation', 'LokasiTanamController@showLocation')->name('showLocation');
		//unused
		Route::put('{id}/store', 'VerifProduksiController@store')->name('store');
	});

	//new verifikasi skl
	Route::get('skl', 'VerifSklController@index')->name('skl');
	Route::group(['prefix' => 'skl', 'as' => 'skl.'], function () {
		Route::get('{id}/check', 'VerifSklController@check')->name('check');
		Route::post('{id}/checkBerkas', 'VerifSklController@checkBerkas')->name('checkBerkas');
		Route::get('{noIjin}/poktan/{poktan_id}/check', 'VerifSklController@verifPks')->name('check.pks');
		Route::put('pks/{id}/store', 'VerifSklController@verifPksStore')->name('check.pks.store');
		Route::post('{id}/checkPksSelesai', 'VerifSklController@checkPksSelesai')->name('checkPksSelesai');
		Route::get('{id}/showlocation', 'LokasiTanamController@showLocation')->name('showLocation');
		Route::post('{id}/storeCheck', 'VerifSklController@storeCheck')->name('storeCheck');
		Route::get('{id}/verifSklShow', 'VerifSklController@verifSklShow')->name('verifSklShow');

		//rekomendasi penerbitan
		Route::post('{id}/recomend', 'VerifSklController@recomend')->name('recomend');

		//daftar rekomendasi skl untuk pejabat
		Route::get('recomendations', 'VerifSklController@recomendations')->name('recomendations');
		Route::group(['prefix' => 'recomendation', 'as' => 'recomendation.'], function () {
			//detail rekomendasi untuk pejabat
			Route::get('{id}/show', 'VerifSklController@showrecom')->name('show');
			//preview draft skl untuk pejabat
			Route::get('{id}/draft', 'VerifSklController@draftSKL')->name('draft');
			//fungsi untuk pejabat menyetujui penerbitan.
			Route::put('{id}/approve', 'VerifSklController@approve')->name('approve');
		});

		//daftar skl diterbitkan
		Route::get('recomendations', 'VerifSklController@recomendations')->name('recomendations');
	});

	// Route::get('{noIjin}/lokasi/{anggota_id}', 'VerifTanamController@lokasicheck')->name('lokasicheck');


	Route::get('skl/{id}/show', 'SklController@show')->name('skl.show');

	//ke bawah ini mungkin di hapus
	Route::get('skl/publishes', 'SklController@publishes')->name('skl.publishes');
	Route::get('skl/published/{id}/print', 'SklController@published')->name('skl.published');
});

Route::group(['prefix' => 'skl', 'as' => 'skl.', 'namespace' => 'Verifikator', 'middleware' => ['auth']], function () {
	// daftar rekomendasi (index rekomendasi dan skl untuk verifikator)
	Route::get('recomended/list', 'VerifSklController@recomended')->name('recomended.list');
	Route::get('{id}/print', 'VerifSklController@printReadySkl')->name('print'); //form view skl untuk admin
	Route::put('{id}/upload', 'VerifSklController@Upload')->name('upload'); //fungsi upload untuk admin
	Route::get('arsip', 'VerifSklController@arsip')->name('arsip');
});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
	// Change password
	if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
		Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
		Route::post('password', 'ChangePasswordController@update')->name('password.update');
		Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
		Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
	}
});

Route::group(['prefix' => 'wilayah', 'as' => 'wilayah.', 'namespace' => 'Wilayah', 'middleware' => ['auth']], function () {
	Route::get('getAllProvinsi', 'GetWilayahController@getAllProvinsi');
	Route::get('getKabupatenByProvinsi/{provinsiId}', 'GetWilayahController@getKabupatenByProvinsi');
	Route::get('getKecamatanByKabupaten/{id}', 'GetWilayahController@getKecamatanByKabupaten');
	Route::get('getDesaByKec/{kecamatanId}', 'GetWilayahController@getDesaByKecamatan');
});

Route::group(['prefix' => 'digisign', 'as' => 'digisign.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
	Route::get('index', 'DigitalSign@index')->name('index');
	Route::post('saveQrImage', 'DigitalSign@saveQrImage')->name('saveQrImage');
});

Route::group(['prefix' => 'support', 'as' => 'support.', 'middleware' => ['auth']], function () {
	// Route::group(['prefix' => 'how_to', 'as' => 'howto.', 'namespace' => 'HowTo'], function () {
	// 	Route::get('/',		'HowToController@show')->name('show');
	// });
	Route::group(['prefix' => 'how_to', 'as' => 'howto.', 'namespace' => 'Howto'], function () {
		Route::get('importir',		'HowtoController@importir')->name('importir');
		Route::get('administrator',	'HowtoController@administrator')->name('administrator');
		Route::get('verifikator',	'HowtoController@verifikator')->name('verifikator');
		Route::get('pejabat',		'HowtoController@pejabat')->name('pejabat');
	});
	Route::group(['prefix' => 'faq', 'as' => 'faq.', 'namespace' => 'Faq'], function () {
	});
	Route::group(['prefix' => 'ticket', 'as' => 'ticket.', 'namespace' => 'Ticket'], function () {
	});
});

Route::group(['prefix' => 'test', 'as' => 'test.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
	Route::get('sample/{id}', 'TestController@index')->name('sample');
	Route::get('files', 'ListFileController@index')->name('files');
	Route::delete('files', 'ListFileController@destroy')->name('files.delete');
});
