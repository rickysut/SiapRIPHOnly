<?php

namespace App\Models2024;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lokasi extends Model
{
	use \Awobaz\Compoships\Compoships;
	use HasFactory, SoftDeletes;

	protected $table = 't2024_lokasis';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $fillable = [
		'kode_spatial',
		'npwp',
		'no_ijin',
		'poktan_id',
		'anggota_id',
		'ktp_petani',
		'nama_petani',
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
		return $this->belongsTo(MasterAnggota::class, 'ktp_petani', 'ktp_petani');
	}

	public function pullriph()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function pks()
	{
		return $this->belongsTo(Pks::class, ['poktan_id', 'no_ijin'], ['poktan_id', 'no_ijin']);
	}

	public function datarealisasi()
	{
		return $this->hasOne(DataRealisasi::class, 'lokasi_id');
	}

	public function spatial()
	{
		return $this->belongsTo(MasterSpatial::class, 'kode_spatial', 'kode_spatial');
	}
}
