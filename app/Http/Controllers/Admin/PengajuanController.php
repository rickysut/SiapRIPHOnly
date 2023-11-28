<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifProduksi;
use App\Models\AjuVerifSkl;
use App\Models\AjuVerifTanam;
use App\Models\CommitmentCheck;
use App\Models\Completed;
use App\Models\PullRiph;
use App\Models\Lokasi;
use App\Models\LokasiCheck;
use App\Models\MasterPoktan;
use App\Models\Pks;
use App\Models\PksCheck;
use App\Models\UserDocs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
	//halaman daftar pengajuan untuk importir
	public function index(Request $request)
	{
		$module_name = 'Proses RIPH';
		$page_title = 'Daftar Pengajuan Verifikasi';
		$page_heading = 'Daftar Pengajuan Verifikasi';
		$heading_class = 'fal fa-ballot-check';

		$verifTanams = AjuVerifTanam::where('npwp', Auth::user()->data_user->npwp_company)
			->with('commitment')
			->get();

		$verifProduksis = AjuVerifProduksi::where('npwp', Auth::user()->data_user->npwp_company)
			->with('commitment')
			->get();

		$verifSkls = AjuVerifSkl::where('npwp', Auth::user()->data_user->npwp_company)
			->with('commitment')
			->get();
		// dd($verifSkls);
		/**
		 * memerlukan:
		 * migrasi dan model file verifSkl dan table avskl
		 * controller untuk verifikasi dan pengajuan SKL
		 * syarat pengajuan:
		 * 1. Volume Produksi yang dilaporkan sudah >100%
		 * 2. hasil verifikasi produksi = 8/telah di verifikasi produksi (cek lagi kode status)
		 */

		return view('admin.pengajuan.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifTanams', 'verifProduksis', 'verifSkls'));
	}

	private function getDataPengajuan($commitment)
	{
		$user = Auth::user();
		$npwp_company = $user->data_user->npwp_company;

		$verifTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->first() ?? new AjuVerifTanam();
		$verifProduksi = AjuVerifProduksi::where('no_ijin', $commitment->no_ijin)->first() ?? new AjuVerifProduksi();
		$verifSkl = AjuVerifSkl::where('no_ijin', $commitment->no_ijin)->first() ?? new AjuVerifSkl();
		$userDocs = UserDocs::where('no_ijin', $commitment->no_ijin)->first() ?? new UserDocs();
		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get() ?? new Pks();
		$lokasis = Lokasi::where('no_ijin', $commitment->no_ijin)->get() ?? new Lokasi();

		$summaryData = [
			'company' => $user->data_user->company_name,
			'noIjin' => $commitment->no_ijin,
			'periode' => $commitment->periodetahun,
			'avtDate' => $verifTanam->created_at,
			'avtVerifAt' => $verifTanam->verif_at,
			'avtStatus' => $verifTanam->status,
			'avtMetode' => $verifTanam->metode,
			'avtNote' => $verifTanam->note,
			'avpDate' => $verifProduksi->created_at,
			'avpVerifAt' => $verifProduksi->verif_at,
			'avpStatus' => $verifProduksi->status,
			'avpMetode' => $verifProduksi->metode,
			'avpNote' => $verifProduksi->note,
			'avsklDate' => $verifSkl->created_at,
			'avsklVerifAt' => $verifSkl->verif_at,
			'avsklStatus' => $verifSkl->status,
			'avsklMetode' => $verifSkl->metode,
			'avsklNote' => $verifSkl->note,
			'publishedAt' => $verifSkl->published_date,
			'userDocs' => $userDocs,
			'wajibTanam' => $commitment->luas_wajib_tanam,
			'wajibProduksi' => $commitment->volume_produksi,
			'realisasiTanam' => $commitment->datarealisasi->sum('luas_lahan'),
			'realisasiProduksi' => $commitment->datarealisasi->sum('volume'),
			'hasGeoloc' => $commitment->datarealisasi->count(),
			'countPoktan' => $pks->count(),
			'countPks' => $pks->where('berkas_pks', '!=', null)->count(),
			'countAnggota' => $lokasis->count(),
		];

		return $summaryData;
	}


	//pindahkan pengajuan verifikasi tanam verifTanamController ke sini
	public function ajuVerifTanam($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi Tanam';
		$page_heading = 'Pengajuan Verifikasi Tanam';
		$heading_class = 'fal fa-file-invoice';

		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);;
		$data = $this->getDataPengajuan($commitment);

		return view('admin.pengajuan.verifskl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment') + $data);
	}

	public function ajuVerifTanamStore($id)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);
		$completed = Completed::where('no_ijin', $commitment->no_ijin)
			->first();

		if ($completed && $completed->url) {
			session()->flash('error', 'SKL telah terbit. Anda tidak dapat membuat pengajuan verifikasi kembali untuk RIPH ini.');
			return redirect()->back();
		}

		$verifTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->first();
		$verifProduksi = AjuVerifProduksi::where('no_ijin', $commitment->no_ijin)->first();
		$userDoc = UserDocs::where('no_ijin', $commitment->no_ijin)->first();
		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get();

		//data validasi
		$lokasis = Lokasi::where('no_ijin', $commitment->no_ijin)->get();
		$wajibTanam = $commitment->luas_wajib_tanam;
		$wajibProduksi = $commitment->volume_produksi;
		$realisasiTanam = $lokasis->sum('luas_tanam');
		$realisasiProduksi = $lokasis->sum('volume');

		// Validasi berkas
		if ($userDoc === null) {
			$errorMessage = 'Anda belum memiliki kelengkapan dokumen untuk diperiksa.';
		} elseif ($userDoc->sptjmtanam === null) {
			$errorMessage = 'Surat Pertanggungjawaban Mutlak tidak ditemukan.';
		} elseif ($userDoc->spvt === null) {
			$errorMessage = 'Surat Pengajuan Verifikasi Tanam tidak ditemukan.';
		} elseif ($userDoc->rta === null) {
			$errorMessage = 'Form Realisasi Tanam tidak ditemukan.';
		} elseif ($userDoc->sphtanam === null) {
			$errorMessage = 'Dokumen SPH-SBS (Tanam) tidak ditemukan.';
		}

		$optionalMessage = 'Pengajuan Verifikasi Produksi untuk RIPH No ' . $commitment->no_ijin . ' tidak dapat dilakukan. Ajukan kembali setelah Anda melengkapi data dan syarat-syarat yang diperlukan.';

		if (isset($errorMessage)) {
			return redirect()->route('admin.task.commitment')->withErrors($errorMessage . $optionalMessage);
		}

		// Continue with creating or updating ajuVerifTanam.
		AjuVerifTanam::updateOrCreate(
			[
				'npwp' => $commitment->npwp,
				'commitment_id' => $commitment->id,
				'no_ijin' => $commitment->no_ijin,
			],
			[
				'status' => '1',
			]
		);

		return redirect()->route('admin.task.commitment')
			->with('success', 'Pengajuan verifikasi tanam berhasil dibuat.');
	}

	//pindahkan pengajuan verifikasi produksi ke sini
	public function ajuVerifProduksi($id)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);

		$total_luastanam = $commitment->datarealisasi->sum('luas_lahan');
		$total_volume = $commitment->datarealisasi->sum('volume');

		// aktifkan saat production
		abort_if($total_volume < $commitment->volume_produksi, Response::HTTP_FORBIDDEN, 'Total produksi dilaporkan tidak memenuhi syarat');

		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi Produksi';
		$page_heading = 'Pengajuan Verifikasi Produksi';
		$heading_class = 'fal fa-file-invoice';
		$data = $this->getDataPengajuan($commitment);

		return view('admin.pengajuan.verifskl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment') + $data);
	}

	public function ajuVerifProduksiStore($id)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);
		$completed = Completed::where('no_ijin', $commitment->no_ijin)
			->first();

		if ($completed && $completed->url) {
			session()->flash('error', 'SKL telah terbit. Anda tidak dapat membuat pengajuan verifikasi kembali untuk RIPH ini.');
			return redirect()->back();
		}

		$checkTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->first();
		$checker = $checkTanam->check_by;

		$verifTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->first();
		$verifProduksi = AjuVerifProduksi::where('no_ijin', $commitment->no_ijin)->first();
		$userDoc = UserDocs::where('no_ijin', $commitment->no_ijin)->first();
		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get();

		//data validasi
		$lokasis = Lokasi::where('no_ijin', $commitment->no_ijin)->get();
		$wajibTanam = $commitment->luas_wajib_tanam;
		$wajibProduksi = $commitment->volume_produksi;
		$realisasiTanam = $commitment->datarealisasi->sum('luas_lahan');
		$realisasiProduksi = $commitment->datarealisasi->sum('volume');

		// Validasi berkas
		if ($userDoc === null) {
			$errorMessage = 'Anda belum memiliki kelengkapan dokumen untuk diperiksa.';
		} elseif ($userDoc->sptjmproduksi === null) {
			$errorMessage = 'Surat Pertanggungjawaban Mutlak tidak ditemukan.';
		} elseif ($userDoc->spvp === null) {
			$errorMessage = 'Surat Pengajuan Verifikasi Produksi tidak ditemukan.';
		} elseif ($userDoc->rpo === null) {
			$errorMessage = 'Form Realisasi Produksi tidak ditemukan.';
		} elseif ($userDoc->sphproduksi === null) {
			$errorMessage = 'Dokumen SPH-SBS (Produksi) tidak ditemukan.';
		} elseif ($realisasiProduksi < $wajibProduksi) {
			$errorMessage = 'Jumlah Realisasi Produksi yang dilaporkan tidak memenuhi syarat.';
		}

		$optionalMessage = 'Pengajuan Verifikasi Produksi untuk RIPH No ' . $commitment->no_ijin . ' tidak dapat dilakukan. Ajukan kembali setelah Anda melengkapi data dan syarat-syarat yang diperlukan.';

		if (isset($errorMessage)) {
			return redirect()->route('admin.task.commitment')->withErrors($errorMessage . $optionalMessage);
		}



		// Continue with creating or updating AjuVerifProduksi.
		AjuVerifProduksi::updateOrCreate(
			[
				'npwp' => $commitment->npwp,
				'commitment_id' => $commitment->id,
				'no_ijin' => $commitment->no_ijin,
			],
			[
				'status' => '1',
				'check_by' => $checker,
			]
		);

		return redirect()->route('admin.task.commitment')
			->with('success', 'Pengajuan verifikasi produksi berhasil dibuat.');
	}

	//pengajuan verifikasi skl
	public function ajuVerifSkl($id)
	{
		/**
		 * syarat pengajuan SKL
		 * 1. Status Verifikasi Produksi = 4
		 * 2. Berkas SPH-SBS (Produksi) = ada
		 * 3. total volume produksi >= komitment
		 */

		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Penerbitan SKL';
		$page_heading = 'Pengajuan Penerbitan SKL';
		$heading_class = 'fal fa-file-invoice';

		$commitment = PullRiph::find($id);
		$data = $this->getDataPengajuan($commitment);

		return view('admin.pengajuan.verifskl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment') + $data);
	}

	public function ajuVerifSklStore($id)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);
		$completed = Completed::where('no_ijin', $commitment->no_ijin)
			->first();

		if ($completed && $completed->url) {
			session()->flash('error', 'SKL telah terbit. Anda tidak dapat membuat pengajuan verifikasi kembali untuk RIPH ini.');
			return redirect()->back();
		}

		$verifTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->first();
		$verifProduksi = AjuVerifProduksi::where('no_ijin', $commitment->no_ijin)->first();
		$userDoc = UserDocs::where('no_ijin', $commitment->no_ijin)->first();
		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get();

		//data validasi
		$lokasis = Lokasi::where('no_ijin', $commitment->no_ijin)->get();
		$wajibTanam = $commitment->luas_wajib_tanam;
		$realisasiTanam = $commitment->datarealisasi->sum('luas_lahan');
		$wajibProduksi = $commitment->volume_produksi;
		$realisasiProduksi = $commitment->datarealisasi->sum('volume');

		// Validasi berkas
		if ($userDoc === null) {
			$errorMessage = 'Anda belum memiliki kelengkapan dokumen untuk diperiksa.';
		} elseif ($userDoc->sptjmtanam === null) {
			$errorMessage = 'Surat Pertanggungjawaban Mutlak (tanam) tidak ditemukan.';
		} elseif ($userDoc->sptjmproduksi === null) {
			$errorMessage = 'Surat Pertanggungjawaban Mutlak (produksi) tidak ditemukan.';
		} elseif ($userDoc->rta === null) {
			$errorMessage = 'Form Realisasi Tanam tidak ditemukan.';
		} elseif ($userDoc->rpo === null) {
			$errorMessage = 'Form Realisasi Produksi tidak ditemukan.';
		} elseif ($userDoc->sphproduksi === null) {
			$errorMessage = 'Dokumen SPH-SBS (Tanam dan Produksi) tidak ditemukan.';
		} elseif ($userDoc->formLa === null) {
			$errorMessage = 'Dokumen Laporan Akhir tidak ditemukan.';
		} elseif ($realisasiProduksi < $wajibProduksi) {
			$errorMessage = 'Realisasi Produksi yang dilaporkan tidak memenuhi syarat.';
		} elseif ($verifProduksi === null || $verifProduksi->status !== '4') {
			$errorMessage = 'Hasil Verifikasi tahap Produksi tidak memenuhi syarat.';
		}

		$optionalMessage = 'Pengajuan Keterangan Lunas untuk RIPH No ' . $commitment->no_ijin . ' tidak dapat dilakukan. Ajukan kembali setelah Anda melengkapi syarat-syarat yang diperlukan.';

		if (isset($errorMessage)) {
			return redirect()->route('admin.task.commitment')->withErrors($errorMessage . $optionalMessage);
		}

		AjuVerifSkl::updateOrCreate(
			[
				'npwp' => $commitment->npwp,
				'commitment_id' => $commitment->id,
				'no_ijin' => $commitment->no_ijin,
			],
			[
				'status' => '1',
			]
		);
		return redirect()->route('admin.task.commitment')
			->with('success', 'Permohonan Penerbitan Surat Keterangan Lunas berhasil diajukan.');
	}

	public function showAjuTanam($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi Tanam';
		$page_heading = 'Pengajuan Verifikasi Tanam';
		$heading_class = 'fal fa-file-invoice';

		$commitment = PullRiph::find($id);
		$data = $this->getDataPengajuan($commitment);

		return view('admin.pengajuan.verifskl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment') + $data);
	}

	public function showAjuProduksi($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Penerbitan SKL';
		$page_heading = 'Pengajuan Penerbitan SKL';
		$heading_class = 'fal fa-file-invoice';

		$commitment = PullRiph::find($id);
		$data = $this->getDataPengajuan($commitment);

		return view('admin.pengajuan.verifskl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment') + $data);
	}

	public function showAjuSkl($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Penerbitan SKL';
		$page_heading = 'Pengajuan Penerbitan SKL';
		$heading_class = 'fal fa-file-invoice';

		$commitment = PullRiph::find($id);
		$data = $this->getDataPengajuan($commitment);

		return view('admin.pengajuan.verifskl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment') + $data);
	}
}
