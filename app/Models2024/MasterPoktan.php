<?php

namespace App\Models2024;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPoktan extends Model
{
	use HasFactory;
	use Auditable;

	public $table = 'master_poktans';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $fillable = [
		'id',
		'npwp',
		'poktan_id',
		'id_provinsi',
		'id_kabupaten',
		'id_kecamatan',
		'id_kelurahan',
		'nama_kelompok',
		'nama_pimpinan',
		'hp_pimpinan',
		'status'
	];

	public function pks()
	{
		return $this->belongsTo(Pks::class, 'poktan_id', 'poktan_id');
	}

	public function anggota()
	{
		return $this->hasMany(MasterAnggota::class, 'poktan_id', 'poktan_id');
	}
}
