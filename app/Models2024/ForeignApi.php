<?php

namespace App\Models2024;

use \DateTimeInterface;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForeignApi extends Model
{
    use HasFactory, SoftDeletes, Auditable;

	protected $table = 'foreign_api';

	protected $fillable = [
		'provider',
		'key',
		'status',
	];
}
