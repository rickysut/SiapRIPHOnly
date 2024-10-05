<?php

namespace App\Models2024;

use App\Models\DataUser;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Completed extends Model
{
	use HasFactory, SoftDeletes, Auditable;

	public $table = 'completeds';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $fillable = [
		'no_skl',
		'periodetahun',
		'no_ijin',
		'npwp',
		'published_date',
		'luas_tanam',
		'volume',
		'status',
		'skl_upload',
		'url',
	];

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function skl()
	{
		return $this->belongsTo(AjuVerifSkl::class, 'no_skl', 'no_skl');
	}
}
