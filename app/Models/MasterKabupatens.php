<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKabupaten extends Model
{
	use HasFactory, SoftDeletes;

	public $table = 'kabupatens';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'provinsi_id',
		'kabupaten_id',
		'nama_kab',
		'kd_bast',
		'lat',
		'lng',
	];

	public function provinsi()
	{
		return $this->belongsTo(MasterProvinsi::class, 'provinsi_id', 'provinsi_id');
	}

	public function kecamatan()
	{
		return $this->hasMany(MasterKecamatan::class, 'kabupaten_id', 'kabupaten_id');
	}

	// public function pksmitra()
	// {
	// 	return $this->hasMany(PksMitra::class, 'kd_kab', 'kabupaten_id');
	// }
}
