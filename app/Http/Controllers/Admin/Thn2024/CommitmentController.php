<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;

use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\MassDestroyPullriphRequest;
use App\Http\Controllers\Traits\SimeviTrait;
use Symfony\Component\HttpFoundation\Response;

use App\Models2024\AjuVerifProduksi;
use App\Models2024\AjuVerifSkl;
use App\Models2024\AjuVerifTanam;
use App\Models2024\Lokasi;
use App\Models2024\MasterAnggota;
use App\Models2024\MasterSpatial;
use App\Models2024\Skl;
use App\Models2024\PullRiph;
use App\Models2024\Pks;
use App\Models2024\MasterPoktan;
use App\Models\UserDocs;
use App\Models\Varietas;
use App\Models\PenangkarRiph;
use Illuminate\Support\Facades\Storage;
use Svg\Tag\Rect;

class CommitmentController extends Controller
{
	use SimeviTrait;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$module_name = 'Proses RIPH';
		$page_title = 'Daftar Komitmen';
		$page_heading = 'Daftar Komitmen';
		$heading_class = 'fal fa-ballot-check';

		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitments = PullRiph::where('npwp', $npwp_company)
			->with('skl')
			->select('id', 'no_ijin', 'periodetahun', 'tgl_ijin', 'volume_riph', 'luas_wajib_tanam', 'volume_produksi')
			->get();
		// dd($commitments);
		$pksCount = 0; // Initialize with a default value
		$pksFileCount = 0; // Initialize with a default value

		if ($commitments) {
			foreach ($commitments as $commitment) {
				$sumLuas = $commitment->datarealisasi->sum('luas_lahan');
				$sumVolume = $commitment->datarealisasi->sum('volume');
				$minThresholdTanam = $commitment->luas_wajib_tanam;
				$minThresholdProd = $commitment->volume_produksi;

				$commitment->sumLuas = $sumLuas;
				$commitment->sumVolume = $sumVolume;
				$commitment->minThresholdTanam = $minThresholdTanam;
				$commitment->minThresholdProd = $minThresholdProd;
				$thesePks = Pks::where('no_ijin', $commitment->no_ijin)->select('id', 'berkas_pks')->get();
				$pksCount = $thesePks->count();
				$pksFileCount = $thesePks
					->whereNotNull('berkas_pks')
					->count();
				$userDocs = UserDocs::where('no_ijin', $commitment->no_ijin)
					->first();

				$ajuTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->select('status')
					->first();

				$ajuProduksi = AjuVerifProduksi::where('no_ijin', $commitment->no_ijin)->select('status')
					->first();

				$ajuSkl = AjuVerifSkl::where('no_ijin', $commitment->no_ijin)->select('status')
					->first();

				// Add userDocs to the commitment
				$commitment->userDocs = $userDocs;
				$commitment->ajuTanam = $ajuTanam;
				$commitment->ajuProduksi = $ajuProduksi;
				$commitment->ajuSkl = $ajuSkl;
				// $commitment->skl = $skl;
			}

			// $countLokasi = $commitment->lokasi->count('id');
			// dd($countLokasi);
			$noIjin = str_replace(['.', '-', '/'], '', $commitment->no_ijin);
		}
		return view('t2024.commitment.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwp_company', 'commitments', 'pksCount', 'pksFileCount', 'noIjin'));
	}

	public function show($noIjin)
	{
		$noIjin = substr($noIjin, 0, 4) . '/' .
			substr($noIjin, 4, 2) . '.' .
			substr($noIjin, 6, 3) . '/' .
			substr($noIjin, 9, 1) . '/' .
			substr($noIjin, 10, 2) . '/' .
			substr($noIjin, 12, 4);
		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)->where('no_ijin', $noIjin)->first();

		$module_name = 'Komitmen';
		$page_title = 'Data Komitmen';
		$page_heading = 'Data Komitmen: ' . $commitment->no_ijin;
		$heading_class = 'fal fa-file-edit';

		return view('t2024.commitment.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwp_company', 'commitment'));
	}

	public function realisasi($noIjin)
	{
		$module_name = 'Komitmen';
		$page_title = 'Data Realisasi';
		$page_heading = 'Realisasi Komitmen';
		$heading_class = 'fal fa-file-edit';
		$ijin = $noIjin;
		$noIjin = substr($noIjin, 0, 4) . '/' .
			substr($noIjin, 4, 2) . '.' .
			substr($noIjin, 6, 3) . '/' .
			substr($noIjin, 9, 1) . '/' .
			substr($noIjin, 10, 2) . '/' .
			substr($noIjin, 12, 4);

		$npwp = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::select('id', 'npwp', 'no_ijin', 'status')
			->where('npwp', $npwp)
			->where('no_ijin',$noIjin)->first();

		$poktans = Pks::where('no_ijin', $noIjin)
			->groupBy('poktan_id')
			->get();

		$docs = UserDocs::where('no_ijin', $commitment->no_ijin)->first();
		$penangkars = PenangkarRiph::where('npwp', $npwp)
			->when(isset($commitment->no_ijin), function ($query) use ($commitment) {
				return $query->where('no_ijin', $commitment->no_ijin);
			}, function ($query) use ($commitment) {
				return $query->where('commitment_id', $commitment->id);
			})
			->get();
		$varietass = Varietas::all();
		$commitmentStatus = $commitment->status;
		if (empty($commitmentStatus) || $commitmentStatus == 3 || $commitmentStatus == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}
		return view('t2024.commitment.realisasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'noIjin','penangkars', 'docs', 'npwp', 'varietass', 'disabled', 'poktans', 'ijin'));
	}




	public function updatePks(Request $request, $id)
	{
		DB::beginTransaction();

		try {
			$npwp_company = Auth::user()->data_user->npwp_company;
			$pks = Pks::findOrFail($id);
			$commitment = PullRiph::where('no_ijin', $pks->no_ijin)->first();
			$noIjin = str_replace(['/', '.'], '', $commitment->no_ijin);

			$filenpwp = str_replace(['.', '-'], '', $npwp_company);
			$pks->no_perjanjian = $request->input('no_perjanjian');
			$pks->tgl_perjanjian_start = $request->input('tgl_perjanjian_start');
			$pks->tgl_perjanjian_end = $request->input('tgl_perjanjian_end');
			$pks->varietas_tanam = $request->input('varietas_tanam');
			$pks->periode_tanam = $request->input('periode_tanam');

			if ($request->hasFile('berkas_pks')) {
				$file = $request->file('berkas_pks');
				$request->validate([
					'berkas_pks' => 'mimes:pdf',
				]);
				$filename = 'pks_' . $filenpwp . '_' . $noIjin . '_' . $pks->poktan_id . '_' . time() . '.' . $file->extension();
				$path = 'uploads/' . $filenpwp . '/' . $commitment->periodetahun;
				$file->storeAs($path, $filename, 'public');
				if (Storage::disk('public')->exists($path . '/' . $filename)) {
					$pks->berkas_pks = $filename;
				} else {
					return redirect()->back()->with('error', "Gagal mengunggah berkas. Error: " . $e->getMessage());
				}
			}

			$pks->save();

			DB::commit();

			return response()->json(['message' => 'Data PKS berhasil diperbarui'], 200);
		} catch (\Exception $e) {
			DB::rollback();
			return response()->json(['message' => 'Gagal memperbarui data PKS: ' . $e->getMessage()], 500);
		}
	}


	public function storeUserDocs(Request $request, $id)
	{
		$commitment = PullRiph::find($id);
		$realnpwp = $commitment->npwp;
		$npwp = str_replace(['.', '-'], '', $commitment->npwp);
		$realNoIjin = $commitment->no_ijin;
		$noIjin = str_replace(['/', '.'], '', $realNoIjin);
		$userFiles = [];

		try {
			DB::beginTransaction();

			$fileFields = [
				'sptjmtanam',
				'sptjmproduksi',
				'spvt',
				'rta',
				'sphtanam',
				'logbooktanam',
				'spvp',
				'rpo',
				'formLa',
				'sphproduksi',
				'logbookproduksi',
				'spskl'
				// Tambahkan field-file lainnya di sini
			];

			// Validasi MIME type
			$rules = [];
			$messages = [];
			foreach ($fileFields as $field) {
				$rules[$field] = 'mimetypes:application/pdf'; // Hanya izinkan file PDF

				// Periksa apakah file ada dalam permintaan sebelum menambahkan pesan kustom
				if ($request->hasFile($field)) {
					$messages[$field . '.mimetypes'] = 'Berkas ' . $request->file($field)->getClientOriginalName() . ' harus memiliki tipe MIME application/pdf.';
				}
			}

			// Lakukan validasi
			$request->validate($rules, $messages);

			// Jika validasi berhasil, lanjutkan dengan proses penyimpanan file
			foreach ($fileFields as $field) {
				if ($request->hasFile($field)) {
					$file = $request->file($field);

					$fileExtension = $file->extension();
					$file_name = $field . '_' . $noIjin . '_' . time() . '.' . $fileExtension;
					$file_path = $file->storeAs('uploads/' . $npwp . '/' . $commitment->periodetahun, $file_name, 'public');
					$userFiles[$field] = $file_name;
				}
			}

			$data = UserDocs::updateOrCreate(
				[
					'npwp' => $realnpwp,
					'commitment_id' => $id,
					'no_ijin' => $realNoIjin
				],
				array_merge($request->all(), $userFiles) // Menggabungkan data form dan file dalam satu array
			);
			DB::commit();

			// Flash message sukses
			return redirect()->back()->with('success', 'Berkas berhasil diunggah.');
		} catch (\Exception $e) {
			// Rollback transaksi jika ada kesalahan
			DB::rollBack();

			// Flash message kesalahan
			return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah berkas: ' . $e->getMessage());
		}
	}

	public function submission($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Data Pengajuan';
		$heading_class = 'fal fa-file-invoice';

		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);

		$total_luastanam = $commitment->datarealisasi->sum('luas_lahan');
		$total_volume = $commitment->datarealisasi->sum('volume');
		// dd($total_volume);
		return view('t2024.commitment.realisasi', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}


	public function destroy($id)
	{
		abort_if(Gate::denies('commitment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$pullRiph = PullRiph::find($id);
		$pullRiph->delete();

		return back();
	}

	public function massDestroy(MassDestroyPullriphRequest $request)
	{
		PullRiph::whereIn('id', request('ids'))->delete();
		return response(null, Response::HTTP_NO_CONTENT);
	}
}
