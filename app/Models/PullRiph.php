<?php

namespace App\Models;

use App\Traits\Auditable;
use Dflydev\DotAccessData\Data;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PullRiph extends Model
{
	use HasFactory;
	use SoftDeletes;
	use Auditable;

	public $table = 'pull_riphs';

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

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}

	public function userDocs()
	{
		return $this->hasOne(UserDocs::class, 'commitment_id');
	}

	public function penangkar_riph()
	{
		return $this->hasMany(PenangkarRiph::class, 'no_ijin', 'no_ijin');
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
		return $this->hasOne(AjuVerifTanam::class, 'no_ijin', 'no_ijin');
	}

	public function ajuproduksi()
	{
		return $this->hasOne(AjuVerifProduksi::class, 'no_ijin', 'no_ijin');
	}

	public function ajuskl()
	{
		return $this->hasOne(AjuVerifSkl::class, 'no_ijin', 'no_ijin');
	}

	public function skl()
	{
		return $this->hasOne(Skl::class, 'no_ijin', 'no_ijin');
	}

	public function completed()
	{
		return $this->hasOne(Completed::class, 'no_ijin', 'no_ijin');
	}

	//unused

	public function pengajuan()
	{
		return $this->hasMany(Pengajuan::class, 'no_ijin', 'no_ijin');
	}

	public function commitmentcheck()
	{
		return $this->hasMany(CommitmentCheck::class, 'no_ijin', 'no_ijin');
	}

	public function datarealisasi()
	{
		return $this->hasMany(DataRealisasi::class, 'no_ijin', 'no_ijin');
	}
}
