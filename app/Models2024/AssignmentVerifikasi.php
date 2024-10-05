<?php

namespace App\Models2024;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignmentVerifikasi extends Model
{
    use HasFactory, SoftDeletes, Auditable;

	public $table = 't2024_assignment_verifikasis';

	protected $fillable = [
		'tcode',
		'pengajuan_id',
		'kode_pengajuan',
		'no_ijin',
		'user_id',
		'no_sk',
		'tgl_sk',
		'file',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function pengajuan()
	{
		return $this->belongsTo(AjuVerifikasi::class, 'pengajuan_id');
	}
}
