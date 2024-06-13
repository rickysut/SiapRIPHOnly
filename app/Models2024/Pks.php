<?php

namespace App\Models2024;

use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;
use App\Models\Varietas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Pks extends Model
{
	use \Awobaz\Compoships\Compoships;
	use HasFactory;
	use SoftDeletes;

	public $table = 't2024_pks';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $fillable = [
		'npwp',
		'no_ijin',
		'poktan_id',
		'nama_poktan',
		'no_perjanjian',
		'tgl_perjanjian_start',
		'tgl_perjanjian_end',
		'jumlah_anggota',
		'luas_rencana',
		'varietas_tanam',
		'periode_tanam',
		'provinsi_id',
		'kabupaten_id',
		'kecamatan_id',
		'kelurahan_id',
		'status',
		'berkas_pks',
	];

	public function lokasi()
	{
		return $this->hasMany(Lokasi::class, ['poktan_id', 'no_ijin'], ['poktan_id', 'no_ijin']);
	}

	public function masterpoktan()
	{
		return $this->belongsTo(MasterPoktan::class, 'poktan_id');
	}

	public function anggota()
	{
		return $this->hasMany(MasterAnggota::class, 'poktan_id', 'poktan_id');
	}

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function varietas()
	{
		return $this->belongsTo(Varietas::class, 'varietas_tanam');
	}

	public function provinsi()
	{
		return $this->belongsTo(MasterProvinsi::class, 'provinsi_id', 'provinsi_id');
	}
	public function kabupaten()
	{
		return $this->belongsTo(MasterKabupaten::class, 'kabupaten_id', 'kabupaten_id');
	}
	public function kecamatan()
	{
		return $this->belongsTo(MasterKecamatan::class, 'kecamatan_id', 'kecamatan_id');
	}
	public function desa()
	{
		return $this->belongsTo(MasterDesa::class, 'kelurahan_id', 'kelurahan_id');
	}
}
