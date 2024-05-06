<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifProduksi;
use App\Models\AjuVerifTanam;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Gate;

use App\Models\LokasiCheck;
use App\Models\Pengajuan;
use App\Models\CommitmentCheck;
use App\Models\PksCheck;
use App\Models\Lokasi;
use App\Models\MasterPoktan;
use App\Models\PullRiph;
use App\Models\Pks;
use App\Models\UserDocs;
use Illuminate\Support\Facades\DB;

class VerifProduksiController extends Controller
{

	public function index()
	{
		abort_if(Gate::denies('onfarm_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		//page level
		$module_name = 'Permohonan';
		$page_title = 'Daftar Pengajuan';
		$page_heading = 'Pengajuan Verifikasi Produksi';
		$heading_class = 'fa fa-map-marked-alt';

		$verifikator = Auth::user()->id;

		$verifTanams = AjuVerifTanam::where('check_by', $verifikator)->get();
		$noIjins = $verifTanams->pluck('no_ijin')->toArray();

		$verifikasis1 = AjuVerifProduksi::where('check_by', $verifikator)
			->orderBy('created_at', 'desc')
			->get();

		$verifikasis2 = AjuVerifProduksi::whereNull('check_by')
			->whereIn('no_ijin', $noIjins)
			->orderBy('created_at', 'desc')
			->get();

		$verifikasis = $verifikasis1->merge($verifikasis2)->sortByDesc('created_at');



		// dd($verifikasis);
		return view('admin.verifikasi.produksi.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasis'));
	}

	public function check($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		// Page level
		$module_name = 'Permohonan';
		$page_title = 'Data Pengajuan';
		$page_heading = 'Data Pengajuan Verifikasi';
		$heading_class = 'fa fa-file-search';

		// Populate related data
		$verifikasi = AjuVerifProduksi::findOrFail($id);
		$noIjin = str_replace(['/', '.'], '', $verifikasi->no_ijin);
		$verifTanam = AjuVerifTanam::where('no_ijin', $verifikasi->no_ijin)->first();
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$userDocs = UserDocs::where('no_ijin', $verifikasi->no_ijin)->first();
		// $pkschecks = PksCheck::where('pengajuan_id', $verifikasi->id)->get();
		$lokasichecks = LokasiCheck::where('pengajuan_id', $verifikasi->id)->orderBy('created_at', 'desc')->get();

		$pkss = Pks::withCount('lokasi')->where('no_ijin', $verifikasi->no_ijin)
			// ->with(['pkscheck' => function ($query) use ($id) {
			// 	$query->where('pengajuan_id', $id);
			// }])
			->get();

		// $pkss->each(function ($pks) {
		// 	$pks->pksCheck = $pks->pkscheck->isNotEmpty() ? $pks->pkscheck->first() : null;
		// });

		$poktanIds = Pks::where('no_ijin', $verifikasi->no_ijin)->pluck('poktan_id'); // Retrieve the poktan_id values

		// Group poktan_id values and retrieve unique nama_kelompok values
		$poktans = MasterPoktan::whereIn('id', $poktanIds)
			->groupBy('poktan_id')
			->pluck('nama_kelompok', 'poktan_id');
		// dd($poktans);
		$lokasis = collect();
		foreach ($pkss as $pkscheck) {
			$lokasi = Lokasi::where('poktan_id', $pkscheck->poktan_id)
				->where('no_ijin', $verifikasi->no_ijin)
				->get();
			$lokasis->push($lokasi);
		}

		$total_luastanam = $commitment->datarealisasi->sum('luas_lahan');
		$total_volume = $commitment->datarealisasi->sum('volume');

		// $pks = Pks::where('no_ijin', $commitment->no_ijin)->get();
		$countPoktan = $pkss->count();
		$countPks = $pkss->where('berkas_pks', '!=', null)->count();

		return view('admin.verifikasi.produksi.checks', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'lokasichecks', 'pkss', 'poktans', 'lokasis', 'total_luastanam', 'total_volume', 'countPoktan', 'countPks', 'verifTanam', 'userDocs', 'noIjin'));
	}

	public function checkBerkas(Request $request, $id)
	{
		$user = Auth::user();
		$verifProduksi = AjuVerifProduksi::find($id);
		$npwp = $verifProduksi->npwp;
		$noIjin = $verifProduksi->no_ijin;
		$commitmentId = $verifProduksi->commitment_id;

		$commitment = PullRiph::where('no_ijin', $noIjin)->first();

		try {
			DB::beginTransaction();
			$checks = [
				'sptjmtanamcheck',
				'rtacheck',
				'sphtanamcheck',
				'spvpcheck',
				'sptjmproduksicheck',
				'rpocheck',
				'sphproduksicheck',
				'logbookproduksicheck',
				'formLacheck',
			];
			// Create an empty data array to hold the updates
			$data = [];
			foreach ($checks as $check) {
				// Use the column name from the checks array as the input name
				$data[$check] = $request->input($check);
			}
			$data['prodcheck_by'] = $user->id;
			$data['prodverif_at'] = Carbon::now();
			UserDocs::updateOrCreate(
				[
					'npwp' => $npwp,
					'commitment_id' => $commitmentId,
					'no_ijin' => $noIjin,
				],
				$data
			);

			// dd($data);
			if ($verifProduksi->status == '1') {
				$verifProduksi->status = '2'; //pemeriksaan berkas selesai
			}
			$verifProduksi->save();
			DB::commit();
			// Flash message sukses
			return redirect()->back()->with('success', 'Hasil pemeriksaan berkas dan status berhasil disimpan.');
		} catch (\Exception $e) {
			// Rollback transaksi jika ada kesalahan
			DB::rollBack();

			// Flash message kesalahan
			return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggh berkas: ' . $e->getMessage());
		}
	}

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

		$verifikasi = AjuVerifProduksi::where('no_ijin', $no_ijin)->first();
		$npwp = $verifikasi->npwp;
		$commitment = PullRiph::where('no_ijin', $no_ijin)->first();
		$pks = Pks::where('npwp', $npwp)
			->where('no_ijin', $no_ijin)
			->where('poktan_id', $poktan_id)
			->first();
		$actionRoute = route('verification.produksi.check.pks.store', $pks->id);
		$cancelRoute = route('verification.produksi.check', $verifikasi->id);
		// dd($actionRoute);
		return view('admin.verifikasi.tanam.verifPks', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'pks', 'npwp', 'commitment', 'actionRoute', 'cancelRoute'));
	}

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
		return redirect()->route('verification.produksi.check', ['id' => $verifId])->with('success', 'Data Pemeriksaan berhasil disimpan');
	}

	public function checkPksSelesai(Request $request, $id)
	{
		$user = Auth::user();
		$verifProduksi = AjuVerifProduksi::find($id);
		$npwp = $verifProduksi->npwp;
		$noIjin = $verifProduksi->no_ijin;

		if ($verifProduksi == '2') {
			$verifProduksi->status = '3'; //pemeriksaan PKS selesai
		}
		$verifProduksi->check_by = $user->id;
		$verifProduksi->verif_at = Carbon::now();

		$verifProduksi->save();

		return redirect()->back()->with('success', 'Hasil pemeriksaan berkas dan status berhasil disimpan.');
	}

	public function storeCheck(Request $request, $id)
	{
		$request->validate([
			'note' => 'required',
			'metode' => 'required',
			'status' => 'required',
			'ndhprp' => 'nullable|file|mimes:pdf|max:2048', // Aturan validasi untuk berkas NDHPRP (PDF)
			'baproduksi' => 'nullable|file|mimes:pdf|max:2048', // Aturan validasi untuk berkas Baproduksi (PDF)
		]);
		$user = Auth::user();
		$verifikasi = AjuVerifProduksi::find($id);
		abort_if(
			Gate::denies('online_access') ||
				($verifikasi->no_ijin != $request->input('no_ijin') &&
					$verifikasi->npwp != $request->input('npwp')),
			Response::HTTP_FORBIDDEN,
			'403 Forbidden'
		);
		$npwp = $verifikasi->npwp;
		$noIjin = $verifikasi->no_ijin;
		$commitmentId = $verifikasi->commitment_id;
		$commitment = PullRiph::where('no_ijin', $noIjin)->first();

		$fileNpwp = str_replace(['.', '-'], '', $npwp);
		$fileNoIjin = str_replace(['/', '.'], '', $noIjin);

		try {
			DB::beginTransaction();
			$ndhprpFile = $verifikasi->ndhprp;
			$baproduksiFile = $verifikasi->baproduksi;

			if ($request->hasFile('ndhprp')) {
				$file = $request->file('ndhprp');
				// Validasi tipe berkas (PDF)
				$request->validate([
					'ndhprp' => 'file|mimes:pdf|max:2048',
				]);
				$ndhprpFile = 'notdinprod_' . $fileNoIjin . '.' . $file->extension();
				$file->storeAs('uploads/' . $fileNpwp . '/' . $commitment->periodetahun, $ndhprpFile, 'public');
			}

			if ($request->hasFile('baproduksi')) {
				$file = $request->file('baproduksi');
				// Validasi tipe berkas (PDF)
				$request->validate([
					'baproduksi' => 'file|mimes:pdf|max:2048',
				]);
				$baproduksiFile = 'baproduksi_' . $fileNoIjin . '.' . $file->extension();
				$file->storeAs('uploads/' . $fileNpwp . '/' . $commitment->periodetahun, $baproduksiFile, 'public');
			}

			AjuVerifProduksi::updateOrCreate(
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
					'baproduksi' => $baproduksiFile,
					'ndhprp' => $ndhprpFile,
				]
			);

			DB::commit();

			return redirect()->route('verification.produksi.show', $id)->with('success', 'Data berhasil disimpan');
		} catch (\Exception $e) {
			// Rollback the transaction if an exception occurs
			DB::rollback();

			return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
		}
	}

	public function show($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		// Page level
		$module_name = 'Permohonan';
		$page_title = 'Ringkasan Hasil Verifikasi Produksi'; //muncul pada hasil print
		$page_heading = 'Ringkasan Hasil Verifikasi Produksi';
		$heading_class = 'fal fa-file-check';

		// Populate related data
		$verifikasi = AjuVerifProduksi::findOrFail($id);
		$noIjin = str_replace(['/', '.'], '', $verifikasi->no_ijin);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$userDocs = UserDocs::where('no_ijin', $verifikasi->no_ijin)->first();
		$anggotas = Lokasi::where('no_ijin', $commitment->no_ijin);

		$pkss = Pks::withCount('lokasi')->where('no_ijin', $verifikasi->no_ijin)
			->get();

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->datarealisasi->sum('volume');

		$countPoktan = $pkss->count();
		$countPks = $pkss->where('berkas_pks', '!=', null)->count();
		$countAnggota = $anggotas->count();
		$hasGeoloc = $anggotas->sum('nama_lokasi');

		return view('admin.verifikasi.produksi.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'pkss', 'total_luastanam', 'total_volume', 'countPoktan', 'countPks', 'userDocs', 'noIjin', 'hasGeoloc', 'countAnggota'));
	}


	// ke bawa ini kemungkinan dihapus

	public function farmlist($id)
	{
		abort_if(Gate::denies('onfarm_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Verifikasi';
		$page_title = 'Verifikasi Lapangan';
		$page_heading = 'Daftar Lokasi Sampling';
		$heading_class = 'fal fa-map-marked-alt';

		$verifikasi = AjuVerifProduksi::findOrFail($id);
		$lokasis = Lokasi::where('no_ijin', $verifikasi->no_ijin);
		$lokasichecks = LokasiCheck::where('pengajuan_id', $id)->get();
		return view('admin.verifikasi.onfarm.farmlist', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'lokasichecks'));
	}

	public function farmcheck($noIjin, $anggota_id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Verifikasi Data';
		$page_title = 'Verifikasi Data Lokasi';
		$page_heading = 'Pemeriksaan Data Tanam dan Produksi';
		$heading_class = 'fal fa-ballot-check';

		//convert $noIjin to its original form.
		// $no_ijin = substr_replace(substr_replace(substr_replace(substr_replace(substr_replace($noIjin, '/', 4, 0), '.', 7, 0), '/', 11, 0), '/', 13, 0), '/', 16, 0);
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
		$verifikasi = Pengajuan::where('no_ijin', $no_ijin)
			->latest()
			->first();
		$commitmentcheck = CommitmentCheck::where('no_pengajuan', $verifikasi->no_pengajuan)->first();
		$pkscheck = PksCheck::where('pengajuan_id', $verifikasi->id)->first();
		$lokasicheck = LokasiCheck::where('anggota_id', $anggota_id)
			->where('poktan_id', $pks->poktan_id)
			->where('pengajuan_id', $verifikasi->id)
			->where('commitcheck_id', $commitmentcheck->id)
			->where('pkscheck_id', $pkscheck->id)
			->first();

		$anggotamitra = $lokasi;
		$pksmitra = $pkscheck;
		$verifcommit = $commitmentcheck;
		$verifpks = $pkscheck;
		$veriflokasi = $lokasicheck;
		// dd($veriflokasi);
		return view('admin.verifikasi.onfarm.farmcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'lokasi', 'pks', 'commitment', 'verifikasi', 'commitmentcheck', 'pkscheck', 'lokasicheck', 'pksmitra', 'anggotamitra', 'verifcommit', 'verifpks', 'veriflokasi'));
	}

	public function farmedit($id)
	{
		$lokasicheck = LokasiCheck::findOrFail($id);
	}

	public function farmupdate(Request $request, $id)
	{
		$user = Auth::user();
		$lokasicheck = LokasiCheck::findOrFail($id);
		$lokasicheck->onfarmverif_by = $user->id;
		$lokasicheck->onfarmverif_at = Carbon::now();
		$lokasicheck->latitude = $request->input('latitude');
		$lokasicheck->longitude = $request->input('longitude');
		$lokasicheck->altitude = $request->input('altitude');
		$lokasicheck->polygon = $request->input('polygon');
		$lokasicheck->luas_verif = $request->input('luas_verif');
		// $lokasicheck->tgl_ukur = Carbon::now();
		$lokasicheck->volume_verif = $request->input('volume_verif');
		// $lokasicheck->tgl_timbang = $request->input('tgl_timbang');
		$lokasicheck->volume_verif = $request->input('volume_verif');
		$lokasicheck->volume_verif = $request->input('volume_verif');
		$lokasicheck->metode = $request->input('metode');
		$lokasicheck->onfarmnote = $request->input('onfarmnote');
		$lokasicheck->onfarmstatus = $request->input('onfarmstatus');
		$lokasicheck->metode = $request->input('metode');
		// $no_ijin = $request->input('mod_noijin');
		// dd($no_ijin);
		dd($lokasicheck);
		// $lokasicheck->save();
		return redirect()->route('verification.onfarm.farmlist', $lokasicheck->pengajuan_id)
			->with('success', 'Data berhasil disimpan');
	}

	public function update(Request $request, $id)
	{
		$user = Auth::user();
		$verifikasi = Pengajuan::findOrFail($id);
		$verifikasi->onfarmcheck_by = $user->id;
		$verifikasi->onfarmdate = Carbon::now();
		$verifikasi->luas_verif = $request->input('luas_verif');
		$verifikasi->volume_verif = $request->input('volume_verif');
		$verifikasi->onfarmstatus = $request->input('onfarmstatus');
		$verifikasi->status = $request->input('onfarmstatus');
		$verifikasi->metode = $request->input('metode');
		$verifikasi->onfarmnote = $request->input('onfarmnote');
		$filenpwp = $request->input('npwp');
		$noIjin = $request->input('noIjin');
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$commitment->status = $request->input('onfarmstatus');
		if ($request->hasFile('baonfarm')) {
			$file = $request->file('baonfarm');
			$filename = 'baonfarm_' . $noIjin . '.' . $file->extension();
			$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun, $filename, 'public');
			$verifikasi->baonfarm = $filename;
		}
		$verifikasi->save();
		$commitment->save();
		return redirect()->route('verification.onfarm')
			->with('success', 'Data berhasil disimpan');
	}
}
