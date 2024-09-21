<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKabupaten extends Model
{
    use HasFactory;
	public $table = 'data_kabupatens';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'provinsi_id',
		'kabupaten_id',
		'kode_dagri',
		'nama_kab',
		'lat',
		'lng',
		'polygon',
	];

	public function provinsi()
	{
		return $this->belongsTo(MasterProvinsi::class, 'provinsi_id', 'provinsi_id');
	}

	public function kecamatan()
	{
		return $this->hasMany(MasterKecamatan::class, 'kabupaten_id', 'kabupaten_id');
	}
}
