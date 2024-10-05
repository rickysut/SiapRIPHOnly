<?php

namespace App\Models2024;

use App\Models\DataUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AjuVerifProduksi extends Model
{
	public $table = 't2024_avproduksis';

	protected $fillable = [
		'tcode',
		'npwp',
		'no_ijin',
		'status',
		'note',


		//file upload
		'baproduksi', //berita acara hasil pemeriksaan realisasi produksi
		'ndhprp', //nota dinas hasil pemeriksaan realisasi produksi

		'check_by',
		'verif_at',
		'report_url',
		'metode',
	];

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}

	public static function newPengajuanCount(): int
	{
		$user = Auth::user(); // Ambil informasi pengguna saat ini

		//lokal model (AjuVerifProduksi)
		return self::where('status', '1')
			->where(function ($query) use ($user) {
				$query->where('check_by', $user->id)
					->orWhereNull('check_by');
			})
			->count();
	}

	public function NewRequest(): int
	{
		$user = Auth::user(); // Ambil informasi pengguna saat ini

		//lokal model (AjuVerifProduksi)
		return self::where('status', '1')
			->where(function ($query) use ($user) {
				$query->where('check_by', $user->id)
					->orWhereNull('check_by');
			})
			->count();
	}

	public function proceedVerif(): int
	{
		$user = Auth::user(); // Ambil informasi pengguna saat ini

		return self::whereIn('status', ['2', '3'])
			->whereNull('baproduksi')
			->where('check_by', $user->id)
			->count();
	}

	public static function getNewPengajuan()
	{
		$user = Auth::user(); // Ambil informasi pengguna saat ini
		return self::where('status', '1')
			->where(function ($query) use ($user) {
				$query->where('check_by', $user->id)
					->orWhereNull('check_by');
			})
			->get();
	}

	public function verifikator()
	{
		return $this->belongsTo(User::class, 'check_by', 'id');
	}

	public function assignments()
	{
		return $this->hasMany(AssignmentVerifikasi::class, 'pengajuan_id');
	}
}
