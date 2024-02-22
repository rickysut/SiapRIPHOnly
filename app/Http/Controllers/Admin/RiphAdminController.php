<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreRiphAdminRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateRiphAdminRequest;
use App\Models\RiphAdmin;
use App\Http\Controllers\Controller;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class RiphAdminController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// abort_if(Gate::denies('master_riph_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$module_name = 'Master RIPH';
		$page_title = 'Master Data RIPH';
		$page_heading = 'Master RIPH';
		$heading_class = 'fal fa-ballot';
		$riph_admin = RiphAdmin::all();

		return view('admin.riphAdmin.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'riph_admin'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		// abort_if(Gate::denies('master_riph_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$module_name = 'Master RIPH';
		$page_title = '';
		$page_heading = 'Master RIPH';
		$heading_class = 'fal fa-ballot';

		return view('admin.riphAdmin.create', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \App\Http\Requests\StoreRiphAdminRequest  $request
	 * @return \Illuminate\Http\Response
	 */

	public function storefetched(Request $request)
	{
		$periode = $request->input('periode');
		$status = $request->input('status');
		$importir = $request->input('importir');
		$volumeRiph = $request->input('volumeRIPH');

		if ($status == '') {
			return redirect()->route('admin.riphAdmin.index')
				->with('error', 'Tekan tombol cari terlebih dahulu! Anda dapat menyimpannya setelah data muncul pada kolom di bawah ini.');
		}

		if ($status == 'SUCCESS') {
			$v_beban_tanam = $volumeRiph * 0.05 / 6;
			$v_beban_produksi = $volumeRiph * 0.05;

			$datariph = RiphAdmin::updateOrCreate(
				[
					'periode' => $periode,
				],
				[
					'status' => $status,
					'jumlah_importir' => $importir,
					'v_pengajuan_import' => $volumeRiph,
					'v_beban_tanam' => $v_beban_tanam,
					'v_beban_produksi' => $v_beban_produksi,
				]
			);

			return redirect()->route('admin.riphAdmin.index')
				->with('success', 'Data berhasil disimpan.');
		}
	}

	public function store(Request $request)
	{
		$datariph = new RiphAdmin();
		$datariph->periode = $request->input('periode');
		$datariph->jumlah_importir = $request->input('jumlah_importir');
		$datariph->v_pengajuan_import = $request->input('v_pengajuan_import');
		$datariph->v_beban_tanam = $request->input('v_beban_tanam');
		$datariph->v_beban_produksi = $request->input('v_beban_produksi');

		// dd($datariph);
		$datariph->save();
		return redirect()->route('admin.riphAdmin.index')
			->with('success', 'Data berhasil disimpan.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\RiphAdmin  $riphAdmin
	 * @return \Illuminate\Http\Response
	 */
	public function show(RiphAdmin $riphAdmin)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\RiphAdmin  $riphAdmin
	 * @return \Illuminate\Http\Response
	 */
	public function edit(RiphAdmin $riphAdmin)
	{
		// abort_if(Gate::denies('master_riph_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Master RIPH';
		$page_title = 'Ubah Data';
		$page_heading = 'Ubah Data Master RIPH';
		$heading_class = 'fal fa-ballot';

		return view('admin.riphAdmin.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'riphAdmin'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \App\Http\Requests\UpdateRiphAdminRequest  $request
	 * @param  \App\Models\RiphAdmin  $riphAdmin
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		// dd($request->all());

		$riphAdmin = RiphAdmin::updateOrCreate(
			['id' => $id],
			$request->all()
		);


		return redirect()->route('admin.riphAdmin.index')->with('message', 'Berhasil update data riph-admin');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\RiphAdmin  $riphAdmin
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(RiphAdmin $riphAdmin)
	{
		//
	}
}
