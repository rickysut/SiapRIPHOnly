<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifTanam;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Gate;
use Illuminate\Support\Facades\DB;

use App\Models\LokasiCheck;
use App\Models\CommitmentCheck;
use App\Models\DataRealisasi;
use App\Models\PksCheck;
use App\Models\Lokasi;
use App\Models\PullRiph;
use App\Models\MasterPoktan;
use App\Models\Pks;
use App\Models\UserDocs;

class VerifTanamController extends Controller
{
	public function index()
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		//page level
		$module_name = 'Permohonan';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Daftar Pengajuan Verifikasi Tanam';
		$heading_class = 'fal fa-file-search';

		//table pengajuan. jika sudah mengajukan SKL, maka pengajuan terkait tidak muncul
		// $verifikasis = AjuVerifTanam::orderBy('created_at', 'desc')
		// 	->get();

		$verifikasis = AjuVerifTanam::where(function ($query) {
			$query->where('check_by', Auth::user()->id)
				->orWhereNull('check_by');
		})
			->orderBy('created_at', 'desc')
			->get();

		return view('admin.verifikasi.tanam.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasis'));
	}

	//halaman pemeriksaan
	public function check($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		// Page level
		$module_name = 'Permohonan';
		$page_title = 'Data Pengajuan';
		$page_heading = 'Data Pengajuan Verifikasi';
		$heading_class = 'fa fa-file-search';

		// Populate related data
		$verifikasi = AjuVerifTanam::findOrFail($id);
		$noIjin = str_replace(['/', '.'], '', $verifikasi->no_ijin);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$userDocs = UserDocs::where('no_ijin', $verifikasi->no_ijin)->first();

		$lokasichecks = LokasiCheck::where('pengajuan_id', $verifikasi->id)->orderBy('created_at', 'desc')->get();

		$pkss = Pks::withCount('lokasi')->where('no_ijin', $verifikasi->no_ijin)
			->get();

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$countPoktan = $pkss->count();
		$countPks = $pkss->where('berkas_pks', '!=', null)->count();


		return view('admin.verifikasi.tanam.check', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'lokasichecks', 'pkss', 'total_luastanam', 'total_volume', 'countPoktan', 'countPks', 'userDocs', 'noIjin'));
	}

	// data monitoring tanggal dan daftar tanam-produksi

	//set status hasil pemeriksaan pada tab-pane kelengkapan berkas
	public function checkBerkas(Request $request, $id)
	{
		$user = Auth::user();
		$verifTanam = AjuVerifTanam::find($id);
		$npwp = $verifTanam->npwp;
		$noIjin = $verifTanam->no_ijin;
		$commitmentId = $verifTanam->commitment_id;

		$commitment = PullRiph::where('no_ijin', $noIjin)->first();

		try {
			DB::beginTransaction();
			$checks = [
				'sptjmtanamcheck',
				'spvtcheck',
				'rtacheck',
				'sphtanamcheck',
				'spdstcheck',
				'logbooktanamcheck',
			];
			// Create an empty data array to hold the updates
			$data = [];
			foreach ($checks as $check) {
				// Use the column name from the checks array as the input name
				$data[$check] = $request->input($check);
			}
			$data['tanamcheck_by'] = $user->id;
			$data['tanamverif_at'] = Carbon::now();
			UserDocs::updateOrCreate(
				[
					'npwp' => $npwp,
					'commitment_id' => $commitmentId,
					'no_ijin' => $noIjin,
				],
				$data
			);

			if ($verifTanam->status == '1') {
				$verifTanam->status = '2'; //pemeriksaan berkas selesai
			}
			$verifTanam->check_by = $user->id;
			$verifTanam->save();
			DB::commit();
			// Flash message sukses
			return redirect()->back()->with('success', 'Tahap pemeriksaan berkas kelengkapan dinyatakan selesai dan berhasil disimpan.');
		} catch (\Exception $e) {
			// Rollback transaksi jika ada kesalahan
			DB::rollBack();

			// Flash message kesalahan
			return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
		}
	}

	//halaman pemeriksaan perjanjian kerjasama
	public function verifPks($noIjin, $poktan_id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Verifikasi';
		$page_title = 'Verifikasi Data';
		$page_heading = 'Data dan Berkas PKS';
		$heading_class = 'fal fa-ballot-check';

		$no_ijin = substr_replace($noIjin, '/', 4, 0);
		$no_ijin = substr_replace($no_ijin, '.', 7, 0);
		$no_ijin = substr_replace($no_ijin, '/', 11, 0);
		$no_ijin = substr_replace($no_ijin, '/', 13, 0);
		$no_ijin = substr_replace($no_ijin, '/', 16, 0);

		$verifikasi = AjuVerifTanam::where('no_ijin', $no_ijin)->first();
		$npwp = $verifikasi->npwp;
		$commitment = PullRiph::where('no_ijin', $no_ijin)->first();
		$pks = Pks::where('npwp', $npwp)
			->where('no_ijin', $no_ijin)
			->where('poktan_id', $poktan_id)
			->first();
		$actionRoute = route('verification.tanam.check.pks.store', $pks->id);
		$cancelRoute = route('verification.tanam.check', $verifikasi->id);
		// dd($actionRoute);
		return view('admin.verifikasi.tanam.verifPks', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'pks', 'npwp', 'commitment', 'actionRoute', 'cancelRoute'));
	}

	//set statu hasil pemeriksaan single PKS
	public function verifPksStore(Request $request, $id)
	{
		$user = Auth::user();
		$verifId = $request->input('verifId');
		$pks = Pks::findOrFail($id);
		$pks->status = $request->input('status');
		$pks->note = $request->input('note');
		$pks->verif_by = $user->id;
		$pks->verif_at = Carbon::now();

		$pks->save();
		return redirect()->route('verification.tanam.check', ['id' => $verifId])->with('success', 'Hasil Pemeriksaan berkas Perjanjian Kerjasama berhasil disimpan');
	}

	//set status hasil pemeriksaan pada tab-pane Perjanjian Kemitraan
	public function checkPksSelesai(Request $request, $id)
	{
		$user = Auth::user();
		$verifTanam = AjuVerifTanam::find($id);
		$npwp = $verifTanam->npwp;
		$noIjin = $verifTanam->no_ijin;

		if ($verifTanam->status == '2') {
			$verifTanam->status = '3'; //pemeriksaan PKS selesai
		}
		$verifTanam->check_by = $user->id;
		$verifTanam->verif_at = Carbon::now();
		// dd($verifTanam);
		$verifTanam->save();

		return redirect()->back()->with('success', 'Tahap Pemeriksaan seluruh berkas Perjanjian dinyatakan selesai.');
	}

	//set status hasil pemeriksaan verifikasi tanam
	public function storeCheck(Request $request, $id)
	{
		// Verifikator
		$user = Auth::user();

		// Pilih tabel pengajuan
		$verifikasi = AjuVerifTanam::findOrFail($id);
		abort_if(
			Gate::denies('online_access') ||
			($verifikasi->no_ijin != $request->input('no_ijin') &&
				$verifikasi->npwp != $request->input('npwp')),
			Response::HTTP_FORBIDDEN,
			'403 Forbidden'
		);

		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$npwp = $verifikasi->npwp;
		$noIjin = $verifikasi->no_ijin;
		$commitmentId = $verifikasi->commitment_id;
		$fileNpwp = str_replace(['.', '-'], '', $npwp);
		$fileNoIjin = str_replace(['/', '.'], '', $noIjin);

		try {
			DB::beginTransaction();

			// Inisialisasi variabel untuk berkas batanam dan ndhprt
			$filenameBatanam = $verifikasi->batanam;
			$filenameNdhprt = $verifikasi->ndhprt;

			// Periksa apakah ada berkas batanam yang diunggah
			if ($request->hasFile('batanam')) {
				$file = $request->file('batanam');

				// Validasi tipe berkas (PDF)
				$request->validate([
					'batanam' => 'file|mimes:pdf|max:2048', // Hanya izinkan file PDF
				]);

				$filenameBatanam = 'batanam_' . $fileNoIjin . '.' . $file->extension();
				$file->storeAs('uploads/' . $fileNpwp . '/' . $commitment->periodetahun, $filenameBatanam, 'public');
			}

			// Periksa apakah ada berkas ndhprt yang diunggah
			if ($request->hasFile('ndhprt')) {
				$file = $request->file('ndhprt');

				// Validasi tipe berkas (PDF)
				$request->validate([
					'ndhprt' => 'file|mimes:pdf|max:2048', // Hanya izinkan file PDF
				]);

				$filenameNdhprt = 'notdintanam_' . $fileNoIjin . '.' . $file->extension();
				$file->storeAs('uploads/' . $fileNpwp . '/' . $commitment->periodetahun, $filenameNdhprt, 'public');
			}

			// Use updateOrCreate to create or update the record based on the identifiers
			AjuVerifTanam::updateOrCreate(
				[
					'npwp' => $npwp,
					'commitment_id' => $commitmentId,
					'no_ijin' => $noIjin,
				],
				[
					'note' => $request->input('note'),
					'metode' => $request->input('metode'),
					'status' => $request->input('status'),
					'check_by' => $user->id,
					'verif_at' => Carbon::now(),
					'batanam' => $filenameBatanam, // the filename
					'ndhprt' => $filenameNdhprt, // the file name
				]
			);

			DB::commit();
			return redirect()->route('verification.tanam.show', $id)->with('success', 'Pemeriksaan Realisasi Komitmen Wajib Tanam dinyatakan Selesai dan Data berhasil disimpan.');
		} catch (\Exception $e) {
			DB::rollback();
			return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
		}
	}


	//view hasil pemeriksaan
	public function show($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		// Page level
		$module_name = 'Permohonan';
		$page_title = 'Ringkasan Hasil Verifikasi Tanam'; //muncul pada hasil print
		$page_heading = 'Ringkasan Hasil Verifikasi Tanam';
		$heading_class = 'fal fa-file-check';

		// Populate related data
		$verifikasi = AjuVerifTanam::findOrFail($id);
		$noIjin = str_replace(['/', '.'], '', $verifikasi->no_ijin);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$userDocs = UserDocs::where('no_ijin', $verifikasi->no_ijin)->first();
		// $commitmentcheck = CommitmentCheck::where('pengajuan_id', $verifikasi->id)->firstOrFail();
		// $pkschecks = PksCheck::where('pengajuan_id', $verifikasi->id)->get();
		$anggotas = Lokasi::where('no_ijin', $commitment->no_ijin);
		// $lokasichecks = LokasiCheck::where('pengajuan_id', $verifikasi->id)->orderBy('created_at', 'desc')->get();

		$pkss = Pks::withCount('lokasi')->where('no_ijin', $verifikasi->no_ijin)
			->get();

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		// $pks = Pks::where('no_ijin', $commitment->no_ijin)->get();
		$countPoktan = $pkss->count();
		$countPks = $pkss->where('berkas_pks', '!=', null)->count();
		$countAnggota = $anggotas->count();
		$hasGeoloc = $anggotas->sum('nama_lokasi');

		return view('admin.verifikasi.tanam.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'pkss', 'total_luastanam', 'total_volume', 'countPoktan', 'countPks', 'userDocs', 'noIjin', 'hasGeoloc', 'countAnggota'));
	}

	//view lokasi tanam untuk verifikasi (view only)
	public function lokasicheck($noIjin, $anggota_id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Verifikasi Data';
		$page_title = 'Verifikasi Data Lokasi';
		$page_heading = 'Pemeriksaan Data Tanam dan Produksi';
		$heading_class = 'fal fa-ballot-check';

		$no_ijin = substr_replace($noIjin, '/', 4, 0);
		$no_ijin = substr_replace($no_ijin, '.', 7, 0);
		$no_ijin = substr_replace($no_ijin, '/', 11, 0);
		$no_ijin = substr_replace($no_ijin, '/', 13, 0);
		$no_ijin = substr_replace($no_ijin, '/', 16, 0);

		$lokasi = Lokasi::where('anggota_id', $anggota_id)
			->where('no_ijin', $no_ijin)
			->first();

		$pks = Pks::where('poktan_id', $lokasi->poktan_id)
			->where('no_ijin', $no_ijin)
			->latest()
			->first();
		$commitment = PullRiph::where('no_ijin', $no_ijin)->first();

		//karena verifikasi dengan nomor ijin yang sama dapat muncul berulang
		$verifikasi = AjuVerifTanam::where('no_ijin', $no_ijin)
			->latest()
			->first();
		$commitmentcheck = CommitmentCheck::where('no_pengajuan', $verifikasi->no_pengajuan)->first();
		$pkscheck = PksCheck::where('pengajuan_id', $verifikasi->id)->first();
		$lokasicheck = LokasiCheck::where('anggota_id', $anggota_id)
			->where('poktan_id', $pks->poktan_id)
			// ->where('pkscheck_id', $pkscheck->id)
			->first();

		$anggotamitra = $lokasi;
		$pksmitra = $pkscheck;
		$verifcommit = $commitmentcheck;
		$verifpks = $pkscheck;
		$veriflokasi = $lokasicheck;
		// dd($anggotamitra);
		return view('admin.verifikasi.tanam.locationcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'lokasi', 'pks', 'commitment', 'verifikasi', 'commitmentcheck', 'pkscheck', 'lokasicheck', 'pksmitra', 'anggotamitra', 'verifcommit', 'verifpks', 'veriflokasi'));
	}

	// public function lokasistore(Request $request)
	// {
	// 	$user = Auth::user();
	// 	$locationcheck = new LokasiCheck();
	// 	$locationcheck->pengajuan_id = $request->input('pengajuan_id');
	// 	$locationcheck->commitcheck_id = $request->input('verifcommit_id');
	// 	$locationcheck->pkscheck_id = $request->input('verifpks_id');
	// 	$locationcheck->poktan_id = $request->input('poktan_id');
	// 	$locationcheck->anggota_id = $request->input('anggotamitra_id');
	// 	$locationcheck->npwp = $request->input('npwp');
	// 	$locationcheck->no_ijin = $request->input('no_ijin');
	// 	$locationcheck->onlineverif_at = Carbon::now();
	// 	$locationcheck->onlineverif_by = $user->id;
	// 	$locationcheck->onlinestatus = $request->input('onlinestatus');
	// 	$locationcheck->onlinenote = $request->input('onlinenote');
	// 	// dd($locationcheck);
	// 	$locationcheck->save();
	// 	return redirect()->route('verification.data.show', $locationcheck->pengajuan_id)
	// 		->with('success', 'Data berhasil disimpan');
	// }

	//  public function commitmentcheck($id)
	//  {
	// 	 abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

	// 	 $module_name = 'Verifikasi';
	// 	 $page_title = 'Verifikasi Data';
	// 	 $page_heading = 'Pemeriksaan Berkas Komitmen';
	// 	 $heading_class = 'fal fa-file-search';

	// 	 $user = Auth::user();
	// 	 $commitmentcheck = CommitmentCheck::findOrFail($id);
	// 	 $commitment = PullRiph::findOrFail($commitmentcheck->pengajuan->commitment_id);

	// 	 // dd($commitmentcheck);

	// 	 return view('admin.verifikasi.online.commitmentcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'user', 'commitmentcheck', 'commitment'));
	//  }

	//  public function commitmentstore(Request $request, $id)
	//  {
	// 	 abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

	// 	 $user = Auth::user();
	// 	 $commitmentcheck = CommitmentCheck::findOrFail($id);

	// 	 $pengajuan = Pengajuan::find($commitmentcheck->pengajuan_id);
	// 	 $commitmentcheck->verif_by = $user->id;
	// 	 $commitmentcheck->verif_at = Carbon::now();
	// 	 $commitmentcheck->formRiph = $request->input('formRiph');
	// 	 $commitmentcheck->formSptjm = $request->input('formSptjm');
	// 	 $commitmentcheck->logbook = $request->input('logbook');
	// 	 $commitmentcheck->formRt = $request->input('formRt');
	// 	 $commitmentcheck->formRta = $request->input('formRta');
	// 	 $commitmentcheck->formRpo = $request->input('formRpo');
	// 	 $commitmentcheck->formLa = $request->input('formLa');
	// 	 $commitmentcheck->note = $request->input('note');

	// 	 $commitmentcheck->save();
	// 	 return redirect()->route('verification.data.show', $pengajuan->id)
	// 		 ->with('success', 'Data Pemeriksaan berhasil disimpan');
	//  }

	// public function pkscheck($id, $poktan_id)
	// {
	// 	abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

	// 	$module_name = 'Verifikasi';
	// 	$page_title = 'Verifikasi Data';
	// 	$page_heading = 'Data dan Berkas PKS';
	// 	$heading_class = 'fal fa-ballot-check';

	// 	$pks = Pks::where('poktan_id', $poktan_id)->latest()->first();
	// 	$commitment = PullRiph::where('no_ijin', $pks->no_ijin)
	// 		->first();
	// 	$verifikasi = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)
	// 		->latest()
	// 		->first();

	// 	$pkscheck = PksCheck::where('pks_id', $pks->id)->latest()->first();
	// 	return view('admin.verifikasi.tanam.pkscheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pks', 'commitment', 'verifikasi', 'pkscheck'));
	// }

	// public function pksstore(Request $request, $poktan_id)
	// {
	// 	$user = Auth::user();
	// 	$pks = Pks::find($poktan_id);

	// 	PksCheck::updateOrCreate(
	// 		[
	// 			'npwp'
	// 		],
	// 	);
	// 	$pkscheck = new PksCheck();
	// 	$pkscheck->pengajuan_id = $request->input('pengajuan_id');
	// 	$pkscheck->pks_id = $request->input('pks_id');
	// 	$pkscheck->poktan_id = $request->input('poktan_id');
	// 	$pkscheck->npwp = $request->input('npwp');
	// 	$pkscheck->no_ijin = $request->input('no_ijin');
	// 	$pkscheck->status = $request->input('status');
	// 	$pkscheck->verif_at = Carbon::now();
	// 	$pkscheck->verif_by = $user->id;
	// 	// $pkscheck->note = $request->input('note');
	// 	// dd($pkscheck);
	// 	$pkscheck->save();

	// 	$verifikasi = AjuVerifTanam::where('id', $pkscheck->pengajuan_id)->first();
	// 	$status = '3';
	// 	$verifikasi->status = $status;

	// 	$verifikasi->save();
	// 	return redirect()->back()
	// 		->with('success', 'Data Pemeriksaan berhasil disimpan');
	// }

	// public function pksedit($id)
	// {

	// 	$module_name = 'Verifikasi';
	// 	$page_title = 'Verifikasi Data';
	// 	$page_heading = 'Ubah data Verifikasi PKS';
	// 	$heading_class = 'fal fa-ballot-check';

	// 	$pkscheck = PksCheck::find($id);
	// 	// dd($pkscheck);
	// 	$pks = Pks::where('poktan_id', $pkscheck->poktan_id)->first();
	// 	$commitment = PullRiph::where('no_ijin', $pkscheck->no_ijin)
	// 		->first();
	// 	$verifikasi = AjuVerifTanam::find($pkscheck->pengajuan_id);
	// 	// $commitmentcheck = CommitmentCheck::where('pengajuan_id', $verifikasi->id)
	// 	// 	->first();

	// 	// dd($pkscheck);
	// 	return view('admin.verifikasi.online.pksedit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pks', 'commitment', 'verifikasi', 'pkscheck'));
	// }

	// public function pksupdate(Request $request, $id)
	// {
	// 	$user = Auth::user();

	// 	$pkscheck = PksCheck::find($id);
	// 	// dd($pkscheck);
	// 	$pkscheck->note = $request->input('note');
	// 	$pkscheck->status = $request->input('status');
	// 	$pkscheck->verif_at = Carbon::now();
	// 	$pkscheck->verif_by = $user->id;

	// 	$pkscheck->save();
	// 	return redirect()->back()
	// 		->with('success', 'Data Pemeriksaan berhasil disimpan');
	// }
}
