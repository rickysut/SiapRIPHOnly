<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;
use App\Models2024\MasterAnggota;
use Illuminate\Http\Request;

class MasterCpclController extends Controller
{
    protected $module_name;

    public function __construct()
    {
        $this->module_name = 'Master Data CPCL';
    }

    public function index()
    {
       $module_name = $this->module_name;
		$page_title = 'Daftar Anggota Poktan';
		$page_heading = 'Daftar Anggota Poktan';
		$heading_class = 'fal fa-users';

		return view('t2024.mastercpcl.index', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
    }

    public function create()
    {
		$module_name = $this->module_name;
		$page_title = 'Registrasi Anggota Baru';
		$page_heading = 'Registrasi Anggota Baru';
		$heading_class = 'fal fa-user-plus';

		return view('t2024.mastercpcl.create', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
    }

    public function store(Request $request)
    {
        //
    }

    public function show($nik)
    {
        $module_name = $this->module_name;
		$page_title = 'Profil Anggota';
		$page_heading = 'Profil Anggota';
		$heading_class = 'fal fa-address-card';

		$cpcl = MasterAnggota::where('ktp_petani', $nik)->first();
		// dd($cpcl);

		return view('t2024.mastercpcl.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'cpcl'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($nik)
    {
        $module_name = $this->module_name;
		$page_title = 'Profil CPCL';
		$page_heading = 'Profil CPCL';
		$heading_class = 'fal fa-address-card';

		$cpcl = MasterAnggota::where('ktp_petani', $nik)->first();
		// dd($cpcl);

		return view('t2024.mastercpcl.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'cpcl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
