<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AjuVerifTanam extends Model
{
	use HasFactory;

	public $table = 'avtanams';

	protected $fillable = [
		'npwp',
		'commitment_id',
		'no_ijin',
		'status',
		'note',


		//file upload
		'batanam', //berita acara hasil pemeriksaan realisasi tanam
		'ndhprt', //nota dinas hasil pemeriksaan realisasi tanam

		'check_by',
		'verif_at',
		'metode',
	];

	public static function newPengajuanCount(): int
	{
		return self::where('status', '1')->count();
	}

	public function NewRequest(): int
	{
		return self::where('status', '1')->count();
	}

	public function proceedVerif(): int
	{
		$user = Auth::user(); // Ambil informasi pengguna saat ini

		return self::whereIn('status', ['2', '3'])
			->whereNull('batanam')
			->where('check_by', $user->id)
			->count();
	}


	public static function getNewPengajuan()
	{
		return self::where('status', '1')->get();
	}

	public function pks()
	{
		return $this->hasMany(Pks::class, 'no_ijin', 'no_ijin');
	}

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}
}
