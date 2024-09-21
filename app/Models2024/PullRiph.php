<?php

namespace App\Models2024;

use App\Models\DataUser;
use App\Models2024\UserFile;
// use App\Models\PenangkarRiph;
use App\Models\User;
use App\Models\UserDocs;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PullRiph extends Model
{
	use HasFactory;
	use SoftDeletes;
	use Auditable;

	public $table = 't2024_pull_riphs';

	protected $fillable = [
		'user_id',
		'npwp',
		'keterangan',
		'nama',
		'no_ijin',
		'periodetahun',
		'tgl_ijin',
		'tgl_akhir',
		'no_hs',
		'volume_riph',
		'volume_produksi',
		'luas_wajib_tanam',
		'stok_mandiri',
		'pupuk_organik',
		'npk',
		'dolomit',
		'za',
		'mulsa',
		'status',
		'formRiph',
		'formSptjm',
		'logBook',
		'formRt',
		'formRta',
		'formRpo',
		'formLa',
		'no_doc',
		'poktan_share',
		'importir_share',
		'status',
		'skl',
		'datariph'
	];

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getFormattedNoIjinAttribute()
    {
        // return str_replace(['/', '.', ' '], '', $this->no_ijin);
    }

	public function user()
	{
		return $this->belongsTo(User::class, 'npwp_company', 'npwp');
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}

	public function userDocs()
	{
		return $this->hasOne(UserDocs::class, 'no_ijin', 'no_ijin');
	}
	public function userfiles()
	{
		return $this->hasMany(UserFile::class, 'no_ijin', 'no_ijin');
	}

	public function pks()
	{
		return $this->hasMany(Pks::class, 'no_ijin', 'no_ijin');
	}

	public function lokasi()
	{
		return $this->hasMany(Lokasi::class, 'no_ijin', 'no_ijin');
	}

	public function ajutanam()
	{
		return $this->hasMany(AjuVerifikasi::class, 'no_ijin', 'no_ijin')->where('kind', 'TANAM');
	}

	public function latestAjutanam()
    {
        return $this->hasOne(AjuVerifikasi::class, 'no_ijin', 'noIjin')->where('kind', 'TANAM')->latest();
    }

	public function ajuproduksi()
	{
		return $this->hasMany(AjuVerifikasi::class, 'no_ijin', 'no_ijin')->where('kind', 'PRODUKSI');
	}

	public function latestAjuproduksi()
    {
        return $this->hasOne(AjuVerifikasi::class, 'no_ijin', 'noIjin')->where('kind', 'PRODUKSI')->latest();
    }

	public function ajuskl()
	{
		return $this->hasMany(AjuVerifSkl::class, 'no_ijin', 'no_ijin');
	}

	public function skl()
	{
		return $this->hasOne(Skl::class, 'no_ijin', 'no_ijin');
	}

	public function completed()
	{
		return $this->hasOne(Completed::class, 'no_ijin', 'no_ijin');
	}
}
