<?php

namespace App\Http\Controllers\Wilayah;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SimeviTrait;
use Illuminate\Http\Request;
use App\Models\MasterProvinsi;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterDesa;
use Illuminate\Support\Facades\Http;

class GetWilayahController extends Controller
{
	use SimeviTrait;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getAllProvinsi()
	{
		// Ambil data dari API BPS
		$responseProvinsi = Http::get('https://sig.bps.go.id/rest-bridging/getwilayah?level=provinsi&parent=0');
		$provincesJson = $responseProvinsi->json();

		// Ambil data lokal dari database termasuk yang telah dihapus (soft-deleted)
		$localProvinces = MasterProvinsi::select('provinsi_id', 'nama', 'kode_dagri')->get()->toArray();

		// Ubah data API BPS menjadi format yang bisa dibandingkan
		$foreignProvinces = collect($provincesJson)->map(function ($province) {
			return [
				'provinsi_id' => $province['kode_bps'],
				'nama' => $province['nama_bps'],
				'kode_dagri' => $province['kode_dagri'],
			];
		})->sortBy('provinsi_id')->values()->toArray();

		// Sort data lokal untuk memastikan perbandingan yang akurat
		$localProvinces = collect($localProvinces)->sortBy('provinsi_id')->values()->toArray();

		// Menggunakan array_udiff_assoc untuk membandingkan array
		$inApiNotInLocal = array_udiff_assoc(
			$foreignProvinces,
			$localProvinces,
			function ($a, $b) {
				return strcmp(json_encode($a), json_encode($b));
			}
		);

		$inLocalNotInApi = array_udiff_assoc(
			$localProvinces,
			$foreignProvinces,
			function ($a, $b) {
				return strcmp(json_encode($a), json_encode($b));
			}
		);

		// Bandingkan kedua set data
		$status = (count($inApiNotInLocal) > 0 || count($inLocalNotInApi) > 0) ? 'Need Update' : 'clear';

		return response()->json([
			'status' => $status,
			'data' => $foreignProvinces,
		]);
	}


	public function getKabupatenByProvinsi($provinsiId)
	{
		$responseKabupaten = Http::get("https://sig.bps.go.id/rest-bridging/getwilayah?level=kabupaten&parent=$provinsiId");
		$kabupatensJson = $responseKabupaten->json();

		$kabupatens = collect($kabupatensJson)->map(function ($kabupaten) use ($provinsiId) {
			return [
				'provinsi_id' => $provinsiId,
				'kabupaten_id' => $kabupaten['kode_bps'],
				'nama_kab' => $kabupaten['nama_bps'],
			];
		})->sortBy('nama_kab')->values()->all();

		return response()->json([
			'data' => $kabupatens,
		]);
	}

	public function getKecamatanByKabupaten($kabupatenId)
	{
		$responseKecamatan = Http::get("https://sig.bps.go.id/rest-bridging/getwilayah?level=kecamatan&parent=$kabupatenId");
		$kecamatansJson = $responseKecamatan->json();

		$kecamatans = collect($kecamatansJson)->map(function ($kecamatan) use ($kabupatenId) {
			return [
				'kabupaten_id' => $kabupatenId,
				'kecamatan_id' => $kecamatan['kode_bps'],
				'nama_kecamatan' => $kecamatan['nama_bps'],
			];
		})->sortBy('nama_kecamatan')->values()->all();

		return response()->json([
			'data' => $kecamatans,
		]);
	}

	public function getDesaBykecamatan($kecamatanId)
	{
		$responseDesa = Http::get("https://sig.bps.go.id/rest-bridging/getwilayah?level=desa&parent=$kecamatanId");
		$desaJson = $responseDesa->json();

		$kelurahans = collect($desaJson)->map(function ($kelurahan) use ($kecamatanId) {
			return [
				'kecamatan_id' => $kecamatanId,
				'kelurahan_id' => $kelurahan['kode_bps'],
				'nama_desa' => $kelurahan['nama_bps'],
			];
		})->sortBy('nama_desa')->values()->all();

		return response()->json([
			'data' => $kelurahans,
		]);


		$kelurahans  = MasterDesa::select('kecamatan_id', 'kelurahan_id', 'nama_desa')
			->where('kecamatan_id', $kecamatanId)
			->orderBy('nama_desa', 'ASC')->get();
		return response()->json([
			'data' => $kelurahans,
		]);
	}

	public function getDesaById($id)
	{
		$desa = MasterDesa::where('kelurahan_id', $id)->first();

		$data = $desa ? [
			'kelurahan_id' => $desa->kelurahan_id,
			'nama_desa' => $desa->nama_desa,
		] : [
			'kelurahan_id' => 'Kode tidak terdaftar',
			'nama_desa' => 'Desa tidak terdaftar',
		];

		return response()->json($data);
	}

	public function getKecById($id)
	{
		$kecamatan = MasterKecamatan::where('kecamatan_id', $id)->first();

		$data = $kecamatan ? [
			'kecamatan_id' => $kecamatan->kecamatan_id,
			'nama_kecamatan' => $kecamatan->nama_kecamatan,
		] : [
			'kecamatan_id' => 'Kode tidak terdaftar',
			'nama_kecamatan' => 'Kecamatan tidak terdaftar',
		];

		return response()->json($data);
	}

	public function getKabById($id)
	{
		$kabupaten = MasterKabupaten::where('kabupaten_id', $id)->first();

		$data = $kabupaten ? [
			'kabupaten_id' => $kabupaten->kabupaten_id,
			'nama_kab' => $kabupaten->nama_kab,
		] : [
			'kabupaten_id' => 'Kode tidak terdaftar',
			'nama_kab' => 'Kabupaten tidak terdaftar',
		];

		return response()->json($data);
	}

	public function getProvById($id)
	{
		$provinsi = MasterProvinsi::where('provinsi_id', $id)->first();

		$data = $provinsi ? [
			'provinsi_id' => $provinsi->provinsi_id,
			'nama' => $provinsi->nama,
		] : [
			'provinsi_id' => 'Kode tidak terdaftar',
			'nama' => 'Provinsi tidak terdaftar',
		];

		return response()->json($data);
	}
}
