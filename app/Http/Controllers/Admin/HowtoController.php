<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HowtoController extends Controller
{
	public function index()
	{
		$module_name = 'How To Support List';
		$page_title = 'Daftar Halaman HowTo';
		$page_heading = 'Daftar Halaman HowTo';
		$heading_class = 'fal fa-question-circle';
		$page_desc = 'Daftar halaman To';

		return view('support.howto.index', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

	public function show()
	{
		$module_name = 'How To Support List';
		$page_title = 'Panduan Simethris';
		$heading_class = 'fal fa-question-circle';

		if (Auth::user()->roles[0]->title == 'Admin') {
			$page_heading = 'Panduan Pengguna Administrator';
			$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Administrator';
			$asset = asset('uploads/master/howto_admin.pdf');
		}

		if (Auth::user()->roles[0]->title == 'Pejabat') {
			$page_heading = 'Panduan Pengguna Pejabat';
			$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Pejabat';
			$asset = asset('uploads/master/howto_pejabat.pdf');
		}

		if (Auth::user()->roles[0]->title == 'Verifikator' || Auth::user()->roles[0]->title == 'Verifikator') {
			$page_heading = 'Panduan Pengguna Verifikator';
			$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Verifikator';
			$asset = asset('uploads/master/howto_verifikator.pdf');
		}

		if (Auth::user()->roles[0]->title == 'User') {
			$page_heading = 'Panduan Pengguna';
			$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Pelaku Usaha';
			$asset = asset('uploads/master/howto_importir.pdf');
		}

		return view('support.howto.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'asset'));
	}

	public function administrator()
	{
		$module_name = 'How To Support List';
		$page_title = 'Panduan Simethris';
		$page_heading = 'Panduan Pengguna Administrator';
		$heading_class = 'fal fa-question-circle';
		$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Administrator';
		$asset = asset('storage/uploads/master/howto_admin.pdf');

		return view('support.howto.administrator', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'asset'));
	}

	public function importir()
	{
		$module_name = 'How To Support List';
		$page_title = 'Panduan Simethris';
		$page_heading = 'Panduan Pengguna Pelaku Usaha';
		$heading_class = 'fal fa-question-circle';
		$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Pelaku Usaha';
		$asset = asset('storage/uploads/master/howto_importir.pdf');

		return view('support.howto.importir', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'asset'));
	}

	public function verifikator()
	{
		$module_name = 'How To Support List';
		$page_title = 'Panduan Simethris';
		$page_heading = 'Panduan Pengguna Verifikator';
		$heading_class = 'fal fa-question-circle';
		$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Verifikator';
		$asset = asset('storage/uploads/master/howto_verifikator.pdf');

		return view('support.howto.verifikator', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'asset'));
	}

	public function pejabat()
	{
		$module_name = 'How To Support List';
		$page_title = 'Panduan Simethris';
		$page_heading = 'Panduan Pengguna Pejabat/Pimpinan';
		$heading_class = 'fal fa-question-circle';
		$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Pejabat/Pimpinan';
		$asset = asset('storage/uploads/master/howto_pejabat.pdf');

		return view('support.howto.pejabat', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'asset'));
	}
}
