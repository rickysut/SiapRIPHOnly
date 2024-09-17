<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;
use App\Models2024\AjuVerifikasi;
use App\Models2024\AjuVerifSkl;
use App\Models2024\Completed;
use App\Models2024\FileManagement;
use App\Models2024\Lokasi;
use App\Models2024\MasterAnggota;
use App\Models2024\MasterPoktan;
use App\Models2024\MasterSpatial;
use App\Models2024\Pks;
use App\Models2024\PullRiph;
use App\Models2024\UserFile;
use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;
use App\Models\MasterProvinsis;
use App\Models\UserDocs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Svg\Tag\Rect;

class DataFeederController extends Controller
{
	private function formatNoIjin($noIjin)
	{
		return substr($noIjin, 0, 4) . '/' .
			substr($noIjin, 4, 2) . '.' .
			substr($noIjin, 6, 3) . '/' .
			substr($noIjin, 9, 1) . '/' .
			substr($noIjin, 10, 2) . '/' .
			substr($noIjin, 12, 4);
	}

	public function getAllMyCommitment(Request $request)
	{
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);

		//Catatan: tambahkan logika jika tidak memiliki npwp, tidak bisa masuk atau jika role 2
		$npwp = Auth::user()->data_user->npwp_company;

		// Base query
		$query = PullRiph::select('id', 'no_ijin', 'periodetahun', 'tgl_ijin', 'volume_riph', 'luas_wajib_tanam', 'volume_produksi')
			->where('npwp', $npwp)
			->with(['skl', 'lokasi', 'pks', 'ajutanam', 'ajuproduksi', 'ajuskl', 'completed', 'userFiles']);
		// dd($query);
		// Apply search filter
		if (!empty($searchValue)) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('no_ijin', 'like', "%{$searchValue}%")
					->orWhere('periodetahun', 'like', "%{$searchValue}%")
					->orWhere('tgl_ijin', 'like', "%{$searchValue}%")
					->orWhere('volume_riph', 'like', "%{$searchValue}%")
					->orWhere('luas_wajib_tanam', 'like', "%{$searchValue}%")
					->orWhere('volume_produksi', 'like', "%{$searchValue}%");
			});
		}

		// Calculate total and filtered records
		$totalRecords = $query->count();
		$filteredRecords = $query->count();

		// Apply ordering
		if (!empty($order)) {
			$orderColumnIndex = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$orderColumn = $columns[$orderColumnIndex]['data'];
			switch ($orderColumn) {
				case 'ijin_full':
					$query = $query->orderBy('no_ijin', $orderDirection);
					break;
				case 'periodetahun':
					$query = $query->orderBy('periodetahun', $orderDirection);
					break;
				default:
					$query = $query->orderBy('id', 'asc');
					break;
			}
		}

		// Apply pagination
		$data = $query->skip($start)->take($length)->get();

		// Process data
		foreach ($data as $commitment) {
			$commitment->sumRealisasiLuas = $commitment->lokasi->sum('luas_tanam');
			$commitment->sumRealisasiPanen = $commitment->lokasi->sum('volume');
			$commitment->pksCount = $commitment->pks->count();
			$commitment->pksFileCount = $commitment->pks->whereNotNull('berkas_pks')->count();

			$commitment->pksComplete = ($commitment->pksCount === $commitment->pksFileCount) ? 'Lengkap' : 'Belum Lengkap';

			$UserFiles = $commitment->userfiles->whereIn('kind', ['spvt', 'sptjmtanam', 'rta', 'sphtanam', 'logbook', 'spvp', 'sptjmproduksi', 'rpo', 'sphproduksi', 'logbookproduksi', 'formLa']);

			//pemeriksaan berkas tanam
			$tanamDocsKinds = ['spvt', 'sptjmtanam', 'rta', 'sphtanam'];
			$foundTanamKinds = $UserFiles->pluck('kind')->unique()->toArray();
			$missingTanamKinds = array_diff($tanamDocsKinds, $foundTanamKinds);

			if (!empty($missingTanamKinds)) {
				$tanamDocsComplete = false;
			} else {
				$tanamDocsComplete = true;
				foreach ($UserFiles as $file) {
					if (is_null($file->file_url)) {
						$tanamDocsComplete = false;
						break;
					}
				}
			}
			$commitment->tanamDocs = $tanamDocsComplete ? "Lengkap" : "Belum Lengkap";

			// pemeriksaan berkas produksi
			$produksiDocsKinds = ['spvp', 'sptjmproduksi', 'rpo', 'sphproduksi', 'formLa'];
			$foundProduksiKinds = $UserFiles->pluck('kind')->unique()->toArray();
			$missingProduksiKinds = array_diff($produksiDocsKinds, $foundProduksiKinds);

			if (!empty($missingProduksiKinds)) {
				$produksiDocsComplete = false;
			} else {
				$produksiDocsComplete = true;
				foreach ($UserFiles as $file) {
					if (is_null($file->file_url)) {
						$produksiDocsComplete = false;
						break;
					}
				}
			}
			$commitment->produksiDocs = $produksiDocsComplete ? "Lengkap" : "Belum Lengkap";

			$commitment->noIjin = str_replace(['/', '.'], "", $commitment->no_ijin);
			$commitment->avTanamStatus = $commitment->ajutanam()->exists() ? $commitment->ajutanam()->latest()->first()->status : "Tidak ada";
			$commitment->avProdStatus = $commitment->ajuproduksi()->exists() ? $commitment->ajuproduksi()->latest()->first()->status : "Tidak ada";
			$commitment->avSklStatus = $commitment->ajuskl()->exists() ? $commitment->ajuskl()->latest()->first()->status : "Tidak ada";
			$commitment->completeStatus = $commitment->completed()->exists() ? 'Lunas' : "Belum Lunas";

			$commitment->siapVerifTanam = (
				$commitment->sumRealisasiLuas === 0 || //harus ada penanaman
				$commitment->pksComplete !== 'Lengkap' || //pks harus lengkap
				$commitment->tanamDocs !== 'Lengkap') //berkas harus lengkap
				? 'Belum Siap' : 'Siap';

			$commitment->siapVerifProduksi =
				($commitment->volume_produksi * 1000 > $commitment->sumRealisasiPanen || //harus false
					$commitment->pksComplete !== 'Lengkap' || //pks harus lengkap
					$commitment->tanamDocs !== 'Lengkap' || //berkas tanam harus lengkap
					$commitment->produksiDocs !== 'Lengkap') //berkas produksi harus lengkap
				? 'Belum Siap' : 'Siap';

			$commitment->siapVerifSkl =
				($commitment->pksComplete !== 'Lengkap' || //pks harus lengkap
					$commitment->tanamDocs !== 'Lengkap' || //berkas tanam harus lengkap
					$commitment->produksiDocs !== 'Lengkap' || //berkas produksi harus lengkap
					$commitment->avProdStatus !== '6') //status verifikasi produksi harus 6
				? 'Belum Siap' : 'Siap';
			// $commitment->siapVerifSkl = ( $commitment->avProdStatus !== '4') ? 'Belum Siap' : 'Siap';

		}

		// Map data to the required format
		$mappedData = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'ijin_full' => $item->no_ijin,
				'noIjin' => $item->noIjin,
				'periodetahun' => $item->periodetahun,
				'tgl_terbit' => $item->tgl_ijin,
				'volume' => $item->volume_riph,
				'wajib_tanam' => floor($item->luas_wajib_tanam * 1000) / 1000 * 10000, //meter persegi
				'wajib_produksi' => $item->volume_produksi,
				'sumRealisasiLuas' => $item->sumRealisasiLuas, //meter persegi
				'sumRealisasiPanen' => $item->sumRealisasiPanen,
				'pksComplete' => $item->pksComplete,
				'tanamDocs' => $item->tanamDocs,
				'siapVerifTanam' => $item->siapVerifTanam, //
				'produksiDocs' => $item->produksiDocs,
				'siapVerifProduksi' => $item->siapVerifProduksi, //,
				'avTanamStatus' => $item->avTanamStatus,
				'avProdStatus' => $item->avProdStatus,
				'siapVerifSkl' => $item->siapVerifSkl, //
				'avSklStatus' => $item->avSklStatus,
				'completeStatus' => $item->completeStatus,
			];
		});

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $mappedData,
		]);
	}

	public function getAllMyCommitmentOld(Request $request)
	{
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);
		$npwp = Auth::user()->data_user->npwp_company;

		// Base query
		$query = PullRiph::select('id', 'no_ijin', 'periodetahun', 'tgl_ijin', 'volume_riph', 'luas_wajib_tanam', 'volume_produksi')
			->where('npwp', $npwp)
			->with(['skl', 'lokasi', 'pks', 'ajutanam', 'ajuproduksi', 'ajuskl', 'completed', 'userDocs']);

		// Apply search filter
		if (!empty($searchValue)) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('no_ijin', 'like', "%{$searchValue}%")
					->orWhere('periodetahun', 'like', "%{$searchValue}%")
					->orWhere('tgl_ijin', 'like', "%{$searchValue}%")
					->orWhere('volume_riph', 'like', "%{$searchValue}%")
					->orWhere('luas_wajib_tanam', 'like', "%{$searchValue}%")
					->orWhere('volume_produksi', 'like', "%{$searchValue}%");
			});
		}

		// Calculate total and filtered records
		$totalRecords = $query->count();
		$filteredRecords = $query->count();

		// Apply ordering
		if (!empty($order)) {
			$orderColumnIndex = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$orderColumn = $columns[$orderColumnIndex]['data'];
			switch ($orderColumn) {
				case 'ijin_full':
					$query = $query->orderBy('no_ijin', $orderDirection);
					break;
				case 'periodetahun':
					$query = $query->orderBy('periodetahun', $orderDirection);
					break;
				default:
					$query = $query->orderBy('id', 'asc');
					break;
			}
		}

		// Apply pagination
		$data = $query->skip($start)->take($length)->get();

		// Process data
		foreach ($data as $commitment) {
			$commitment->sumRealisasiLuas = $commitment->lokasi->sum('luas_tanam');
			$commitment->sumRealisasiPanen = $commitment->lokasi->sum('volume');
			$commitment->pksCount = $commitment->pks->count();
			$commitment->pksFileCount = $commitment->pks->whereNotNull('berkas_pks')->count();

			$commitment->pksComplete = ($commitment->pksCount === $commitment->pksFileCount) ? 'Lengkap' : 'Belum Lengkap';

			$UserFiles = $commitment->userfiles->whereIn('kind', ['spvt', 'sptjmtanam', 'rta', 'sphtanam', 'logbook', 'spvp', 'sptjmproduksi', 'rpo', 'sphproduksi', 'logbookproduksi', 'formLa']);

			//pemeriksaan berkas tanam
			$tanamDocsKinds = ['spvt', 'sptjmtanam', 'rta', 'sphtanam', 'logbook'];
			$foundTanamKinds = $UserFiles->pluck('kind')->unique()->toArray();
			$missingTanamKinds = array_diff($tanamDocsKinds, $foundTanamKinds);

			if (!empty($missingTanamKinds)) {
				$tanamDocsComplete = false;
			} else {
				$tanamDocsComplete = true;
				foreach ($UserFiles as $file) {
					if (is_null($file->file_url)) {
						$tanamDocsComplete = false;
						break;
					}
				}
			}
			$commitment->tanamDocs = $tanamDocsComplete ? "Lengkap" : "Belum Lengkap";

			// pemeriksaan berkas produksi
			$produksiDocsKinds = ['spvp', 'sptjmproduksi', 'rpo', 'sphproduksi', 'logbook', 'formLa'];
			$foundProduksiKinds = $UserFiles->pluck('kind')->unique()->toArray();
			$missingProduksiKinds = array_diff($produksiDocsKinds, $foundProduksiKinds);

			if (!empty($missingProduksiKinds)) {
				$produksiDocsComplete = false;
			} else {
				$produksiDocsComplete = true;
				foreach ($UserFiles as $file) {
					if (is_null($file->file_url)) {
						$produksiDocsComplete = false;
						break;
					}
				}
			}
			$commitment->produksiDocs = $produksiDocsComplete ? "Lengkap" : "Belum Lengkap";

			$commitment->noIjin = str_replace(['/', '.'], "", $commitment->no_ijin);
			$commitment->avTanamStatus = $commitment->ajutanam()->exists() ? $commitment->ajutanam()->latest()->first()->status : "Tidak ada";
			$commitment->avProdStatus = $commitment->ajuproduksi()->exists() ? $commitment->ajuproduksi()->latest()->first()->status : "Tidak ada";
			$commitment->avSklStatus = $commitment->ajuskl()->exists() ? $commitment->ajuskl()->latest()->first()->status : "Tidak ada";
			$commitment->completeStatus = $commitment->completed()->exists() ? 'Lunas' : "Belum Lunas";

			$commitment->siapVerifTanam = (
				$commitment->sumRealisasiLuas === 0 || //harus ada penanaman
				$commitment->pksComplete !== 'Lengkap' || //pks harus lengkap
				$commitment->tanamDocs !== 'Lengkap') //berkas harus lengkap
				? 'Belum Siap' : 'Siap';

			$commitment->siapVerifProduksi =
				($commitment->volume_produksi > $commitment->sumRealisasiPanen || //harus false
					$commitment->pksComplete !== 'Lengkap' || //pks harus lengkap
					$commitment->tanamDocs !== 'Lengkap' || //berkas tanam harus lengkap
					$commitment->produksiDocs !== 'Lengkap') //berkas produksi harus lengkap
				? 'Belum Siap' : 'Siap';

			$commitment->siapVerifSkl =
				($commitment->pksComplete !== 'Lengkap' || //pks harus lengkap
					$commitment->tanamDocs !== 'Lengkap' || //berkas tanam harus lengkap
					$commitment->produksiDocs !== 'Lengkap' || //berkas produksi harus lengkap
					$commitment->avProdStatus !== '6') //status verifikasi produksi harus 6
				? 'Belum Siap' : 'Siap';
			// $commitment->siapVerifSkl = ( $commitment->avProdStatus !== '4') ? 'Belum Siap' : 'Siap';

		}

		// Map data to the required format
		$mappedData = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'ijin_full' => $item->no_ijin,
				'noIjin' => $item->noIjin,
				'periodetahun' => $item->periodetahun,
				'tgl_terbit' => $item->tgl_ijin,
				'volume' => $item->volume_riph,
				'wajib_tanam' => floor($item->luas_wajib_tanam * 1000) / 1000 * 10000, //meter persegi
				'wajib_produksi' => $item->volume_produksi,
				'sumRealisasiLuas' => $item->sumRealisasiLuas, //meter persegi
				'sumRealisasiPanen' => $item->sumRealisasiPanen,
				'pksComplete' => $item->pksComplete,
				'tanamDocs' => $item->tanamDocs,
				'siapVerifTanam' => $item->siapVerifTanam,
				'produksiDocs' => $item->produksiDocs,
				'siapVerifProduksi' => $item->siapVerifProduksi, //,
				'avTanamStatus' => $item->avTanamStatus,
				'avProdStatus' => $item->avProdStatus,
				'siapVerifSkl' => $item->siapVerifSkl,
				'avSklStatus' => $item->avSklStatus,
				'completeStatus' => $item->completeStatus,
			];
		});

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $mappedData,
		]);
	}

	public function getPksById($id)
	{
		$pks = Pks::select('id', 'kode_poktan', 'npwp', 'no_ijin', 'no_perjanjian', 'tgl_perjanjian_start', 'tgl_perjanjian_end', 'varietas_tanam', 'periode_tanam', 'berkas_pks')
			->with(['varietas' => function ($query) {
				$query->select('id', 'nama_varietas');
			}])
			->where('id', $id)->first();

		$commitment = PullRiph::where('no_ijin', $pks->no_ijin)->first();
		$npwp = str_replace(['.', '-'], '', $commitment->npwp);
		$periodetahun = $commitment->periodetahun;

		$berkasPks = UserFile::where('kind', 'pks')->where('no_ijin', $pks->no_ijin)->where('file_code', $pks->kode_poktan)->first();
		$linkBerkas = optional($berkasPks)->file_url ?? null;

		if ($pks) {
			$data = $pks->toArray();
			$data['linkBerkas'] = $linkBerkas;

			return response()->json($data);
		} else {
			return response()->json([], 404);
		}
	}

	public function timeline(Request $request, $noIjin)
	{
		// Format the noIjin parameter
		$noIjin = $this->formatNoIjin($noIjin);

		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);

		// Base query
		$query = Lokasi::select('id', 'kode_spatial', 'kode_poktan', 'no_ijin', 'tgl_tanam', 'tgl_panen')
			->where('no_ijin', $noIjin)
			->with([
				'pks' => function ($query) {
					$query->select('id', 'no_ijin', 'kode_poktan', 'tgl_perjanjian_start', 'tgl_perjanjian_end');
				},
				'pullriph' => function ($query) {
					$query->select('id', 'no_ijin', 'tgl_ijin', 'tgl_akhir');
				}
			]);

		// Calculate total records
		$totalRecords = $query->count();

		// Apply search filter if any
		if (!empty($searchValue)) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('kode_spatial', 'like', "%{$searchValue}%")
					->orWhere('no_ijin', 'like', "%{$searchValue}%");
			});
		}

		// Calculate filtered records
		$filteredRecords = $query->count();

		// Apply order
		if (!empty($order)) {
			$orderColumnIndex = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$orderColumn = $columns[$orderColumnIndex]['data'];

			switch ($orderColumn) {
				case 'kode_spatial':
				case 'no_ijin':
					$query = $query->orderBy($orderColumn, $orderDirection);
					break;
			}
		}

		// Apply pagination
		$lokasis = $query->skip($start)
			->take($length)
			->get();

		// Map data to the required format
		$data = $lokasis->map(function ($item) {
			return [
				'id' => $item->id,
				'kode_spatial' => $item->kode_spatial,
				'mulai_ijin' => $item->pullriph->tgl_ijin ?? '',
				'akhir_ijin' => $item->pullriph->tgl_akhir ?? '',
				'awal_pks' => $item->pks->tgl_perjanjian_start ?? '',
				'akhir_pks' => $item->pks->tgl_perjanjian_end ?? '',
				'awal_tanam' => $item->tgl_tanam,
				'akhir_tanam' => $item->tgl_akhir_tanam,
				'awal_panen' => $item->tgl_panen,
				'akhir_panen' => $item->tgl_akhir_panen
			];
		});

		// Return response in JSON format
		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $data,
		]);
	}

	public function getPksByIjin(Request $request, $noIjin)
	{
		$noIjin = $this->formatNoIjin($noIjin);

		$commitment = PullRiph::where('no_ijin', $noIjin)->first();
		if (!$commitment) {
			return response()->json([
				"draw" => intval($request->input('draw')),
				"recordsTotal" => 0,
				"recordsFiltered" => 0,
				"data" => [],
			]);
		}

		$pksQuery = Pks::query()
			->select('id', 'kode_poktan', 'no_ijin', 'nama_poktan', 'no_perjanjian', 'tgl_perjanjian_start', 'tgl_perjanjian_end', 'varietas_tanam', 'periode_tanam', 'berkas_pks', 'status', 'note')
			->where('no_ijin', $commitment->no_ijin)
			->withCount('lokasi')
			->with(['lokasi' => function ($query) {
				$query->selectRaw('kode_poktan, no_ijin, sum(luas_lahan) as total_luas_lahan')
					->groupBy('kode_poktan', 'no_ijin');
			}]);

		$pksRecords = $pksQuery->get();

		// Retrieve UserFile records that match no_ijin and have file_code matching kode_poktan
		$userFiles = UserFile::query()
			->where('no_ijin', $commitment->no_ijin)
			->whereIn('file_code', $pksRecords->pluck('kode_poktan'))
			->select('no_ijin', 'file_code', 'file_url') // Select only file_code and file_url
			->get()
			->groupBy('file_code');

		// Merge UserFile records into Pks results
		$pksRecords->each(function ($pksRecord) use ($userFiles) {
			$userFile = $userFiles->get($pksRecord->kode_poktan);
			$pksRecord->file_url = $userFile ? $userFile->first()->file_url : null;
		});

		// Paginate according to DataTables request
		$draw = $request->input('draw');
		$length = $request->input('length');
		$start = $request->input('start');

		if ($length <= 0) {
			$length = 10; // Set to default if length <= 0
		}

		$paginatedData = $pksRecords->slice($start, $length)->values();
		$totalRecords = $pksRecords->count();

		foreach ($paginatedData as $item) {
			if (is_null($item->tgl_perjanjian_start) || is_null($item->varietas_tanam) || is_null($item->berkas_pks)) {
				$item->statusData = null;
			} else {
				$item->statusData = 'Filled'; // Set appropriate status if not null
			}

			$item->total_luas_lahan = $item->lokasi->sum('total_luas_lahan');
		}

		return response()->json([
			"draw" => intval($draw),
			"recordsTotal" => $totalRecords,
			"recordsFiltered" => $totalRecords,
			"data" => $paginatedData,
		]);
	}


	public function getLokasiByPks(Request $request, $noIjin, $poktanId)
	{
		// Format the noIjin parameter
		$noIjin = $this->formatNoIjin($noIjin);

		$dataRealisasi = Lokasi::select('origin', 'no_ijin', 'tcode', 'kode_poktan', 'luas_tanam', 'volume')
			->where('no_ijin', $noIjin)
			->where('kode_poktan', $poktanId)
			->get();

		$realisasiLuasTanam = $dataRealisasi->sum('luas_tanam');
		$realisasiProduksi = $dataRealisasi->sum('volume');

		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);

		// Base query
		$query = Lokasi::where('no_ijin', $noIjin)
			->where('kode_poktan', $poktanId)
			->with('spatial');

		// Calculate total records
		$totalRecords = $query->count();

		// Apply search filter if any
		if (!empty($searchValue)) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('ktp_petani', 'like', "%{$searchValue}%")
					->orWhere('kode_spatial', 'like', "%{$searchValue}%");
			});
		}

		// Calculate filtered records
		$filteredRecords = $query->count();

		// Apply order
		if (!empty($order)) {
			$orderColumnIndex = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$orderColumn = $columns[$orderColumnIndex]['data'];

			switch ($orderColumn) {
				case 'ktp_petani':
				case 'spatial_petani':
				case 'kode_spatial':
				case 'spatial_ktp':
					$query = $query->orderBy($orderColumn, $orderDirection);
					break;
			}
		}

		// Apply pagination
		$pks = $query->skip($start)
			->take($length)
			->get();

		// Map data to the required format
		$data = $pks->map(function ($item) {
			$spatial = $item->spatial;
			return [
				'id' => $item->id,
				'origin' => $item->origin,
				'kode_spatial' => $item->kode_spatial,
				'tcode' => $item->tcode,
				'ktp_petani' => $item->ktp_petani,
				'nama_petani' => $item->nama_petani,
				'spatial_petani' => $spatial ? $spatial->nama_petani : null,
				'spatial_ktp' => $spatial ? $spatial->ktp_petani : null,
				'luas_tanam' => $item->luas_tanam,
				'tgl_tanam' => $item->tgl_tanam ? date('d-m-Y', strtotime($item->tgl_tanam)) : null,
				'volume_panen' => $item->volume,
				'tgl_panen' => $item->tgl_panen ? date('d-m-Y', strtotime($item->tgl_panen)) : null,
			];
		});

		// Return response in JSON format
		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'totalRealisasiLuas' => $realisasiLuasTanam,
			'totalRealisasiProduksi' => $realisasiProduksi,
			'data' => $data,
		]);
	}

	public function getLokasiByIjin(Request $request, $noIjin)
	{
		// Format the noIjin parameter
		$noIjin = $this->formatNoIjin($noIjin);

		// Memeriksa apakah ada minimal satu record dengan status yang tidak null
		// $hasNonNullStatus = Lokasi::where('no_ijin', $noIjin)
		// 	->whereNotNull('status')
		// 	->exists();

		// if (!$hasNonNullStatus) {
		// 	// Jika tidak ada record dengan status yang tidak null, kembalikan data kosong
		// 	return response()->json([
		// 		'draw' => $request->input('draw', 1),
		// 		'recordsTotal' => 0,
		// 		'recordsFiltered' => 0,
		// 		'totalRealisasiLuas' => 0,
		// 		'totalRealisasiProduksi' => 0,
		// 		'data' => [],
		// 	]);
		// }

		// Jika ada minimal satu record dengan status yang tidak null, ambil semua data
		$dataRealisasi = Lokasi::select('no_ijin', 'luas_tanam', 'volume', 'status', 'is_selected')
			->where('no_ijin', $noIjin)
			->get();

		$realisasiLuasTanam = $dataRealisasi->sum('luas_tanam');
		$realisasiProduksi = $dataRealisasi->sum('volume');

		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);

		// Base query
		$query = Lokasi::where('no_ijin', $noIjin)
			->with(['spatial', 'masteranggota.masterpoktan']);

		// Calculate total records
		$totalRecords = $query->count();

		// Apply search filter if any
		if (!empty($searchValue)) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('nama_petani', 'like', "%{$searchValue}%")
					->orWhere('ktp_petani', 'like', "%{$searchValue}%")
					->orWhere('kode_spatial', 'like', "%{$searchValue}%");
			});
		}

		// Calculate filtered records
		$filteredRecords = $query->count();

		// Apply order
		if (!empty($order)) {
			$orderColumnIndex = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$orderColumn = $columns[$orderColumnIndex]['data'];

			switch ($orderColumn) {
				case 'nama_kelompok':
				case 'ktp_petani':
				case 'nama_petani':
				case 'kode_spatial':
				case 'spatial_ktp':
					$query = $query->orderBy($orderColumn, $orderDirection);
					break;
			}
		}

		// Apply pagination
		$lokasis = $query->skip($start)
			->take($length)
			->get();

		// Map data to the required format
		$data = $lokasis->map(function ($item) {
			$spatial = $item->spatial;
			return [
				'id' => $item->id,
				'tcode' => $item->tcode,
				'nama_kelompok' => $item->masteranggota->masterpoktan->nama_kelompok,
				'kode_poktan' => $item->kode_poktan,
				'kode_spatial' => $item->kode_spatial,
				'ktp_petani' => $item->ktp_petani,
				'nama_petani' => $item->masteranggota->nama_petani,
				'spatial_petani' => $spatial ? $spatial->nama_petani : null,
				'spatial_ktp' => $spatial ? $spatial->ktp_petani : null,
				'luas_lahan' => $item->luas_lahan,
				'luas_tanam' => $item->luas_tanam,
				'tgl_tanam' => $item->tgl_tanam ? date('d-m-Y', strtotime($item->tgl_tanam)) : null,
				'volume_panen' => $item->volume,
				'tgl_panen' => $item->tgl_panen ? date('d-m-Y', strtotime($item->tgl_panen)) : null,
				'prodStatus' => $item->prodStatus,
				'distStatus' => $item->distStatus,
				'status' => $item->status,
			];
		});

		// Return response in JSON format
		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'totalRealisasiLuas' => $realisasiLuasTanam,
			'totalRealisasiProduksi' => $realisasiProduksi,
			'data' => $data,
		]);
	}

	public function getLokasiByIjinNik($noIjin, $nik)
	{
		$noIjin = $this->formatNoIjin($noIjin);

		// Base query
		$query = Lokasi::where('no_ijin', $noIjin)
			->where('ktp_petani', $nik)
			->with(['spatial', 'masteranggota.masterpoktan', 'fototanam', 'fotoproduksi'])
			->get();

		// Map data to the required format
		$data = $query->map(function ($item) {
			$spatial = $item->spatial;
			$masterAnggota = $item->masteranggota->first();
			$nama_kelompok = optional(optional($masterAnggota)->masterpoktan)->nama_kelompok;

			$fotoTanam = $item->fototanam->map(function ($foto) {
				return [
					'id' => $foto->id,
					'url' => $foto->filename,
				];
			});

			$fotoProduksi = $item->fotoproduksi->map(function ($foto) {
				return [
					'id' => $foto->id,
					'url' => $foto->filename,
				];
			});

			return [
				'id' => $item->id,
				'nama_kelompok' => $item->nama_poktan,
				'kode_spatial' => optional($spatial)->kode_spatial,
				'ktp_petani' => $item->ktp_petani,
				'nama_petani' => optional($item->masteranggota)->nama_petani,
				'nama_kelompok_poktan' => $nama_kelompok,
				'spatial_petani' => optional($spatial)->nama_petani,
				'spatial_ktp' => optional($spatial)->ktp_petani,
				'latitude' => optional($spatial)->latitude,
				'longitude' => optional($spatial)->longitude,
				'polygon' => optional($spatial)->polygon,
				'luas_lahan' => $item->luas_lahan,
				'luas_tanam' => $item->luas_tanam,
				'awal_tanam' => $item->tgl_tanam ? date('d-m-Y', strtotime($item->tgl_tanam)) : null,
				'akhir_tanam' => $item->tgl_akhir_tanam ? date('d-m-Y', strtotime($item->tgl_akhir_tanam)) : null,
				'volume_panen' => $item->volume,
				'tgl_panen' => $item->tgl_panen ? date('d-m-Y', strtotime($item->tgl_panen)) : null,
				'status' => $item->status,
				'fototanam' => $fotoTanam,
				'fotoProduksi' => $fotoProduksi,
			];
		});

		// Return response in JSON format
		return response()->json($data);
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
		Log::info('Request data:', $request->all());

		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('searchValue', '');
		$kabupatenId = $request->input('kabupaten_id');
		$statusLahan = $request->input('status_lahan');
		$statusMitra = $request->input('status_mitra');

		$orderColumnIndex = $request->input('order.0.column', 0);
		$orderDirection = $request->input('order.0.dir', 'asc');

		$orderableColumns = ['kode_spatial', 'nama_anggota', 'luas_lahan', 'nama_kabupaten'];
		$orderColumn = $orderableColumns[$orderColumnIndex];

		$query = MasterSpatial::with([
			'kabupaten:kabupaten_id,nama_kab',
			'anggota:ktp_petani,nama_petani',
		]);

		if ($searchValue) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('kode_spatial', 'like', "%$searchValue%")
					->orWhere('ktp_petani', 'like', "%$searchValue%")
					->orWhereHas('anggota', function ($q) use ($searchValue) {
						$q->where('nama_petani', 'like', "%$searchValue%");
					})
					->orWhereHas('kabupaten', function ($q) use ($searchValue) {
						$q->where('nama_kab', 'like', "%$searchValue%");
					});
			});
		}

		// Handle sorting
		if ($orderColumn === 'nama_anggota') {
			$query = $query->join('t2024_master_anggotas', 't2024_master_spatials.ktp_petani', '=', 't2024_master_anggotas.ktp_petani')
				->orderBy('t2024_master_anggotas.nama_petani', $orderDirection);
		} elseif ($orderColumn === 'nama_kabupaten') {
			$query = $query->join('data_kabupatens', 't2024_master_spatials.kabupaten_id', '=', 'data_kabupatens.kabupaten_id')
				->orderBy('data_kabupatens.nama_kab', $orderDirection);
		} else {
			$query = $query->orderBy("t2024_master_spatials.$orderColumn", $orderDirection);
		}

		if ($kabupatenId) {
			$query->where('t2024_master_spatials.kabupaten_id', $kabupatenId);
		}

		if (isset($statusMitra)) {
			$query->where('t2024_master_spatials.status', $statusMitra);
		}

		if (isset($statusLahan)) {
			$query->where('t2024_master_spatials.is_active', $statusLahan);
		}

		// Count total records and filtered records
		$totalRecords = MasterSpatial::count();
		$filteredRecords = $query->count();

		// Fetch paginated data
		$spatials = $query->offset($start)->limit($length)->get()->map(function ($item) {
			return [
				'id' => $item->id,
				'kode_spatial' => $item->kode_spatial,
				'luas_lahan' => $item->luas_lahan,
				'ktp_petani' => $item->ktp_petani,
				'nama_anggota' => $item->anggota ? $item->anggota->nama_petani : null,
				'kml_url' => $item->kml_url,
				'kabupaten_id' => $item->kabupaten_id,
				'nama_kabupaten' => $item->kabupaten ? $item->kabupaten->nama_kab : null,
				'active' => $item->is_active ? $item->is_active : null,
				'status' => $item->status ? $item->status : null,
			];
		});

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
				'kode_poktan' => $item->kode_poktan,
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
			'provinsi' => function ($query) {
				$query->select('provinsi_id', 'nama');
			},
			'kabupaten' => function ($query) {
				$query->select('kabupaten_id', 'nama_kab');
			},
			'kecamatan' => function ($query) {
				$query->select('kecamatan_id', 'nama_kecamatan');
			},
			'desa' => function ($query) {
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


	public function getRequestVerif(Request $request)
	{
		$user = Auth::user();
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);
		$periodeFilter = $request->input('periode', null);
		$statusFilter = $request->input('status', null);

		$completedNoIjins = Completed::pluck('no_ijin');

		$query = AjuVerifikasi::select('id', 'kind', 'tcode', 'npwp', 'no_ijin', 'check_by', 'verif_at', 'status', 'note', 'created_at')
			->orderBy('id', 'ASC')
			->whereNotIn('no_ijin', $completedNoIjins)
			->where(function ($query) use ($user) {
				$query->whereHas('assignments', function ($query) use ($user) {
					$query->where('user_id', $user->id);
				})
					->orWhere(function ($query) use ($user) {
						if ($user->id == 1) {
							$query->where('id', '>', 0);
						}
					});
			})
			->with([
				'verifikator:id,name',
				'datauser:id,npwp_company,company_name',
				'commitment:id,no_ijin,periodetahun',
				'assignments:id,tcode,pengajuan_id,user_id',
				'assignments.user:id,name'
			]);


		if ($periodeFilter) {
			$query->whereHas('commitment', function ($query) use ($periodeFilter) {
				$query->where('periodetahun', $periodeFilter);
			});
		}

		//filter ini untuk daftar pengajuan tanam
		if ($statusFilter !== null) {
			switch ($statusFilter) {
				case 1:
					$query->where('status', '>', 0);
					break;
				case 2:
					$query->whereBetween('status', [1, 5]);
					break;
				case 3:
					$query->where('status', '>', 5);
					break;
				default:
					$query->whereBetween('status', [1, 5]);
					break;
			}
		} else {
			$query->whereBetween('status', [0, 5]);
		}

		$data = $query->get();

		$data = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'tcode' => $item->tcode,
				'check_by' => $item->check_by,
				// 'verifikator' => $item->verifikator ? $item->verifikator->name : null,
				'periode' => $item->commitment ? $item->commitment->periodetahun : null,
				'perusahaan' => $item->datauser ? $item->datauser->company_name : null,
				'no_ijin' => $item->no_ijin,
				'created_at' => $item->created_at,
				'verif_at' => $item->verif_at,
				'status' => $item->status,
				'ijin' => str_replace(['/', '.', '-'], '', $item->no_ijin),
				'assignments' => $item->assignments->map(function ($assignment) {
					return [
						'id' => $assignment->id,
						'user_id' => $assignment->user_id,
						'user_name' => $assignment->user ? $assignment->user->name : null,
						'tcode' => $assignment ? $assignment->tcode : null,
						'no_sk' => $assignment->no_sk,
						'tgl_sk' => $assignment->tgl_sk,
						'file' => $assignment->file,
					];
				}),
				'tableSource' => $item->kind,
			];
		});

		if ($searchValue) {
			$data = $data->filter(function ($item) use ($searchValue) {
				return strpos(strtolower($item['periode']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['perusahaan']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['no_ijin']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['ijin']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['status']), strtolower($searchValue)) !== false;
			})->values();
		}

		if (!empty($order)) {
			$orderColumn = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$columnName = $columns[$orderColumn]['data'];

			$data = $data->sortBy(function ($item) use ($columnName) {
				return $item[$columnName];
			}, SORT_REGULAR, $orderDirection === 'desc')->values();
		}

		$totalRecords = $data->count();
		$filteredRecords = $data->count();

		$verifList = $data->slice($start, $length)->values();

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $verifList,
		]);
	}

	public function getRequestSkl(Request $request)
	{
		$user = Auth::user();
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);
		$periodeFilter = $request->input('periode', null);
		$statusFilter = $request->input('status', null);

		$query = AjuVerifSkl::orderBy('id', 'ASC')
			->where('status', '!=', 4)
			->with([
				'datauser:id,npwp_company,company_name',
				'verifikator:id,name',
				'recomendBy:id,name',
				'direktur:id,name',
				'commitment:id,no_ijin,periodetahun',
			]);

		if ($periodeFilter) {
			$query->whereHas('commitment', function ($query) use ($periodeFilter) {
				$query->where('periodetahun', $periodeFilter);
			});
		}

		//filter ini untuk daftar pengajuan tanam
		if ($statusFilter !== null) {
			switch ($statusFilter) {
				case 1:
					$query->where('status', '>', 0);
					break;
				case 2:
					$query->whereBetween('status', [1, 5]);
					break;
				case 3:
					$query->where('status', '>', 5);
					break;
				default:
					$query->whereBetween('status', [1, 5]);
					break;
			}
		} else {
			$query->whereBetween('status', [0, 5]);
		}

		$data = $query->get();

		$data = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'tcode' => $item->tcode,
				'check_by' => $item->check_by,
				'datauser' => $item->datauser ? $item->datauser->name : null,
				'verifikator' => $item->verifikator ? $item->verifikator->name : null,
				'recomendby' => $item->recomendby ? $item->recomendby->name : null,
				'direktur' => $item->direktur ? $item->direktur->name : null,
				'periode' => $item->commitment ? $item->commitment->periodetahun : null,
				'perusahaan' => $item->datauser ? $item->datauser->company_name : null,
				'no_ijin' => $item->no_ijin,
				'ijin' => str_replace(['/', '.', '-'], '', $item->no_ijin),
				'created_at' => $item->created_at,
				'verif_at' => $item->verif_at,
				'report_url' => $item->report_url,
				'draft_url' => $item->draft_url,
				'no_skl' => $item->no_skl,
				'published_at' => $item->published_at,
				'skl_url' => $item->skl_url,
				'status' => $item->status,
			];
		});

		if ($searchValue) {
			$data = $data->filter(function ($item) use ($searchValue) {
				return strpos(strtolower($item['periode']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['perusahaan']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['no_ijin']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['ijin']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['status']), strtolower($searchValue)) !== false;
			})->values();
		}

		if (!empty($order)) {
			$orderColumn = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$columnName = $columns[$orderColumn]['data'];

			$data = $data->sortBy(function ($item) use ($columnName) {
				return $item[$columnName];
			}, SORT_REGULAR, $orderDirection === 'desc')->values();
		}

		$totalRecords = $data->count();
		$filteredRecords = $data->count();

		$verifList = $data->slice($start, $length)->values();

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $verifList,
		]);
	}

	public function getDataPengajuan($noIjin)
	{
		$noIjin = $this->formatNoIjin($noIjin);

		$commitment = PullRiph::where('no_ijin', $noIjin)->first();
		$verifTanam = AjuVerifikasi::where('no_ijin', $noIjin)->where('kind', 'TANAM')->latest()->first() ?? new AjuVerifikasi();
		$verifProduksi = AjuVerifikasi::where('no_ijin', $noIjin)->where('kind', 'PRODUKSI')->latest()->first() ?? new AjuVerifikasi();
		$verifSkl = AjuVerifSkl::where('no_ijin', $noIjin)->latest()->first() ?? new AjuVerifSkl();

		$userFiles = $commitment->userfiles
			->whereIn('kind', [
				'spvt',
				'sptjmtanam',
				'rta',
				'sphtanam',
				'logbook',
				'spvp',
				'sptjmproduksi',
				'rpo',
				'sphproduksi',
				'logbookproduksi',
				'formLa'
			]);

		$tanamDocsKinds = ['spvt', 'sptjmtanam', 'rta', 'sphtanam', 'logbook'];
		$prodDocsKinds = ['spvp', 'sptjmproduksi', 'rpo', 'sphproduksi', 'formLa'];

		$docTanamStatuses = [];
		$docProdStatuses = [];

		foreach ($tanamDocsKinds as $kind) {
			$document = $userFiles->firstWhere('kind', $kind);
			if ($document) {
				$docTanamStatuses[$kind] = $document->file_url ? 'Ada' : 'Tidak Ada';
			} else {
				$docTanamStatuses[$kind] = 'Tidak Ada';
			}
		}

		foreach ($prodDocsKinds as $kind) {
			$document = $userFiles->firstWhere('kind', $kind);
			if ($document) {
				$docProdStatuses[$kind] = $document->file_url ? 'Ada' : 'Tidak Ada';
			} else {
				$docProdStatuses[$kind] = 'Tidak Ada';
			}
		}

		// Filter and process tanam files
		$tanamFiles = $userFiles->filter(function ($file) use ($tanamDocsKinds) {
			return in_array($file->kind, $tanamDocsKinds);
		})->map(function ($file) use ($docTanamStatuses) {
			$file->berkas = $docTanamStatuses[$file->kind] ?? 'Tidak Ada';
			return $file;
		})->values()->toArray();

		// Filter and process prod files
		$prodFiles = $userFiles->filter(function ($file) use ($prodDocsKinds) {
			return in_array($file->kind, $prodDocsKinds);
		})->map(function ($file) use ($docProdStatuses) {
			$file->berkas = $docProdStatuses[$file->kind] ?? 'Tidak Ada';
			return $file;
		})->values()->toArray();

		$userDocs = UserDocs::where('no_ijin', $noIjin)->first() ?? new UserDocs();

		$pks = Pks::where('no_ijin', $noIjin)->get() ?? new Pks();
		$lokasis = Lokasi::where('no_ijin', $noIjin)->get() ?? new Lokasi();

		$data = [
			'company' => $commitment->datauser->company_name,
			'noIjin' => $commitment->no_ijin,
			'periode' => $commitment->periodetahun,
			'avtDate' => $verifTanam->created_at,
			'avtVerifAt' => $verifTanam->verif_at,
			'avtStatus' => $verifTanam->status,
			'avtMetode' => $verifTanam->metode,
			'avtNote' => $verifTanam->note,

			'avpDate' => $verifProduksi->created_at,
			'avpVerifAt' => $verifProduksi->verif_at,
			'avpMetode' => $verifProduksi->metode,
			'avpNote' => $verifProduksi->note,
			'avpStatus' => $verifProduksi->status,
			'avsklDate' => $verifSkl->created_at,
			'avsklVerifAt' => $verifSkl->verif_at,
			'avsklStatus' => $verifSkl->status,
			'avsklMetode' => $verifSkl->metode,
			'avsklNote' => $verifSkl->note,
			'avsklRecomendBy' => $verifSkl->recomend_by,
			'avsklRecomendAt' => $verifSkl->recomend_at,
			'avsklRecomendNote' => $verifSkl->recomend_note,
			'avsklApprovedBy' => $verifSkl->approved_by,
			'avsklApprovedAt' => $verifSkl->approved_at,
			'avsklPublishedAt' => $verifSkl->published_at,

			'wajibTanam' => $commitment->luas_wajib_tanam,
			'wajibProduksi' => $commitment->volume_produksi,
			'realisasiTanam' => $commitment->lokasi->sum('luas_tanam'),
			'realisasiProduksi' => $commitment->lokasi->sum('volume'),
			'countAnggota' => $commitment->lokasi->groupBy('ktp_petani')->count(),
			'countPoktan' => $commitment->lokasi->groupBy('kode_poktan')->count(),
			'countPks' => $pks->where('berkas_pks', '!=', null)->count(),
			'countSpatial' => $lokasis->count(),
			'countTanam' => $lokasis->where('luas_tanam', '!=', null)->count(),
			// 'userDocs' => $userDocs,
			'tanamFiles' => $tanamFiles,
			'prodFiles' => $prodFiles,
		];

		return response()->json($data);
	}

	public function getRequestVerifTanam(Request $request)
	{
		$user = Auth::user();
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);
		$periodeFilter = $request->input('periode', null);
		$statusFilter = $request->input('status', null);

		// Build the query
		$query = AjuVerifikasi::select('id', 'kind', 'tcode', 'npwp', 'no_ijin', 'check_by', 'verif_at', 'status', 'note', 'created_at')
			->where('kind', 'TANAM')
			->where(function ($query) use ($user) {
				$query->whereHas('assignments', function ($query) use ($user) {
					$query->where('user_id', $user->id);
				})
					->orWhere(function ($query) use ($user) {
						if ($user->id == 1) {
							$query->where('id', '>', 0); // or any condition that always returns true
						}
					});
			})
			->with([
				'verifikator:id,name',
				'datauser:id,npwp_company,company_name',
				'commitment:id,no_ijin,periodetahun',
				'assignments:id,tcode,pengajuan_id,user_id',
				'assignments.user:id,name'
			]);

		if ($periodeFilter) {
			$query->whereHas('commitment', function ($query) use ($periodeFilter) {
				$query->where('periodetahun', $periodeFilter);
			});
		}

		//filter ini untuk daftar pengajuan tanam
		if ($statusFilter !== null) {
			switch ($statusFilter) {
				case 1:
					$query->where('status', '>', 0);
					break;
				case 2:
					$query->whereBetween('status', [1, 5]);
					break;
				case 3:
					$query->where('status', '>', 5);
					break;
				default:
					$query->whereBetween('status', [1, 5]);
					break;
			}
		} else {
			$query->whereBetween('status', [1, 7]);
		}

		$data = $query->get();

		$data = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'tcode' => $item->tcode,
				'check_by' => $item->check_by,
				// 'verifikator' => $item->verifikator ? $item->verifikator->name : null,
				'periode' => $item->commitment ? $item->commitment->periodetahun : null,
				'perusahaan' => $item->datauser ? $item->datauser->company_name : null,
				'no_ijin' => $item->no_ijin,
				'created_at' => $item->created_at,
				'verif_at' => $item->verif_at,
				'status' => $item->status,
				'ijin' => str_replace(['/', '.', '-'], '', $item->no_ijin),
				'assignments' => $item->assignments->map(function ($assignment) {
					return [
						'id' => $assignment->id,
						'user_id' => $assignment->user_id,
						'user_name' => $assignment->user ? $assignment->user->name : null,
						'tcode' => $assignment ? $assignment->tcode : null,
						'no_sk' => $assignment->no_sk,
						'tgl_sk' => $assignment->tgl_sk,
						'file' => $assignment->file,
					];
				}),
				'tableSource' => $item->kind,
			];
		});

		if ($searchValue) {
			$data = $data->filter(function ($item) use ($searchValue) {
				return strpos(strtolower($item['periode']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['perusahaan']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['no_ijin']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['ijin']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['status']), strtolower($searchValue)) !== false;
			})->values();
		}

		if (!empty($order)) {
			$orderColumn = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$columnName = $columns[$orderColumn]['data'];

			$data = $data->sortBy(function ($item) use ($columnName) {
				return $item[$columnName];
			}, SORT_REGULAR, $orderDirection === 'desc')->values();
		}

		$totalRecords = $data->count();
		$filteredRecords = $data->count();

		$verifList = $data->slice($start, $length)->values();

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $verifList,
		]);
	}

	public function getRequestVerifProduksi(Request $request)
	{
		$user = Auth::user();
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);
		$periodeFilter = $request->input('periode', null);
		$statusFilter = $request->input('status', null);

		// Build the query
		$query = AjuVerifikasi::select('id', 'kind', 'tcode', 'npwp', 'no_ijin', 'check_by', 'verif_at', 'status', 'note', 'created_at')
			->where(function ($query) use ($user) {
				$query->whereHas('assignments', function ($query) use ($user) {
					$query->where('user_id', $user->id);
				})
					->orWhere(function ($query) use ($user) {
						if ($user->id == 1) {
							$query->where('id', '>', 0); // or any condition that always returns true
						}
					});
			})
			->where('kind', 'PRODUKSI')
			->with([
				'verifikator:id,name',
				'datauser:id,npwp_company,company_name',
				'commitment:id,no_ijin,periodetahun',
				'assignments:id,tcode,pengajuan_id,user_id',
				'assignments.user:id,name'
			]);

		if ($periodeFilter) {
			$query->whereHas('commitment', function ($query) use ($periodeFilter) {
				$query->where('periodetahun', $periodeFilter);
			});
		}

		if ($statusFilter !== null) {
			switch ($statusFilter) {
				case 1:
					$query->where('status', '>', 0);
					break;
				case 2:
					$query->whereBetween('status', [1, 5]);
					break;
				case 3:
					$query->where('status', '>', 5);
					break;
				default:
					$query->whereBetween('status', [1, 5]);
					break;
			}
		} else {
			$query->whereBetween('status', [1, 7]);
		}

		$data = $query->get();

		$data = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'tcode' => $item->tcode,
				'check_by' => $item->check_by,
				// 'verifikator' => $item->verifikator ? $item->verifikator->name : null,
				'periode' => $item->commitment ? $item->commitment->periodetahun : null,
				'perusahaan' => $item->datauser ? $item->datauser->company_name : null,
				'no_ijin' => $item->no_ijin,
				'created_at' => $item->created_at,
				'verif_at' => $item->verif_at,
				'status' => $item->status,
				'ijin' => str_replace(['/', '.', '-'], '', $item->no_ijin),
				'assignments' => $item->assignments->map(function ($assignment) {
					return [
						'id' => $assignment->id,
						'user_id' => $assignment->user_id,
						'user_name' => $assignment->user ? $assignment->user->name : null,
						'tcode' => $assignment ? $assignment->tcode : null,
						'no_sk' => $assignment->no_sk,
						'tgl_sk' => $assignment->tgl_sk,
						'file' => $assignment->file,
					];
				}),
				'tableSource' => 'PRODUKSI',
			];
		});

		if ($searchValue) {
			$data = $data->filter(function ($item) use ($searchValue) {
				return strpos(strtolower($item['periode']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['perusahaan']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['no_ijin']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['ijin']), strtolower($searchValue)) !== false ||
					strpos(strtolower($item['status']), strtolower($searchValue)) !== false;
			})->values();
		}

		if (!empty($order)) {
			$orderColumn = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$columnName = $columns[$orderColumn]['data'];

			$data = $data->sortBy(function ($item) use ($columnName) {
				return $item[$columnName];
			}, SORT_REGULAR, $orderDirection === 'desc')->values();
		}

		$totalRecords = $data->count();
		$filteredRecords = $data->count();

		$verifList = $data->slice($start, $length)->values();

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $verifList,
		]);
	}

	public function getVerifTanamHistory(Request $request, $noIjin)
	{
		// Format the $noIjin
		$noIjin = $this->formatNoIjin($noIjin);

		// Get the request inputs
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);

		// Build the query
		$query = AjuVerifikasi::select('id', 'no_ijin', 'check_by', 'verif_at', 'status', 'report_url', 'created_at')
			->where('no_ijin', $noIjin)
			->where('kind', 'TANAM')
			->with('verifikator');

		// Get total records count
		$totalRecords = $query->count();

		// Apply search filter
		if (!empty($searchValue)) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('check_by', 'like', "%{$searchValue}%")
					->orWhere('verif_at', 'like', "%{$searchValue}%")
					->orWhere('status', 'like', "%{$searchValue}%")
					// ->orWhere('note', 'like', "%{$searchValue}%")
				;
			});
		}

		// Get filtered records count
		$filteredRecords = $query->count();

		// Apply ordering
		if (!empty($order)) {
			$orderColumnIndex = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$orderColumn = $columns[$orderColumnIndex]['data'];

			switch ($orderColumn) {
				case 'status':
					$query = $query->orderBy('status', $orderDirection);
					break;
				case 'check_by':
					$query = $query->orderBy('check_by', $orderDirection);
					break;
				case 'verif_at':
					$query = $query->orderBy('verif_at', $orderDirection);
					break;
					// case 'note':
					// 	$query = $query->orderBy('note', $orderDirection);
					// 	break;
				default:
					$query = $query->orderBy('id', 'desc');
					break;
			}
		}

		// Apply pagination
		$data = $query->skip($start)
			->take($length)
			->get();

		// Process the data
		if ($data) {
			foreach ($data as $history) {
				$history->checkBy = ($history->check_by && $history->verifikator) ? $history->verifikator->name : null;
			}
		}

		// Format the response
		$response = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'createdAt' => $item->created_at,
				'checkBy' => $item->checkBy,
				'status' => $item->status,
				'verifAt' => $item->verif_at,
				// 'note' => $item->note,
				'reportUrl' => $item->report_url,
			];
		});

		// Return JSON response
		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $response,
		]);
	}

	public function getVerifProdHistory(Request $request, $noIjin)
	{
		// Format the $noIjin
		$noIjin = $this->formatNoIjin($noIjin);

		// Get the request inputs
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);

		// Build the query
		$query = AjuVerifikasi::select('id', 'kind', 'no_ijin', 'check_by', 'verif_at', 'status', 'report_url', 'created_at')
			->where('no_ijin', $noIjin)
			->where('kind', 'PRODUKSI')
			->with('verifikator');

		// Get total records count
		$totalRecords = $query->count();

		// Apply search filter
		if (!empty($searchValue)) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('check_by', 'like', "%{$searchValue}%")
					->orWhere('verif_at', 'like', "%{$searchValue}%")
					->orWhere('status', 'like', "%{$searchValue}%")
					// ->orWhere('note', 'like', "%{$searchValue}%")
				;
			});
		}

		// Get filtered records count
		$filteredRecords = $query->count();

		// Apply ordering
		if (!empty($order)) {
			$orderColumnIndex = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$orderColumn = $columns[$orderColumnIndex]['data'];

			switch ($orderColumn) {
				case 'status':
					$query = $query->orderBy('status', $orderDirection);
					break;
				case 'check_by':
					$query = $query->orderBy('check_by', $orderDirection);
					break;
				case 'verif_at':
					$query = $query->orderBy('verif_at', $orderDirection);
					break;
					// case 'note':
					// 	$query = $query->orderBy('note', $orderDirection);
					// 	break;
				default:
					$query = $query->orderBy('id', 'desc');
					break;
			}
		}

		// Apply pagination
		$data = $query->skip($start)
			->take($length)
			->get();

		// Process the data
		if ($data) {
			foreach ($data as $history) {
				$history->checkBy = ($history->check_by && $history->verifikator) ? $history->verifikator->name : null;
			}
		}

		// Format the response
		$response = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'createdAt' => $item->created_at,
				'checkBy' => $item->checkBy,
				'status' => $item->status,
				'verifAt' => $item->verif_at,
				'note' => $item->note,
				'reportUrl' => $item->report_url,
			];
		});

		// Return JSON response
		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $response,
		]);
	}
	public function getVerifSklHistory(Request $request, $noIjin)
	{
		// Format the $noIjin
		$noIjin = $this->formatNoIjin($noIjin);

		// Get the request inputs
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);

		// Build the query
		$query = AjuVerifSkl::select('id', 'no_ijin', 'check_by', 'verif_at', 'status', 'verif_note', 'created_at')
			->where('no_ijin', $noIjin)
			->with('verifikator');

		// Get total records count
		$totalRecords = $query->count();

		// Apply search filter
		if (!empty($searchValue)) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('check_by', 'like', "%{$searchValue}%")
					->orWhere('verif_at', 'like', "%{$searchValue}%")
					->orWhere('status', 'like', "%{$searchValue}%")
					->orWhere('verif_note', 'like', "%{$searchValue}%");
			});
		}

		// Get filtered records count
		$filteredRecords = $query->count();

		// Apply ordering
		if (!empty($order)) {
			$orderColumnIndex = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$orderColumn = $columns[$orderColumnIndex]['data'];

			switch ($orderColumn) {
				case 'status':
					$query = $query->orderBy('status', $orderDirection);
					break;
				case 'check_by':
					$query = $query->orderBy('check_by', $orderDirection);
					break;
				case 'verif_at':
					$query = $query->orderBy('verif_at', $orderDirection);
					break;
				case 'verif_note':
					$query = $query->orderBy('note', $orderDirection);
					break;
				default:
					$query = $query->orderBy('id', 'desc');
					break;
			}
		}

		// Apply pagination
		$data = $query->skip($start)
			->take($length)
			->get();

		// Process the data
		if ($data) {
			foreach ($data as $history) {
				$history->checkBy = ($history->check_by && $history->verifikator) ? $history->verifikator->name : null;
			}
		}

		// Format the response
		$response = $data->map(function ($item) {
			return [
				'id' => $item->id,
				'createdAt' => $item->created_at,
				'checkBy' => $item->checkBy,
				'status' => $item->status,
				'verifAt' => $item->verif_at,
				'note' => $item->verif_note,
			];
		});

		// Return JSON response
		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $response,
		]);
	}

	//lokasi sampling menggunakan slovin
	public function getLocationSampling(Request $request, $noIjin)
	{
		// Ambil parameter dari request
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);

		// Format no_ijin
		$noIjin = $this->formatNoIjin($noIjin);

		// Ambil data lokasi dari database
		$lokasis = Lokasi::select('id', 'tcode', 'kode_spatial', 'no_ijin', 'is_selected')
			->where('no_ijin', $noIjin)
			->with(['spatial' => function ($query) {
				$query->select('kode_spatial', 'kelurahan_id', 'kecamatan_id', 'kabupaten_id')
					->with('desa:kelurahan_id,nama_desa')
					->with('kecamatan:kecamatan_id,nama_kecamatan')
					->with('kabupaten:kabupaten_id,nama_kab');
			}])
			->get();

		$populasi = $lokasis->count(); // Total populasi

		// Hitung ukuran sampel menggunakan rumus Slovin
		$me = 0.2; // Margin error 5%
		if ($populasi > 0) {
			$n = $populasi / (1 + $populasi * pow($me, 2));
			$n = ceil($n);
		} else {
			$n = 0;
		}

		// Pisahkan lokasi yang sudah dipilih
		$selectedLocations = $lokasis->where('is_selected', 1);
		$selectedCount = $selectedLocations->count();
		$remainingSampleSize = $n - $selectedCount;

		// Ambil lokasi yang belum dipilih dan acak untuk memenuhi ukuran sampel yang tersisa
		if ($remainingSampleSize > 0) {
			$remainingLocations = $lokasis->where('is_selected', 0)->shuffle()->take($remainingSampleSize);
		} else {
			$remainingLocations = collect();
		}

		// Gabungkan lokasi terpilih dan lokasi yang dipilih acak
		$sampleLocations = $selectedLocations->merge($remainingLocations);

		// Terapkan penyaringan jika ada
		if ($searchValue) {
			$sampleLocations = $sampleLocations->filter(function ($item) use ($searchValue) {
				return strpos(strtolower($item->kode_spatial), strtolower($searchValue)) !== false ||
					strpos(strtolower($item->spatial->desa->nama_desa), strtolower($searchValue)) !== false ||
					strpos(strtolower($item->spatial->kecamatan->nama_kecamatan), strtolower($searchValue)) !== false ||
					strpos(strtolower($item->spatial->kabupaten->nama_kab), strtolower($searchValue)) !== false;
			});
		}

		// Terapkan pengurutan jika ada
		if (!empty($order)) {
			$orderColumn = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$columnName = $columns[$orderColumn]['data'];

			$sampleLocations = $sampleLocations->sortBy(function ($item) use ($columnName) {
				return $item->{$columnName};
			}, SORT_REGULAR, $orderDirection === 'desc');
		}

		// Hitung total dan filtered records
		$totalRecords = $n;
		$filteredRecords = $sampleLocations->count();

		// Paginasi data
		$sampleLocations = $sampleLocations->slice($start, $length)->values();

		// Kembalikan respons JSON
		return response()->json([
			'draw' => intval($draw),
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $sampleLocations
		]);
	}

	public function getVerifTanamByIjin(Request $request, $noIjin)
	{
		$userId = Auth::user()->id;
		$noIjin = $this->formatNoIjin($noIjin);

		$query = AjuVerifikasi::select('id', 'kind', 'npwp', 'no_ijin', 'check_by', 'verif_at', 'status', 'note', 'created_at')
			->where('no_ijin', $noIjin)
			->where('kind', 'TANAM')
			->with([
				'commitment:no_ijin,tgl_ijin,tgl_akhir,periodetahun',
				'datauser:id,npwp_company,company_name,logo',
				'verifikator:id,name',
			])->first();

		if ($query) {
			$data = [
				'id' => $query->id,
				'check_by' => $query->check_by,
				'verifikator' => $query->verifikator ? $query->verifikator->name : null,
				'periode' => $query->commitment ? $query->commitment->periodetahun : null,
				'perusahaan' => $query->datauser ? $query->datauser->company_name : null,
				'sumLuas' => $query->commitment->lokasi->sum('luas_lahan'),
				'sumLuasTanam' => $query->commitment->lokasi->sum('luas_tanam'),
				'countPks' => $query->commitment->pks->where('berkas_pks', '!=', null)->count(),
				'countPoktan' => $query->commitment->lokasi->groupBy('kode_poktan')->count(),
				'countSpatial' => $query->commitment->lokasi->count(),
				'countTanam' => $query->commitment->lokasi->where('luas_tanam', '!=', null)->count(),
				'countAnggota' => $query->commitment->lokasi->groupBy('ktp_petani')->count(),
				'logo' => $query->datauser && $query->datauser->logo ? asset('storage/' . $query->datauser->logo) : asset('/img/avatars/farmer.png'),
				'no_ijin' => $query->no_ijin,
				'tgl_ijin' => $query->commitment->tgl_ijin,
				'tgl_akhir' => $query->commitment->tgl_akhir,
				'created_at' => $query->created_at,
				'verif_at' => $query->verif_at,
				'status' => $query->status,
				'ijin' => str_replace(['/', '.', '-'], '', $query->no_ijin),
			];
		} else {
			$data = null; // Handle the case where no data is found
		}

		return response()->json([
			'userId' => $userId,
			'data' => $data,
		]);
	}
	public function getVerifProduksiByIjin(Request $request, $noIjin)
	{
		$userId = Auth::user()->id;
		$noIjin = $this->formatNoIjin($noIjin);

		$query = AjuVerifikasi::select('id', 'kind', 'npwp', 'no_ijin', 'check_by', 'verif_at', 'status', 'note', 'created_at')
			->where('no_ijin', $noIjin)
			->where('kind', 'PRODUKSI')
			->with([
				'commitment:no_ijin,tgl_ijin,tgl_akhir,periodetahun,volume_produksi',
				'datauser:id,npwp_company,company_name,logo',
				'verifikator:id,name',
			])->first();

		if ($query) {
			$data = [
				'id' => $query->id,
				'check_by' => $query->check_by,
				'verifikator' => $query->verifikator ? $query->verifikator->name : null,
				'periode' => $query->commitment ? $query->commitment->periodetahun : null,
				'perusahaan' => $query->datauser ? $query->datauser->company_name : null,
				'sumLuas' => $query->commitment->lokasi->sum('luas_lahan'),
				'sumLuasTanam' => $query->commitment->lokasi->sum('luas_tanam'),
				'sumWajibVol' => $query->commitment ? $query->commitment->volume_produksi : null,
				'sumPanen' => $query->commitment->lokasi->sum('volume'),
				'countPks' => $query->commitment->pks->where('berkas_pks', '!=', null)->count(),
				'countPoktan' => $query->commitment->lokasi->groupBy('kode_poktan')->count(),
				'countSpatial' => $query->commitment->lokasi->count(),
				'countTanam' => $query->commitment->lokasi->where('luas_tanam', '!=', null)->count(),
				'countAnggota' => $query->commitment->lokasi->groupBy('ktp_petani')->count(),
				'logo' => $query->datauser && $query->datauser->logo ? asset('storage/' . $query->datauser->logo) : asset('/img/avatars/farmer.png'),
				'no_ijin' => $query->no_ijin,
				'tgl_ijin' => $query->commitment->tgl_ijin,
				'tgl_akhir' => $query->commitment->tgl_akhir,
				'created_at' => $query->created_at,
				'verif_at' => $query->verif_at,
				'status' => $query->status,
				'ijin' => str_replace(['/', '.', '-'], '', $query->no_ijin),
			];
		} else {
			$data = null; // Handle the case where no data is found
		}

		return response()->json([
			'userId' => $userId,
			'data' => $data,
		]);
	}

	private function haversineDistance($lat1, $lon1, $lat2, $lon2)
	{
		$deltaLat = deg2rad($lat2 - $lat1);
		$deltaLon = deg2rad($lon2 - $lon1);
		$a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($deltaLon / 2) * sin($deltaLon / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$distance = 6371 * $c;

		return $distance;
	}

	//response saat android meminta data lokasi kabupaten
	public function responseGetLocationInKabupaten(Request $request)
	{
		// Ambil parameter dari URL
		$kabupatenIds = $request->query('kabupaten_id', []);
		$viewport = $request->query('viewport', null);

		// Validasi bahwa kabupaten_id adalah array
		if (!is_array($kabupatenIds)) {
			return response()->json(['error' => 'Invalid kabupaten_id format'], 400);
		}

		// Dekode viewport jika disediakan
		$viewport = is_string($viewport) ? json_decode($viewport, true) : null;

		// Buat instance query builder
		$spatialsQuery = MasterSpatial::query();

		if (!empty($kabupatenIds)) {
			// Filter berdasarkan ID kabupaten
			$spatialsQuery->whereIn('kabupaten_id', $kabupatenIds);

			if ($viewport) {
				// Jika viewport disediakan, filter berdasarkan area viewport
				$spatialsQuery->whereRaw('CAST(latitude AS DECIMAL(10, 6)) BETWEEN ? AND ?', [$viewport['south'], $viewport['north']])
					->whereRaw('CAST(longitude AS DECIMAL(10, 6)) BETWEEN ? AND ?', [$viewport['west'], $viewport['east']]);
			}
		} else if ($viewport) {
			// Jika hanya viewport yang disediakan, gunakan viewport tanpa filter kabupaten
			$spatialsQuery->whereRaw('CAST(latitude AS DECIMAL(10, 6)) BETWEEN ? AND ?', [$viewport['south'], $viewport['north']])
				->whereRaw('CAST(longitude AS DECIMAL(10, 6)) BETWEEN ? AND ?', [$viewport['west'], $viewport['east']]);
		}

		// Dapatkan hasil query
		$spatials = $spatialsQuery->get();

		// Kembalikan hasil sebagai JSON
		return response()->json($spatials);
	}

	public function responseGetLocationInKabupatenSummary(Request $request)
	{
		$kabupatenIds = $request->query('kabupaten_id', []);

		// Handle case where kabupatenIds is empty
		if (empty($kabupatenIds)) {
			// If kabupatenIds is empty, get all records
			$summary = MasterSpatial::selectRaw(
				'SUM(luas_lahan) AS total_luas,
            COUNT(*) AS total_lahan,
            SUM(CASE WHEN is_active = 1 THEN luas_lahan ELSE 0 END) AS total_lahan_aktif,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS jml_lahan_aktif,
            SUM(CASE WHEN status = 1 AND is_active = 1 THEN luas_lahan ELSE 0 END) AS total_lahan_mitra,
            SUM(CASE WHEN status = 1 AND is_active = 1 THEN 1 ELSE 0 END) AS jml_lahan_mitra'
			)->first();
		} else {
			// If kabupatenIds is not empty, filter by kabupatenIds
			$summary = MasterSpatial::whereIn('kabupaten_id', $kabupatenIds)->selectRaw(
				'SUM(luas_lahan) AS total_luas,
            COUNT(*) AS total_lahan,
            SUM(CASE WHEN is_active = 1 THEN luas_lahan ELSE 0 END) AS total_lahan_aktif,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS jml_lahan_aktif,
            SUM(CASE WHEN status = 1 AND is_active = 1 THEN luas_lahan ELSE 0 END) AS total_lahan_mitra,
            SUM(CASE WHEN status = 1 AND is_active = 1 THEN 1 ELSE 0 END) AS jml_lahan_mitra'
			)->first();
		}

		// Handle case where no records are found
		if (!$summary) {
			return response()->json(['error' => 'No data found'], 404);
		}

		// Extract values
		$totalLuas = $summary->total_luas;
		$jmlLahan = $summary->total_lahan;

		$totalLahanAktif = $summary->total_lahan_aktif;
		$jmlLahanAktif = $summary->jml_lahan_aktif;

		$totalLahanMitra = $summary->total_lahan_mitra;
		$jmlLahanMitra = $summary->jml_lahan_mitra;

		$luasTersedia = $totalLahanAktif - $totalLahanMitra;
		$jmlTersedia = $jmlLahanAktif - $jmlLahanMitra;

		$data = [
			'totalLuas' => $totalLuas,
			'jmlLahan' => $jmlLahan,
			'totalLahanAktif' => $totalLahanAktif,
			'jmlLahanAktif' => (int) $jmlLahanAktif,
			'prosenAktif' => floor($totalLahanAktif / $totalLuas * 100),
			'totalLahanMitra' => $totalLahanMitra,
			'jmlLahanMitra' => (int) $jmlLahanMitra, // Cast to integer
			'prosenMitra' => $totalLahanAktif > 0 ? floor($totalLahanMitra / $totalLahanAktif * 100) : 0,
			'luasTersedia' => $luasTersedia,
			'jmlTersedia' => $jmlTersedia,
			'prosenTersedia' => $totalLahanAktif > 0 ? floor($luasTersedia / $totalLahanAktif * 100) : 0,
		];

		return response()->json($data);
	}


	public function responseGetLocationByKode(Request $request)
	{
		$kodeSpatial = $request->query('kode_spatial');

		// Misalnya ambil data dari database
		$spatials = MasterSpatial::where('kode_spatial', $kodeSpatial)->get();

		return response()->json($spatials);
	}


	public function responseGetSpatialDetail(Request $request)
	{
		// Validate the incoming request data
		$request->validate([
			'kode_spatial' => 'required|string',
		]);

		// Retrieve the kode_spatial from the request
		$kodeSpatial = $request->input('kode_spatial');

		// Fetch the spatial entity details from the database
		$spatialDetail = MasterSpatial::where('kode_spatial', $kodeSpatial)->first();

		// Check if the spatial entity exists
		if ($spatialDetail) {
			// Return the details as a JSON response
			return response()->json([
				'details' => [
					'status' => $spatialDetail->status,
					'latitude' => $spatialDetail->latitude,
					'longitude' => $spatialDetail->longitude,
					'nama_petani' => $spatialDetail->nama_petani,
					'polygon' => $spatialDetail->polygon,
					'luas' => $spatialDetail->luas_lahan,
					'wilayah' => $spatialDetail->provinsi->nama,
				]
			]);
		} else {
			// Return an empty details array if the spatial entity was not found
			return response()->json([
				'details' => []
			]);
		}
	}

	public function responseGetSpatialMoreDetail($spatial)
	{
		//informasi lahan
		$lahan = MasterSpatial::where('kode_spatial', $spatial)
			->with([
				'provinsi' => function ($query) {
					$query->select('provinsi_id', 'nama');
				},
				'kabupaten' => function ($query) {
					$query->select('kabupaten_id', 'nama_kab');
				},
				'kecamatan' => function ($query) {
					$query->select('kecamatan_id', 'nama_kecamatan');
				},
				'desa' => function ($query) {
					$query->select('kelurahan_id', 'nama_desa');
				}
			])
			->first();
		//informasi kelompok tani
		$poktan = MasterPoktan::where('kode_poktan', $lahan->kode_poktan)->first();
		$lokasi = Lokasi::where('kode_spatial', $spatial)->first();
		if ($lokasi) {
			$kemitraanAktif = PullRiph::where('no_ijin', $lokasi->no_ijin)
				->whereDoesntHave('completed')
				->first();

			$historyKemitraan = PullRiph::where('no_ijin', $lokasi->no_ijin)
				->whereHas('completed')
				->get();
		} else {
			// Jika lokasi tidak ditemukan, set kemitraanAktif dan historyKemitraan ke null atau koleksi kosong
			$kemitraanAktif = null;
			$historyKemitraan = collect();
		}
		return response()->json([
			'infoLahan' => $lahan,
			'infoPoktan' => $poktan,
			'lokasi' => $lokasi,
			'kemitraanAktif' => $kemitraanAktif,
			'historyKemitraan' => $historyKemitraan
		]);
	}

	public function getInvalidNik()
	{
		// Retrieve records where the length of 'ktp_petani' is less than 16 characters
		$invalidNiks = MasterSpatial::select('kode_spatial', 'ktp_petani', 'nama_petani')->whereRaw('CHAR_LENGTH(ktp_petani) < 16')
			->orWhereNull('ktp_petani') // Optionally include records with null NIK
			->get();

		return $invalidNiks;
	}


	//response saat android mengirim data current location dan nomor riph (no_ijin)
	public function responseGetLocByRad(Request $request)
	{
		$request->validate([
			'noIjin' => 'required|string',
			'latitude' => 'required|string',
			'longitude' => 'required|string',
			'radius' => 'required|integer', //remark jika tidak digunakan atau menggunakan nilai statis
		]);

		$formattedNoIjin = $request->input('noIjin');
		$centerLat = $request->input('latitude');
		$centerLng = $request->input('longitude');
		$radius = $request->input('radius'); //beri nilai default jika statis

		$locations = Lokasi::select('id', 'no_ijin', 'kode_spatial')
			->with(['spatial' => function ($query) {
				$query->select('kode_spatial', 'latitude', 'longitude');
			}])
			->where('no_ijin', $formattedNoIjin)
			->get();

		$filteredLocations = $locations->filter(function ($location) use ($centerLat, $centerLng, $radius) {
			$latitude = $location->spatial->latitude;
			$longitude = $location->spatial->longitude;
			$distance = $this->haversineDistance($centerLat, $centerLng, $latitude, $longitude);
			return $distance <= $radius;
		});

		return response()->json([
			'Jarak (km)' => $radius,
			'No Ijin' => $formattedNoIjin,
			'Device Location' => 'Lat: ' . $centerLat . ' || Long: ' . $centerLng,
			'Jumlah titik' => $filteredLocations->count(),
			'data' => $filteredLocations,
		]);
	}

	//response saat marker diklik dalam responseGetLocByDRad data dari header
	public function getLocDataByIjinBySpatial($noIjin, $spatial)
	{
		$noIjin = $this->formatNoIjin($noIjin);

		$query = Lokasi::where('no_ijin', $noIjin)
			->where('kode_spatial', $spatial)
			->with(['spatial', 'masteranggota.masterpoktan', 'fototanam', 'fotoproduksi'])
			->get();

		$data = $query->map(function ($item) {
			$spatial = $item->spatial;
			$masterAnggota = $item->masteranggota->first();
			$nama_kelompok = optional(optional($masterAnggota)->masterpoktan)->nama_kelompok;

			$fotoTanam = $item->fototanam->map(function ($foto) {
				return [
					'id' => $foto->id,
					'url' => $foto->filename,
				];
			});

			$fotoProduksi = $item->fotoproduksi->map(function ($foto) {
				return [
					'id' => $foto->id,
					'url' => $foto->filename,
				];
			});

			return [
				'id' => $item->id,
				'nama_kelompok' => $item->nama_poktan,
				'kode_spatial' => optional($spatial)->kode_spatial,
				'ktp_petani' => $item->ktp_petani,
				'nama_petani' => optional($item->masteranggota)->nama_petani,
				'nama_kelompok_poktan' => $nama_kelompok,
				'spatial_petani' => optional($spatial)->nama_petani,
				'spatial_ktp' => optional($spatial)->ktp_petani,
				'latitude' => optional($spatial)->latitude,
				'longitude' => optional($spatial)->longitude,
				'polygon' => optional($spatial)->polygon,
				'luas_lahan' => $item->luas_lahan,
				'luas_tanam' => $item->luas_tanam,
				'awal_tanam' => $item->tgl_tanam ? date('d-m-Y', strtotime($item->tgl_tanam)) : null,
				'akhir_tanam' => $item->tgl_akhir_tanam ? date('d-m-Y', strtotime($item->tgl_akhir_tanam)) : null,
				'volume_panen' => $item->volume,
				'tgl_panen' => $item->tgl_panen ? date('d-m-Y', strtotime($item->tgl_panen)) : null,
				'status' => $item->status,
				'fototanam' => $fotoTanam,
				'fotoProduksi' => $fotoProduksi,
			];
		});

		// Return response in JSON format
		return response()->json($data);
	}

	//response saat marker diklik dalam responseGetLocByDRad data dari body
	public function postLocDataByIjinBySpatial(Request $request)
	{
		$request->validate([
			'noIjin' => 'required|string',
			'kode_spatial' => 'required|string',
		]);

		$noIjin = $request->input('noIjin');
		$kdspatial = $request->input('kode_spatial');

		$query = Lokasi::where('no_ijin', $noIjin)
			->where('kode_spatial', $kdspatial)
			->with(['spatial', 'masteranggota.masterpoktan', 'fototanam', 'fotoproduksi'])
			->get();

		$data = $query->map(function ($item) {
			$spatial = $item->spatial;
			$masterAnggota = $item->masteranggota->first();
			$nama_kelompok = optional(optional($masterAnggota)->masterpoktan)->nama_kelompok;

			$fotoTanam = $item->fototanam->map(function ($foto) {
				return [
					'id' => $foto->id,
					'url' => $foto->filename,
				];
			});

			$fotoProduksi = $item->fotoproduksi->map(function ($foto) {
				return [
					'id' => $foto->id,
					'url' => $foto->filename,
				];
			});

			return [
				'id' => $item->id,
				'nama_kelompok' => $item->nama_poktan,
				'kode_spatial' => optional($spatial)->kode_spatial,
				'ktp_petani' => $item->ktp_petani,
				'nama_petani' => optional($item->masteranggota)->nama_petani,
				'nama_kelompok_poktan' => $nama_kelompok,
				'spatial_petani' => optional($spatial)->nama_petani,
				'spatial_ktp' => optional($spatial)->ktp_petani,
				'latitude' => optional($spatial)->latitude,
				'longitude' => optional($spatial)->longitude,
				'polygon' => optional($spatial)->polygon,
				'luas_lahan' => $item->luas_lahan,
				'luas_tanam' => $item->luas_tanam,
				'awal_tanam' => $item->tgl_tanam ? date('d-m-Y', strtotime($item->tgl_tanam)) : null,
				'akhir_tanam' => $item->tgl_akhir_tanam ? date('d-m-Y', strtotime($item->tgl_akhir_tanam)) : null,
				'volume_panen' => $item->volume,
				'tgl_panen' => $item->tgl_panen ? date('d-m-Y', strtotime($item->tgl_panen)) : null,
				'status' => $item->status,
				'fototanam' => $fotoTanam,
				'fotoProduksi' => $fotoProduksi,
			];
		});

		// Return response in JSON format
		return response()->json($data);
	}

	//get spatial by status
	public function getspatial(Request $request)
	{
		$informasi = 'Daftar Kode Spatial Wajib Tanam';
		$app = 'Simethris 4 Alpha';
		$Uses = '?s=status&p=provinsi_id&b=kabupaten_id&c=kecamatan_id&l=kelurahan_id';

		$validated = $request->validate([
			's' => 'nullable|integer',
			'p' => 'nullable|integer',
			'b' => 'nullable|integer',
			'c' => 'nullable|integer',
			'l' => 'nullable|integer',
		]);

		$s = $validated['s'] ?? null;
		$p = $validated['p'] ?? null;
		$b = $validated['b'] ?? null;
		$c = $validated['c'] ?? null;
		$l = $validated['l'] ?? null;

		$query = MasterSpatial::select(
			'kode_spatial',
			'ktp_petani',
			'nama_petani',
			'provinsi_id',
			'kabupaten_id',
			'kecamatan_id',
			'kelurahan_id',
			'luas_lahan',
			'status'
		);

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

		$spatials = $query->get();

		$formattedSpatials = $spatials->map(function ($spatial) {
			return [
				'kode_spatial' => $spatial->kode_spatial,
				'luas_lahan' => $spatial->luas_lahan,
				'ktp_petani' => $spatial->ktp_petani,
				'nama_petani' => $spatial->anggota->nama_petani,
				'kode_poktan' => $spatial->anggota->masterpoktan->kode_poktan ?? null,
				'nama_kelompok' => $spatial->anggota->masterpoktan->nama_kelompok ?? null,
				'status' => $spatial->status,
				'wilayah' => [
					'provinsi_id' => $spatial->provinsi_id,
					'kabupaten_id' => $spatial->kabupaten_id,
					'kecamatan_id' => $spatial->kecamatan_id,
					'kelurahan_id' => $spatial->kelurahan_id,
				]
			];
		});
		return response()->json([
			'Informasi' => $informasi,
			'Applikasi' => $app,
			'Penggunaan' => $Uses,
			'data_spatial' => $formattedSpatials,
		]);
	}

	public function filemanagement(Request $request)
	{
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);
		$npwp = Auth::user()->data_user->npwp_company;
		$commitments = PullRiph::select('no_ijin')
			->where('npwp', $npwp)
			->pluck('no_ijin');

		// Base query
		$query = FileManagement::select('no_ijin', 'berkas', 'tanggal', 'source')
			->whereIn('no_ijin', $commitments);

		// Apply search filter
		if (!empty($searchValue)) {
			$query = $query->where(function ($q) use ($searchValue) {
				$q->where('no_ijin', 'like', "%{$searchValue}%")
					->orWhere('berkas', 'like', "%{$searchValue}%")
					->orWhere('source', 'like', "%{$searchValue}%")
					->orWhereDate('tanggal', '=', $searchValue); // Assuming searchValue is a date for exact match
			});
		}

		// Calculate total records
		$totalRecords = $query->count();

		// Apply ordering
		if (!empty($order)) {
			$orderColumnIndex = $order[0]['column'];
			$orderDirection = $order[0]['dir'];
			$orderColumn = $columns[$orderColumnIndex]['data'];
			switch ($orderColumn) {
				case 'no_ijin':
					$query = $query->orderBy('no_ijin', $orderDirection);
					break;
				case 'berkas':
					$query = $query->orderBy('berkas', $orderDirection);
					break;
				case 'tanggal':
					$query = $query->orderBy('tanggal', $orderDirection);
					break;
				case 'tanggal':
					$query = $query->orderBy('source', $orderDirection);
					break;
				default:
					$query = $query->orderBy('tanggal', 'desc');
					break;
			}
		}

		// Apply pagination
		$data = $query->skip($start)->take($length)->get();

		// Calculate filtered records
		$filteredRecords = $data->count();

		// Format data for view
		$data = $data->map(function ($item) {
			return [
				'no_ijin' => $item->no_ijin,
				'berkas' => $item->berkas,
				'tanggal' => \Carbon\Carbon::parse($item->tanggal)->format('d F, Y'), // Format tanggal sesuai kebutuhan
				'source' => $item->source,
			];
		});

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $data,
		]);
	}

	public function getAllSkls(Request $request)
	{
		$draw = $request->input('draw', 1);
		$start = $request->input('start', 0);
		$length = $request->input('length', 10);
		$searchValue = $request->input('search.value', '');
		$order = $request->input('order', []);
		$columns = $request->input('columns', []);

		$query = Completed::with(['datauser:id,npwp_company,company_name']);

		// Count total records without filters
		$totalRecords = $query->count();

		if (!empty($searchValue)) {
			$query->where(function ($q) use ($searchValue) {
				$q->whereHas('datauser', function ($query) use ($searchValue) {
					$query->where('npwp_company', 'like', "%{$searchValue}%")
						->orWhere('company_name', 'like', "%{$searchValue}%");
				});
			});
		}

		// Count filtered records after applying search
		$filteredRecords = $query->count();

		// Apply pagination
		$data = $query->skip($start)->take($length)->get();

		return response()->json([
			'draw' => $draw,
			'recordsTotal' => $totalRecords,
			'recordsFiltered' => $filteredRecords,
			'data' => $data,
		]);
	}

	/**
	 * Get Data Wilayah
	 */
	public function getAllProvinsi()
	{
		$data = MasterProvinsi::select('provinsi_id', 'nama')->get();
		return response()->json(['data' => $data,]);
	}
	public function getKabByProv($prov)
	{
		$data = MasterKabupaten::select('provinsi_id', 'kabupaten_id', 'nama_kab')->where('provinsi_id', $prov)->get();
		return response()->json(['data' => $data,]);
	}
	public function getKecByKab($kab)
	{
		$data = MasterKecamatan::select('kabupaten_id', 'kecamatan_id', 'nama_kecamatan')->where('kabupaten_id', $kab)->get();
		return response()->json(['data' => $data,]);
	}
	public function getKelByKec($kec)
	{
		$data = MasterDesa::select('kecamatan_id', 'kelurahan_id', 'nama_desa')->where('kecamatan_id', $kec)->get();
		return response()->json(['data' => $data,]);
	}
}
