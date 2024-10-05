<?php

namespace App\Models2024;

use App\Models\DataUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjuVerifSkl extends Model
{
	public $table = 't2024_avskls';

	protected $fillable = [
		'npwp',
		'no_ijin',
		'tcode',
		'status',

		//file upload
		'report_url',
		'baskls', //berita acara hasil pemeriksaan realisasi produksi
		'ndhpskl', //nota dinas hasil pemeriksaan realisasi tanam

		'check_by',
		'verif_at',
		'metode',
		'verif_note',

		'recomend_by',
		'recomend_at',
		'recomend_note',
		'draft_url', //untuk di ttd

		'approved_by',
		'approved_at',
		'no_skl',
		'published_at',
		'skl_url', //skl sudah ttd
	];
	public function NewRequest(): int
	{
		$completedNoIjin = Completed::pluck('no_ijin')->toArray();
		return self::where('status', '0')
			->whereNotIn('no_ijin', $completedNoIjin)
			->count();
	}

	public function inProcess(): int
	{
		$completedNoIjin = Completed::pluck('no_ijin')->toArray();
		return self::whereBetween('status', [0, 3])
			->whereNotIn('no_ijin', $completedNoIjin)
			->count();
	}



	public static function newPengajuanCount(): int
	{
		return self::where('status', '0')->count();
	}
	public static function getNewPengajuan()
	{
		return self::where('status', '0')->get();
	}

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}

	public function verifikator()
	{
		return $this->belongsTo(User::class, 'check_by', 'id');
	}

	public function recomendby()
	{
		return $this->belongsTo(User::class, 'recomend_by', 'id');
	}

	public function direktur()
	{
		return $this->belongsTo(User::class, 'approved_by', 'id');
	}
}
