<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models2024\PullRiph;
use App\Models2024\AjuVerifProduksi;
use App\Models2024\AjuVerifSkl;
use App\Models2024\AjuVerifTanam;
use App\Models2024\Completed;
use App\Models2024\DataRealisasi;
use App\Models2024\Lokasi;
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
		$ajutanam = AjuVerifTanam::whereIn('no_ijin', $noIjins)->get();

		// Cari ajuproduksi dengan nomor ijin dari $noIjins
		$ajuproduksi = AjuVerifProduksi::whereIn('no_ijin', $noIjins)->get();

		// Cari skl dengan nomor ijin dari $noIjins
		$ajuskl = AjuVerifSkl::whereIn('no_ijin', $noIjins)->get();

		// Cari completed dengan nomor ijin dari $noIjins
		$completed = Completed::whereIn('no_ijin', $noIjins)->get();
		return view('t2024.pullriph.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwp_company', 'noIjins', 'ajutanam', 'ajuproduksi', 'ajuskl', 'completed'));
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



	public function store(Request $request)
	{
		$jsonData = [];

		try {
			// Get and sanitize NPWP
			$stnpwp = $request->get('npwp');
			$npwp = str_replace(['.', '-'], '', $stnpwp);

			// Get and sanitize no_ijin
			$noijin = $request->get('no_ijin');
			$fijin = str_replace(['.', '/'], '', $noijin);

			// Construct the file source path
			$fileSource = 'uploads/' . $fijin . '.json';

			// Check if the file exists
			if (!Storage::disk('public')->exists($fileSource)) {
				throw new Exception("File not found: " . $fileSource);
			}

			// Load file content
			$response = Storage::disk('public')->get($fileSource);

			// Decode JSON content into an associative array
			$jsonData = json_decode($response, true);

			// Validate the JSON structure as needed
			if (json_last_error() !== JSON_ERROR_NONE) {
				throw new Exception("Error decoding JSON: " . json_last_error_msg());
			}
		} catch (\Exception $e) {
			// Handle exceptions and errors
			$errorMessage = $e->getMessage();
			return redirect()->back()->with('error', 'Error while trying to access the file. Please contact the administrator for this error: (' . $errorMessage . ')');
		}

		$user = Auth::user();
		DB::beginTransaction();
		try {
			// Update or create PullRiph record
			$riph = PullRiph::updateOrCreate(
				[
					'npwp' => $stnpwp,
					'no_ijin' => $noijin,
					'user_id' => $user->id
				],
				[
					'keterangan'        => $request->get('keterangan'),
					'nama'              => $request->get('nama'),
					'periodetahun'      => $request->get('periodetahun'),
					'tgl_ijin'          => $request->get('tgl_ijin'),
					'tgl_akhir'         => $request->get('tgl_akhir'),
					'no_hs'             => $request->get('no_hs'),
					'volume_riph'       => $request->get('volume_riph'),
					'volume_produksi'   => $request->get('volume_produksi'),
					'luas_wajib_tanam'  => $request->get('luas_wajib_tanam'),
					'stok_mandiri'      => $request->get('stok_mandiri'),
					'pupuk_organik'     => $request->get('pupuk_organik'),
					'npk'               => $request->get('npk'),
					'dolomit'           => $request->get('dolomit'),
					'za'                => $request->get('za'),
					'mulsa'             => $request->get('mulsa'),
					'datariph'          => json_encode($jsonData) // Save the decoded JSON data as string
				]
			);

			if ($riph) {
				if (!isset($jsonData['riph']['wajib_tanam']['rencanalokasi']['loop'])) {
					return redirect()->back()->with('error', 'Gagal menyimpan. Data tidak lengkap.');
				} else {
					DataRealisasi::where([
						'npwp_company' => $stnpwp,
						'no_ijin' => $noijin,
					])->forceDelete();

					Lokasi::where([
						'npwp' => $stnpwp,
						'no_ijin' => $noijin,
					])->forceDelete();

					if (is_array($jsonData['riph']['wajib_tanam']['rencanalokasi']['loop'])) {
						foreach ($jsonData['riph']['wajib_tanam']['rencanalokasi']['loop'] as $rlokasi) {
							Lokasi::updateOrCreate(
								[
									'npwp' => $stnpwp,
									'no_ijin' => $noijin,
									'kode_spatial' => $rlokasi['kode_spatial'],
									'luas_lahan' => trim($rlokasi['luas_lahan']),
								]
							);
						}
					} elseif (is_object($jsonData['riph']['wajib_tanam']['rencanalokasi']['loop'])) {
						$rlokasi = $jsonData['riph']['wajib_tanam']['rencanalokasi']['loop'];
						Lokasi::updateOrCreate(
							[
								'npwp' => $stnpwp,
								'no_ijin' => $noijin,
								'kode_spatial' => $rlokasi['kode_spatial'],
								'luas_lahan' => trim($rlokasi['luas_lahan']),
							]
						);
					}
				}
			}
			DB::commit();
		} catch (\Exception $e) {
			DB::rollback();
			$errorMessage = $e->getMessage();
			Log::error("Error: $errorMessage. Code: " . $e->getCode() . ". Trace: " . $e->getTraceAsString());
			return redirect()->back()->with('error', 'Pull Store Method. Please contact the administrator for this error: (' . $errorMessage . ')');
		}

		return redirect()->route('2024.user.commitment.index')->with('success', 'Sukses menyimpan data dan dapat Anda lihat pada daftar di bawah ini.');
	}
}
