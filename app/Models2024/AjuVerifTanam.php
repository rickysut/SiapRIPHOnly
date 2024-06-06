<?php

namespace App\Models2024;

use App\Models\DataUser;
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
		$user = Auth::user(); // Ambil informasi pengguna saat ini
		return self::where('status', '1')
			->where('check_by', $user->id)
			->count();
	}

	public function NewRequest(): int
	{
		$user = Auth::user(); // Ambil informasi pengguna saat ini
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
			->whereNull('batanam')
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

	public function pks()
	{
		return $this->hasMany(Pks::class, 'no_ijin', 'no_ijin');
	}

	public function commitment()
	{
		return $this->hasOne(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}
}
