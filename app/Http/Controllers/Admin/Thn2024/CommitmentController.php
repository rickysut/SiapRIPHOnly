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
use App\Models2024\ForeignApi;
use App\Models2024\Lokasi;
use App\Models2024\MasterAnggota;
use App\Models2024\MasterSpatial;
use App\Models2024\Skl;
use App\Models2024\PullRiph;
use App\Models2024\Pks;
use App\Models2024\MasterPoktan;
use App\Models2024\UserFile;
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

		return view('t2024.commitment.index', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
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
		$commitment = PullRiph::select('id', 'npwp', 'no_ijin', 'status', 'periodetahun')
			->where('npwp', $npwp)
			->where('no_ijin', $noIjin)->first();

		$poktans = Pks::where('no_ijin', $noIjin)
			->groupBy('kode_poktan')
			->get();

		$docs = UserFile::where('no_ijin', $commitment->no_ijin)->whereIn(
			'kind',
			[
				'sptjmtanam',
				'sptjmproduksi',
				'spvt',
				'rta',
				'sphtanam',
				'logbook',
				'spvp',
				'rpo',
				'formLa',
				'sphproduksi',
				'spskl'
			]
		)->get();

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

		// return $docs;

		return view('t2024.commitment.realisasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'noIjin', 'penangkars', 'docs', 'npwp', 'varietass', 'disabled', 'poktans', 'ijin'));
	}

	public function findmarker(Request $request)
	{
		$module_name = 'Verifikasi';
		$page_title = 'Simulator Spatial';
		$page_heading = 'Marker Finder Simulator';
		$heading_class = 'fal fa-map-marked-alt';

		$ijins = PullRiph::select('no_ijin')->get();

		$myLocus = [
			[
				'id' => 1,
				'latitude' => -6.286147,
				'longitude' => 106.838966,
				'name' => 'Lokasi Pengujian 1',
			],
			[
				'id' => 2,
				'latitude' => -6.66440,
				'longitude' => 106.863234,
				'name' => 'Lokasi Pengujian 2',
			],
			[
				'id' => 3,
				'latitude' => -7.34105,
				'longitude' => 110.0749047922466,
				'name' => 'Kebun Temanggung',
			],
		];

		$mapkey = ForeignApi::find(1);
		return view('t2024.commitment.simulator', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'mapkey', 'ijins', 'myLocus'));
	}

	public function realisasimobile(Request $request, $noIjin, $spatial)
	{
		$spatial = $spatial;
		$module_name = 'Verifikasi';
		$page_title = 'Simulator Spatial';
		$page_heading = 'Verifikasi Lahan ' . $spatial;
		$heading_class = 'fal fa-map-marker';

		$ijin = $noIjin;

		$noIjin = substr($noIjin, 0, 4) . '/' .
			substr($noIjin, 4, 2) . '.' .
			substr($noIjin, 6, 3) . '/' .
			substr($noIjin, 9, 1) . '/' .
			substr($noIjin, 10, 2) . '/' .
			substr($noIjin, 12, 4);

		// dd($spatial);

		$mapkey = ForeignApi::find(1);
		$npwpCompany = Auth::user()->data_user->npwp_company;
		$npwp = preg_replace('/[^0-9]/', '', $npwpCompany);
		$lokasi = Lokasi::where('no_ijin', $noIjin)->where('kode_spatial', $spatial)->first();
		// dd($spatial);
		$pks = Pks::where('poktan_id', $lokasi->poktan_id)->where('no_ijin', $noIjin)->first();
		$spatial = MasterSpatial::select('id', 'kode_spatial', 'nama_petani', 'latitude', 'longitude', 'polygon', 'altitude', 'luas_lahan', 'kabupaten_id', 'ktp_petani')->where('kode_spatial', $spatial)
			->first();

		$data = [
			'npwpCompany' => $npwpCompany,
			'npwp' => $npwp,
			'noIjin' => $noIjin,
			'lokasi' => $lokasi,
			'pks' => $pks,
			'spatial' => $spatial,
			'lokasi' => $lokasi,
			'pks' => $pks,
			'spatial' => $spatial,
			'anggota' => $spatial->anggota,
			'ijin' => $ijin,
		];

		$mapkey = ForeignApi::find(1);
		return view('t2024.commitment.realisasilokasimobile', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'data', 'mapkey', 'ijin', 'lokasi'));
	}

	public function updatePks(Request $request, $id)
	{
		DB::beginTransaction();

		try {
			$npwp_company = Auth::user()->data_user->npwp_company;
			$pks = Pks::where('id', $id)->first();
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
					'berkas_pks' => 'mimes:pdf|max:2048',
				]);
				$filename = 'pks_' . $noIjin . '_' . time() . '.' . $file->extension();
				$directory = 'uploads/' . $filenpwp . '/' . $commitment->periodetahun . '/' . $noIjin;

				$file->storeAs($directory, $filename, 'public');

				if (Storage::disk('public')->exists($directory . '/' . $filename)) {
					$path = url($directory . '/' . $filename);
					$pks->berkas_pks = $path;
					$userFile = UserFile::updateOrCreate(
						[
							'kind' => 'pks',
							'no_ijin' => $pks->no_ijin,
							'file_code' => $pks->kode_poktan,
						],
						[
							'file_url' => $path,
						]
					);
				} else {
					return redirect()->back()->with('error', "Gagal mengunggah berkas.");
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

	public function storeUserDocs(Request $request, $ijin)
	{
		$noIjin = substr($ijin, 0, 4) . '/' .
			substr($ijin, 4, 2) . '.' .
			substr($ijin, 6, 3) . '/' .
			substr($ijin, 9, 1) . '/' .
			substr($ijin, 10, 2) . '/' .
			substr($ijin, 12, 4);

		$commitment = PullRiph::where('no_ijin', $noIjin)->first();
		$realnpwp = $commitment->npwp;
		$npwp = str_replace(['.', '-'], '', $realnpwp);

		try {
			DB::beginTransaction();

			$fileFields = [
				'sptjmtanam',
				'sptjmproduksi',
				'spvt',
				'rta',
				'sphtanam',
				'spvp',
				'rpo',
				'formLa',
				'sphproduksi',
				'spskl'
			];
			$rules = [];
			$messages = [];
			foreach ($fileFields as $field) {
				$rules[$field] = 'mimetypes:application/pdf|max:2048';

				if ($request->hasFile($field)) {
					$messages[$field . '.mimetypes'] = 'Berkas ' . $request->file($field)->getClientOriginalName() . ' harus memiliki tipe MIME application/pdf.';
				}
			}

			// Lakukan validasi
			$request->validate($rules, $messages);

			foreach ($fileFields as $field) {
				if ($request->hasFile($field)) {
					$file = $request->file($field);

					$fileExtension = $file->extension();
					$file_name = $field . '_' . $ijin . '_' . time() . '.' . $fileExtension;
					// $file_path = $file->storeAs('uploads/' . $npwp . '/' . $commitment->periodetahun, $file_name, 'public');
					$path = 'uploads/' . $npwp . '/' . $commitment->periodetahun . '/' . $ijin;
					$file->storeAs($path, $file_name, 'public');
					$fullPath = url($path . '/' . $file_name);
					// $fullPath = url('storage/' . $file_path);

					$userFiles = [
						'file_url' => $fullPath,
						'file_code' => $file_name
					];

					// Panggil updateOrCreate di dalam loop untuk setiap file yang ditemukan
					UserFile::updateOrCreate(
						[
							'kind' => $field,
							'no_ijin' => $noIjin
						],
						$userFiles // Menggabungkan data form dan file dalam satu array
					);
				}
			}

			DB::commit();
			return redirect()->back()->with('success', 'Berkas berhasil diunggah.');
		} catch (\Exception $e) {
			DB::rollBack();
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
