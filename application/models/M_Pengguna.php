<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Illuminate\Database\Eloquent\Model as Eloquent;
class M_Pengguna extends Eloquent
{
	protected $table = 'pengguna';
	protected $primaryKey = 'id';
	public $timestamps = false;
	protected $fillable = [
		'nama',
		'email',
		'username',
		'password',
		'role_id',
		'foreign_id',
	];

	
}