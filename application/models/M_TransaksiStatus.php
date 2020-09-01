<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Illuminate\Database\Eloquent\Model as Eloquent;
class M_TransaksiStatus extends Eloquent
{
	public $timestamps = false;
	protected $table = 'transaksi_status';
	protected $primaryKey = 'id';
	protected $fillable = [
		'transaksi_id',
		'status_id',
		'waktu',
	];

	protected $dates = [
		'waktu',
	];

	public function dataTransaksi()
	{
		return $this->belongsTo(new M_Transaksi(), 'transaksi_id');
	}

	public function dataStatus()
	{
		return $this->belongsTo(new M_Status(), 'status_id');
	}

}