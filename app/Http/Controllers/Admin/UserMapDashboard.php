<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataRealisasi;
use App\Models\Lokasi;
use App\Models\PullRiph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMapDashboard extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function index()
	{
		$usermap = Auth::user()->data_user->npwp_company;
		// $anggotaMitras = Lokasi::where('npwp', $usermap)->get();
		$dataRealisasis = DataRealisasi::where('npwp_company', $usermap)->with(['fototanam', 'fotoproduksi'])->get();

		$result = [];
		foreach ($dataRealisasis as $dataRealisasi) {
			$result[] = [
				'id' => $dataRealisasi->id,
				'npwp' => str_replace(['.', '-'], '', $dataRealisasi->npwp_company),
				'company' => $dataRealisasi->commitment->datauser->company_name,
				'noIjin' => str_replace(['.', '/'], '', $dataRealisasi->no_ijin),
				'no_ijin' => $dataRealisasi->no_ijin,
				'perioderiph' => $dataRealisasi->commitment->periodetahun,
				'pks_mitra_id' => $dataRealisasi->poktan_id,
				'no_perjanjian' => $dataRealisasi->pks->no_perjanjian,
				'nama_kelompok' => $dataRealisasi->masterkelompok->nama_kelompok,
				'nama_petani' => $dataRealisasi->masteranggota->nama_petani,

				'nama_lokasi' => $dataRealisasi->nama_lokasi,
				'varietas' => $dataRealisasi->pks->varietas->nama_varietas,
				'latitude' => $dataRealisasi->latitude,
				'longitude' => $dataRealisasi->longitude,
				'polygon'	=> $dataRealisasi->polygon,
				'altitude' => $dataRealisasi->altitude,

				'tgl_tanam' => $dataRealisasi->mulai_tanam,
				'tgl_akhir_tanam' => $dataRealisasi->akhir_tanam,
				'luas_tanam' => $dataRealisasi->luas_lahan,
				'fotoTanam' => $dataRealisasi->fototanam,

				'tgl_panen' => $dataRealisasi->mulai_panen,
				'tgl_akhir_panen' => $dataRealisasi->akhir_panen,
				'volume' => $dataRealisasi->volume,
				'fotoProduksi' => $dataRealisasi->fotoproduksi,

			];
		}

		// $anggotaMitras = Lokasi::with([
		// 	'pks' => function ($query) {
		// 		$query->with('commitment');
		// 	},
		// 	'pks',
		// 	'masteranggota',
		// ])
		// 	->whereNotNull('latitude')
		// 	->where('npwp', $usermap)
		// 	->get();
		// // dd($anggotaMitras->datarealisasi);

		// $result = [];

		// foreach ($anggotaMitras as $anggotaMitra) {
		// 	$luasTanam = $anggotaMitra->luas_tanam ? $anggotaMitra->luas_tanam : 'belum tanam';
		// 	$volume = $anggotaMitra->volume ? $anggotaMitra->volume : 'belum panen';
		// 	$result[] = [
		// 		'id' => $anggotaMitra->id,
		// 		'npwp' => str_replace(['.', '-'], '', $anggotaMitra->npwp),
		// 		'latitude' => $anggotaMitra->latitude,
		// 		'longitude' => $anggotaMitra->longitude,
		// 		'polygon' => $anggotaMitra->polygon,

		// 		'pks_mitra_id' => $anggotaMitra->poktan_id,
		// 		'no_ijin' => $anggotaMitra->pullriph->no_ijin,
		// 		'periodetahun' => $anggotaMitra->pullriph->periodetahun,
		// 		'no_perjanjian' => $anggotaMitra->pks->no_perjanjian,
		// 		'nama_petani' => $anggotaMitra->masteranggota->nama_petani,
		// 		'nama_kelompok' => $anggotaMitra->pks->masterpoktan->nama_kelompok,
		// 		'nama_lokasi' => $anggotaMitra->nama_lokasi,

		// 		'altitude' => $anggotaMitra->altitude,
		// 		'luas_kira' => $anggotaMitra->luas_kira,
		// 		'tgl_tanam' => $anggotaMitra->tgl_tanam,
		// 		'luas_tanam' => $luasTanam,
		// 		'varietas' => $anggotaMitra->varietas,
		// 		'tgl_panen' => $anggotaMitra->tgl_panen,
		// 		'volume' => $volume,
		// 		'tanam_pict' => $anggotaMitra->tanam_pict,
		// 		'panen_pict' => $anggotaMitra->panen_pict,

		// 		'company' => $anggotaMitra->pullriph->datauser->company_name,
		// 	];
		// }

		return response()->json($result);
	}

	public function ByYears($periodeTahun)
	{
		$usermap = Auth::user()->data_user->npwp_company;
		if ($periodeTahun === 'all') {
			$commitment = PullRiph::where('npwp', $usermap)->get(); // Mengambil semua data jika $periodeTahun tidak disediakan
		} else {
			$commitment = PullRiph::where('npwp', $usermap)->where('periodetahun', $periodeTahun)->get();
		}
		$dataRealisasis = DataRealisasi::whereIn('no_ijin', $commitment->pluck('no_ijin'))
			->with(['fototanam', 'fotoproduksi'])->get();

		// $dataRealisasis = DataRealisasi::where('npwp_company', $usermap)->with(['fototanam', 'fotoproduksi'])->get();

		$result = [];
		foreach ($dataRealisasis as $dataRealisasi) {
			// $periodetahun = $dataRealisasi->commitment->periodetahun;
			// if ($periodetahun == $periodeTahun) {
			$result[] = [
				'id' => $dataRealisasi->id,
				'npwp' => str_replace(['.', '-'], '', $dataRealisasi->npwp_company),
				'company' => $dataRealisasi->commitment->datauser->company_name,
				'noIjin' => str_replace(['.', '/'], '', $dataRealisasi->no_ijin),
				'no_ijin' => $dataRealisasi->no_ijin,
				'perioderiph' => $dataRealisasi->commitment->periodetahun,
				'pks_mitra_id' => $dataRealisasi->poktan_id,
				'no_perjanjian' => $dataRealisasi->pks->no_perjanjian,
				'nama_kelompok' => $dataRealisasi->masterkelompok->nama_kelompok,
				'nama_petani' => $dataRealisasi->masteranggota->nama_petani,

				'nama_lokasi' => $dataRealisasi->nama_lokasi,
				'varietas' => $dataRealisasi->pks->varietas->nama_varietas,
				'latitude' => $dataRealisasi->latitude,
				'longitude' => $dataRealisasi->longitude,
				'polygon'	=> $dataRealisasi->polygon,
				'altitude' => $dataRealisasi->altitude,

				'tgl_tanam' => $dataRealisasi->mulai_tanam,
				'tgl_akhir_tanam' => $dataRealisasi->akhir_tanam,
				'luas_tanam' => $dataRealisasi->luas_lahan,
				'fotoTanam' => $dataRealisasi->fototanam,

				'tgl_panen' => $dataRealisasi->mulai_panen,
				'tgl_akhir_panen' => $dataRealisasi->akhir_panen,
				'volume' => $dataRealisasi->volume,
				'fotoProduksi' => $dataRealisasi->fotoproduksi,

			];
			// }
		}

		// $anggotaMitras = Lokasi::with([
		// 	'pks' => function ($query) {
		// 		$query->with('commitment');
		// 	},
		// 	'pks',
		// 	'masteranggota'
		// ])
		// 	->whereNotNull('latitude')
		// 	->where('npwp', $usermap)
		// 	->get();
		// $result = [];

		// foreach ($anggotaMitras as $anggotaMitra) {
		// 	$periodetahun = $anggotaMitra->pullriph->periodetahun;
		// 	if ($periodetahun == $periodeTahun) {
		// 		$luasTanam = $anggotaMitra->luas_tanam ? $anggotaMitra->luas_tanam : 'belum tanam';
		// 		$volume = $anggotaMitra->volume ? $anggotaMitra->volume : 'belum panen';

		// 		$result[] = [
		// 			'periodetahun' => $periodetahun,
		// 			'id' => $anggotaMitra->id,
		// 			'npwp' => str_replace(['.', '-'], '', $anggotaMitra->npwp),
		// 			'latitude' => $anggotaMitra->latitude,
		// 			'longitude' => $anggotaMitra->longitude,
		// 			'polygon' => $anggotaMitra->polygon,

		// 			'pks_mitra_id' => $anggotaMitra->poktan_id,
		// 			'no_ijin' => $anggotaMitra->pullriph->no_ijin,
		// 			'periodetahun' => $anggotaMitra->pullriph->periodetahun,
		// 			'no_perjanjian' => $anggotaMitra->pks->no_perjanjian,
		// 			'nama_petani' => $anggotaMitra->masteranggota->nama_petani,
		// 			'nama_kelompok' => $anggotaMitra->pks->masterpoktan->nama_kelompok,
		// 			'nama_lokasi' => $anggotaMitra->nama_lokasi,

		// 			'altitude' => $anggotaMitra->altitude,
		// 			'luas_kira' => $anggotaMitra->luas_kira,
		// 			'tgl_tanam' => $anggotaMitra->tgl_tanam,
		// 			'luas_tanam' => $luasTanam,
		// 			'varietas' => $anggotaMitra->varietas,
		// 			'tgl_panen' => $anggotaMitra->tgl_panen,
		// 			'volume' => $volume,
		// 			'tanam_pict' => $anggotaMitra->tanam_pict,
		// 			'panen_pict' => $anggotaMitra->panen_pict,

		// 			'company' => $anggotaMitra->pullriph->datauser->company_name,
		// 		];
		// 	}
		// }
		return response()->json($result);
	}

	public function show($id) //id table data_realisasi
	{
		$usermap = Auth::user()->data_user->npwp_company;
		$dataRealisasi = DataRealisasi::find($id);

		$result[] = [
			'id' => $id,
			'npwp' => str_replace(['.', '-'], '', $dataRealisasi->npwp_company),
			'company' => $dataRealisasi->commitment->datauser->company_name,
			'noIjin' => str_replace(['.', '/'], '', $dataRealisasi->no_ijin),
			'no_ijin' => $dataRealisasi->no_ijin,
			'perioderiph' => $dataRealisasi->commitment->periodetahun,
			'pks_mitra_id' => $dataRealisasi->poktan_id,
			'no_perjanjian' => $dataRealisasi->pks->no_perjanjian,
			'nama_kelompok' => $dataRealisasi->masterkelompok->nama_kelompok,
			'nama_petani' => $dataRealisasi->masteranggota->nama_petani,

			'nama_lokasi' => $dataRealisasi->nama_lokasi,
			'varietas' => $dataRealisasi->pks->varietas->nama_varietas,
			'latitude' => $dataRealisasi->latitude,
			'longitude' => $dataRealisasi->longitude,
			'polygon'	=> $dataRealisasi->polygon,
			'altitude' => $dataRealisasi->altitude,

			'tgl_tanam' => $dataRealisasi->mulai_tanam,
			'tgl_akhir_tanam' => $dataRealisasi->akhir_tanam,
			'luas_tanam' => $dataRealisasi->luas_lahan,
			'fotoTanam' => $dataRealisasi->fototanam,

			'tgl_panen' => $dataRealisasi->mulai_panen,
			'tgl_akhir_panen' => $dataRealisasi->akhir_panen,
			'volume' => $dataRealisasi->volume,
			'fotoProduksi' => $dataRealisasi->fotoproduksi,

		];
		// $anggotaMitra = Lokasi::with([
		// 	'pks' => function ($query) {
		// 		$query->with('commitment');
		// 	},
		// 	'pks',
		// 	'masteranggota'
		// ])->find($id);

		// $result[] = [
		// 	'id' => $anggotaMitra->id,
		// 	// 'latitude' => $anggotaMitra->latitude,
		// 	// 'longitude' => $anggotaMitra->longitude,
		// 	// 'polygon' => $anggotaMitra->polygon,

		// 	'pks_mitra_id' => $anggotaMitra->poktan_id,
		// 	'npwp' => str_replace(['.', '-'], '', $anggotaMitra->npwp),
		// 	'no_ijin' => $anggotaMitra->pullriph->no_ijin,
		// 	'periodetahun' => $anggotaMitra->pullriph->periodetahun,
		// 	'no_perjanjian' => $anggotaMitra->pks->no_perjanjian,
		// 	'nama_petani' => $anggotaMitra->masteranggota->nama_petani,
		// 	'nama_kelompok' => $anggotaMitra->pks->masterpoktan->nama_kelompok,
		// 	'nama_lokasi' => $anggotaMitra->nama_lokasi,

		// 	'altitude' => $anggotaMitra->altitude,
		// 	'luas_kira' => $anggotaMitra->luas_kira,
		// 	'tgl_tanam' => $anggotaMitra->tgl_tanam,
		// 	'luas_tanam' => $anggotaMitra->luas_tanam,
		// 	'varietas' => $anggotaMitra->varietas,
		// 	'tgl_panen' => $anggotaMitra->tgl_panen,
		// 	'volume' => $anggotaMitra->volume,
		// 	'tanam_pict' => $anggotaMitra->tanam_pict,
		// 	'panen_pict' => $anggotaMitra->panen_pict,

		// 	'company' => $anggotaMitra->pullriph->datauser->company_name,
		// ];
		return response()->json($result);
	}
}
