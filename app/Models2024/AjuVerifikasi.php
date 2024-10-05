<?php

namespace App\Models2024;

use App\Models\DataUser;
use App\Models\User;
use App\Models2024\Completed;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AjuVerifikasi extends Model
{
    use HasFactory, SoftDeletes;

	public $table = 't2024_aju_verifikasis';

	protected $fillable = [
		'kind',
		'tcode',
		'npwp',
		'no_ijin',
		'status',
		'note',

		'fileBa', //berita acara hasil pemeriksaan realisasi tanam
		'fileNdhp', //nota dinas hasil pemeriksaan realisasi tanam

		'check_by',
		'verif_at',
		'report_url', //laporan hasil verifikasi
		'metode', //metode verifikasi
	];

	//counter ini untuk di menu
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
		return self::whereBetween('status', [0, 5])
			->whereNotIn('no_ijin', $completedNoIjin)
			->count();
	}

	public static function getNewPengajuan()
	{
		return self::where('status', '0')->get();
	}




	public static function newPengajuanCount(): int
	{
		return self::where('status', '0')->count();
	}

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}

	public function assignments()
	{
		return $this->hasMany(AssignmentVerifikasi::class, 'pengajuan_id');
	}

	public function verifikator()
	{
		return $this->belongsTo(User::class, 'check_by', 'id');
	}
}
