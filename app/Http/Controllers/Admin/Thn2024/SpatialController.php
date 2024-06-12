<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;
use App\Models2024\ForeignApi;
use App\Models2024\MasterSpatial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
		DB::beginTransaction();

		try {
			$request->validate([
				'kml_url' => 'required|file|mimes:kml,xml,application/vnd.google-earth.kml+xml|max:2048', // Maksimum ukuran file 2MB
			]);

			if ($request->hasFile('kml_url')) {
				$file = $request->file('kml_url');
				$kdLokasi = $request->input('kode_spatial');

				$filename = $kdLokasi . '_' . time() . '.' . $file->getClientOriginalExtension();
				$path = 'uploads/kml/';
				$filePath = $file->storeAs($path, $filename, 'public');
			} else {
				throw new \Exception('File not found');
			}

			// dd($request->input('tgl_peta'), $request->input('tgl_tanam'), $filePath);

			MasterSpatial::updateOrCreate(
				['kode_spatial' => $request->input('kode_spatial')],
				[
					'komoditas' => $request->input('komoditas'),
					'ktp_petani' => $request->input('ktp_petani'),
					'latitude' => $request->input('latitude'),
					'longitude' => $request->input('longitude'),
					'polygon' => $request->input('polygon'),
					'altitude' => $request->input('altitude'),
					'luas_lahan' => $request->input('luas_lahan'),
					'nama_lahan' => $request->input('nama_lahan'),
					'provinsi_id' => $request->input('provinsi_id'),
					'kabupaten_id' => $request->input('kabupaten_id'),
					'kecamatan_id' => $request->input('kecamatan_id'),
					'kelurahan_id' => $request->input('kelurahan_id'),
					'nama_petugas' => $request->input('nama_petugas'),
					'tgl_peta' => $request->input('tgl_peta'),
					'tgl_tanam' => $request->input('tgl_tanam'),
					'kml_url' => $filePath,
				]
			);

			DB::commit();
			return redirect()->route('2024.spatial.index')->with('success', 'Data successfully saved.');
		} catch (\Exception $e) {

			DB::rollBack();

			if (isset($filePath)) {
				Storage::disk('public')->delete($filePath);
			}

			return redirect()->back()->with('error', $e->getMessage());
		}
	}



	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$module_name = 'Spatial';
		$page_title = 'Data Spatial';
		$page_heading = 'Data Lokasi Tanam';
		$heading_class = 'fal fa-map-marked-alt';

		$kode = substr_replace($id, '_', 3, 0);
		$spatial = MasterSpatial::where('kode_spatial', $kode)
			->with('anggota')
			->first();

		$mapkey = ForeignApi::find(1);
		return view('t2024.spatial.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'mapkey', 'spatial'));
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
