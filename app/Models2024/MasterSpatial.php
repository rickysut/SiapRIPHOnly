<?php

namespace App\Models2024;

use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class MasterSpatial extends Model
{
    use HasFactory, SoftDeletes;

	public $table = 't2024_master_spatials';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public $fillable = [
		'origin',
		'komoditas',
		'kode_spatial',
		'kode_poktan',
		'ktp_petani',
		'nama_petani',
		'latitude',
		'longitude',
		'polygon',
		'altitude',
		'imagery',
		'luas_lahan',
		'catatan',
		'provinsi_id',
		'kabupaten_id',
		'kecamatan_id',
		'kelurahan_id',
		'kml_url',
		'is_active',
		'status',
	];

	// // Accessor untuk mendekripsi ktp_petani
    // public function getKtpPetaniAttribute($value)
    // {
    //     return Crypt::decryptString($value);
    // }

    // // Mutator untuk mengenkripsi ktp_petani
    // public function setKtpPetaniAttribute($value)
    // {
    //     $this->attributes['ktp_petani'] = Crypt::encryptString($value);
    // }

	public function masterpoktan()
	{
		return $this->belongsTo(MasterPoktan::class, 'kode_poktan', 'kode_poktan');
	}

	public function anggota()
	{
		return $this->belongsTo(MasterAnggota::class, 'ktp_petani', 'ktp_petani');
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

	public function lokasi()
	{
		return $this->belongsTo(Lokasi::class, 'kode_spatial', 'kode_spatial');
	}

	public function jadwal()
	{
		return $this->hasMany(MasterJadwalTanam::class, 'kode_spatial', 'kode_spatial');
	}
}
