<?php

namespace App\Models2024;

use App\Models\MasterDesa;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterProvinsi;
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

	public function provinsi()
	{
		return $this->belongsTo(MasterProvinsi::class, 'id_provinsi', 'provinsi_id');
	}
	public function kabupaten()
	{
		return $this->belongsTo(MasterKabupaten::class, 'id_kabupaten', 'kabupaten_id');
	}
	public function kecamatan()
	{
		return $this->belongsTo(MasterKecamatan::class, 'id_kecamatan', 'kecamatan_id');
	}
	public function desa()
	{
		return $this->belongsTo(MasterDesa::class, 'id_kelurahan', 'kelurahan_id');
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
