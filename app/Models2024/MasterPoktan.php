<?php

namespace App\Models2024;

use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPoktan extends Model
{
	use HasFactory, SoftDeletes, Auditable;

	public $table = 't2024_master_poktans';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $fillable = [
		'id',
		'kode_poktan',
		'alamat',
		'provinsi_id',
		'kabupaten_id',
		'kecamatan_id',
		'kelurahan_id',
		'nama_kelompok',
		'nama_pimpinan',
		'hp_pimpinan',
		'status'
	];

	public function pks()
	{
		return $this->hasMany(Pks::class, 'kode_poktan', 'kode_poktan');
	}

	public function anggota()
	{
		return $this->hasMany(MasterAnggota::class, 'kode_poktan', 'kode_poktan');
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

	public function getProvinsiFromKabupatenAttribute()
    {
        if (!$this->id_provinsi && $this->id_kabupaten) {
            $provinsiId = substr($this->id_kabupaten, 0, 2);
            return MasterProvinsi::where('provinsi_id', $provinsiId)->first();
        }
        return null;
    }
}
