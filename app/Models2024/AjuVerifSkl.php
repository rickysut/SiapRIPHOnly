<?php

namespace App\Models2024;

use App\Models\DataUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjuVerifSkl extends Model
{
	public $table = 'avskls';

	protected $fillable = [
		'npwp',
		'commitment_id',
		'no_ijin',
		'no_pengajuan',
		'status',
		'note',


		//file upload
		'baskls', //berita acara hasil pemeriksaan realisasi produksi
		'ndhpskl', //nota dinas hasil pemeriksaan realisasi tanam

		'check_by',
		'verif_at',
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
		return self::where('status', '1')->count();
	}

	public function proceedVerif(): int
	{
		return self::whereIn('status', ['2', '3'])->count();
	}

	public function NewRequest(): int
	{
		return self::where('status', '1')->count();
	}

	public static function getNewPengajuan()
	{
		return self::where('status', '1')->get();
	}

	public function skl()
	{
		return $this->hasOne(Skl::class, 'pengajuan_id', 'id');
	}
}
