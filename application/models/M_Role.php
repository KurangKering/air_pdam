<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Illuminate\Database\Eloquent\Model as Eloquent;
class M_Role extends Eloquent
{
	protected $table = 'role';
	protected $primaryKey = 'id';
	protected $fillable = [
		'nama',
	];

	
}