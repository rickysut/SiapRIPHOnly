<?php

namespace App\Models2024;

use App\Models\FotoProduksi;
use App\Models\FotoTanam;
use App\Models\MasterPoktan;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataRealisasi extends Model
{
	use HasFactory, SoftDeletes, Auditable;

	public $table = 't2024_data_realisasi';

	protected $fillable = [
		'npwp_company',
		'no_ijin',
		'poktan_id', //relasi ke master kelompok
		'pks_id', //relasi ke table pks
		'ktp_petani', //relasi ke table master anggota
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
		return $this->belongsTo(MasterAnggota::class, 'ktp_petani', 'ktp_petani');
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
