<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Models\MasterProvinsi;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterDesa;

class MasterWilayahController extends Controller
{
	protected $module_name;

	public function __construct()
	{
		$this->module_name = 'Master Data Wilayah';
	}
	public function index()
	{
		$module_name = $this->module_name;
		$page_title = 'Daftar Wilayah';
		$page_heading = 'Daftar Wilayah';
		$heading_class = 'fal fa-globe-asia';
		$page_desc = 'Kode Wilayah Kerja Statistik pada tahun 2020 Semester 2. (Sumber: Basis Data BPS).';

		return view('t2024.masterwilayah.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'page_desc'));
	}
	public function updateFromBPS()
	{
		// Ambil data provinsi dari API BPS
		$responseProvinsi = Http::get('https://sig.bps.go.id/rest-bridging/getwilayah?level=provinsi&parent=0');
		$dataProvinsi = $responseProvinsi->json();

		if ($responseProvinsi->successful() && is_array($dataProvinsi)) {
			// Ambil semua provinsi yang ada di database
			$existingProvinces = MasterProvinsi::all()->keyBy('provinsi_id');

			DB::beginTransaction();

			try {
				// Proses pembaruan atau penambahan data provinsi
				foreach ($dataProvinsi as $provinsi) {
					$provinsiId = $provinsi['kode_bps'];
					$provinsiName = $provinsi['nama_bps'];

					if ($existingProvinces->has($provinsiId)) {
						if ($existingProvinces[$provinsiId]->nama != $provinsiName) {
							$existingProvinces[$provinsiId]->update(['nama' => $provinsiName]);
						}
						$existingProvinces->forget($provinsiId);
					} else {
						MasterProvinsi::create([
							'provinsi_id' => $provinsiId,
							'nama' => $provinsiName
						]);
					}

					// Ambil data kabupaten dari API BPS berdasarkan provinsiId
					$responseKabupaten = Http::get("https://sig.bps.go.id/rest-bridging/getwilayah?level=kabupaten&parent=$provinsiId");
					$dataKabupaten = $responseKabupaten->json();

					if ($responseKabupaten->successful() && is_array($dataKabupaten)) {
						// Ambil semua kabupaten yang ada di database untuk provinsi ini
						$existingKabupaten = MasterKabupaten::where('provinsi_id', $provinsiId)->get()->keyBy('kabupaten_id');

						foreach ($dataKabupaten as $kabupaten) {
							$kabupatenId = $kabupaten['kode_bps'];
							$kabupatenName = $kabupaten['nama_bps'];

							if ($existingKabupaten->has($kabupatenId)) {
								if ($existingKabupaten[$kabupatenId]->nama != $kabupatenName) {
									$existingKabupaten[$kabupatenId]->update(['nama' => $kabupatenName]);
								}
								$existingKabupaten->forget($kabupatenId);
							} else {
								MasterKabupaten::create([
									'provinsi_id' => $provinsiId,
									'kabupaten_id' => $kabupatenId,
									'nama_kab' => $kabupatenName
								]);
							}

							// Ambil data kecamatan dari API BPS berdasarkan kabupatenId
							// $responseKecamatan = Http::get("https://sig.bps.go.id/rest-bridging/getwilayah?level=kecamatan&parent=$kabupatenId");
							// $dataKecamatan = $responseKecamatan->json();

							// if ($responseKecamatan->successful() && is_array($dataKecamatan)) {
							// 	// Ambil semua kecamatan yang ada di database untuk kabupaten ini
							// 	$existingKecamatan = MasterKecamatan::where('kabupaten_id', $kabupatenId)->get()->keyBy('kecamatan_id');

							// 	foreach ($dataKecamatan as $kecamatan) {
							// 		$kecamatanId = $kecamatan['kode_bps'];
							// 		$kecamatanName = $kecamatan['nama_bps'];

							// 		if ($existingKecamatan->has($kecamatanId)) {
							// 			if ($existingKecamatan[$kecamatanId]->nama != $kecamatanName) {
							// 				$existingKecamatan[$kecamatanId]->update(['nama' => $kecamatanName]);
							// 			}
							// 			$existingKecamatan->forget($kecamatanId);
							// 		} else {
							// 			MasterKecamatan::create([
							// 				'kabupaten_id' => $kabupatenId,
							// 				'kecamatan_id' => $kecamatanId,
							// 				'nama_kecamatan' => $kecamatanName
							// 			]);
							// 		}

							// 		// Ambil data desa/kelurahan dari API BPS berdasarkan kecamatanId
							// 		$responseDesa = Http::get("https://sig.bps.go.id/rest-bridging/getwilayah?level=desa&parent=$kecamatanId");
							// 		$dataDesa = $responseDesa->json();

							// 		if ($responseDesa->successful() && is_array($dataDesa)) {
							// 			// Ambil semua desa/kelurahan yang ada di database untuk kecamatan ini
							// 			$existingDesa = MasterDesa::where('kecamatan_id', $kecamatanId)->get()->keyBy('desa_id');

							// 			foreach ($dataDesa as $desa) {
							// 				$desaId = $desa['kode_bps'];
							// 				$desaName = $desa['nama_bps'];

							// 				if ($existingDesa->has($desaId)) {
							// 					if ($existingDesa[$desaId]->nama != $desaName) {
							// 						$existingDesa[$desaId]->update(['nama' => $desaName]);
							// 					}
							// 					$existingDesa->forget($desaId);
							// 				} else {
							// 					MasterDesa::create([
							// 						'kecamatan_id' => $kecamatanId,
							// 						'kelurahan_id' => $desaId,
							// 						'nama_desa' => $desaName
							// 					]);
							// 				}
							// 			}

							// 			// Hapus desa/kelurahan yang tidak ada di data baru
							// 			foreach ($existingDesa as $desa) {
							// 				$desa->delete();
							// 			}
							// 		}
							// 	}

							// 	// Hapus kecamatan yang tidak ada di data baru
							// 	foreach ($existingKecamatan as $kecamatan) {
							// 		$kecamatan->delete();
							// 	}
							// }
						}

						// Hapus kabupaten yang tidak ada di data baru
						foreach ($existingKabupaten as $kabupaten) {
							$kabupaten->delete();
						}
					}
				}

				// Commit transaksi
				DB::commit();

				return response()->json(['message' => 'Data has been updated successfully.']);
			} catch (\Exception $e) {
				// Rollback transaksi jika terjadi error
				DB::rollback();
				return response()->json(['message' => 'Failed to update data.'], 500);
			}
		} else {
			return response()->json(['message' => 'Failed to fetch data from BPS API.'], 500);
		}
	}

	public function updateProvinsiFromBPS()
	{
		$response = Http::get('https://sig.bps.go.id/rest-bridging/getwilayah?level=provinsi&parent=0');
		$data = $response->json();

		if ($response->successful() && is_array($data)) {
			$existingProvinces = MasterProvinsi::all()->keyBy('provinsi_id');

			DB::beginTransaction();

			try {
				foreach ($data as $provinsi) {
					$provinsiId = $provinsi['kode_bps'];
					$provinsiDagri = $provinsi['kode_dagri'];
					$provinsiName = $provinsi['nama_bps'];

					if ($existingProvinces->has($provinsiId)) {
						if ($existingProvinces[$provinsiId]->nama != $provinsiName) {
							$existingProvinces[$provinsiId]->update(['nama' => $provinsiName]);
						}
						$existingProvinces->forget($provinsiId);
					} else {
						MasterProvinsi::create([
							'provinsi_id' => $provinsiId,
							'kode_dagri' => $provinsiDagri,
							'nama' => $provinsiName
						]);
					}
				}

				foreach ($existingProvinces as $provinsi) {
					$provinsi->delete();
				}

				DB::commit();

				return response()->json(['message' => 'Provinces table has been updated successfully.']);
			} catch (\Exception $e) {
				DB::rollback();
				return response()->json(['message' => 'Failed to update provinces table.'], 500);
			}
		} else {
			return response()->json(['message' => 'Failed to fetch data from BPS API.'], 500);
		}
	}

	public function updateKabupatenFromBPS($provinsiId)
	{
		$response = Http::get("https://sig.bps.go.id/rest-bridging/getwilayah?level=kabupaten&parent=$provinsiId");
		$data = $response->json();

		if ($response->successful() && is_array($data)) {
			$existingKabupaten = MasterKabupaten::where('provinsi_id', $provinsiId)->get()->keyBy('kabupaten_id');

			DB::beginTransaction();

			try {
				foreach ($data as $kabupaten) {
					$kabupatenId = $kabupaten['kode_bps'];
					$kabupatenDagri = $kabupaten['kode_dagri'];
					$kabupatenName = $kabupaten['nama_bps'];

					if ($existingKabupaten->has($kabupatenId)) {
						if ($existingKabupaten[$kabupatenId]->nama_kab != $kabupatenName) {
							$existingKabupaten[$kabupatenId]->update(['nama_kab' => $kabupatenName]);
						}
						$existingKabupaten->forget($kabupatenId);
					} else {
						MasterKabupaten::create([
							'provinsi_id' => $provinsiId,
							'kabupaten_id' => $kabupatenId,
							'kode_dagri' => $kabupatenDagri,
							'nama_kab' => $kabupatenName
						]);
					}
				}

				foreach ($existingKabupaten as $kabupaten) {
					$kabupaten->delete();
				}

				DB::commit();

				// Return a JSON response
				return response()->json(['success' => true, 'message' => 'Daftar Kabupaten berhasil diperbarui.']);
			} catch (\Exception $e) {
				DB::rollback();
				return response()->json(['success' => false, 'message' => 'Gagal memperbarui Daftar Kabupaten: ' . $e->getMessage()]);
			}
		} else {
			return response()->json(['success' => false, 'message' => 'Gagal memperoleh data dari BPS.']);
		}
	}


	public function updateKecamatanFromBPS($provinsiId)
	{
		// Mengirim permintaan GET ke endpoint BPS dengan provinsiId
		$response = Http::get("https://sig.bps.go.id/rest-bridging/getwilayah?level=kecamatan&parent=$provinsiId");
		$data = $response->json();

		if ($response->successful() && is_array($data)) {
			// Mulai transaksi database
			DB::beginTransaction();

			try {
				foreach ($data as $kecamatan) {
					// Memecah kode_bps untuk mendapatkan kabupaten_id
					$kabupatenId = substr($kecamatan['kode_bps'], 0, 4);

					// Memperbarui atau membuat data kecamatan
					DB::table('data_kecamatans')->updateOrInsert(
						['kecamatan_id' => $kecamatan['kode_bps']],
						[
							'kabupaten_id' => $kabupatenId,
							'nama_kecamatan' => $kecamatan['nama_bps'],
							'kode_dagri' => $kecamatan['kode_dagri']
						]
					);
				}

				// Commit transaksi jika semua berjalan lancar
				DB::commit();

				// Return a JSON response
				return response()->json(['success' => true, 'message' => 'Daftar Kecamatan berhasil diperbarui.']);
			} catch (\Exception $e) {
				DB::rollback();
				return response()->json(['success' => false, 'message' => 'Gagal memperbarui Daftar Kecamatan: ' . $e->getMessage()]);
			}
		} else {
			return response()->json(['success' => false, 'message' => 'Gagal memperoleh data dari BPS.']);
		}
	}

	public function updateDesaFromBPS($provinsiId)
	{
		// Mengirim permintaan GET ke endpoint BPS dengan provinsiId
		$response = Http::get("https://sig.bps.go.id/rest-bridging/getwilayah?level=desa&parent=$provinsiId");
		$data = $response->json();

		if ($response->successful() && is_array($data)) {
			// Memulai transaksi database
			DB::beginTransaction();

			try {
				foreach ($data as $desa) {
					// Mendapatkan kecamatan_id dari 7 digit pertama kode_bps
					$kecamatanId = substr($desa['kode_bps'], 0, 7);

					// Memperbarui atau menambahkan data desa
					DB::table('data_desas')->updateOrInsert(
						['kelurahan_id' => $desa['kode_bps']],
						[
							'kecamatan_id' => $kecamatanId,
							'nama_desa' => $desa['nama_bps'],
							'kode_dagri' => $desa['kode_dagri']
						]
					);
				}

				// Commit transaksi jika semua berhasil
				DB::commit();
				return response()->json(['success' => true, 'message' => "Data desa untuk provinsi ID $provinsiId berhasil diperbarui."]);
			} catch (\Exception $e) {
				// Rollback transaksi jika terjadi kesalahan
				DB::rollBack();
				return response()->json(['success' => false, 'message' => 'Gagal memerbarui data Desa: ' . $e->getMessage()]);
			}
		} else {
			return response()->json(['success' => false, 'message' => 'Gagal memperoleh data dari BPS.']);
		}
	}

	public function updateAllDesaFromBPS()
	{
		$provinsiIds = MasterProvinsi::select('provinsi_id')->pluck('provinsi_id');

		return response()->json(['provinsiIds' => $provinsiIds]);
	}
}
