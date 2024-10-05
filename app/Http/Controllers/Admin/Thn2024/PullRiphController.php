<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;
use App\Models2024\AjuVerifikasi;
use Illuminate\Http\Request;

use App\Models2024\PullRiph;
use App\Models2024\AjuVerifProduksi;
use App\Models2024\AjuVerifSkl;
use App\Models2024\AjuVerifTanam;
use App\Models2024\Completed;
use App\Models2024\DataRealisasi;
use App\Models2024\Lokasi;
use App\Models2024\MasterAnggota;
use App\Models2024\MasterPoktan;
use App\Models2024\MasterSpatial;
use App\Models2024\Pks;
use Exception;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PullRiphController extends Controller
{

	public function index()
	{
		abort_if(Gate::denies('pull_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$module_name = 'Proses RIPH';
		$page_title = 'Tarik Data RIPH';
		$page_heading = 'Tarik Data RIPH';
		$heading_class = 'fa fa-sync-alt';
		$npwp_company = (Auth::user()::find(Auth::user()->id)->data_user->npwp_company ?? null);
		$noIjins = PullRiph::where('npwp', $npwp_company)->select('no_ijin')->get();
		// Cari ajutanam yang memiliki nomor ijin dari $noIjins
		$pengajuan = AjuVerifikasi::whereIn('no_ijin', $noIjins)
			->whereNotIn('status', [0, 7])
			->get();

		// Cari skl dengan nomor ijin dari $noIjins
		$ajuskl = AjuVerifSkl::whereIn('no_ijin', $noIjins)->get();

		// Cari completed dengan nomor ijin dari $noIjins
		$completed = Completed::whereIn('no_ijin', $noIjins)->get();

		// dd($pengajuan);
		return view('t2024.pullriph.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwp_company', 'noIjins', 'pengajuan',  'ajuskl', 'completed'));
	}

	public function checkYear(Request $request)
	{
		$nomor = $request->input('nomor');
		$year = substr($nomor, -4);

		if (is_numeric($year) && (int)$year < 2023) {
			return response()->json(['success' => false, 'message' => 'Periode RIPH Anda tidak dapat digunakan pada simethris versi ini']);
		}

		return response()->json(['success' => true]);
	}

	public function pull(Request $request)
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
				'npwp' => $request->string('npwp'),
				'nomor' =>  $request->string('nomor')
			);

			$response = $client->__soapCall('get_riph', $parameter);
		} catch (\Exception $e) {
			$errorMessage = $e->getMessage();
			Log::error("Error: $errorMessage. Code: " . $e->getCode() . ". Trace: " . $e->getTraceAsString());
			return redirect()->back()->with('error', 'Pull Method. Error while trying to retrieve data. Please Contact Administrator for this error: (' . $errorMessage . ')');
		}
		$res = json_decode(json_encode((array)simplexml_load_string($response)), true);

		return $res;
	}

	//ini yang asli
	// public function store(Request $request)
	// {
	// 	$filepath = '';
	// 	try {
	// 		$options = array(
	// 			'soap_version' => SOAP_1_1,
	// 			'exceptions' => true,
	// 			'trace' => 1,
	// 			'cache_wsdl' => WSDL_CACHE_MEMORY,
	// 			'connection_timeout' => 25,
	// 			'style' => SOAP_RPC,
	// 			'use' => SOAP_ENCODED,
	// 		);

	// 		$client = new \SoapClient('https://riph.pertanian.go.id/api.php/simethris?wsdl', $options);
	// 		$stnpwp = $request->get('npwp');
	// 		$npwp = str_replace(['.', '-'], '', $stnpwp);
	// 		$noijin = $request->get('no_ijin');
	// 		$fijin = str_replace(['.', '/'], '', $noijin);
	// 		$parameter = array(
	// 			'user' => 'simethris',
	// 			'pass' => 'wsriphsimethris',
	// 			'npwp' => $npwp,
	// 			'nomor' => $request->get('no_ijin')
	// 		);
	// 		$response = $client->__soapCall('get_riph', $parameter);
	// 		$datariph = json_encode((array)simplexml_load_string($response));
	// 		$filepath = 'uploads/' . $npwp . '/' . $fijin . '.json';
	// 		Storage::disk('public')->put($filepath, $datariph);
	// 	} catch (\Exception $e) {
	// 		$errorMessage = $e->getMessage();
	// 		Log::error("Error: $errorMessage. Code: " . $e->getCode() . ". Trace: " . $e->getTraceAsString());
	// 		return redirect()->back()->with('error', 'Soap Error while trying to connect to Client. Please Contact Administrator for this error: (' . $errorMessage . ')');
	// 	}

	// 	$user = Auth::user();
	// 	DB::beginTransaction();
	// 	try {
	// 		$dtjson = json_decode($datariph);
	// 		$riph = PullRiph::updateOrCreate(
	// 			[
	// 				'npwp' => $stnpwp,
	// 				'no_ijin' => $noijin,
	// 				'user_id' => $user->id
	// 			],
	// 			[
	// 				'keterangan' => $request->get('keterangan'),
	// 				'nama' => $dtjson->riph->persetujuan->nama,
	// 				'periodetahun' => $request->get('periodetahun'),
	// 				'tgl_ijin' => $dtjson->riph->persetujuan->tgl_ijin,
	// 				'tgl_akhir' => $dtjson->riph->persetujuan->tgl_akhir,
	// 				'no_hs' => $request->get('no_hs'),
	// 				'volume_riph' => $dtjson->riph->wajib_tanam->volume_riph,
	// 				'volume_produksi' => $dtjson->riph->wajib_tanam->volume_produksi,
	// 				'luas_wajib_tanam' => $dtjson->riph->wajib_tanam->luas_wajib_tanam,
	// 				'stok_mandiri' => $dtjson->riph->wajib_tanam->stok_mandiri,
	// 				'pupuk_organik' => $dtjson->riph->wajib_tanam->kebutuhan_pupuk->pupuk_organik,
	// 				'npk' => $dtjson->riph->wajib_tanam->kebutuhan_pupuk->npk,
	// 				'dolomit' => $dtjson->riph->wajib_tanam->kebutuhan_pupuk->dolomit,
	// 				'za' => $dtjson->riph->wajib_tanam->kebutuhan_pupuk->za,
	// 				'mulsa' => $dtjson->riph->wajib_tanam->mulsa,
	// 				'datariph' => $filepath
	// 			]
	// 		);

	// 		if ($riph) {
	// 			if (!isset($dtjson->riph->wajib_tanam->lokasi) || empty($dtjson->riph->wajib_tanam->lokasi)) {
	// 				return redirect()->back()->with('error', 'Failed to save. Location data is incomplete.');
	// 			}

	// 			$existingLokasiRecords = Lokasi::withTrashed()->where('npwp', $stnpwp)->where('no_ijin', $noijin)->pluck('kode_spatial')->toArray();
	// 			$newLokasiRecords = [];

	// 			if (is_array($dtjson->riph->wajib_tanam->lokasi)) {
	// 				foreach ($dtjson->riph->wajib_tanam->lokasi as $lokasi) {
	// 					$kodeSpatial = trim($lokasi->kode_spatial, ' ');
	// 					$newLokasiRecords[] = $kodeSpatial;
	// 				}
	// 			} else {
	// 				$kode_spatial = trim($dtjson->riph->wajib_tanam->lokasi->kode_spatial, ' ');
	// 				$newLokasiRecords[] = $kode_spatial;
	// 			}

	// 			$recordsToReactivate = array_intersect($existingLokasiRecords, $newLokasiRecords);
	// 			$recordsToDelete = array_diff($existingLokasiRecords, $newLokasiRecords);
	// 			$recordsToAdd = array_diff($newLokasiRecords, $existingLokasiRecords);

	// 			if (!empty($recordsToDelete)) {
	// 				Lokasi::where('origin', 'foreign')->where('npwp', $stnpwp)->where('no_ijin', $noijin)->whereIn('kode_spatial', $recordsToDelete)->delete();
	// 			}

	// 			if (!empty($recordsToReactivate)) {
	// 				Lokasi::withTrashed()->where('origin', 'foreign')->where('npwp', $stnpwp)->where('no_ijin', $noijin)->whereIn('kode_spatial', $recordsToReactivate)->restore();
	// 			}

	// 			$masterSpatials = MasterSpatial::whereIn('kode_spatial', $newLokasiRecords)->get();
	// 			$poktanGroups = $masterSpatials->groupBy('kode_poktan');

	// 			$existingPksRecords = Pks::where('npwp', $stnpwp)->where('no_ijin', $noijin)->pluck('kode_poktan')->toArray();
	// 			$newPksRecords = $poktanGroups->keys()->toArray();
	// 			$pksToReactivate = array_intersect($existingPksRecords, $newPksRecords);
	// 			$pksToDelete = array_diff($existingPksRecords, $newPksRecords);
	// 			$pksToAdd = array_diff($newPksRecords, $existingPksRecords);

	// 			if (!empty($pksToDelete)) {
	// 				Pks::where('npwp', $stnpwp)->where('no_ijin', $noijin)->whereIn('kode_poktan', $pksToDelete)->delete();
	// 			}

	// 			if (!empty($pksToReactivate)) {
	// 				Pks::withTrashed()->where('npwp', $stnpwp)->where('no_ijin', $noijin)->whereIn('kode_poktan', $pksToReactivate)->restore();
	// 			}

	// 			foreach ($pksToAdd as $kode_poktan) {
	// 				$nama_poktan = $poktanGroups[$kode_poktan]->first()->masterpoktan->nama_kelompok ?? '';
	// 				Pks::withTrashed()->updateOrCreate(
	// 					[
	// 						'npwp' => $stnpwp,
	// 						'no_ijin' => $noijin,
	// 						'kode_poktan' => $kode_poktan
	// 					],
	// 					[
	// 						'nama_poktan' => $nama_poktan,
	// 						'deleted_at' => null
	// 					]
	// 				);
	// 			}

	// 			foreach ($newLokasiRecords as $kodeSpatial) {
	// 				$masterSpatial = $masterSpatials->where('kode_spatial', $kodeSpatial)->first();
	// 				// \Log::info('Updating or creating:', [
	// 				// 	'origin' => 1,
	// 				// 	'npwp' => $stnpwp,
	// 				// 	'no_ijin' => $noijin,
	// 				// 	'kode_spatial' => $kodeSpatial,
	// 				// ]);
	// 				Lokasi::updateOrCreate(
	// 					[
	// 						'npwp' => $stnpwp,
	// 						'no_ijin' => $noijin,
	// 						'kode_spatial' => $kodeSpatial,
	// 						'origin' => 'foreign',
	// 					],
	// 					[
	// 						'kode_poktan' => $masterSpatial->kode_poktan ?? '',
	// 						'ktp_petani' => $masterSpatial->ktp_petani ?? '',
	// 						'luas_lahan' => $masterSpatial->luas_lahan ?? '',
	// 						'deleted_at' => null,
	// 					]
	// 				);
	// 			}

	// 			DB::commit();
	// 		}
	// 	} catch (\Exception $e) {
	// 		DB::rollback();
	// 		$errorMessage = $e->getMessage();
	// 		Log::error("Error: $errorMessage. Code: " . $e->getCode() . ". Trace: " . $e->getTraceAsString());
	// 		return redirect()->back()->with('error', 'Error in store method. Please contact administrator: (' . $errorMessage . ')');
	// 	}

	// 	return redirect()->route('2024.user.commitment.index')->with('success', 'Data saved successfully and can be viewed in the list below.');
	// }

	//ini simulator
	public function store(Request $request)
	{
		$filepath = '';
		try {
				// 		$options = array(
				// 			'soap_version' => SOAP_1_1,
				// 			'exceptions' => true,
				// 			'trace' => 1,
				// 			'cache_wsdl' => WSDL_CACHE_MEMORY,
				// 			'connection_timeout' => 25,
				// 			'style' => SOAP_RPC,
				// 			'use' => SOAP_ENCODED,
				// 		);

				// 		$client = new \SoapClient('https://riph.pertanian.go.id/api.php/simethris?wsdl', $options);
				// 		$stnpwp = $request->get('npwp');
				// 		$npwp = str_replace(['.', '-'], '', $stnpwp);
				// 		$noijin = $request->get('no_ijin');
				// 		$fijin = str_replace(['.', '/'], '', $noijin);
				// 		$parameter = array(
				// 			'user' => 'simethris',
				// 			'pass' => 'wsriphsimethris',
				// 			'npwp' => $npwp,
				// 			'nomor' => $request->get('no_ijin')
				// 		);
				// 		$response = $client->__soapCall('get_riph', $parameter);
				// 		$datariph = json_encode((array)simplexml_load_string($response));

			// Simulate SOAP API response using local JSON file
			$jsonFilePath = storage_path('app/public/uploads/0217PP240D032023.json');
			$datariph = file_get_contents($jsonFilePath);

			// Set file path for storage
			$stnpwp = $request->get('npwp');
			$npwp = str_replace(['.', '-'], '', $stnpwp);
			$noijin = $request->get('no_ijin');
			$fijin = str_replace(['.', '/'], '', $noijin);
			$filepath = 'uploads/' . $npwp . '/' . $fijin . '.json';
			Storage::disk('public')->put($filepath, $datariph);
		} catch (\Exception $e) {
			$errorMessage = $e->getMessage();
			Log::error("Error: $errorMessage. Code: " . $e->getCode() . ". Trace: " . $e->getTraceAsString());
			return redirect()->back()->with('error', 'Error while reading JSON file. Please contact administrator: (' . $errorMessage . ')');
		}

		$user = Auth::user();
		DB::beginTransaction();
		try {
			$dtjson = json_decode($datariph);
			// Update or create PullRiph record
			$riph = PullRiph::updateOrCreate(
				[
					'npwp' => $stnpwp,
					'no_ijin' => $noijin,
					'user_id' => $user->id
				],
				[
					'keterangan' => $request->get('keterangan'),
					'nama' => $dtjson->riph->persetujuan->nama,
					'periodetahun' => $request->get('periodetahun'),
					'tgl_ijin' => $dtjson->riph->persetujuan->tgl_ijin,
					'tgl_akhir' => $dtjson->riph->persetujuan->tgl_akhir,
					'no_hs' => $request->get('no_hs'),
					'volume_riph' => $dtjson->riph->wajib_tanam->volume_riph,
					'volume_produksi' => $dtjson->riph->wajib_tanam->volume_produksi,
					'luas_wajib_tanam' => $dtjson->riph->wajib_tanam->luas_wajib_tanam,
					'stok_mandiri' => $dtjson->riph->wajib_tanam->stok_mandiri,
					'pupuk_organik' => $dtjson->riph->wajib_tanam->kebutuhan_pupuk->pupuk_organik,
					'npk' => $dtjson->riph->wajib_tanam->kebutuhan_pupuk->npk,
					'dolomit' => $dtjson->riph->wajib_tanam->kebutuhan_pupuk->dolomit,
					'za' => $dtjson->riph->wajib_tanam->kebutuhan_pupuk->za,
					'mulsa' => $dtjson->riph->wajib_tanam->mulsa,
					'datariph' => $filepath
				]
			);

			if ($riph) {
				if (!isset($dtjson->riph->wajib_tanam->lokasi) || empty($dtjson->riph->wajib_tanam->lokasi)) {
					return redirect()->back()->with('error', 'Failed to save. Location data is incomplete.');
				}

				$existingLokasiRecords = Lokasi::withTrashed()->where('npwp', $stnpwp)->where('no_ijin', $noijin)->pluck('kode_spatial')->toArray();
				$newLokasiRecords = [];

				if (is_array($dtjson->riph->wajib_tanam->lokasi)) {
					foreach ($dtjson->riph->wajib_tanam->lokasi as $lokasi) {
						$kodeSpatial = trim($lokasi->kode_spatial, ' ');
						$newLokasiRecords[] = $kodeSpatial;
					}
				} else {
					$kode_spatial = trim($dtjson->riph->wajib_tanam->lokasi->kode_spatial, ' ');
					$newLokasiRecords[] = $kode_spatial;
				}

				$recordsToReactivate = array_intersect($existingLokasiRecords, $newLokasiRecords);
				$recordsToDelete = array_diff($existingLokasiRecords, $newLokasiRecords);
				$recordsToAdd = array_diff($newLokasiRecords, $existingLokasiRecords);

				if (!empty($recordsToDelete)) {
					Lokasi::where('origin', 'foreign')->where('npwp', $stnpwp)->where('no_ijin', $noijin)->whereIn('kode_spatial', $recordsToDelete)->delete();
				}

				if (!empty($recordsToReactivate)) {
					Lokasi::withTrashed()->where('origin', 'foreign')->where('npwp', $stnpwp)->where('no_ijin', $noijin)->whereIn('kode_spatial', $recordsToReactivate)->restore();
				}

				$masterSpatials = MasterSpatial::whereIn('kode_spatial', $newLokasiRecords)->get();
				$poktanGroups = $masterSpatials->groupBy('kode_poktan');

				$existingPksRecords = Pks::where('npwp', $stnpwp)->where('no_ijin', $noijin)->pluck('kode_poktan')->toArray();
				$newPksRecords = $poktanGroups->keys()->toArray();
				$pksToReactivate = array_intersect($existingPksRecords, $newPksRecords);
				$pksToDelete = array_diff($existingPksRecords, $newPksRecords);
				$pksToAdd = array_diff($newPksRecords, $existingPksRecords);

				if (!empty($pksToDelete)) {
					Pks::where('npwp', $stnpwp)->where('no_ijin', $noijin)->whereIn('kode_poktan', $pksToDelete)->delete();
				}

				if (!empty($pksToReactivate)) {
					Pks::withTrashed()->where('npwp', $stnpwp)->where('no_ijin', $noijin)->whereIn('kode_poktan', $pksToReactivate)->restore();
				}

				foreach ($pksToAdd as $kode_poktan) {
					$nama_poktan = $poktanGroups[$kode_poktan]->first()->masterpoktan->nama_kelompok ?? '';
					Pks::withTrashed()->updateOrCreate(
						[
							'npwp' => $stnpwp,
							'no_ijin' => $noijin,
							'kode_poktan' => $kode_poktan
						],
						[
							'nama_poktan' => $nama_poktan,
							'deleted_at' => null
						]
					);
				}

				foreach ($newLokasiRecords as $kodeSpatial) {
					$masterSpatial = $masterSpatials->where('kode_spatial', $kodeSpatial)->first();
					// \Log::info('Updating or creating:', [
					// 	'origin' => 1,
					// 	'npwp' => $stnpwp,
					// 	'no_ijin' => $noijin,
					// 	'kode_spatial' => $kodeSpatial,
					// ]);
					Lokasi::updateOrCreate(
						[
							'npwp' => $stnpwp,
							'no_ijin' => $noijin,
							'kode_spatial' => $kodeSpatial,
							'origin' => 'foreign',
						],
						[
							'kode_poktan' => $masterSpatial->kode_poktan ?? '',
							'ktp_petani' => $masterSpatial->ktp_petani ?? '',
							'luas_lahan' => $masterSpatial->luas_lahan ?? '',
							'deleted_at' => null,
						]
					);
				}

				DB::commit();
			}
		} catch (\Exception $e) {
			DB::rollback();
			$errorMessage = $e->getMessage();
			Log::error("Error: $errorMessage. Code: " . $e->getCode() . ". Trace: " . $e->getTraceAsString());
			return redirect()->back()->with('error', 'Error in store method. Please contact administrator: (' . $errorMessage . ')');
		}

		return redirect()->route('2024.user.commitment.index')->with('success', 'Data saved successfully and can be viewed in the list below.');
	}
}
