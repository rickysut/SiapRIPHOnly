<?php

namespace App\Http\Controllers\Admin\Thn2024;

use App\Http\Controllers\Controller;
use App\Models2024\MasterPoktan;
use Illuminate\Http\Request;

class MasterPoktanController extends Controller
{
	protected $module_name;

    public function __construct()
    {
        $this->module_name = 'Master Data Poktan';
    }


	public function index()
	{
		$module_name = $this->module_name;
		$page_title = 'Daftar Kelompok Tani';
		$page_heading = 'Daftar Kelompok Tani';
		$heading_class = 'fal fa-ballot-check';

		return view('t2024.masterpoktan.index', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

    public function create()
    {
        $module_name = $this->module_name;
		$page_title = 'Buat Kelompok Tani';
		$page_heading = 'Kelompok Tani Baru';
		$heading_class = 'fal fa-users';

		return view('t2024.masterpoktan.create', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
    }
    public function store(Request $request)
    {
        $module_name = $this->module_name;
    }

    public function edit($id)
    {
        $module_name = $this->module_name;
		$page_title = 'Profil Kelompok Tani';
		$page_heading = 'Profil Kelompok Tani';
		$heading_class = 'fal fa-users';

		$poktan = MasterPoktan::find($id);

		return view('t2024.masterpoktan.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'poktan'));
    }

    public function update(Request $request, $id)
    {
        $module_name = $this->module_name;
    }
    public function destroy($id)
    {
        $module_name = $this->module_name;
    }

	public function updateIdProvinsi(Request $request)
    {
        $poktans = MasterPoktan::whereNull('id_provinsi')->get();
		// dd($poktans);
        foreach ($poktans as $poktan) {
            $id_provinsi = substr($poktan->id_kabupaten, 0, 2);
            $poktan->update(['id_provinsi' => $id_provinsi]);
        }

        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }
}
