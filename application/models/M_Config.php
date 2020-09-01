<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Illuminate\Database\Eloquent\Model as Eloquent;
class M_Config extends Eloquent
{
	protected $table = 'config';
	protected $primaryKey = 'id';
	protected $fillable = [
		'persen_pajak',
		'harga_per_watt',
	];

	
}