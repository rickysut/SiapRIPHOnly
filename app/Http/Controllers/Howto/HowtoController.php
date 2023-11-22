<?php

namespace App\Http\Controllers\Howto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

	public function administrator()
	{
		$module_name = 'How To Support List';
		$page_title = 'Panduan Simethris';
		$page_heading = 'Panduan Pengguna Administrator';
		$heading_class = 'fal fa-question-circle';
		$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Administrator';

		return view('support.howto.administrator', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

	public function importir()
	{
		$module_name = 'How To Support List';
		$page_title = 'Panduan Simethris';
		$page_heading = 'Panduan Pengguna Pelaku Usaha';
		$heading_class = 'fal fa-question-circle';
		$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Pelaku Usaha';

		return view('support.howto.importir', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

	public function verifikator()
	{
		$module_name = 'How To Support List';
		$page_title = 'Panduan Simethris';
		$page_heading = 'Panduan Pengguna Verifikator';
		$heading_class = 'fal fa-question-circle';
		$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Verifikator';

		return view('support.howto.verifikator', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

	public function pejabat()
	{
		$module_name = 'How To Support List';
		$page_title = 'Panduan Simethris';
		$page_heading = 'Panduan Pengguna Pejabat/Pimpinan';
		$heading_class = 'fal fa-question-circle';
		$page_desc = 'Halaman ini berisi panduan penggunaan bagi pengguna dengan peran sebagai Pejabat/Pimpinan';

		return view('support.howto.pejabat', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}
}
