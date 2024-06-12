<?php

namespace App\Models2024;

use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;

use App\Models2024\MasterPoktan;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterAnggota extends Model
{
	use HasFactory;
	use Auditable;
	use softDeletes;

	public $table = 't2024_master_anggotas';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $fillable = [
		'id',
		'poktan_id',
		'nama_petani',
		'ktp_petani',
		'hp_petani',
		'alamat_petani',
		'kelurahan_id',
		'kecamatan_id',
		'kabupaten_id',
		'provinsi_id',
	];

	public function masterpoktan()
	{
		return $this->belongsTo(MasterPoktan::class, 'poktan_id');
	}

	public function lokasi()
	{
		return $this->hasMany(Lokasi::class, 'ktp_petani', 'ktp_petani');
	}

	public function spatial()
	{
		return $this->hasMany(MasterSpatial::class, 'ktp_petani', 'ktp_petani');
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
