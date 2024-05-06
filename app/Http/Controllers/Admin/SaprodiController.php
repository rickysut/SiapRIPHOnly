<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Saprodi;
use App\Models\Pks;
use App\Models\PullRiph;
use Illuminate\Support\Facades\Auth;

class SaprodiController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module_name = 'Realisasi';
		$page_title = 'Daftar Saprodi';
		$page_heading = 'Daftar Bantuan Saprodi';
		$heading_class = 'fal fa-gifts';

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$saprodis = Saprodi::where('npwp', $npwpCompany)->get();

		return view('admin.saprodi.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwpCompany', 'saprodis'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $id)
	{
		$request->validate([
			'tanggal_saprodi' => 'required',
			'kategori' => 'required',
			'jenis' => 'required',
			'volume' => 'required',
			'satuan' => 'required',
			'harga' => 'required',
			'file' => 'nullable|file|mimes:pdf|max:2048', // Aturan validasi untuk berkas (PDF)
		]);

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$filenpwp = str_replace(['.', '-'], '', $npwpCompany);
		$pks = Pks::where('npwp', $npwpCompany)->findOrFail($id);
		$commitment = PullRiph::where('no_ijin', $pks->no_ijin)->first();
		$saprodi = new Saprodi();
		$saprodi->pks_id = $pks->id;
		$saprodi->npwp = $pks->npwp;
		$saprodi->no_ijin = $pks->no_ijin;
		$saprodi->tanggal_saprodi = $request->input('tanggal_saprodi');
		$saprodi->kategori = $request->input('kategori');
		$saprodi->jenis = $request->input('jenis');
		$saprodi->volume = $request->input('volume');
		$saprodi->satuan = $request->input('satuan');
		$saprodi->harga = $request->input('harga');

		if ($request->hasFile('file')) {
			// Validasi tipe berkas (PDF)
			$request->validate([
				'file' => 'file|mimes:pdf|max:2048',
			]);

			$file = $request->file('file');
			$filename = 'saprodi_' . time() . '.' . $file->extension();
			$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun . '/pks/saprodi/', $filename, 'public');
			$saprodi->file = $filename;
		}

		$saprodi->save();

		return redirect()->route('admin.task.pks.saprodi', $pks->id)->with('message', "Data berhasil disimpan.");
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($pksId, $id)
	{
		$module_name = 'Realisasi';
		$page_title = 'Data Saprodi';
		$page_heading = 'Ubah Data Saprodi';
		$heading_class = 'fal fa-gifts';

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$pks = Pks::find($pksId);
		$commitment = PullRiph::where('no_ijin', $pks->no_ijin)->first();
		$saprodi = Saprodi::find($id);

		return view('admin.pks.saprodiEdit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwpCompany', 'pks', 'saprodi', 'npwpCompany', 'commitment'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $pksId, $id)
	{
		$npwpCompany = Auth::user()->data_user->npwp_company;
		$filenpwp = str_replace(['.', '-'], '', $npwpCompany);
		$pks = Pks::where('npwp', $npwpCompany)
			->findOrFail($pksId);
		$commitment = PullRiph::where('no_ijin', $pks->no_ijin)->first();
		$saprodi = Saprodi::find($id);
		$saprodi->pks_id = $pks->id;
		$saprodi->npwp = $pks->npwp;
		$saprodi->no_ijin = $pks->no_ijin;
		$saprodi->tanggal_saprodi = $request->input('tanggal_saprodi');
		$saprodi->kategori = $request->input('kategori');
		$saprodi->jenis = $request->input('jenis');
		$saprodi->volume = $request->input('volume');
		$saprodi->satuan = $request->input('satuan');
		$saprodi->harga = $request->input('harga');
		if ($request->hasFile('file')) {
			$attch = $request->file('file');
			$attchname = 'saprodi_' . $saprodi->id . '.' . $attch->extension();
			$attch->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun . '/pks/saprodi/', $attchname, 'public');
			$saprodi->file = $attchname;
		}
		// dd($saprodi);
		$saprodi->save();
		return redirect()->route('admin.task.pks.saprodi', $pks->id)->with('message', "Data berhasil disimpan.");
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$saprodi = Saprodi::find($id);
		$pks = Pks::find($saprodi->pks_id);
		$saprodi->delete();
		return redirect()->route('admin.task.pks.saprodi', $pks->id)->with('message', "Data berhasil dihapus.");
	}
}
