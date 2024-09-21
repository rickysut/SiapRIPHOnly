<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProvinsi extends Model
{
    use HasFactory;
	public $table = 'data_provinsis';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'provinsi_id',
		'kode_dagri',
		'nama',
		'lat',
		'lng',
		'polygon',
	];

	public function kabupaten()
	{
		return $this->hasMany(MasterKabupaten::class, 'provinsi_id', 'provinsi_id');
	}
}
