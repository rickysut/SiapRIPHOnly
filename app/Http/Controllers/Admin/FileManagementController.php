<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FileManagement;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileManagementController extends Controller
{
	public function index()
	{
		$module_name = 'File Management';
		$page_title = 'Templates Master';
		$page_heading = 'Templates Master';
		$heading_class = 'fab fa-stack-overflow';

		$templates = FileManagement::all();

		return view('admin.filemanagement.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'templates'));
	}

	public function create()
	{
		$module_name = 'File Management';
		$page_title = 'Templates Master';
		$page_heading = 'New Template';
		$heading_class = 'fal fa-edit';

		return view('admin.filemanagement.create', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

	public function store(Request $request)
	{
		$template = new FileManagement();
		$template->berkas = $request->input('berkas');
		$template->nama_berkas = $request->input('nama_berkas');
		$template->deskripsi = $request->input('deskripsi');
		$filename = preg_replace('/[^\w\s]/', '_', $template->berkas);

		if ($request->hasFile('lampiran')) {
			$file = $request->file('lampiran');
			$filename = 'template_' . $filename . '.' . $file->getClientOriginalExtension();
			$file->storeAs('uploads/master/', $filename, 'public');
			$template->lampiran = $filename;
		}
		// dd($filename);
		$template->save();
		return redirect()->route('admin.template.index')->with('success', 'Template berhasil diunggah.');
	}

	public function download($id)
	{
		$file = FileManagement::find($id);

		if (!$file) {
			return redirect()->back()->with('error', 'Berkas tidak ditemukan');
		}

		$filename = $file->lampiran;
		$path = 'uploads/master/' . $filename;

		$url = Storage::url($path);
		return Response::download(public_path($url), $filename);
	}

	public function destroy($id)
	{
		$template = FileManagement::findOrFail($id);
		$template->delete();
		return redirect()->route('admin.template.index')->with('success', 'tempplate berhasil dihapus.');
	}
}
