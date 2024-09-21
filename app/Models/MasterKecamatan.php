<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKecamatan extends Model
{
    use HasFactory;
	public $table = 'data_kecamatans';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'kabupaten_id',
		'kecamatan_id',
		'kode_dagri',
		'nama_kecamatan',
		'lat',
		'lng',
		'polygon',
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
