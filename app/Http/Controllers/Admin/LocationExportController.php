<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataRealisasi;
use App\Models\Lokasi;
use App\Models\PullRiph;
use App\Models\User;
use Illuminate\Http\Request;

class LocationExportController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module_name = 'Report';
		$page_title = 'Eksport Data Lokasi';
		$page_heading = 'Eksport';
		$heading_class = 'fal fa-tachometer';

		$data = PullRiph::select('no_ijin', 'user_id', 'npwp')->with('datauser')->get();
		$years = $data->pluck('no_ijin')->map(function ($no_ijin) {
			return substr($no_ijin, -4);
		})->unique()->values();

		return view('admin.dataeksport.index', compact('page_title', 'page_heading', 'heading_class', 'module_name', 'years'));
	}

	public function getCompaniesByYear($year)
	{
		$companies = PullRiph::select('no_ijin', 'user_id', 'npwp')->with('datauser')->has('datarealisasi')
			->get()
			->filter(function ($item) use ($year) {
				return substr($item->no_ijin, -4) == $year;
			})
			->map(function ($item) {
				return [
					'no_ijin' => $item->no_ijin,
					'user_id' => $item->user_id,
					'npwp' => $item->npwp,
					'company_name' => $item->datauser->company_name ?? null,
				];
			});

		return response()->json($companies);
	}

	public function getLocationByIjin($noIjin)
	{
		$formattedNoIjin = substr($noIjin, 0, 4) . "/" .
			substr($noIjin, 4, 2) . "." .
			substr($noIjin, 6, 3) . "/" .
			substr($noIjin, 9, 1) . "/" .
			substr($noIjin, 10, 2) . "/" .
			substr($noIjin, 12, 4);

		$lokasi = DataRealisasi::where('no_ijin', $formattedNoIjin)
			->with([
				'commitment' => function ($query) use ($formattedNoIjin) {
					$query->with([
						'pks' => function ($query) use ($formattedNoIjin) {
							$query->where('no_ijin', $formattedNoIjin)->select('no_ijin', 'id', 'npwp', 'poktan_id', 'no_perjanjian');
						},
						'completed'
					]);
				},
				'masteranggota:anggota_id,nama_petani',
				'masterkelompok:id,poktan_id,nama_kelompok'
			])
			->get()
			->map(function ($item) {
				$item->commitment_nama = $item->commitment->nama ?? null;
				$item->status = $item->commitment->completed->status ?? null;
				$item->no_pks = $item->commitment->pks[0]->no_perjanjian ?? null;
				$item->nama_petani = $item->masteranggota->nama_petani ?? null;
				$item->nama_kelompok = $item->masterkelompok->nama_kelompok ?? null;

				// Hapus properti relasi yang sudah dipindahkan
				unset($item->commitment);
				unset($item->masteranggota);
				unset($item->masterkelompok);

				return $item;
			});


		// $company = PullRiph::where('no_ijin', $formattedNoIjin)
		// 	->select('npwp','no_ijin')
		// 	->with(['datauser' => function ($query) {
		// 		$query->select('npwp_company', 'company_name');
		// 	}])
		// 	->with(['datarealisasi' => function ($query) use ($formattedNoIjin) {
		// 		$query->with(['pks' => function ($query) use ($formattedNoIjin) {
		// 			$query->where('no_ijin', $formattedNoIjin)->select('no_ijin', 'id','npwp', 'poktan_id', 'no_perjanjian');
		// 		}])
		// 		->with('masteranggota:anggota_id,nama_petani')
		// 		->with('masterkelompok:id,poktan_id,nama_kelompok');
		// 	}])
		// 	->first();

		return response()->json($lokasi);
	}

	public function getRealisasiCompany($year)
	{
		$companies = PullRiph::where('periodetahun', $year)
                ->has('datarealisasi')  // Filter companies that have datarealisasi
                ->select('id', 'no_ijin', 'nama')
                ->get();

		$count = $companies->count();

    	return response()->json($count);
	}
}
