<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
		$companies = PullRiph::select('no_ijin', 'user_id', 'npwp')->with('datauser')
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

		$company = PullRiph::where('no_ijin', $formattedNoIjin)
			->select('npwp','no_ijin')
			->with(['datauser' => function ($query) {
				$query->select('npwp_company', 'company_name');
			}])
			->with(['datarealisasi' => function ($query) use ($formattedNoIjin) {
				$query->with(['pks' => function ($query) use ($formattedNoIjin) {
					$query->where('no_ijin', $formattedNoIjin)->select('no_ijin', 'id','npwp', 'poktan_id', 'no_perjanjian');
				}])
				->with('masteranggota:anggota_id,nama_petani')
				->with('masterkelompok:id,poktan_id,nama_kelompok');
			}])
			->with(['completed' => function ($query) {
				$query->select('no_ijin', 'status');
			}])
			// ->with(['lokasi' => function ($query) use ($formattedNoIjin) {
			// 	$query->select('no_ijin', 'poktan_id', 'anggota_id', 'nama_lokasi')
			// 		->with(['pks' => function ($query) use ($formattedNoIjin) {
			// 			$query->where('no_ijin', $formattedNoIjin)->select('no_ijin', 'npwp', 'poktan_id', 'no_perjanjian');
			// 		}])
			// 		->with('masteranggota:anggota_id,nama_petani')
			// 		->with('masterkelompok:id,poktan_id,nama_kelompok');
			// }])
			->first();

		return response()->json($company);


		return response()->json($company);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
