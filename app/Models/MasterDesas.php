<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterDesa extends Model
{
	use HasFactory, SoftDeletes;

	public $table = 'desas';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'kecamatan_id',
		'kelurahan_id',
		'nama_desa',
		'kd_bast',
		'lat',
		'lng',
	];

	public function kecamatan()
	{
		return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id', 'kecamatan_id');
	}

	// public function pksmitra()
	// {
	// 	return $this->hasMany(PksMitra::class, 'kd_desa', 'kelurahan_id');
	// }
}
