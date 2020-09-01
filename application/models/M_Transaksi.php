<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Capsule\Manager as DB;

class M_Transaksi extends Eloquent
{
	public $timestamps = false;
	protected $table = 'transaksi';
	protected $primaryKey = 'id';
	protected $fillable = [
		'client_id',
		'periode',
		'file_meteran',
		'file_pembayaran',
		'biaya',
		'harga_per_watt',
		'meteran_awal',
		'meteran_akhir',
		'waktu_input',
		'waktu_verifikasi',
		'status_transaksi_id',
		'created_at',
		'updated_at',
	];

	protected $dates = [
		'periode',
		'waktu_verifikasi',
		'waktu_input',
		'waktu_pembayaran',
	];

	protected $appends = [
		'periode_readable',
		'file_meteran_fullpath',
		'file_pembayaran_fullpath',
	];
	
	
	public function dataStatusTransaksi()
	{
		return $this->belongsTo(new M_Status, 'status_transaksi_id');
	}

	public function dataClient()
	{
		return $this->belongsTo(new M_Client(), 'client_id');
		
	}

	public function detailTransaksi()
	{
		return $this->hasMany(new M_DetailTransaksi, 'transaksi_id', 'id');
	}

	public function dataTransaksiStatus()
	{
		return $this->hasMany(new M_TransaksiStatus, 'transaksi_id', 'id');
	}


	public function getLast($client_id) 
	{
		return $this->with('dataStatusTransaksi','dataClient', 'detailTransaksi')->whereHas('dataClient', function($q) use($client_id) {
			$q->where('id', $client_id);
		})->orderBy('periode', 'desc')->first();
	}
	
	public function getPeriodeReadableAttribute()
	{
		return indoDate($this->periode->copy()->format('Y-m-d'), 'F Y');
	}

	public function getFileMeteranFullpathAttribute()
	{
		return UploadManager::getFullPathMeteran($this->file_meteran);
		
	}
	public function getFilePembayaranFullpathAttribute()
	{
		// $last->file_pembayaran_full_path = $this->CI->uploadmanager->getFullPathPembayaran($last->file_pembayaran);

		return UploadManager::getFullPathPembayaran($this->file_pembayaran);
		
	}
}