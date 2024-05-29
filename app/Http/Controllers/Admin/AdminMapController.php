<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataRealisasi;
use App\Models\Lokasi;
use App\Models\PullRiph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMapController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$dataRealisasis = DataRealisasi::with(['fototanam', 'fotoproduksi'])->get();
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

				'mulaitanam' => $dataRealisasi->mulai_tanam,
				'akhirtanam' => $dataRealisasi->akhir_tanam,
				'luas_tanam' => $dataRealisasi->luas_lahan,
				'fotoTanam' => $dataRealisasi->fototanam,

				'mulaipanen' => $dataRealisasi->mulai_panen,
				'akhirpanen' => $dataRealisasi->akhir_panen,
				'volume' => $dataRealisasi->volume,
				'fotoProduksi' => $dataRealisasi->fotoproduksi,

			];
		}
		return response()->json($result);
	}

	public function ByYears($periodeTahun)
	{
		if ($periodeTahun === 'all') {
			$commitment = PullRiph::all(); // Mengambil semua data jika $periodeTahun tidak disediakan
		} else {
			$commitment = PullRiph::where('periodetahun', $periodeTahun)->get();
		}

		$dataRealisasis = DataRealisasi::whereIn('no_ijin', $commitment->pluck('no_ijin'))
			->get();

		// dd($dataRealisasis);

		$result = [];
		foreach ($dataRealisasis as $dataRealisasi) {
			$result[] = [
				'id'		=> $dataRealisasi->id,
				'noIjin'	=> str_replace(['.', '/'], '', $dataRealisasi->no_ijin),
				'no_ijin'	=> $dataRealisasi->no_ijin,
				'latitude'	=> $dataRealisasi->latitude,
				'longitude' => $dataRealisasi->longitude,
				'polygon'	=> $dataRealisasi->polygon,
				'status' => isset($dataRealisasi->commitment->completed->status) ? $dataRealisasi->commitment->completed->status : 'default_status',
			];
		}
		return response()->json($result);
	}

	public function show($id)
	{
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

			'mulaitanam' => $dataRealisasi->mulai_tanam,
			'akhirtanam' => $dataRealisasi->akhir_tanam,
			'luas_tanam' => $dataRealisasi->luas_lahan,
			'fotoTanam' => $dataRealisasi->fototanam,

			'mulaipanen' => $dataRealisasi->mulai_panen,
			'akhirpanen' => $dataRealisasi->akhir_panen,
			'volume' => $dataRealisasi->volume,
			'fotoProduksi' => $dataRealisasi->fotoproduksi,
		];
		return response()->json($result);
	}

	public function getMapByYears($periodeTahun)
	{
		if ($periodeTahun === 'all') {
			$commitment = PullRiph::select(['id', 'periodetahun', 'no_ijin'])->get();
		} else {
			$commitment = PullRiph::select(['id', 'periodetahun', 'no_ijin'])->where('periodetahun', $periodeTahun)->get();
		}

		$dataRealisasis = DataRealisasi::whereIn('no_ijin', $commitment->pluck('no_ijin'))
			->with(['fototanam', 'fotoproduksi'])->get();

		$result = [];
		foreach ($dataRealisasis as $dataRealisasi) {
			$result[] = [
				'id' => $dataRealisasi->id,
				'noIjin' => str_replace(['.', '/'], '', $dataRealisasi->no_ijin),
				'no_ijin' => $dataRealisasi->no_ijin,
				'latitude' => $dataRealisasi->latitude,
				'longitude' => $dataRealisasi->longitude,
				'polygon' => $dataRealisasi->polygon,
			];
		}
		return response()->json($result);
	}

	public function singleMarker($id)
	{
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

			'mulaitanam' => $dataRealisasi->mulai_tanam,
			'akhirtanam' => $dataRealisasi->akhir_tanam,
			'luas_tanam' => $dataRealisasi->luas_lahan,
			'fotoTanam' => $dataRealisasi->fototanam,

			'mulaipanen' => $dataRealisasi->mulai_panen,
			'akhirpanen' => $dataRealisasi->akhir_panen,
			'volume' => $dataRealisasi->volume,
			'fotoProduksi' => $dataRealisasi->fotoproduksi,
		];
		return response()->json($result);
	}
}
