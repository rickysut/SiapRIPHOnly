<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\RiphAdmin;
use App\Models\Saprodi;
use App\Models\PullRiph;
use App\Models\Lokasi;
use App\Models\Pks;
use App\Models\Pengajuan;
use Carbon\Carbon;

class DashboardDataController extends Controller
{
	public function monitoringDataByYear($periodetahun)
	{
		// abort_if(Auth::user()->roleaccess != 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

		$riphData = RiphAdmin::where('periode', $periodetahun)->get();
		$commitments = PullRiph::where('periodetahun', $periodetahun)->get();

		$jumlahImportir = $riphData->sum('jumlah_importir'); //data dari siap riph adalah jumlah importir, seharusnya jumlah riph yang telah mendapatkan persetujuan import (PI)
		$v_pengajuan_import = $riphData->sum('v_pengajuan_import');
		$v_beban_tanam = $riphData->sum('v_beban_tanam');
		$v_beban_produksi = $riphData->sum('v_beban_produksi');
		$volume_import = $commitments->sum('volume_riph');
		$company = $commitments->count('no_ijin'); // jumlah RIPH yang telah melakukan sinkronisasi data di simethris

		$total_luastanam = $commitments->flatMap(function ($commitment) {
			return $commitment->datarealisasi->pluck('luas_lahan');
		})->sum();
		$total_volume = $commitments->flatMap(function ($commitment) {
			return $commitment->datarealisasi->pluck('volume');
		})->sum();

		$dataRealisasi = $commitments->map(function ($realisasi) {
			// Menghitung total luas tanam dan total volume
			$totalLuasTanam = $realisasi->datarealisasi->sum('luas_lahan');
			$totalVolume = $realisasi->datarealisasi->sum('volume');

			return [
				'company' => $realisasi->datauser->company_name,
				'no_ijin' => $realisasi->no_ijin,
				'volume_riph' => number_format($realisasi->volume_riph, 2, ',', '.'),
				'wajib_tanam' => number_format($realisasi->luas_wajib_tanam, 2, ',', '.'),
				'wajib_produksi' => number_format($realisasi->volume_produksi, 2, ',', '.'),
				'realisasi_tanam' => number_format($totalLuasTanam, 2, ',', '.'),
				'realisasi_produksi' => number_format($totalVolume, 2, ',', '.'),
			];
		});

		//data verifikasi
		$allPengajuan = PullRiph::where(function ($query) use ($periodetahun) {
			$query->whereHas('ajutanam', function ($subquery) use ($periodetahun) {
				$subquery->whereYear('created_at', $periodetahun);
			})->orWhereHas('ajuproduksi', function ($subquery) use ($periodetahun) {
				$subquery->whereYear('created_at', $periodetahun);
			})->orWhereHas('ajuskl', function ($subquery) use ($periodetahun) {
				$subquery->whereYear('created_at', $periodetahun);
			})->orWhereHas('completed', function ($subquery) use ($periodetahun) {
				$subquery->whereYear('created_at', $periodetahun);
			});
		})->with('ajutanam', 'ajuproduksi', 'ajuskl', 'completed')->get();


		$verifikasis = $allPengajuan->map(function ($verifikasi) {
			return [
				'no_pengajuan' => $verifikasi->no_pengajuan,
				'commitment' => [
					'datauser' => [
						'company_name' => $verifikasi->datauser->company_name,
					],
				],
				'no_ijin' => $verifikasi->no_ijin,
				'statusTanam'	=> $verifikasi->ajutanam->status ?? '',
				'statusProduksi' => $verifikasi->ajuproduksi->status ?? '',
				'statusSkl' => $verifikasi->ajuskl->status ?? '',
				'statusCompleted'	=> $verifikasi->completed->url ?? '',
			];
		});

		$ajuTanamCount = $verifikasis->where('statusTanam', '1')->count();
		$ajuProduksiCount = $verifikasis->where('statusProduksi', '1')->count();
		$ajuSklCount = $verifikasis->where('statusSkl', '1')->count();
		$ajucount = $ajuTanamCount + $ajuProduksiCount + $ajuSklCount;

		$prosesTanamCount = $verifikasis->whereIn('statusTanam', [2, 3])->count();
		$prosesProduksiCount = $verifikasis->whereIn('statusProduksi', [2, 3])->count();
		$prosesSklCount = $verifikasis->whereIn('statusSkl', [2, 3])->count();
		$proccesscount = $prosesTanamCount + $prosesProduksiCount + $prosesSklCount;

		//lanjutkan ke proses selanjutnya
		$verifiedTanamCount = $verifikasis->where('statusTanam', 4)->count();
		$verifiedProduksiCount = $verifikasis->where('statusProduksi', 4)->count();
		$verifiedSklCount = $verifikasis->where('statusSkl', 4)->count();
		$verifiedcount = $verifiedTanamCount + $verifiedProduksiCount + $verifiedSklCount;
		$lunascount = $verifikasis->where('statusCompleted', '!=', null)->count();

		$failTanamCount = $verifikasis->where('statusTanam', 5)->count();
		$failProduksiCount = $verifikasis->where('statusProduksi', 5)->count();
		$failSklCount = $verifikasis->where('statusSkl', 5)->count();
		$failCount = $failTanamCount + $failProduksiCount + $failSklCount;

		$recomendationcount = $allPengajuan->where('status', '')
			->count();

		$progresVT = $allPengajuan->where('ajutanam.status', '>=', 1)->where('ajutanam.status', '<=', 4)->map(function ($verifikasi) {
			return [
				'jenis' => 'Verifikasi Tanam',
				'commitment' => [
					'datauser' => [
						'company_name' => $verifikasi->datauser->company_name,
					],
				],
				'no_ijin' => $verifikasi->no_ijin,
				'created_at' => Carbon::parse($verifikasi->ajutanam->created_at)->format('M d, Y'),
				'updated_at' => Carbon::parse($verifikasi->ajutanam->updated_at)->format('M d, Y'),
				'TProgress' => $verifikasi->ajutanam->status ?? '',
			];
		});

		$progresVP = $allPengajuan->where('ajuproduksi.status', '!=', null)->map(function ($verifikasi) {
			return [
				'jenis' => 'Verifikasi Produksi',
				'commitment' => [
					'datauser' => [
						'company_name' => $verifikasi->datauser->company_name,
					],
				],
				'no_ijin' => $verifikasi->no_ijin,
				'created_at' => Carbon::parse($verifikasi->ajuproduksi->created_at)->format('M d, Y'),
				'updated_at' => Carbon::parse($verifikasi->ajuproduksi->updated_at)->format('M d, Y'),
				'PProgress' => $verifikasi->ajuproduksi->status ?? '',
			];
		});

		$progresVSkl = $allPengajuan->where('ajuskl.status', '!=', null)->map(function ($verifikasi) {
			return [
				'jenis' => 'Verifikasi SKL',
				'commitment' => [
					'datauser' => [
						'company_name' => $verifikasi->datauser->company_name,
					],
				],
				'no_ijin' => $verifikasi->no_ijin,
				'created_at' => Carbon::parse($verifikasi->ajuskl->created_at)->format('M d, Y'),
				'updated_at' => Carbon::parse($verifikasi->ajuskl->updated_at)->format('M d, Y'),
				'SklProgress' => $verifikasi->ajuskl->status ?? '',
			];
		});

		$data = [
			'jumlah_importir'       => $jumlahImportir,
			'v_pengajuan_import'    => $v_pengajuan_import,
			'v_beban_tanam'         => $v_beban_tanam,
			'v_beban_produksi'      => $v_beban_produksi,
			'company'               => $company,
			'volume_import'         => $volume_import,
			'total_luastanam'       => $total_luastanam,
			'total_volume'          => $total_volume,
			'prosenTanam'           => ($v_beban_tanam == 0) ? 0 : ($total_luastanam / $v_beban_tanam * 100),
			'prosenProduksi'        => ($v_beban_produksi == 0) ? 0 : ($total_volume / $v_beban_produksi * 100),
			'dataRealisasi'			=> $dataRealisasi,
			'ajucount'				=> $ajucount,
			'proccesscount'			=> $proccesscount,
			'verifiedcount'			=> $verifiedcount,
			'failCount'				=> $failCount,
			'recomendationcount'	=> $recomendationcount,
			'lunascount'			=> $lunascount,
			'verifikasis'			=> $verifikasis,
			'progresVT'				=> $progresVT,
			'progresVP'				=> $progresVP,
			'progresVSkl'			=> $progresVSkl,
		];

		return response()->json($data);
	}


	public function verifikatorMonitoringDataByYear($periodetahun)
	{
		abort_if(Auth::user()->roleaccess != 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

		$allPengajuan = PullRiph::where(function ($query) use ($periodetahun) {
			$query->whereHas('ajutanam', function ($subquery) use ($periodetahun) {
				$subquery->whereYear('created_at', $periodetahun);
			})->orWhereHas('ajuproduksi', function ($subquery) use ($periodetahun) {
				$subquery->whereYear('created_at', $periodetahun);
			})->orWhereHas('ajuskl', function ($subquery) use ($periodetahun) {
				$subquery->whereYear('created_at', $periodetahun);
			})->orWhereHas('completed', function ($subquery) use ($periodetahun) {
				$subquery->whereYear('created_at', $periodetahun);
			});
		})->with('ajutanam', 'ajuproduksi', 'ajuskl', 'completed')->get();


		$verifikasis = $allPengajuan->map(function ($verifikasi) {
			return [
				'no_pengajuan' => $verifikasi->no_pengajuan,
				'commitment' => [
					'datauser' => [
						'company_name' => $verifikasi->datauser->company_name,
					],
				],
				'no_ijin' => $verifikasi->no_ijin,
				'statusTanam'	=> $verifikasi->ajutanam->status ?? '',
				'statusProduksi' => $verifikasi->ajuproduksi->status ?? '',
				'statusSkl' => $verifikasi->ajuskl->status ?? '',
				'statusCompleted'	=> $verifikasi->completed->url ?? '',
			];
		});

		$ajuTanamCount = $verifikasis->where('statusTanam', '1')->count();
		$ajuProduksiCount = $verifikasis->where('statusProduksi', '1')->count();
		$ajuSklCount = $verifikasis->where('statusSkl', '1')->count();
		$ajucount = $ajuTanamCount + $ajuProduksiCount + $ajuSklCount;

		$prosesTanamCount = $verifikasis->whereIn('statusTanam', [2, 3])->count();
		$prosesProduksiCount = $verifikasis->whereIn('statusProduksi', [2, 3])->count();
		$prosesSklCount = $verifikasis->whereIn('statusSkl', [2, 3])->count();
		$proccesscount = $prosesTanamCount + $prosesProduksiCount + $prosesSklCount;

		//lanjutkan ke proses selanjutnya
		$verifiedTanamCount = $verifikasis->where('statusTanam', 4)->count();
		$verifiedProduksiCount = $verifikasis->where('statusProduksi', 4)->count();
		$verifiedSklCount = $verifikasis->where('statusSkl', 4)->count();
		$verifiedcount = $verifiedTanamCount + $verifiedProduksiCount + $verifiedSklCount;
		$lunascount = $verifikasis->where('statusCompleted', '!=', null)->count();

		$failTanamCount = $verifikasis->where('statusTanam', 5)->count();
		$failProduksiCount = $verifikasis->where('statusProduksi', 5)->count();
		$failSklCount = $verifikasis->where('statusSkl', 5)->count();
		$failCount = $failTanamCount + $failProduksiCount + $failSklCount;

		$recomendationcount = $allPengajuan->where('status', '')
			->count();

		$progresVT = $allPengajuan->where('ajutanam.status', '>=', 1)->where('ajutanam.status', '<=', 4)->map(function ($verifikasi) {
			return [
				'jenis' => 'Verifikasi Tanam',
				'commitment' => [
					'datauser' => [
						'company_name' => $verifikasi->datauser->company_name,
					],
				],
				'no_ijin' => $verifikasi->no_ijin,
				'created_at' => Carbon::parse($verifikasi->ajutanam->created_at)->format('M d, Y'),
				'updated_at' => Carbon::parse($verifikasi->ajutanam->updated_at)->format('M d, Y'),
				'TProgress' => $verifikasi->ajutanam->status ?? '',
			];
		});

		$progresVP = $allPengajuan->where('ajuproduksi.status', '!=', null)->map(function ($verifikasi) {
			return [
				'jenis' => 'Verifikasi Produksi',
				'commitment' => [
					'datauser' => [
						'company_name' => $verifikasi->datauser->company_name,
					],
				],
				'no_ijin' => $verifikasi->no_ijin,
				'created_at' => Carbon::parse($verifikasi->ajuproduksi->created_at)->format('M d, Y'),
				'updated_at' => Carbon::parse($verifikasi->ajuproduksi->updated_at)->format('M d, Y'),
				'PProgress' => $verifikasi->ajuproduksi->status ?? '',
			];
		});

		$progresVSkl = $allPengajuan->where('ajuskl.status', '!=', null)->map(function ($verifikasi) {
			return [
				'jenis' => 'Verifikasi SKL',
				'commitment' => [
					'datauser' => [
						'company_name' => $verifikasi->datauser->company_name,
					],
				],
				'no_ijin' => $verifikasi->no_ijin,
				'created_at' => Carbon::parse($verifikasi->ajuskl->created_at)->format('M d, Y'),
				'updated_at' => Carbon::parse($verifikasi->ajuskl->updated_at)->format('M d, Y'),
				'SklProgress' => $verifikasi->ajuskl->status ?? '',
			];
		});

		$data = [
			'ajucount'				=> $ajucount,
			'proccesscount'			=> $proccesscount,
			'verifiedcount'			=> $verifiedcount,
			'failCount'				=> $failCount,
			'recomendationcount'	=> $recomendationcount,
			'lunascount'			=> $lunascount,
			'verifikasis'			=> $verifikasis,
			'progresVT'				=> $progresVT,
			'progresVP'				=> $progresVP,
			'progresVSkl'			=> $progresVSkl,
		];

		return response()->json($data);
	}

	public function userMonitoringDataByYear($periodetahun)
	{
		$npwpuser = Auth::user()->data_user->npwp_company;
		// Retrieve all data from RiphAdmin based on the selected 'periode'
		$commitments = PullRiph::where('periodetahun', $periodetahun)
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
			$prosentanam = ($realisasi_tanam / $wajib_tanam) * 100;
		}
		if ($wajib_produksi == 0) {
			$prosenproduksi = 0;
		} else {
			$prosenproduksi = ($realisasi_produksi / $wajib_produksi) * 100;
		}

		$allPengajuan = PullRiph::whereYear('created_at', $periodetahun)
			->where('npwp', $npwpuser)
			->where(function ($query) {
				$query->has('ajutanam')
					->orWhereHas('ajuproduksi')
					->orWhereHas('ajuskl')
					->orWhereHas('completed');
			})
			->with('ajutanam', 'ajuproduksi', 'ajuskl', 'completed')
			->get();

		$verifikasis = $allPengajuan->map(function ($verifikasi) {
			return [
				'commitment' => [
					'datauser' => [
						'company_name' => $verifikasi->datauser->company_name,
					],
				],
				'no_ijin' => $verifikasi->no_ijin,
				'statusTanam'	=> $verifikasi->ajutanam->status ?? '',
				'statusProduksi' => $verifikasi->ajuproduksi->status ?? '',
				'statusSkl' => $verifikasi->ajuskl->status ?? '',
				'statusCompleted'	=> $verifikasi->completed->url ?? '',
			];
		});

		$data = [
			'volumeImport'			=> $volumeImport,
			'wajib_tanam'			=> $wajib_tanam,
			'wajib_produksi'		=> $wajib_produksi,
			'total_luastanam'		=> $realisasi_tanam,
			'total_volume'			=> $realisasi_produksi,
			'count_poktan'			=> $jumlah_poktan,
			'count_anggota'			=> $jumlah_anggota,
			'prosenTanam'			=> $prosentanam,
			'prosenProduksi'		=> $prosenproduksi,
			'verifikasis'			=> $verifikasis,
		];
		return response()->json($data);
	}

	public function rekapRiphData(Request $request)
	{
		try {
			$options = array(
				'soap_version' => SOAP_1_1,
				'exceptions' => true,
				'trace' => 1,
				'cache_wsdl' => WSDL_CACHE_MEMORY,
				'connection_timeout' => 25,
				'style' => SOAP_RPC,
				'use' => SOAP_ENCODED,
			);

			$client = new \SoapClient('https://riph.pertanian.go.id/api.php/simethris?wsdl', $options);
			$parameter = array(
				'user' => 'simethris',
				'pass' => 'wsriphsimethris',
				'tahun' => $request->string('periodetahun')
			);
			$response = $client->__soapCall('get_rekap', $parameter);
		} catch (\Exception $e) {

			Log::error('Soap Exception: ' . $e->getMessage());
			throw new \Exception('Problem with SOAP call');
		}
		$res = json_decode(json_encode((array)simplexml_load_string($response)), true);

		return $res;
	}

	public function monitoringDataRealisasi($periodetahun)
	{
		$commitments = PullRiph::where('periodetahun', $periodetahun)
			->with('lokasi')
			->get();

		$dataRealisasi = $commitments->map(function ($realisasi) {
			// Menghitung total luas tanam dan total volume
			$totalLuasTanam = $realisasi->datarealisasi->sum('luas_lahan');
			$totalVolume = $realisasi->datarealisasi->sum('volume');

			return [
				'company' => $realisasi->datauser->company_name,
				'no_ijin' => $realisasi->no_ijin,
				'volume_riph' => number_format($realisasi->volume_riph, 2, ',', '.'),
				'wajib_tanam' => number_format($realisasi->luas_wajib_tanam, 2, ',', '.'),
				'wajib_produksi' => number_format($realisasi->volume_produksi, 2, ',', '.'),
				'realisasi_tanam' => number_format($totalLuasTanam, 2, ',', '.'),
				'realisasi_produksi' => number_format($totalVolume, 2, ',', '.'),
			];
		});

		// dd($dataRealisasi);
		$data = [
			'dataRealisasi'			=> $dataRealisasi,
		];
		return response()->json($data);
	}
}
