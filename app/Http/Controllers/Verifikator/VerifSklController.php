<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifProduksi;
use App\Models\AjuVerifSkl;
use App\Models\AjuVerifTanam;
use App\Models\Completed;
use App\Models\DataAdministrator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Gate;

use App\Models\DataUser;
use App\Models\Lokasi;
use App\Models\MasterPoktan;
use App\Models\PullRiph;
use App\Models\Pks;
use App\Models\Skl;
use App\Models\SklReads;
use App\Models\User;
use App\Models\UserDocs;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

//a little change
class VerifSklController extends Controller
{
	protected $sklid = -1;
	protected $msg = '';
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		abort_if(Gate::denies('onfarm_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		//page level
		$module_name = 'Permohonan';
		$page_title = 'Daftar Pengajuan';
		$page_heading = 'Pengajuan Surat Keterangan Lunas';
		$heading_class = 'fal fa-file-certificate';

		//table pengajuan. jika sudah mengajukan SKL, maka pengajuan terkait tidak muncul
		$verifikasis = AjuVerifSkl::orderBy('created_at', 'desc')
			->where('status', '!=', '4')
			->get();

		// dd($verifikasis);
		return view('admin.verifikasi.skl.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasis'));
	}

	public function dataCheck($id)
	{
		$verifikasi = AjuVerifSkl::findOrFail($id);
		$npwp = str_replace(['.', '-'], '', $verifikasi->npwp);

		$commitment = $verifikasi->commitment;
		$npwp_company = $verifikasi->npwp;

		// $verifTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->firstOrNew([]);
		$verifTanam = $verifikasi->commitment->ajutanam;
		$verifProduksi = $verifikasi->commitment->ajuproduksi;
		$userDocs = $verifikasi->commitment->userDocs;
		$pks = $verifikasi->commitment->pks;
		$lokasis = $verifikasi->commitment->lokasi;

		$skl = Skl::where('pengajuan_id', $verifikasi->id)->first();



		$data = [
			//ringkasan umum
			'company' => optional(DataUser::where('npwp_company', $verifikasi->npwp)->first())->company_name,
			'npwp' => $npwp_company,
			'noIjin' => $commitment->no_ijin,
			'periode' => $commitment->periodetahun,
			'tglIjin' => Carbon::parse($commitment->tgl_ijin)->format('d-m-Y'),
			'tglAkhir'	=> Carbon::parse($commitment->tgl_akhir)->format('d-m-Y'),

			'avtDate' => $verifTanam->created_at,
			'avtVerifAt' => $verifTanam->verif_at,
			'avtStatus' => optional($verifTanam)->status,
			'avtMetode' => optional($verifTanam)->metode,
			'avtNote' => optional($verifTanam)->note,
			'ndhprt' => optional($verifTanam)->ndhprt,
			'batanam' => optional($verifTanam)->batanam,

			//ringkasan pengajuan verifikasi produksi
			'avpDate' => optional($verifProduksi)->created_at->format('d-m-Y'),
			'avpVerifAt' => Carbon::parse(optional($verifProduksi)->verif_at)->format('d-m-Y'),
			'avpStatus' => optional($verifProduksi)->status,
			'avpMetode' => optional($verifProduksi)->metode,
			'avpNote' => optional($verifProduksi)->note,
			'ndhprp' => optional($verifProduksi)->ndhprp,
			'baproduksi' => optional($verifProduksi)->baproduksi,

			//ringkasan pengajuan skl
			'avsklDate' => optional($verifikasi)->created_at->format('d-m-Y'),
			'avsklVerifAt' => Carbon::parse(optional($verifikasi)->verif_at)->format('d-m-Y'),
			'avsklStatus' => optional($verifikasi)->status,
			'avsklMetode' => optional($verifikasi)->metode,
			'avsklNote' => optional($verifikasi)->note,
			'ndhpskl' => optional($verifikasi)->ndhpskl,
			'baskls' => optional($verifikasi)->baskls,


			//ringkasan kewajiban dan realisasi
			'wajibTanam' => number_format($commitment->luas_wajib_tanam, 2, '.', ','),
			'wajibProduksi' => number_format($commitment->volume_produksi, 2, '.', ','),

			'realisasiTanam' => number_format($commitment->datarealisasi->sum('luas_lahan'), 2, '.', ','),
			'realisasiProduksi' => number_format($commitment->datarealisasi->sum('volume'), 2, '.', ','),
			'hasGeoloc' => number_format($commitment->datarealisasi->count(), 0, '.', ','),

			//ringkasan kemitraan
			'countPoktan' => $pks->count(),
			'countPks' => $pks->where('berkas_pks', '!=', null)->count(),
			'countAnggota' => $lokasis->count(),

			//tautan dokumen
			'sptjmLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->sptjm}"),
			'formLaLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->formLa}"),
			//tautan dokumen tanam
			'spvtLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->spvt}"),
			'rtaLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->rta}"),
			'sphtanamLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->sphtanam}"),
			'spdstLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->spdst}"),
			'logbooktanamLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->logbooktanam}"),
			'ndhprtLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$verifTanam->ndhprt}"),
			'batanamLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$verifTanam->batanam}"),

			//tautan dokumen produksi
			'spvpLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->spvp}"),
			'rpoLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->rpo}"),
			'sphproduksiLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->sphproduksi}"),
			'spdspLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->spdsp}"),
			'logbookproduksiLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->logbookproduksi}"),
			'ndhprpLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$verifProduksi->ndhprp}"),
			'baproduksiLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$verifProduksi->baproduksi}"),

			//tautan dokumen verifikasi skl
			'spsklLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$userDocs->spskl}"),
			'ndhpsklLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$verifikasi->ndhpskl}"),
			'basklsLink' => asset("storage/uploads/{$npwp}/{$commitment->periodetahun}/{$verifikasi->baskls}"),
			'noSkl' => optional($skl)->no_skl,
			'publishedDate' => optional($skl)->published_date ? optional($skl)->published_date->format('d-m-Y') : null,
			'approvedAt' => optional($skl)->approved_at ? optional($skl)->approved_at : null,

			// 'approvedAt' => $skl->approved_at,
			'submitBy' => optional(User::find(optional($skl)->submit_by))->name ?? 'Tidak dikenali oleh System',


			//used document
			'userDocs' => $userDocs,
		];

		return response()->json($data);
	}

	public function check($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		// Page level
		$module_name = 'Permohonan';
		$page_title = 'Data Pengajuan';
		$page_heading = 'Data Pengajuan Penerbitan SKL';
		$heading_class = 'fa fa-file-search';

		$verifikasi = AjuVerifSkl::findOrFail($id);
		$noIjin = str_replace(['/', '.'], '', $verifikasi->no_ijin);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();

		// Populate related data
		$verifTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->first() ?? new AjuVerifTanam();
		$userDocs = UserDocs::where('no_ijin', $commitment->no_ijin)->first() ?? new UserDocs();

		// Retrieve PKS data, including the count of related Lokasi
		$pkss = Pks::withCount('lokasi')
			->where('no_ijin', $verifikasi->no_ijin)
			->get() ?? new Pks();

		// Retrieve unique nama_kelompok values based on poktan_id
		$poktanIds = $pkss->pluck('poktan_id')->unique();
		$poktans = MasterPoktan::whereIn('id', $poktanIds)
			->pluck('nama_kelompok', 'poktan_id');

		// dd($verifikasi);

		return view('admin.verifikasi.skl.checks', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'pkss', 'poktans', 'verifTanam', 'userDocs', 'noIjin'));
	}

	public function checkBerkas(Request $request, $id)
	{
		$user = Auth::user();
		$verifSkl = AjuVerifSkl::find($id);
		$npwp = $verifSkl->npwp;
		$noIjin = $verifSkl->no_ijin;
		$commitmentId = $verifSkl->commitment_id;

		$commitment = PullRiph::where('no_ijin', $noIjin)->first();

		try {
			DB::beginTransaction();
			$checks = [
				'sptjmtanamcheck',
				'sptjmproduksicheck',
				'rtacheck',
				'rpocheck',
				'sphtanamcheck',
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
			$data['spsklcheck_by'] = $user->id;
			$data['spsklverif_at'] = Carbon::now();
			UserDocs::updateOrCreate(
				[
					'npwp' => $npwp,
					'commitment_id' => $commitmentId,
					'no_ijin' => $noIjin,
				],
				$data
			);

			// dd($data);
			if ($verifSkl->status == '1') {
				$verifSkl->status = '2'; //pemeriksaan berkas selesai
			}
			$verifSkl->save();
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

		$verifikasi = AjuVerifSkl::where('no_ijin', $no_ijin)->first();
		$npwp = $verifikasi->npwp;
		$commitment = PullRiph::where('no_ijin', $no_ijin)->first();
		$pks = Pks::where('npwp', $npwp)
			->where('no_ijin', $no_ijin)
			->where('poktan_id', $poktan_id)
			->first();
		$actionRoute = route('verification.skl.check.pks.store', $pks->id);
		$cancelRoute = route('verification.skl.check', $verifikasi->id);
		// dd($actionRoute);
		return view('admin.verifikasi.skl.verifPks', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'pks', 'npwp', 'commitment', 'actionRoute', 'cancelRoute'));
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
		return redirect()->route('verification.skl.check', ['id' => $verifId])->with('success', 'Data Pemeriksaan berhasil disimpan');
	}

	public function checkPksSelesai(Request $request, $id)
	{
		$user = Auth::user();
		$verifSkl = AjuVerifSkl::find($id);
		$npwp = $verifSkl->npwp;
		$noIjin = $verifSkl->no_ijin;

		//populate Pks with certain conditions
		$pkss = Pks::where('no_ijin', $noIjin)
			->where('berkas_pks', '!=', null)
			->where('status', null)
			->get();

		// dd($pkss);

		//set value for status field of populated $pkss to 'sesuai'
		foreach ($pkss as $pks) {
			$pks->update(['status' => 'sesuai']);
		}

		if ($verifSkl->status == '2') {
			$verifSkl->status = '3'; //pemeriksaan PKS selesai
		}
		$verifSkl->check_by = $user->id;
		$verifSkl->verif_at = Carbon::now();

		$verifSkl->save();

		return redirect()->back()->with('success', 'Hasil pemeriksaan berkas dan status berhasil disimpan.');
	}

	public function storeCheck(Request $request, $id)
	{
		$request->validate([
			'note' => 'required',
			'status' => 'nullable',
			'ndhpskl' => 'nullable|file|mimes:pdf|max:2048', // Aturan validasi untuk berkas NDHPSKL (PDF)
			'baskls' => 'nullable|file|mimes:pdf|max:2048', // Aturan validasi untuk berkas BASKLS (PDF)
		]);
		$user = Auth::user();
		$verifikasi = AjuVerifSkl::find($id);
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
			$ndhpsklFile = $verifikasi->ndhpskl;
			$basklFile = $verifikasi->baskls;

			if ($request->hasFile('ndhpskl')) {
				$file = $request->file('ndhpskl');
				// Validasi tipe berkas (PDF)
				$request->validate([
					'ndhpskl' => 'file|mimes:pdf|max:2048',
				]);
				$ndhpsklFile = 'notdinskl_' . $fileNoIjin . '.' . $file->extension();
				$file->storeAs('uploads/' . $fileNpwp . '/' . $commitment->periodetahun, $ndhpsklFile, 'public');
			}

			if ($request->hasFile('baskls')) {
				$file = $request->file('baskls');
				// Validasi tipe berkas (PDF)
				$request->validate([
					'baskls' => 'file|mimes:pdf|max:2048',
				]);
				$basklFile = 'baskls_' . $fileNoIjin . '.' . $file->extension();
				$file->storeAs('uploads/' . $fileNpwp . '/' . $commitment->periodetahun, $basklFile, 'public');
			}

			AjuVerifSkl::updateOrCreate(
				[
					'npwp' => $npwp,
					'commitment_id' => $commitmentId,
					'no_ijin' => $noIjin,
				],
				[
					'note' => $request->input('note'),
					'status' => $request->input('status'),
					'check_by' => $user->id,
					'verif_at' => Carbon::now(),
				]
			);

			Skl::updateOrCreate(
				[
					'pengajuan_id' => $id,
					'no_ijin' => $verifikasi->no_ijin,
					'npwp' => $verifikasi->npwp,
				],
				[
					'no_skl' => $request->input('no_skl'),
					'published_date' => $request->input('published_date'),
					'submit_by' => $user->id,
				]
			);

			$verifikasi->status = 2;
			// dd($verifikasi);
			$verifikasi->save();

			DB::commit();

			return redirect()->route('verification.skl.verifSklShow', $id)->with('success', 'Data berhasil disimpan');
		} catch (\Exception $e) {
			// Rollback the transaction if an exception occurs
			DB::rollback();

			return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
		}
	}

	public function verifSklShow($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$verifikasi = AjuVerifSkl::findOrFail($id);
		// Page level
		$module_name = 'Permohonan';
		$page_title = 'Ringkasan Rekomendasi SKL'; //muncul pada hasil print
		$page_heading = 'Ringkasan Rekomendasi SKL';
		$heading_class = 'fal fa-file-check';

		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		// dd($commitment);
		return view('admin.verifikasi.skl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment'));
	}

	public function recomend(Request $request, $id)
	{
		abort_if(Gate::denies('verification_skl_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$user = Auth::user();

		$avskl = AjuVerifSkl::findOrFail($id);

		Skl::updateOrCreate(
			[
				'pengajuan_id' => $id,
				'no_ijin' => $avskl->no_ijin,
				'npwp' => $avskl->npwp,
			],
			[
				'no_skl' => $request->input('no_skl'),
				'published_date' => $request->input('published_date'),
				'submit_by' => $user->id,
			]
		);
		$avskl->status = 6;
		$avskl->save();
		return redirect()->route('verification.skl.verifSklShow', $id)->with('success', 'Rekomendasi berhasil diajukan.');
	}

	//daftar rekomendasi skl untuk pejabat
	public function recomendations()
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'Daftar Permohonan';
		$page_heading = 'Daftar Permohonan Penerbitan SKL';
		$heading_class = 'fa fa-file-signature';

		// if (Auth::user()->roles[0]->title == 'Pejabat') {
		$recomends = Skl::where('approved_by', null)
			->get();
		// }
		// dd($recomends);
		return view('admin.verifikasi.skl.recomendations', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}

	//untuk pejabat melihat detail rekomendasi SKL
	public function showrecom($id)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'Permohonan Penerbitan';
		$page_heading = 'Permohonan Penerbitan SKL';
		$heading_class = 'fa fa-file-signature';

		$skl = Skl::findOrfail($id);
		$verifikasi = AjuVerifSkl::find($skl->pengajuan_id);
		$importir = DataUser::where('npwp_company', $verifikasi->npwp)->first();
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$wajib_tanam = $commitment->luas_wajib_tanam;
		$luas_verif = $verifikasi->luas_verif;
		$wajib_produksi = $commitment->volume_produksi;
		$volume_verif = $verifikasi->volume_verif;

		return view('admin.verifikasi.skl.recomshow', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl', 'verifikasi', 'importir', 'wajib_tanam', 'luas_verif', 'wajib_produksi', 'volume_verif', 'commitment'));
	}

	//untuk pejabat melihat draft skl.
	public function draftSKL($id)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}
		$module_name = 'SKL';
		$page_title = 'Draft SKL';
		$page_heading = 'Preview Draft SKL';
		$heading_class = 'fa fa-file-signature';
		$user = Auth::user();

		$skl = Skl::findOrFail($id);
		$verifikasi = AjuVerifSkl::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = DataAdministrator::where('user_id', $user->id)->first();
		$ttd = $user->name;
		// dd($pejabat);
		$wajib_tanam = $commitment->luas_wajib_tanam;
		$wajib_produksi = $commitment->volume_produksi;
		$total_luas = $commitment->datarealisasi->sum('luas_lahan');
		$total_volume = $commitment->datarealisasi->sum('volume');

		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'No. SKL'	=> $skl->no_skl,
			'Pejabat'	=> $pejabat->nama,
			'Status' => 'LUNAS',
		];

		$QrCode = QrCode::size(70)->generate('Perusahaan: ' . $data['Perusahaan'] . ', No. RIPH: ' . $data['No. RIPH'] . ', No. SKL: ' . $data['No. SKL'] . ', Disetujui dan Ditandatangani oleh: ' . $data['Pejabat'] . ', Status: ' . $data['Status'] . ', Tautan Berkas: Belum tersedia.');

		return view('admin.verifikasi.skl.skl_new', compact('page_title', 'skl', 'verifikasi', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'total_luas', 'total_volume'));
	}

	//fungsi untuk pejabat menyetujui skl diterbitkan.
	public function approve($id)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		try {
			return DB::transaction(function () use ($id) {
				$skl = Skl::find($id);
				$skl->approved_by = Auth::user()->id;
				$skl->approved_at = Carbon::now();
				$skl->save();

				$avskl = AjuVerifSkl::where('no_ijin', $skl->no_ijin)->first();
				$avskl->status = 3;
				$avskl->save();

				$pengajuan = AjuVerifSkl::find($skl->pengajuan_id);
				$commitment = PullRiph::find($pengajuan->commitment_id);
				$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
				$noIjin = str_replace(['.', '-', '/'], '', $skl->no_ijin);
				$thn = $commitment->periodetahun;
				$filename = 'skl_' . $noIjin . '.' . 'pdf';
				$path = 'uploads/' . $filenpwp . '/' . $thn . '/' . $filename;
				$filePath = asset('storage/' . $path);
				$skl->skl_upload = $filename;
				// $skl->published_date = Carbon::now();
				$commitment->skl = $filename;
				$completedData['skl_upload'] = $filename;
				$completedData['url'] = $filePath;

				$completedData = [
					'no_skl' => $skl->no_skl,
					'npwp' => $skl->npwp,
					'no_ijin' => $skl->no_ijin,
					'periodetahun' => $thn,
					'published_date' => $skl->published_date,
					'status' => 'Lunas',
					'skl_upload' => $filename,
					'url' => $filePath,
				];
				Completed::updateOrCreate(
					[
						'no_skl' => $skl->no_skl,
						'no_ijin' => $skl->no_ijin,
					],
					$completedData
				);

				return redirect()->route('verification.skl.recomendations')->with(['success' => 'Penerbitan SKL telah Anda setujui dan siap diterbitkan.']);
			});
		} catch (\Exception $e) {
			DB::rollback();
			return back()->with(['error' => 'An error occurred while storing the recommendation.']);
		}
	}

	public function recomended()
	{
		$module_name = 'SKL';
		$page_title = 'Daftar Rekomendasi';
		$page_heading = 'Daftar Rekomendasi Penerbitan SKL';
		$heading_class = 'fa fa-file-signature';

		$recomends = Skl::where('skl_upload', null)
			->get();
		// dd($recomends);
		return view('admin.verifikasi.skl.recomendeds', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}

	public function printReadySkl($id)
	{
		if (Auth::user()->roles[0]->title !== 'Admin') {
			abort(403, 'Unauthorized');
		}
		$module_name = 'SKL';
		$page_heading = 'Preview Draft SKL';
		$heading_class = 'fa fa-file-signature';

		$sklId = Skl::where('pengajuan_id', $id)->first()->id;
		$skl = Skl::find($sklId);
		if (empty($skl->approved_by)) {
			abort(403, 'Tidak dapat dicetak. Pejabat terkait belum menyetujui penerbitan SKL.');
		}
		$verifikasi = AjuVerifSkl::find($sklId);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = DataAdministrator::where('user_id', $skl->approved_by)->first();
		// dd($pejabat);
		$wajib_tanam = $commitment->luas_wajib_tanam;
		$wajib_produksi = $commitment->volume_produksi;
		$total_luas = $commitment->datarealisasi->sum('luas_lahan');
		$total_volume = $commitment->datarealisasi->sum('volume');
		$Url = Completed::where('no_skl', $skl->no_skl)->first()->url;
		// dd($Url);

		$page_title = 'SKL Cetak ' . $skl->skl_upload;
		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'No. SKL' => $skl->no_skl,
			'Pejabat' => $pejabat->nama,
			'Url'	=> $Url,
			'Status' => 'LUNAS',
		];

		$QrCode = QrCode::size(70)->generate('Perusahaan: ' . $data['Perusahaan'] . ', No. RIPH: ' . $data['No. RIPH'] . ', No. SKL: ' . $data['No. SKL'] . ', Disetujui dan Ditandatangani oleh: ' . $data['Pejabat'] . ', Status: ' . $data['Status'] . ', Tautan Berkas: ' . $data['Url']);

		return view('admin.verifikasi.skl.skl_new', compact('page_title', 'skl', 'verifikasi', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'total_luas', 'total_volume'));
	}

	//sub fungsi sklupload
	private function uploadFile($file, $filenpwp, $thn, $filename)
	{
		$path = $file->storeAs('uploads/' . $filenpwp . '/' . $thn, $filename, 'public');
		return asset('storage/' . $path);
		// return Storage::disk('public').url($path);
	}

	public function Upload(Request $request, $id)
	{
		try {
			if (Auth::user()->roles[0]->title !== 'Admin') {
				abort(403, 'Unauthorized');
			}

			$this->sklid = $id;
			$skl = Skl::find($this->sklid);
			$pengajuan = AjuVerifSkl::find($skl->pengajuan_id);
			$commitment = PullRiph::find($pengajuan->commitment_id);

			$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
			$no_skl = str_replace(['.', '/', '-'], '', $skl->no_skl);
			$noIjin = str_replace(['.', '/', '-'], '', $skl->no_ijin);
			$thn = $commitment->periodetahun;
			$total_luastanam = $commitment->datarealisasi->sum('luas_lahan');
			$total_volume = $commitment->datarealisasi->sum('volume');

			$completedData = [
				'no_skl' => $skl->no_skl,
				'npwp' => $skl->npwp,
				'no_ijin' => $skl->no_ijin,
				'periodetahun' => $thn,
				'published_date' => $skl->published_date,
				'luas_tanam' => $total_luastanam,
				'volume' => $total_volume,
				'status' => 'Lunas',
			];

			$request->validate([
				'skl_upload' => 'required|file|mimes:pdf|max:2048', // Aturan validasi untuk berkas SKL (PDF)
			]);

			if ($request->hasFile('skl_upload')) {
				$file = $request->file('skl_upload');
				$filename = 'skl_' . $noIjin . '_' . time() . '.' . $file->extension();
				$filePath = $this->uploadFile($file, $filenpwp, $thn, $filename);
				$skl->skl_upload = $filename;
				// $skl->published_date = Carbon::now();
				$commitment->skl = $filename;
				$completedData['skl_upload'] = $filename;
				$completedData['url'] = $filePath;
			}

			// Mencari atau membuat data Completed berdasarkan nomor SKL
			Completed::updateOrCreate(
				['no_skl' => $skl->no_skl],
				$completedData
			);

			// Simpan perubahan pada model-model terkait
			$skl->save();
			$pengajuan->status = 4;
			$pengajuan->save();
			$commitment->save();

			return redirect()->route('skl.recomended.list')
				->with('success', 'Surat Keterangan Lunas (SKL) berhasil diunggah dan Status Komitmen Wajib Tanam-Produksi telah dinyatakan sebagai LUNAS');
		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}
	}

	public function arsip()
	{
		$module_name = 'SKL';
		$page_title = 'Surat Keterangan Lunas';
		$page_heading = 'Daftar SKL Diterbitkan';
		$heading_class = 'fa fa-award';

		$roleaccess = Auth::user()->roleaccess;
		if ($roleaccess == 1) {
			$completeds = Completed::where('luas_tanam', '!=', null)->get();
		}
		if ($roleaccess == 2) {
			$user = Auth::user();
			$npwp = $user->data_user->npwp_company;
			$completeds = Completed::where('npwp', $npwp)
				->where('luas_tanam', '!=', null)
				->get();
		}

		return view('admin.verifikasi.skl.completed', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'completeds'));
	}
}
