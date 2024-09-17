<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterProvinsi extends Model
{
	use HasFactory, SoftDeletes;

	public $table = 'provinsis';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'provinsi_id',
		'nama',
		'kd_bast',
		'lat',
		'lng',
	];

	public function kabupaten()
	{
		return $this->hasMany(MasterKabupaten::class, 'provinsi_id', 'provinsi_id');
	}

	public function pksmitra()
	{
		return $this->hasMany(PksMitra::class, 'provinsi_id', 'provinsi_id');
	}
}
