<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SklReads extends Model
{
	use HasFactory;

	public $table = 'skl_reads';

	protected $fillable = [
		'skl_id',
		'user_id',
		'read_at',
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function skl()
	{
		return $this->belongsTo(Skl::class, 'skl_id', 'id');
	}

	// Di dalam model SklReads.php
	public static function getNewSklCount(): int
	{
		$roleaccess = Auth::user()->roleaccess;
		if ($roleaccess == 1) {
			$user = Auth::user();
			$unreadSklCount = Skl::whereHas('pengajuan', function ($query) {
				$query->where('status', '4');
			})
				->whereDoesntHave('reads', function ($query) use ($user) {
					$query->where('user_id', $user->id);
				})
				->count();
		}
		if ($roleaccess == 2) {
			$user = Auth::user();
			$unreadSklCount = Skl::where('npwp', $user->data_user->npwp_company)
				->whereHas('pengajuan', function ($query) {
					$query->where('status', '4');
				})
				->whereDoesntHave('reads', function ($query) use ($user) {
					$query->where('user_id', $user->id);
				})
				->count();
		}

		// dd($unreadSklCount);

		return $unreadSklCount;
	}
}
