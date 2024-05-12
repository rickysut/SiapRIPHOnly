<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\AjuVerifProduksi;
use App\Models\AjuVerifSkl;
use App\Models\AjuVerifTanam;
use App\Models\CommitmentBackdate;
use App\Models\AnggotaMitra;
use App\Models\Completed;
use App\Models\PullRiph;
use App\Models\PKS;
use App\Models\Lokasi;
use App\Models\RiphAdmin;
use App\Models\Saprodi;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\ForeignApi;
use Illuminate\Console\Command;

class DashboardController extends Controller
{
	public function index(Request $request)
	{

		$roleaccess = Auth::user()->roleaccess;
		if ($roleaccess == 1) {

			if (Auth::user()->roles[0]->title == 'Admin' || Auth::user()->roles[0]->title == 'Pejabat') {
				$module_name = 'Dashboard';
				$page_title = 'Monitoring Realisasi';
				$page_heading = 'Monitoring';
				$heading_class = 'fal fa-analytics';

				$currentYear = date('Y');
				$riphData = RiphAdmin::orderBy('periode', 'DESC')
					->where('periode', $currentYear)
					->get();
				$jumlah_importir = $riphData->sum('jumlah_importir');
				$v_pengajuan_import = $riphData->sum('v_pengajuan_import');
				$v_beban_tanam = $riphData->sum('v_beban_tanam');
				$v_beban_produksi = $riphData->sum('v_beban_produksi');

				$commitments = PullRiph::where('periodetahun', $currentYear)->get();
				$company = $commitments->count('no_ijin');
				$total_luastanam = $commitments->flatMap(function ($commitment) {
					return $commitment->datarealisasi->pluck('luas_lahan');
				})->sum();
				$total_volume = $commitments->flatMap(function ($commitment) {
					return $commitment->datarealisasi->pluck('volume');
				})->sum();

				$allPengajuan = PullRiph::whereYear('created_at', $currentYear)
					->where(function ($query) {
						$query->has('ajutanam')
							->orWhereHas('ajuproduksi')
							->orWhereHas('ajuskl')
							->orWhereHas('completed');
					})
					->with('ajutanam', 'ajuproduksi', 'ajuskl', 'completed')
					->get();

				$ajuTanam = AjuVerifTanam::whereYear('created_at', $currentYear)->get();
				$ajuProduksi = AjuVerifProduksi::whereYear('created_at', $currentYear)->get();
				$ajuSkl = AjuVerifSkl::whereYear('created_at', $currentYear)->get();
				$completed = Completed::whereYear('created_at', $currentYear)->get();
				// Menghitung jumlah masing-masing aju
				$ajuTanamCount = $ajuTanam->where('status', '1')->count();
				$ajuProduksiCount = $ajuProduksi->where('status', '1')->count();
				$ajuSklCount = $ajuSkl->where('status', '1')->count();

				// Menjumlahkan hasilnya sebagai ajucount
				$prosesValues = [2, 3]; // Nilai status yang ingin Anda cari

				$prosesVerifTanam = $ajuTanam->whereIn('status', $prosesValues)->count();
				$prosesVerifProduksi = $ajuProduksi->whereIn('status', $prosesValues)->count();
				$prosesVerifSkl = $ajuSkl->whereIn('status', $prosesValues)->count();

				$verifiedTanam = $ajuTanam->where('status', '4')->count();
				$verifiedProduksi = $ajuProduksi->where('status', '4')->count();
				$verifiedSkl = $ajuSkl->where('status', '4')->count();

				$proccesscount = $prosesVerifTanam + $prosesVerifProduksi + $prosesVerifSkl;
				$verifiedcount = $verifiedTanam + $verifiedProduksi + $verifiedSkl;
				$recomendationcount = $allPengajuan->where('status', '6')->count() > 0;
				$lunascount = $completed->count() > 0;

				$ajucount = $ajuTanamCount + $ajuProduksiCount + $ajuSklCount;

				// dd($allPengajuan);

				// $prosenTanam = $total_luastanam / $v_beban_tanam * 100;
				$prosenTanam = 0; // Initialize the variable with a default value
				$prosenProduksi = 0; // Initialize the variable with a default value

				if ($v_beban_tanam != 0) {
					$prosenTanam = $total_luastanam / $v_beban_tanam * 100;
				}

				if ($v_beban_produksi != 0) {
					$prosenProduksi = $total_volume / $v_beban_produksi * 100;
				}

				// dd($prosenTanam);
				return view('admin.dashboard.indexadmin', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'currentYear', 'riphData', 'company', 'jumlah_importir', 'v_pengajuan_import', 'v_beban_tanam', 'v_beban_produksi', 'total_luastanam', 'total_volume', 'allPengajuan', 'ajucount', 'proccesscount', 'verifiedcount', 'recomendationcount', 'lunascount', 'prosenTanam', 'prosenProduksi'));
			}
			if (Auth::user()->roles[0]->title == 'Verifikator') {
				$module_name = 'Dashboard';
				$page_title = 'Monitoring Verifikasi';
				$page_heading = 'Monitoring';
				$heading_class = 'fal fa-list-ul';
				$currentYear = date('Y');
				// $allPengajuan = Pengajuan::whereNotNull('status')
				// 	->whereYear('created_at', $currentYear)
				// 	->get();

				// $ajucount = $allPengajuan->where('status', '1')->count() > 0;
				// $proccesscount = $allPengajuan->where('onlinestatus', '2')->where('onfarmstatus', '')->count() > 0;
				// $verifiedcount = $allPengajuan->whereNotNull('onfarmstatus')->count() > 0;
				// $recomendationcount = $allPengajuan->where('status', '6')->count() > 0;
				// $lunascount = $allPengajuan->where('status', '7')->count() > 0;

				return view('admin.dashboard.indexverifikator', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'currentYear'));
			}
		}
		if (($roleaccess == 2)) {

			$npwpuser = Auth::user()->data_user->npwp_company;
			$module_name = 'Dashboard';
			$page_title = 'Ringkasan Data';
			$page_heading = 'Dashboard';
			$heading_class = 'fal fa-tachometer';

			$periodeTahuns = PullRiph::all()->groupBy('periodetahun');
			$currentYear = date('Y');

			$commitments = PullRiph::where('periodetahun',  $currentYear)
				->where('npwp', $npwpuser)
				->get();
			$volumeImport = $commitments->sum('volume_riph');
			$wajib_tanam = $commitments->sum('luas_wajib_tanam');
			$wajib_produksi = $commitments->sum('volume_produksi');

			$jumlah_poktan = $commitments->flatMap(function ($commitment) {
				return $commitment->pks->pluck('id');
			})->count();
			$jumlah_anggota = $commitments->flatMap(function ($commitment) {
				return $commitment->lokasi->pluck('id');
			})->count();
			$realisasi_tanam = $commitments->flatMap(function ($commitment) {
				return $commitment->datarealisasi->pluck('luas_lahan');
			})->sum();
			$realisasi_produksi = $commitments->flatMap(function ($commitment) {
				return $commitment->datarealisasi->pluck('volume');
			})->sum();


			if ($wajib_tanam == 0) {
				$prosentanam = 0;
			} else {
				$prosentanam = $realisasi_tanam / $wajib_tanam * 100;
			}
			if ($wajib_produksi == 0) {
				$prosenproduksi = 0;
			} else {
				$prosenproduksi = $realisasi_produksi / $wajib_produksi * 100;
			}

			// $allPengajuan = Pengajuan::whereNotNull('status')
			// 	->whereYear('created_at', $currentYear)
			// 	->get(['no_pengajuan', 'no_ijin', 'status', 'onlinestatus', 'onfarmstatus']);

			$allPengajuan = PullRiph::whereYear('created_at', $currentYear)
				->where('npwp', $npwpuser)
				->where(function ($query) {
					$query->has('ajutanam')
						->orWhereHas('ajuproduksi')
						->orWhereHas('ajuskl')
						->orWhereHas('completed');
				})
				->with('ajutanam', 'ajuproduksi', 'ajuskl', 'completed')
				->get();

			return view('admin.dashboard.indexuser', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'periodeTahuns', 'volumeImport', 'wajib_tanam', 'wajib_produksi', 'jumlah_poktan', 'jumlah_anggota', 'realisasi_tanam', 'jumlah_anggota', 'realisasi_tanam', 'realisasi_produksi', 'prosentanam', 'prosenproduksi', 'currentYear', 'allPengajuan'));
		}
	}

	public function map()
	{
		$module_name = 'Dashboard';
		$page_title = 'Pemetaan';
		$page_heading = 'Pemetaan';
		$page_desc = 'Peta Lahan Realisasi Komitmen Wajib Tanam-Produksi';
		$heading_class = 'fal fa-map-marked-alt';

		$mapkey = ForeignApi::find(1);

		$anggotaMitras = Lokasi::with([
			'pks' => function ($query) {
				$query->with('commitment');
			},
			'pks',
			'masteranggota'
		])->get();

		$roleaccess = Auth::user()->roleaccess;
		if ($roleaccess == 1) {
			$periodeTahuns = PullRiph::pluck('periodetahun')->unique();
		}
		if ($roleaccess == 2) {
			$periodeTahuns = PullRiph::where('npwp', Auth::user()->data_user->npwp_company)->pluck('periodetahun')->unique();
		}
		// $periodeTahuns = PullRiph::pluck('periodetahun')->unique();
		$users = User::where('roleaccess', 2)
			->has('data_user')
			->get();

		// dd($users);

		return view('admin.dashboard.map', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'page_desc', 'periodeTahuns', 'users', 'mapkey'));
	}

	public function newMap()
	{
		$mapkey = ForeignApi::find(1);
		return view('admin.dashboard.newmap', compact('mapkey'));
	}

	public function monitoring(Request $request)
	{
		$module_name = 'Dashboard';
		$page_title = 'Monitoring Realisasi';
		$page_heading = 'Monitoring';
		$heading_class = 'fal fa-analytics mr-1';

		$periodeTahuns = PullRiph::all()->groupBy('periodetahun');

		return view('admin.dashboard.monitoring', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'periodeTahuns'));
	}

	public function monitoringRealisasi(Request $request, $periodetahun)
	{
		$module_name = 'Dashboard';
		$page_title = 'Monitoring Realisasi' . $request->input('periodetahun');
		$page_heading = 'Monitoring Realisasi' . $request->input('periodetahun');
		$heading_class = 'fas fa-th mr-1';

		$periodeTahuns = PullRiph::all()->groupBy('periodetahun');

		return view('admin.dashboard.monitoringrealisasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'periodeTahuns'));
	}
}
