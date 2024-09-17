<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKecamatan extends Model
{
	use HasFactory, SoftDeletes;

	public $table = 'kecamatans';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'kabupaten_id',
		'kecamatan_id',
		'nama_kecamatan',
		'kd_bast',
		'lat',
		'lng',
	];

	public function kabupaten()
	{
		return $this->belongsTo(MasterKabupaten::class, 'kabupaten_id', 'kabupaten_id');
	}

	public function desa()
	{
		return $this->hasMany(MasterDesa::class, 'kecamatan_id', 'kecamatan_id');
	}
}
