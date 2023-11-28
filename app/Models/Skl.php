<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Skl extends Model
{
	use HasFactory, SoftDeletes;
	public $table = 'skls';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
		'published_date',
	];

	protected $fillable = [
		'pengajuan_id',
		'no_pengajuan',
		'no_skl',
		'npwp',
		'no_ijin',
		'submit_by',
		'published_date',
		'qrcode',
		'nota_attch',
		'publisher',
		'pejabat_id',
	];

	public function NewRecomendation(): int
	{
		return self::whereNull('approved_by')->count();
	}

	public static function newPengajuanCount(): int
	{
		return self::whereNull('approved_by')->count();
	}

	public function NewRequest(): int
	{
		return self::whereNull('approved_by')->count();
	}

	public static function getNewPengajuan()
	{
		return self::whereNull('approved_by')->get();
	}

	//baru disetujui
	public static function newApprovedCount(): int
	{
		return self::whereNotNull('approved_by')
			->whereNull('published_date')->count();
	}

	public static function getNewApproved()
	{
		return self::whereNotNull('approved_by')
			->whereNull('published_date')->get();
	}

	// Di dalam model Skl.php
	// Di dalam model Skl.php
	public static function getNewSkl()
	{
		$userId = Auth::id();
		$userRole = Auth::user()->roles[0]->title;

		if ($userRole === 'Admin' || $userRole === 'Pejabat') {
			// Jika peran pengguna adalah Admin atau Pejabat, dapatkan semua skl yang belum dilihat
			return Skl::whereNotIn('id', function ($query) use ($userId) {
				$query->select('skl_id')->from('skl_reads')->where('user_id', $userId);
			})->get();
		} elseif ($userRole === 'User') {
			// Jika peran pengguna adalah User, dapatkan semua SKL yang belum dilihat sesuai npwp_company dari DataUser
			$userNpwp = Auth::user()->data_user->npwp_company;
			return Skl::whereNotIn('id', function ($query) use ($userId) {
				$query->select('skl_id')->from('skl_reads')->where('user_id', $userId);
			})->where('npwp', $userNpwp)->get();
		}
	}




	//relationship
	public function pengajuan()
	{
		return $this->belongsTo(AjuVerifSkl::class, 'pengajuan_id', 'id');
	}

	public function reads()
	{
		return $this->hasMany(SklReads::class, 'skl_id', 'id');
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function completed()
	{
		return $this->belongsTo(Completed::class, 'no_skl', 'no_skl');
	}
}
