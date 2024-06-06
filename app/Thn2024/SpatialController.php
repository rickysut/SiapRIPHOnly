<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;
use App\Models2024\ForeignApi;
use App\Models2024\MasterSpatial;
use Illuminate\Http\Request;

class SpatialController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module_name = 'Spatial';
		$page_title = 'Data Spatial';
		$page_heading = 'Daftar Spatial Wajib Tanam';
		$heading_class = 'bi bi-globe-asia-australia';

		return view('t2024.spatial.index', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function createsingle()
	{
		$module_name = 'Spatial';
		$page_title = 'Data Spatial';
		$page_heading = 'Buat Peta Lokasi Tanam Baru';
		$heading_class = 'fal fa-map-marked-alt';
		$mapkey = ForeignApi::find(1);

		return view('t2024.spatial.createsingle', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'mapkey'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function storesingle(Request $request)
	{
		MasterSpatial::updateOrCreate(
			['kd_spatial' => $request->input('no')],
			[
				'komoditas' => $request->input('komoditas'),
				'ktp_petani' => $request->input('ktp_petani'),
				'latitude' => $request->input('latitude'),
				'longitude' => $request->input('longitude'),
				'polygon' => $request->input('polygon'),
				'altitude' => $request->input('altitude'),
				'luas_lahan' => $request->input('luas_lahan'),
				'provinsi_id' => $request->input('provinsi_id'),
				'kabupaten_id' => $request->input('kabupaten_id'),
				'kecamatan_id' => $request->input('kecamatan_id'),
				'kelurahan_id' => $request->input('kelurahan_id'),
			]
		);
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
