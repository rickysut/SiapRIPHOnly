<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPoktan;
use App\Models\Pks;
use App\Models\MasterAnggota;
use App\Models\Lokasi;
use App\Models\PullRiph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LokasiController extends Controller
{

	public function index()
	{
		if (Auth::user()->roles[0]->title == 'User') {
			$npwp_company = Auth::user()->data_user->npwp_company;
			$lokasis = Lokasi::where('npwp', $npwp_company)->get();
		} else {
			$lokasis = Lokasi::all();
		}
	}


	public function create()
	{
		//
	}


	public function store(Request $request)
	{
		//
	}


	public function show($lokasiId)
	{
		$module_name = 'Realisasi';
		$page_title = 'Lokasi Tanam';
		$page_heading = 'Realisasi Tanam';
		$heading_class = 'fal fa-farm';

		$npwp_company = Auth::user()->data_user->npwp_company;
		$anggota = Lokasi::find($lokasiId);
		// dd($anggota);
		$commitment = PullRiph::where('no_ijin', $anggota->no_ijin)
			->first();

		$pks = Pks::where('npwp', $npwp_company)
			->where('no_ijin', $anggota->no_ijin)
			->where('poktan_id', $anggota->poktan_id)
			->first();

		if (empty($commitment->status) || $commitment->status == 3 || $commitment->status == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}

		return view('admin.lokasitanam.lokasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'anggota', 'npwp_company', 'commitment', 'pks', 'disabled', 'npwp_company'));
	}

	public function update(Request $request, $anggotaId)
	{
		//
		$npwp_company = Auth::user()->data_user->npwp_company;
		$anggota = Lokasi::where('npwp', $npwp_company)
			->where('anggota_id', $anggotaId) // Use anggota_id instead of id
			->firstOrFail();
		$anggota->nama_lokasi = $request->input('nama_lokasi');
		$anggota->latitude = $request->input('latitude');
		$anggota->longitude = $request->input('longitude');
		$anggota->altitude = $request->input('altitude');
		$anggota->luas_kira = $request->input('luas_kira');
		$anggota->polygon = $request->input('polygon');

		// dd($anggota);
		$anggota->save();
		return redirect()->back()->with('success', 'Data Spasial berhasil diperbarui');
	}

	public function storeTanam(Request $request, $anggotaId)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$filenpwp = str_replace(['.', '-'], '', $npwp_company);
		$anggota = Lokasi::where('npwp', $npwp_company)
			->where('anggota_id', $anggotaId)
			->firstOrFail();

		$commitment = PullRiph::where('no_ijin', $anggota->no_ijin)
			->first();

		$anggota->tgl_tanam = $request->input('tgl_tanam');
		$anggota->luas_tanam = $request->input('luas_tanam');

		$request->validate([
			'tanam_doc' => 'nullable|mimes:pdf',
			'tanam_pict' => 'nullable|image|mimes:jpg,png|max:2048',
		]);

		if ($request->hasFile('tanam_doc')) {
			$file = $request->file('tanam_doc');
			$filename = 'tanam_doc_' . $anggota->anggota_id . '_' . $anggota->poktan_id . '.' . $file->extension();

			// Validasi tipe berkas (PDF)
			$request->validate([
				'tanam_doc' => 'mimes:pdf',
			]);

			$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun, $filename, 'public');
			$anggota->tanam_doc = $filename;
		}

		if ($request->hasFile('tanam_pict')) {
			$file = $request->file('tanam_pict');
			$filename = 'tanam_pict_' . $anggota->anggota_id . '_' . $anggota->poktan_id . '.' . $file->extension();

			// Validasi tipe berkas (JPG/PNG)
			$request->validate([
				'tanam_pict' => 'mimes:jpg,png|max:2048',
			]);

			$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun, $filename, 'public');
			$anggota->tanam_pict = $filename;
		}

		$anggota->save();

		return redirect()->back()->with('success', 'Data Realisasi Tanam berhasil diperbarui');
	}

	public function storeProduksi(Request $request, $anggotaId)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$filenpwp = str_replace(['.', '-'], '', $npwp_company);
		$anggota = Lokasi::where('anggota_id', $anggotaId)
			->firstOrFail();

		$commitment = PullRiph::where('no_ijin', $anggota->no_ijin)
			->first();

		$anggota->volume = $request->input('volume');
		$anggota->tgl_panen = $request->input('tgl_panen');

		$request->validate([
			'panen_doc' => 'nullable|mimes:pdf',
			'panen_pict' => 'nullable|image|mimes:jpg,png|max:2048',
		]);

		if ($request->hasFile('panen_doc')) {
			$file = $request->file('panen_doc');
			$filename = 'panen_doc_' . $anggota->anggota_id . '_' . $anggota->poktan_id . '.' . $file->extension();

			// Validasi tipe berkas (PDF)
			$request->validate([
				'panen_doc' => 'mimes:pdf',
			]);

			$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun, $filename, 'public');
			$anggota->panen_doc = $filename;
		}

		if ($request->hasFile('panen_pict')) {
			$file = $request->file('panen_pict');
			$filename = 'panen_pict_' . $anggota->anggota_id . '_' . $anggota->poktan_id . '.' . $file->extension();

			// Validasi tipe berkas (JPG/PNG)
			$request->validate([
				'panen_pict' => 'mimes:jpg,png|max:2048',
			]);

			$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun, $filename, 'public');
			$anggota->panen_pict = $filename;
		}

		$anggota->save();

		return redirect()->back()->with('success', 'Data Realisasi Produksi berhasil diperbarui');
	}

	public function destroy($id)
	{
		//
	}
}
