<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataRealisasi extends Model
{
	use HasFactory, SoftDeletes, Auditable;

	public $table = 'data_realisasi';

	protected $fillable = [
		'npwp_company',
		'no_ijin',
		'poktan_id', //relasi ke master kelompok
		'pks_id', //relasi ke table pks
		'anggota_id', //relasi ke table master anggota
		'lokasi_id', //relasi ke table lokasis

		//spasial
		'nama_lokasi',
		'latitude',
		'longitude',
		'polygon',
		'altitude',
		'luas_kira',

		//tanam
		'mulai_tanam',
		'akhir_tanam',
		'luas_lahan',


		//produksi
		'mulai_panen',
		'akhir_panen',
		'volume',
	];

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function pks()
	{
		return $this->belongsTo(Pks::class, 'pks_id');
	}

	public function masterkelompok()
	{
		return $this->belongsTo(MasterPoktan::class, 'poktan_id');
	}

	public function masteranggota()
	{
		return $this->belongsTo(MasterAnggota::class, 'anggota_id', 'anggota_id');
	}

	public function lokasi()
	{
		return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
	}

	public function fototanam()
	{
		return $this->hasMany(FotoTanam::class, 'realisasi_id');
	}

	public function fotoproduksi()
	{
		return $this->hasMany(FotoProduksi::class, 'realisasi_id');
	}
}
