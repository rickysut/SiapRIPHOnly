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
		// dd($templates);

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
		$request->validate([
			'berkas' => 'required',
			'nama_berkas' => 'required',
			'deskripsi' => 'required',
			'lampiran' => 'required|file|mimes:pdf|max:2048',
		]);

		$template = new FileManagement();
		$template->berkas = $request->input('berkas');
		$template->nama_berkas = $request->input('nama_berkas');
		$template->deskripsi = $request->input('deskripsi');
		$filename = preg_replace('/[^\w\s]/', '_', $template->berkas);

		if ($request->hasFile('lampiran')) {
			// Validasi tipe berkas (PDF)
			$file = $request->file('lampiran');
			$request->validate([
				'lampiran' => 'mimes:pdf', // Hanya izinkan file PDF
			]);

			$filename = 'template_' . $filename . '.' . $file->extension();
			$file->storeAs('uploads/master/', $filename, 'public');
			$template->lampiran = $filename;
		}

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
		$template = FileManagement::find($id);
		$template->delete();
		return redirect()->route('admin.template.index')->with('success', 'tempplate berhasil dihapus.');
	}
}
