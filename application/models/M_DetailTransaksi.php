<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Illuminate\Database\Eloquent\Model as Eloquent;
class M_DetailTransaksi extends Eloquent
{
	public $timestamps = false;
	protected $table = 'detail_transaksi';
	protected $primaryKey = 'id';
	protected $fillable = [
		'id',
		'transaksi_id',
		'tanggal',
		'awal',
		'akhir',
		'pemakaian',
	];

	protected $dates = [
		'tanggal',
	];

	protected $appends = [
		'tanggal_dmy',
	];

	public function getTanggalDmyAttribute()
	{
		return $this->tanggal->copy()->format('d-m-Y');
	}
	
}