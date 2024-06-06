<?php

namespace App\Models2024;

use App\Models\MasterPoktan;
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

	public $table = 'master_anggotas';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $fillable = [
		'id',
		'npwp',
		'anggota_id',
		'poktan_id',
		'nama_petani',
		'ktp_petani',
		'luas_lahan',
		'periode_tanam'
	];

	public function masterpoktan()
	{
		return $this->belongsTo(MasterPoktan::class, 'poktan_id', 'poktan_id');
	}

	public function lokasi()
	{
		return $this->hasMany(Lokasi::class, 'ktp_petani', 'ktp_petani');
	}

	public function spatial()
	{
		return $this->hasMany(MasterSpatial::class, 'ktp_petani', 'ktp_petani');
	}
}
