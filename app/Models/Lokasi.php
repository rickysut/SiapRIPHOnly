<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lokasi extends Model
{
	use HasFactory, SoftDeletes;

	protected $table = 'lokasis';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $fillable = [
		'npwp',
		'no_ijin',
		'poktan_id',
		'anggota_id',
		'nama_lokasi',
		'luas_lahan',
		'periode_tanam',
		'latitude',
		'longitude',
		'altitude',
		'polygon',
		'luas_kira',
		'tgl_tanam',
		'luas_tanam',
		'tanam_doc',
		'tanam_pict',
		'tgl_panen',
		'volume',
		'panen_doc',
		'panen_pict',
		'status',
		'varietas', //unused
	];

	public function masteranggota()
	{
		return $this->belongsTo(MasterAnggota::class, 'anggota_id', 'anggota_id');
	}

	public function masterkelompok()
	{
		return $this->belongsTo(MasterPoktan::class, 'poktan_id');
	}

	public function pullriph()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function pks()
	{
		return $this->belongsTo(Pks::class, 'poktan_id', 'poktan_id');
	}

	public function datarealisasi()
	{
		return $this->hasMany(DataRealisasi::class, 'lokasi_id');
	}
}
