<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Illuminate\Database\Eloquent\Model as Eloquent;
class M_Client extends Eloquent
{
	public $timestamps = false;
	protected $table = 'client';
	protected $primaryKey = 'id';
	protected $fillable = [
		'nama_perusahaan',
		'kap_mesin_produksi',
		'satuan_kap_mesin_produksi',
		'kap_prod_produksi',
		'kap_prod_operasional',
		'kap_prod_hari_operasional',
		'kap_prod_jumlah_produksi',
		'water_meter_no_seri',
		'water_meter_kondisi',
		'meteran_akhir',
		'periode',
		'status',
		'periode_readable',
	];

	protected $dates = [
		'periode',
	];

	protected $appends = [
		'periode_readable',
		'periode_bulan',
		'periode_tahun',
	];


	public function dataTransaksi()
	{
		return $this->hasMany(new M_Transaksi(), 'client_id', 'id');
	}

	public function lastTransaksi()
	{
		return $this->hasOne(new M_Transaksi(), 'client_id', 'id')->orderBy('periode', 'desc');
	}

	public function getPeriodeReadableAttribute()
	{
		return indoDate($this->periode->copy()->format('Y-m-d'), 'F Y');
	}
	public function getPeriodeBulanAttribute()
	{
		return $this->periode->copy()->format('n');
	}
	public function getPeriodeTahunAttribute()
	{
		return $this->periode->copy()->format('Y');
	}
}	