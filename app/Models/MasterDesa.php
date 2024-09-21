<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDesa extends Model
{
    use HasFactory;

	public $table = 'data_desas';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'kecamatan_id',
		'kelurahan_id',
		'kode_dagri',
		'nama_desa',
		'lat',
		'lng',
		'polygon',
	];

	public function kecamatan()
	{
		return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id', 'kecamatan_id');
	}
}
