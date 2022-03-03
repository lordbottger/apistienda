<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;
use BinaryCabin\LaravelUUID\Traits\HasUUID;

class Categoria extends Model
{
	use HasUUID;

	protected $table = 'categorias';
	protected $primaryKey ="id";
	public $incrementing = false;
	protected $keyType = 'string';
	protected $uuidFieldName = 'id';
}