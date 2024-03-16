<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ListFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allFile = Storage::disk('local')->allFiles('public');
		// dd($files);

		$files = preg_grep('/\.php\d*$/', $allFile);

        return view('admin.filemanagement.index', compact('files'));
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
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
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
    public function destroy()
	{
		$filePath = '/storage/uploads/404623290085000/2023/foto_produksi_2252_1709631270_65e6e726151d9.php2';

		try {
			// Hapus file dari penyimpanan
			Storage::delete($filePath);
		} catch (\Exception $e) {
			// Tangkap dan log pesan kesalahan
			Log::error('Error deleting file: ' . $e->getMessage());
			// Kembalikan ke halaman sebelumnya dengan pesan kesalahan
			return back()->with('error', 'Failed to delete file: ' . $e->getMessage());
		}

		// Redirect kembali ke halaman sebelumnya jika penghapusan berhasil
		return back();
	}
}
