<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;
use App\Models2024\DataRealisasi;
use App\Models2024\Lokasi;
use App\Models2024\MasterAnggota;
use App\Models2024\MasterPoktan;
use App\Models2024\MasterSpatial;
use App\Models2024\Pks;
use App\Models2024\PullRiph;
use App\Models\MasterDesa;
use App\Models\MasterKecamatan;
use Illuminate\Http\Request;
use Svg\Tag\Rect;

class DataFeederController extends Controller
{
	public function getPksById($id)
	{
		$pks = Pks::select('id', 'npwp', 'no_ijin', 'no_perjanjian', 'tgl_perjanjian_start', 'tgl_perjanjian_end', 'varietas_tanam', 'periode_tanam', 'berkas_pks')
			->with(['varietas' => function ($query) {
				$query->select('id', 'nama_varietas');
			}])
			->find($id);

		$commitment = PullRiph::where('no_ijin', $pks->no_ijin)->first();
		$npwp = str_replace(['.', '-'], '', $commitment->npwp);
		$periodetahun = $commitment->periodetahun;

		$linkBerkas = $pks->berkas_pks ? asset('storage/uploads/' . $npwp . '/' . $periodetahun . '/' . $pks->berkas_pks) : null;

		if ($pks) {
			$data = $pks->toArray();
			$data['linkBerkas'] = $linkBerkas;

			return response()->json($data);
		} else {
			return response()->json([], 404);
		}
	}

	public function getPksByIjin(Request $request, $noIjin)
	{
		$noIjin = substr($noIjin, 0, 4) . '/' .
			substr($noIjin, 4, 2) . '.' .
			substr($noIjin, 6, 3) . '/' .
			substr($noIjin, 9, 1) . '/' .
			substr($noIjin, 10, 2) . '/' .
			substr($noIjin, 12, 4);

		$commitment = PullRiph::where('no_ijin', $noIjin)->first();
		$query = Pks::query()
			->select('id', 'no_ijin', 'poktan_id', 'nama_poktan','no_perjanjian', 'tgl_perjanjian_start', 'tgl_perjanjian_end', 'varietas_tanam', 'periode_tanam', 'berkas_pks')
			->where('no_ijin', $commitment->no_ijin)
			->withCount('lokasi')
			->withSum('lokasi', 'luas_lahan');

		// Paginasi sesuai permintaan DataTables
		$draw = $request->input('draw');
		$length = $request->input('length');
		$start = $request->input('start');

		if ($length <= 0) {
			$length = 10; // Atur ke nilai default jika length <= 0
		}

		$data = $query->paginate($length, ['*'], 'page', max(1, $start / $length + 1));
		$items = $data->items();
		foreach ($items as $item) {
			if (is_null($item->tgl_perjanjian_start) || is_null($item->varietas_tanam) || is_null($item->berkas_pks)) {
				$item->status = null;
			} else {
				$item->status = 'Filled'; // Atur status yang sesuai jika tidak null
			}
		}

		return response()->json([
			"draw" => intval($draw),
			"recordsTotal" => $data->total(),
			"recordsFiltered" => $data->total(),
			"data" => $data->items(),
		]);
	}

	public function getLokasiByPks(Request $request, $noIjin, $poktanId)
	{
		$noIjin = substr($noIjin, 0, 4) . '/' .
			substr($noIjin, 4, 2) . '.' .
			substr($noIjin, 6, 3) . '/' .
			substr($noIjin, 9, 1) . '/' .
			substr($noIjin, 10, 2) . '/' .
			substr($noIjin, 12, 4);

		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');

		$pks = Lokasi::select('id', 'no_ijin', 'poktan_id', 'kode_spatial', 'nama_petani', 'ktp_petani')->where('no_ijin', $noIjin)
        ->where('poktan_id', $poktanId)
		->with('datarealisasi', 'spatial.anggota')
		->get();

		$query = $pks->map(function ($item) {
			$datarealisasi = $item->datarealisasi;
			$spatial = $item->spatial;
    		$masteranggota = $spatial ? $spatial->anggota : null;
			return [
				'id' => $item->id,
				'nama_kelompok' => $item->nama_poktan,
				'kode_spatial' => $item->kode_spatial,
				'ktp_petani' => $item->ktp_petani,
				'nama_petani' => $item->nama_petani,
				'spatial_petani' => $masteranggota ? $masteranggota->nama_petani:null,
				'spatial_ktp' => $masteranggota ? $masteranggota->ktp_petani : null,
				'luas_tanam' => $datarealisasi ? $datarealisasi->luas_tanam : 0,
				'tgl_tanam' => $datarealisasi ? $datarealisasi->mulai_tanam : null,
				'volume_panen' => $datarealisasi ? $datarealisasi->volume : 0,
				'tgl_panen' => $datarealisasi ? $datarealisasi->mulai_panen : null,
			];
		});

		// dd($pks);

		return response()->json([
			'draw' => $draw,
			// 'recordsTotal' => $totalRecords,
			// 'recordsFiltered' => $filteredRecords,
			'data' => $query,
		]);
	}

	public function getSpatialByKecamatan(Request $request, $kecId)
	{
		$spatials = MasterSpatial::select('id', 'kode_spatial', 'kecamatan_id')->where('kecamatan_id', $kecId)->get();
		return response()->json($spatials);
	}

	public function getSpatialByKode(Request $request, $spatial)
	{
		$spatial = substr($spatial, 0, 3) . '-' .
			substr($spatial, 3, 3) . '-' .
			substr($spatial, 6, 4);

		$lokasi = MasterSpatial::select('id', 'kode_spatial', 'latitude', 'longitude', 'polygon', 'luas_lahan', 'ktp_petani')->where('kode_spatial', $spatial)->first();
		$anggota = MasterAnggota::select('id', 'ktp_petani', 'nama_petani')->where('ktp_petani', $lokasi->ktp_petani)->first();

		$data = [
			'lokasi_id' => $lokasi->id,
			'kode_spatial' => $lokasi->kode_spatial,
			'latitude' => $lokasi->latitude,
			'longitude' => $lokasi->longitude,
			'polygon' => $lokasi->polygon,
			'luas_lahan' => $lokasi->luas_lahan,
			'ktp_petani' => $lokasi->ktp_petani,
			'nama_petani' => $anggota->nama_petani,
		];

		return response()->json($data);
	}

	public function getAllSpatials(Request $request)
	{
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');

		$query = MasterSpatial::select('id', 'kode_spatial', 'luas_lahan', 'ktp_petani', 'provinsi_id', 'kabupaten_id')
			->with([
				'anggota:id,poktan_id,nama_petani,ktp_petani',
				'provinsi:provinsi_id,nama',
				'kabupaten:kabupaten_id,nama_kab',
				'jadwal:kode_spatial,awal_masa,akhir_masa',
			]);

		if ($request->has('search') && !empty($request->input('search')['value'])) {
			$searchValue = $request->input('search')['value'];
			$query->where(function ($subQuery) use ($searchValue) {
				$subQuery->where('kode_spatial', 'like', '%' . $searchValue . '%')
					->orWhere('luas_lahan', 'like', '%' . $searchValue . '%')
					->orWhere('ktp_petani', 'like', '%' . $searchValue . '%')
					->orWhereHas('anggota', function ($subSubQuery) use ($searchValue) {
						$subSubQuery->where('nama_petani', 'like', '%' . $searchValue . '%');
					})
					->orWhereHas('provinsi', function ($subSubQuery) use ($searchValue) {
						$subSubQuery->where('nama', 'like', '%' . $searchValue . '%');
					})
					->orWhereHas('kabupaten', function ($subSubQuery) use ($searchValue) {
						$subSubQuery->where('nama_kab', 'like', '%' . $searchValue . '%');
					});
			});
		}

		if ($request->has('order')) {
			$orderColumn = $request->input('order')[0]['column'];
			$orderDirection = $request->input('order')[0]['dir'];
			$columnName = $request->input('columns')[$orderColumn]['data'];
			$query->orderBy($columnName, $orderDirection);
		}
		$totalRecords = MasterSpatial::count();

		$filteredRecords = $query->count();

		$spatials = $query->offset($start)->limit($length)->get();

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $spatials,
		]);
	}

	public function getAllPoktan(Request $request)
	{
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');

		$data = MasterPoktan::with([
			'provinsi',
			'kabupaten',
			'kecamatan',
			'desa',
		])->get();

		$query = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'nama_kelompok' => $item->nama_kelompok,
				'nama_pimpinan' => $item->nama_pimpinan,
				'hp_pimpinan' => $item->hp_pimpinan,
				'provinsi_id' => $item->provinsi_id,
				'nama_provinsi' => $item->provinsi ? $item->provinsi->nama : null,
				'kabupaten_id' => $item->kabupaten_id,
				'nama_kabupaten' => $item->kabupaten ? $item->kabupaten->nama_kab : null,
				'kecamatan_id' => $item->kecamatan_id,
				'nama_kecamatan' => $item->kecamatan ? $item->kecamatan->nama_kecamatan : null,
				'kelurahan_id' => $item->kelurahan_id,
				'nama_desa' => $item->desa ? $item->desa->nama_desa : null,
			];
		});

		if ($searchValue) {
			$query = $query->filter(function ($item) use ($searchValue) {
				return strpos(strtolower($item['nama_kelompok']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['nama_pimpinan']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['hp_pimpinan']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['nama_provinsi']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['nama_kabupaten']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['nama_kecamatan']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['nama_desa']), strtolower($searchValue)) !== false;
			});
		}

		if ($request->has('order')) {
			$orderColumn = $request->input('order')[0]['column'];
			$orderDirection = $request->input('order')[0]['dir'];
			$columnName = $request->input('columns')[$orderColumn]['data'];

			// Gunakan switch case atau if else untuk menentukan kolom pengurutan
			switch ($columnName) {
				case 'nama_kelompok':
					$query = $query->sortBy('nama_kelompok');
					break;
				case 'nama_pimpinan':
					$query = $query->sortByDesc('nama_pimpinan');
					break;
				case 'kontak':
					$query = $query->sortByDesc('kontak');
					break;
				case 'nama_provinsi':
					$query = $query->sortByDesc('nama_provinsi');
					break;
				case 'nama_kabupaten':
					$query = $query->sortByDesc('nama_kabupaten');
					break;
				case 'nama_kecamatan':
					$query = $query->sortByDesc('nama_kecamatan');
					break;
				case 'nama_desa':
					$query = $query->sortByDesc('nama_desa');
					break;
			}
		}

		$totalRecords = $data->count();
		$filteredRecords = $query->count();

		$poktans = $query->slice($start)->take($length)->values();

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $poktans,
		]);
	}

	public function getAllCpcl(Request $request)
	{
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');

		$data = MasterAnggota::with([
			'provinsi' => function($query) {
				$query->select('provinsi_id', 'nama');
			},
			'kabupaten' => function($query) {
				$query->select('kabupaten_id', 'nama_kab');
			},
			'kecamatan' => function($query) {
				$query->select('kecamatan_id', 'nama_kecamatan');
			},
			'desa' => function($query) {
				$query->select('kelurahan_id', 'nama_desa');
			},
			'spatial'
		])->get();

		$query = $data->map(function ($item) {
			$spatialCount = $item->spatial->count();
			$spatialSum = $item->spatial->sum('luas_lahan');
			return [
				'id' => $item->id,
				'nama_petani' => $item->nama_petani,
				'ktp_petani' => $item->ktp_petani,
				'kontak' => $item->hp_petani,
				'provinsi_id' => $item->provinsi_id,
				'nama_provinsi' => $item->provinsi ? $item->provinsi->nama : null,
				'kabupaten_id' => $item->kabupaten_id,
				'nama_kabupaten' => $item->kabupaten ? $item->kabupaten->nama_kab : null,
				'kecamatan_id' => $item->kecamatan_id,
				'nama_kecamatan' => $item->kecamatan ? $item->kecamatan->nama_kecamatan : null,
				'kelurahan_id' => $item->kelurahan_id,
				'nama_desa' => $item->desa ? $item->desa->nama_desa : null,
				'jumlah_spatial' => $spatialCount,
				'total_luas' => $spatialSum,
			];
		});

		if ($searchValue) {
			$query = $query->filter(function ($item) use ($searchValue) {
				return strpos(strtolower($item['nama_petani']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['ktp_petani']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['kontak']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['nama_provinsi']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['nama_kabupaten']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['nama_kecamatan']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['nama_desa']), strtolower($searchValue)) !== false;
					strtolower($item['jumlah_spatial']) == strtolower($searchValue);
			});
		}

		if ($request->has('order')) {
			$orderColumn = $request->input('order')[0]['column'];
			$orderDirection = $request->input('order')[0]['dir'];
			$columnName = $request->input('columns')[$orderColumn]['data'];

			// Gunakan switch case atau if else untuk menentukan kolom pengurutan
			switch ($columnName) {
				case 'nama_petani':
					$query = $query->sortBy('nama_petani');
					break;
				case 'ktp_petani':
					$query = $query->sortByDesc('ktp_petani');
					break;
				case 'kontak':
					$query = $query->sortByDesc('kontak');
					break;
				case 'nama_provinsi':
					$query = $query->sortByDesc('nama_provinsi');
					break;
				case 'nama_kabupaten':
					$query = $query->sortByDesc('nama_kabupaten');
					break;
				case 'nama_kecamatan':
					$query = $query->sortByDesc('nama_kecamatan');
					break;
				case 'nama_desa':
					$query = $query->sortByDesc('nama_desa');
					break;
				case 'jumlah_spatial':
					$query = $query->sortByDesc('nama_desa');
					break;
			}
		}

		$totalRecords = $data->count();
		$filteredRecords = $query->count();

		$poktans = $query->slice($start)->take($length)->values();

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $poktans,
		]);
	}

	public function getAllCpclByKec(Request $request, $kecId)
	{
		$cpcls = MasterAnggota::select('ktp_petani', 'nama_petani', 'kecamatan_id')->where('kecamatan_id', $kecId)->get();
		$data = [];
		foreach ($cpcls as $cpcl) {
			$data[] = [
				'ktp_petani' => $cpcl->ktp_petani,
				'nama_petani' => $cpcl->nama_petani,
				'kecamatan_id' => $cpcl->kecamatan_id
			];
		}

		return response()->json($data);
	}

	public function updateOrCreateDesa()
	{
		// Path ke file JSON
		$jsonFilePath = storage_path('app/public/output_file.json');

		// Baca file JSON
		if (!file_exists($jsonFilePath)) {
			return response()->json(['error' => 'File JSON tidak ditemukan.'], 404);
		}

		$jsonData = file_get_contents($jsonFilePath);
		$desas = json_decode($jsonData, true);

		foreach ($desas as $desa) {
			MasterDesa::updateOrCreate(
				['kelurahan_id' => $desa['kelurahan_id']],
				[
					'kecamatan_id' => $desa['kecamatan_id'],
					'nama_desa' => strtoupper($desa['nama_desa']), // Mengubah nama desa menjadi huruf kapital
				]
			);
		}


		return response()->json(['success' => 'Data desa berhasil diupdate atau dibuat.'], 200);
	}

	public function getCpclByNik(Request $request, $nik)
	{
		$nikData = MasterAnggota::select('ktp_petani', 'nama_petani')->where('ktp_petani', $nik)->first();

		$data = $nikData ? [
			'ktp_petani' => $nikData->ktp_petani,
			'nama_petani' => $nikData->nama_petani,
		] : [
			'ktp_petani' => 'KTP tidak terdaftar',
			'nama_petani' => 'KTP tidak terdaftar',
		];
		return response()->json($data);
	}
}
