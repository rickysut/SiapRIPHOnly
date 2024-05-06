<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\DataUser;
use App\Models\Skl;
use App\Models\PullRiph;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\Completed;

use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SklController extends Controller
{
	protected $sklid = -1;
	protected $msg = '';

	//digunakan oleh Administrator/Verifikator untuk melihat daftar verifikasi yang siap direkomendasikan terbit skl.
	public function index()
	{
		abort_if(Gate::denies('verification_skl_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$module_name = 'SKL';
		$page_title = 'Daftar Rekomendasi';
		$page_heading = 'Daftar Rekomendasi & SKL';
		$heading_class = 'fa fa-file-signature';

		$recomends = Pengajuan::where('status', '>=', 4)
			->get();

		return view('admin.verifikasi.skl.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
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
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = $user;
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.skl.published', $skl->id)
		];

		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		return view('admin.verifikasi.skl.draftSKL', compact('page_title', 'skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
	}

	//dilakukan oleh verifikator/administrator
	public function recomend(Request $request)
	{
		abort_if(Gate::denies('verification_skl_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$user = Auth::user();
		$recomend = new Skl();
		$recomend->pengajuan_id = $request->input('pengajuan_id');
		$recomend->no_pengajuan = $request->input('no_pengajuan');
		$recomend->no_ijin = $request->input('no_ijin');
		$recomend->npwp = $request->input('npwp');
		$recomend->no_skl = $request->input('no_skl');
		$recomend->submit_by = $user->id;

		$pengajuan = Pengajuan::where('no_pengajuan', $request->input('no_pengajuan'))->first();
		$pengajuan->status = '6';

		$commitment = PullRiph::where('no_ijin', $request->input('no_ijin'))->first();
		$commitment->status = '6';

		// dd($commitment);
		$recomend->save();
		$pengajuan->save();
		$commitment->save();
		return redirect()->route('verification.skl')
			->with('success', 'Komitmen No. RIPH: ' . $commitment->no_ijin . ', berhasil diajukan untuk penerbitan SKL.');
	}

	//untuk pejabat melihat rekomendasi SKL
	public function showrecom($id)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'Rekomendasi Penerbitan';
		$page_heading = 'Rekomendasi SKL Terbit';
		$heading_class = 'fa fa-file-signature';

		$skl = Skl::findOrfail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$importir = DataUser::where('npwp_company', $pengajuan->npwp)->first();
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;

		return view('admin.verifikasi.skl.recomshow', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl', 'pengajuan', 'importir', 'wajib_tanam', 'luas_verif', 'wajib_produksi', 'volume_verif', 'commitment'));
	}

	//oleh pejabat bagian ini ditunda dulu sementara. diganti dengan storerecom di bawahnya
	// public function storerecom($id)
	// {
	// 	if (Auth::user()->roles[0]->title !== 'Pejabat') {
	// 		abort(403, 'Unauthorized');
	// 	}

	// 	$this->sklid = $id;
	// 	// if ($request->hasFile('sklfile')) {
	// 	// 	$file = $request->file('sklfile');
	// 	// 	$filename = 'skl_' . $noIjin . '.' . $file->extension();
	// 	// 	$filePath = $this->uploadFile($file, $filenpwp, $request->input('periodetahun'), $filename);
	// 	// 	$oldskl->sklfile = $filename;
	// 	// 	$completed->url = $filePath;
	// 	// }
	// 	// dd($completed);
	// 	DB::transaction(function () {
	// 		try {
	// 			$skl = Skl::find($this->sklid);
	// 			$skl->approved_by = Auth::user()->id;
	// 			$skl->approved_at = Carbon::now();
	// 			$skl->published_date = Carbon::now();
	// 			// dd($skl);
	// 			$pengajuan = Pengajuan::find($skl->pengajuan_id);
	// 			$commitment = PullRiph::find($pengajuan->commitment_id);
	// 			//$commitment = PullRiph::where('no_ijin', $pengajuan->no_ijin)->first();


	// 			$pengajuan->status = '7';
	// 			$commitment->status = '7';
	// 			$commitment->skl = $skl->no_skl;

	// 			$completed = new Completed();
	// 			$completed->no_skl = $skl->no_skl;
	// 			$completed->npwp = $skl->npwp;
	// 			$completed->no_ijin = $skl->no_ijin;
	// 			$completed->periodetahun = $commitment->periodetahun;
	// 			$completed->published_date = Carbon::now();
	// 			$completed->luas_tanam = $pengajuan->luas_verif;
	// 			$completed->volume = $pengajuan->volume_verif;
	// 			$completed->status = 'Lunas';

	// 			$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
	// 			$noIjin = str_replace(['.', '/'], '', $skl->no_ijin);
	// 			$pejabat = User::find($skl->approved_by);

	// 			$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
	// 			$luas_verif = $pengajuan->luas_verif;
	// 			$wajib_produksi = $commitment->volume_riph * 0.05;
	// 			$volume_verif = $pengajuan->volume_verif;
	// 			$total_luas = $commitment->lokasi->sum('luas_tanam');
	// 			$total_volume = $commitment->lokasi->sum('volume');
	// 			$data = [
	// 				'Perusahaan' => $commitment->datauser->company_name,
	// 				'No. RIPH' => $commitment->no_ijin,
	// 				'Status' => 'LUNAS',
	// 				'Tautan' => route('verification.skl.show', $skl->id),
	// 			];

	// 			// $QrCode = QrCode::size(70)->generate(json_encode($data));
	// 			$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

	// 			// dd($commitment);
	// 			// dompdf disini
	// 			$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
	// 			$no_skl = str_replace(['.', '/', '-'], '', $skl->no_skl);
	// 			$thn = substr($skl->no_ijin, -4);

	// 			$view = view('admin.verifikasi.skl.domskl', compact('skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
	// 			$html = mb_convert_encoding($view, 'HTML-ENTITIES', 'UTF-8');

	// 			// dd($html);
	// 			$pdf = app('dompdf.wrapper');
	// 			$pdf->setPaper('A4', 'portrait');
	// 			$pdf->loadHtml($html);

	// 			$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
	// 			$no_skl = str_replace(['.', '/', '-'], '', $skl->no_skl);
	// 			$thn = substr($skl->no_ijin, -4);
	// 			Storage::disk('public')->put('uploads/' . $filenpwp . '/' . $thn . '/' . $no_skl . '.pdf', $pdf->output());
	// 			$pdfUrl = 'uploads/' . $filenpwp . '/' . $thn . '/' . $no_skl . '.pdf';

	// 			$skl->file_name = $pdfUrl;
	// 			$pdfpublic = Storage::disk('public')->url($pdfUrl);
	// 			$completed->url = $pdfpublic;

	// 			$skl->save();
	// 			$pengajuan->save();
	// 			$commitment->save();
	// 			// $completed->save();

	// 			DB::commit();
	// 		} catch (\Exception $e) {
	// 			// Something went wrong, rollback the transaction
	// 			DB::rollback();
	// 			$this->msg = 'Error! SKL Gagal diterbitkan';
	// 		}
	// 	});
	// 	if ($this->msg === '') {
	// 		return redirect()->route('verification.skl.published', ['id' => $this->sklid]);
	// 	} else {
	// 		return back()->with(['message' => $this->msg]);
	// 	}
	// }



	//fungsi untuk administrator mencetak dokumen skl yang diterbitkan




	//summary single skl
	public function show($id)
	{
		$module_name = 'SKL';
		$page_title = 'Data SKL';
		$page_heading = 'Data SKL Terbit';
		$heading_class = 'fal fa-file-certificate';

		$skl = Skl::find($id);

		return view('admin.verifikasi.skl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl'));
	}

	//fungsi untuk melihat daftar skl yang telah terbit. sesuaikan dengan user role.


	//daftar ini digunakan oleh semua user role. mungkin di hapus
	public function publishes()
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'SKL Terbit';
		$page_heading = 'Daftar SKL Terbit';
		$heading_class = 'fa fa-file-certificate';

		$recomends = Pengajuan::where('status', '7')
			->get();

		return view('admin.verifikasi.skl.publishes', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}
	/**
	 * Menampilkan halaman data SKL
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	// public function published($id)
	// {
	// 	$skl = Skl::findOrfail($id);
	// 	if (Storage::disk('public')->exists($skl->file_name)) {

	// 		return Storage::disk('public')->response($skl->file_name);
	// 	}
	// }
	/**
	 * Menampilkan halaman SKL (print)
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function published_old($id)
	{
		$module_name = 'SKL';
		$page_title = 'Surat Keterangan Lunas';
		$page_heading = 'SKL Diterbitkan';
		$heading_class = 'fa fa-award';

		$skl = Skl::findOrfail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = User::find($skl->approved_by);
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');
		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.skl.show', $skl->id),
		];

		// $QrCode = QrCode::size(70)->generate(json_encode($data));
		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		// dd($commitment);
		// dompdf disini
		$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
		$no_skl = str_replace(['.', '/', '-'], '', $skl->no_skl);
		$thn = substr($skl->no_ijin, -4);


		$view = view('admin.verifikasi.skl.domskl', compact('skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
		$html = mb_convert_encoding($view, 'HTML-ENTITIES', 'UTF-8');


		// dd($html);
		$pdf = app('dompdf.wrapper');
		$pdf->setPaper('A4', 'portrait');
		$pdf->loadHtml($html);
		return $pdf->stream();

		// $pdfUrl = Storage::disk('public')->put('uploads/' . $filenpwp . '/' . $thn . '/' . $no_skl . '.pdf', $pdf->output());

		// $skl->file_name = $pdfUrl;
		// $skl->save();


	}
	public function arsipskl($id)
	{
		$skl = Skl::findOrFail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = User::find($skl->approved_by);
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.skl.published', $skl->id)
		];

		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		return view('admin.verifikasi.skl.sklPdf', compact('skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
	}
	public function arsipskl_baru($id)
	{
		$skl = Skl::findOrFail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = User::find($skl->approved_by);
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.skl.published', $skl->id)
		];

		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		$view = view('admin.verifikasi.skl.sklPdf', compact('skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
		$html = mb_convert_encoding($view, 'HTML-ENTITIES', 'UTF-8');

		$pdf = app('dompdf.wrapper');
		// $options = $pdf->getOptions();
		//  $options->set('orientation', 'landscape');
		$pdf->setPaper('A4', 'landscape');
		$pdf->loadHTML($html);

		return $pdf->download('sklpdf');
	}
	public function create()
	{
		//
	}
	public function store(Request $request)
	{
		//
	}
	public function edit($id)
	{
		//
	}
	public function update(Request $request, $id)
	{
		//
	}
	public function destroy($id)
	{
		//
	}



	//untuk pejabat melihat daftar rekomendasi penerbitan skl.
	public function recomendations()
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'Daftar Rekomendasi';
		$page_heading = 'Daftar Rekomendasi Penerbitan SKL';
		$heading_class = 'fa fa-file-signature';

		$recomends = Pengajuan::where('status', '6')
			->get();
		// dd($recomends);
		return view('admin.verifikasi.skl.recomendations', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}
}
