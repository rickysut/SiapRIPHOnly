<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models2024\MasterSpatial;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class SpatialController extends Controller
{
	public function getLokasiTanam(Request $request)
	{
		// About information
		$about = [
			'Daftar Kode Spatial Wajib Tanam Simethris 4beta',
			'Parameter: ?s=status&p=provinsi_id&b=kabupaten_id&c=kecamatan_id&l=kelurahan_id&k=kode_poktan',
			'status dimaksud adalah status lahan.',
			'wilayah dimaksud adalah Wilayah di mana lahan tersebut berada.'
		];

		try {
			// Validate request parameters
			$validated = $request->validate([
				's' => 'nullable|integer',
				'p' => 'nullable|integer',
				'b' => 'nullable|integer',
				'c' => 'nullable|integer',
				'l' => 'nullable|integer',
				'k' => 'nullable|string',
			]);

			$s = $validated['s'] ?? null;
			$p = $validated['p'] ?? null;
			$b = $validated['b'] ?? null;
			$c = $validated['c'] ?? null;
			$l = $validated['l'] ?? null;
			$k = $validated['k'] ?? null;

			// Build the query
			$query = MasterSpatial::select(
				'kode_spatial',
				'ktp_petani',
				'nama_petani',
				'provinsi_id',
				'kabupaten_id',
				'kecamatan_id',
				'kelurahan_id',
				'luas_lahan',
				'status',
				'is_active'
			)->where('is_active', 1);

			if (!is_null($s)) {
				$query->where('status', $s);
			}
			if (!is_null($p)) {
				$query->where('provinsi_id', $p);
			}
			if (!is_null($b)) {
				$query->where('kabupaten_id', $b);
			}
			if (!is_null($c)) {
				$query->where('kecamatan_id', $c);
			}
			if (!is_null($l)) {
				$query->where('kelurahan_id', $l);
			}
			if (!is_null($k)) {
				$query->where('kode_poktan', $k); // Updated to filter by 'kode_poktan'
			}

			// Paginate the results with a maximum of 10 records per page
			$spatials = $query->paginate(min($request->input('per_page', 10), 10));

			// Format the data
			$formattedSpatials = $spatials->getCollection()->map(function ($spatial) {
				return [
					'kode_spatial' => $spatial->kode_spatial,
					'luas_lahan' => $spatial->luas_lahan,
					'status' => $spatial->status,
					'ktp_petani' => $spatial->ktp_petani,
					'nama_petani' => $spatial->anggota->nama_petani,
					'kode_poktan' => $spatial->anggota->masterpoktan->kode_poktan ?? null,
					'nama_kelompok' => $spatial->anggota->masterpoktan->nama_kelompok ?? null,
					'wilayah' => [
						'provinsi_id' => $spatial->provinsi_id,
						'kabupaten_id' => $spatial->kabupaten_id,
						'kecamatan_id' => $spatial->kecamatan_id,
						'kelurahan_id' => $spatial->kelurahan_id,
					]
				];
			});

			// Return the paginated data along with the "about" information
			return response()->json([
				'success' => true,
				'Status' => 'SUCCESS',
				'Tentang' => $about,
				'data_spatial' => $formattedSpatials,
				'pagination' => [
					'total' => $spatials->total(),
					'per_page' => $spatials->perPage(),
					'current_page' => $spatials->currentPage(),
					'last_page' => $spatials->lastPage(),
					'next_page_url' => $spatials->nextPageUrl(),
					'prev_page_url' => $spatials->previousPageUrl(),
				]
			]);
		}catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'error' => $e->getMessage(),
				'message' => 'Failed to update spatial data.',
				'Status' => 'FAIL',
				'Tentang' => $about,
				'data_spatial' => [],
				'pagination' => [
					'total' => 0,
					'per_page' => null,
					'current_page' => null,
					'last_page' => null,
					'next_page_url' => null,
					'prev_page_url' => null,
				]
			]);
		}
	}


	/**
	 * Batch Update Status Lokasi Tanam
	 */
	public function batchUpdateStatusLokasi(Request $request)
	{
		// Validasi input
		$validated = $request->validate([
			'status' => 'required|integer',
			'kode_spatial' => 'required|array',
			'kode_spatial.*' => 'required|string', // atau sesuaikan dengan tipe data kode_spatial Anda
		]);

		$status = $validated['status'];
		$kodeSpatialList = $validated['kode_spatial'];

		DB::beginTransaction();

		try {
			foreach ($kodeSpatialList as $kodeSpatial) {
				// Lakukan update status untuk setiap kode_spatial
				$spatial = MasterSpatial::where('kode_spatial', $kodeSpatial)->first();

				if ($spatial) {
					$spatial->status = $status;
					$spatial->save();
				}
			}

			DB::commit();

			return response()->json(['success' => true]);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['success' => false, 'message' => 'Failed to update spatial data.', 'error' => $e->getMessage()], 500);
		}
	}

	/**
	 * Update Status Lokasi Tanam
	 */
	public function updateStatusLokasi(Request $request, $kodeSpatial)
	{
		$validated = $request->validate([
			'status' => 'required|integer',
		]);

		$spatial = MasterSpatial::where('kode_spatial', $kodeSpatial)->first();

		if ($spatial) {
			$spatial->status = $validated['status'];
			$spatial->save();

			return response()->json(['success' => true]);
		} else {
			return response()->json(['success' => false, 'message' => 'Spatial data not found.'], 404);
		}
	}
}
