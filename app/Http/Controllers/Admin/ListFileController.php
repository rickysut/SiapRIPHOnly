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
		$file1 = '/uploads/404623290085000/2023/foto_produksi_2252_1709631270_65e6e726151d9.php2';
		$file2 = '/uploads/404623290085000/2023/foto_produksi_2252_1709631279_65e6e72fc1ca4.php2';
		$file3 = '/uploads/404623290085000/2023/foto_tanam_2329_1709632171_65e6eaaba27cb.php1';

		try {
			Storage::disk('public')->delete($file1);
			Storage::disk('public')->delete($file2);
			Storage::disk('public')->delete($file3);
			return back()->with('success', 'file deleted: ' . $e->getMessage());
		} catch (\Exception $e) {
			Log::error('Error deleting file: ' . $e->getMessage());
			return back()->with('error', 'Failed to delete file: ' . $e->getMessage());
		}
		return back();
	}
}
