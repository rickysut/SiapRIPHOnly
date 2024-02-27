<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifProduksi;
use App\Models\AjuVerifTanam;
use App\Models\DataRealisasi;
use App\Models\FotoProduksi;
use App\Models\FotoTanam;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Gate;
use Illuminate\Support\Facades\DB;

use App\Models\Lokasi;
use App\Models\MasterAnggota;
use App\Models\MasterPoktan;
use App\Models\Pks;
use App\Models\PullRiph;
use App\Models\ForeignApi;

class LokasiTanamController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($noIjin)
	{
		$no_ijin = substr_replace($noIjin, '/', 4, 0);
		$no_ijin = substr_replace($no_ijin, '.', 7, 0);
		$no_ijin = substr_replace($no_ijin, '/', 11, 0);
		$no_ijin = substr_replace($no_ijin, '/', 13, 0);
		$no_ijin = substr_replace($no_ijin, '/', 16, 0);
		$commitment = PullRiph::where('no_ijin', $no_ijin)->first();

		$pkss = Pks::withCount('lokasi')->where('no_ijin', $commitment->no_ijin)
			->get();
		$daftarPks = $pkss->map(function ($pks) use ($noIjin) {
			$pksRoute = route('verification.tanam.check.pks', [
				'noIjin' => $noIjin,
				'poktan_id' => $pks->poktan_id
			]);
			return [
				'noPks' => $pks->no_perjanjian,
				'kelompok' => $pks->masterpoktan->nama_kelompok,
				'mulaiPks' => $pks->tgl_perjanjian_start,
				'akhirPks' => $pks->tgl_perjanjian_end,
				'status' => $pks->status,
				'pksRoute' => $pksRoute,
			];
		});

		$query = Lokasi::where('no_ijin', $no_ijin)
			// ->where('polygon', '!=', null) //uncomment this line to include the condition
			->with('masterkelompok', 'masteranggota')
			->select('id', 'npwp', 'no_ijin', 'poktan_id', 'anggota_id', 'nama_lokasi', 'luas_tanam', 'volume');

		$lokasis = $query->get()->map(function ($lokasi) use ($noIjin) {
			//ubah route halaman ke daftar lokasi dari setiap petani
			$showRoute = route('verification.listLokasibyPetani', [
				'noIjin' => $noIjin,
				'lokasiId' => $lokasi->id
			]);
			return [
				'poktan'		=> $lokasi->masterkelompok->nama_kelompok,
				'anggota'		=> $lokasi->masteranggota->nama_petani,
				'jumlahTitik'	=> $lokasi->datarealisasi->count(),
				'luas_tanam'	=> $lokasi->datarealisasi->sum('luas_lahan'),
				'volume'		=> $lokasi->datarealisasi->sum('volume'),
				'show'			=> $showRoute,
			];
		});

		$realisasis = DataRealisasi::where('no_ijin', $commitment->no_ijin)
			->get();
		$dataRealisasi = $realisasis->map(function ($realisasi) {
			return [
				'mulai_ijin' => $realisasi->commitment->tgl_ijin,
				'akhir_ijin' => $realisasi->commitment->tgl_akhir,
				'kelompok' => $realisasi->masterkelompok->nama_kelompok,
				'no_perjanjian' => $realisasi->pks->no_perjanjian,
				'mulai_perjanjian' => $realisasi->pks->tgl_perjanjian_start,
				'akhir_perjanjian' => $realisasi->pks->tgl_perjanjian_end,
				'anggota' => $realisasi->masteranggota->nama_petani,
				'mulai_tanam' => $realisasi->mulai_tanam,
				'akhir_tanam' => $realisasi->akhir_tanam,
				'luas_tanam' => $realisasi->luas_lahan,
				'mulai_panen' => $realisasi->mulai_panen,
				'akhir_panen' => $realisasi->akhir_panen,
				'volume' => $realisasi->volume,
				'lokasi' => $realisasi->nama_lokasi,
			];
		});
		$data = [
			'daftarPks' => $daftarPks,
			'lokasis' => $lokasis,
			'datarealisasi' => $dataRealisasi,
		];
		return response()->json($data);
	}

	public function listLokasibyPetani($noIjin, $lokasiId)
	{
		$module_name = 'Verifikasi Data';
		$page_title = 'Verifikasi Data Lokasi';
		$page_heading = 'Pemeriksaan Data Tanam dan Produksi';
		$heading_class = 'fal fa-ballot-check';

		$no_ijin = substr_replace($noIjin, '/', 4, 0);
		$no_ijin = substr_replace($no_ijin, '.', 7, 0);
		$no_ijin = substr_replace($no_ijin, '/', 11, 0);
		$no_ijin = substr_replace($no_ijin, '/', 13, 0);
		$no_ijin = substr_replace($no_ijin, '/', 16, 0);

		$ajuVerifTanam = AjuVerifTanam::where('no_ijin', $no_ijin)->firstOrFail()->id;
		$lokasi = Lokasi::find($lokasiId);
		$dataRealisasi = DataRealisasi::where('no_ijin', $no_ijin)
			->where('lokasi_id', $lokasiId)
			->get();

		$pks = $lokasi->pks->no_perjanjian;
		$poktan = $lokasi->masterkelompok->nama_kelompok;
		$anggota = $lokasi->masteranggota->nama_petani;
		$luasAwal = $lokasi->masteranggota->luas_lahan;

		// dd($pks, $poktan, $anggota, $luasAwal, $dataRealisasi);
		return view('admin.verifikasi.listLokasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'lokasi', 'pks', 'poktan', 'anggota', 'luasAwal', 'dataRealisasi', 'ajuVerifTanam'));
	}

	public function showLocation($id)
	{
		$module_name = 'Verifikasi Data';
		$page_title = 'Verifikasi Data Lokasi';
		$page_heading = 'Pemeriksaan Data Tanam dan Produksi';
		$heading_class = 'fal fa-ballot-check';

		$mapkey = ForeignApi::find(1);

		$lokasi = DataRealisasi::findOrFail($id);
		$noIjin = $lokasi->no_ijin;
		$realNpwp = $lokasi->npwp_company;
		$pks = $lokasi->pks->no_perjanjian;

		$fotoTanams = FotoTanam::where('realisasi_id', $id)->get();
		$fotoProduksis = FotoProduksi::where('realisasi_id', $id)->get();

		return view('admin.verifikasi.locationcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'lokasi', 'noIjin', 'realNpwp', 'pks', 'fotoTanams', 'fotoProduksis' ,'mapkey'));
	}
}
