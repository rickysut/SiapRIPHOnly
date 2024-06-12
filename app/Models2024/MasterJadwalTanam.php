<?php

namespace App\Models2024;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterJadwalTanam extends Model
{
    use HasFactory, SoftDeletes;
	public $table = 't2024_master_jadwal_tanams';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'kode_spatial',
		'awal_masa',
		'akhir_masa',
	];

	public function spatial()
	{
		return $this->belongsTo(MasterSpatial::class, 'kode_spatial', 'kode_spatial');
	}
}
