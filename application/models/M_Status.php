<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Illuminate\Database\Eloquent\Model as Eloquent;
class M_Status extends Eloquent
{
	// public $timestamps = false;
	protected $table = 'status';
	protected $primaryKey = 'id';
	protected $fillable = [
		'status',
		'singkatan',
	];

	
}