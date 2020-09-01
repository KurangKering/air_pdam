<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Illuminate\Support\Carbon;

class TrackTransaksi  {

	public function __construct($params)
	{
		$this->CI =& get_instance();
		$this->CI->load->library('UploadManager');
		$this->client_id = $params['client_id'];
	}

	public function generate()
	{
		$client = $this->getLast();

		$result = array();
		$result['client'] = $this->getClient($client);
		$result['periode_terakhir'] = $this->getPeriode($client);
		$result['periode_terakhir_readable'] = $this->getPeriodeReadable($client);
		$result['current'] = $this->filterCurrent($client);
		$result['next'] = $this->filterNext($client);
		$result['status_transaksi'] = $this->getStatus($client);
		$result['status_readable'] = $this->getStatusReadable($client);
		$result['keterangan_periode'] = $this->getKeterangan($client);
		
		return $result;
	}

	private function filterNext($client)
	{
		$next = null;
		$periode_sekarang = $this->getPeriode($client)->copy();
		$periode_selanjutnya = $periode_sekarang->copy()->modify('last day of next month')->endOfDay();
		$status_sekarang = $this->getStatus($client);
		$now = Carbon::now();


		if ($now > $periode_sekarang) {
			if ($status_sekarang == StatusTransaksi::TRANSAKSI_BERHASIL) {
				if ($this->getKeterangan($client) == KeteranganPeriode::SUCCESS) {
					return $next;
				}
				if ($now > $periode_selanjutnya) {
					$next = array(
						$bulan =  $periode_selanjutnya->format('n'),
						$bulan_huruf =  hBulan($periode_selanjutnya->format('n')),
						$tahun = $periode_selanjutnya->format('Y'),
						$periode =  $periode_selanjutnya,

					);
				}
			} else if ($status_sekarang == NULL) {
				$next = array(
					$bulan =  $periode_sekarang->format('n'),
					$bulan_huruf =  hBulan($periode_sekarang->format('n')),
					$tahun = $periode_sekarang->format('Y'),
					$periode =  $periode_sekarang,
				);
			} else {
				return $next;
			}
		} else {
			return null;
		}

		$next = array(
			'bulan' => $bulan,
			'bulan_huruf'    => $bulan_huruf,
			'tahun' => $tahun,
			'periode' => $periode,
			'periode_readable' => indoDate($periode->format('Y-m-d'), 'F Y'),
		);
		return $next;

	}
	private function filterCurrent($client)
	{
		$current = null;
		if ($client->lastTransaksi == NULL) {
			return $current;
		}


		$status_sekarang = $this->getStatus($client);
		$last = $client->lastTransaksi;
		$last->bulan          = $last->periode->format('n');
		$last->bulan_huruf    = hBulan($last->periode->format('n'));
		$last->tahun          = $last->periode->format('Y');
		$last->status_message = $last->dataStatusTransaksi->status;
		$last->status = $last->status_transaksi_id;
		
		return $last;
		
		
	}


	private function getKeterangan($client) {
		$now = Carbon::now();
		$periode_sekarang = $this->getPeriode($client)->copy();
		$status_sekarang = $this->getStatus($client);
		$periode_selanjutnya = $periode_sekarang->copy()->modify('last day of next month');
		$batas_periode_selanjutnya = $periode_selanjutnya->copy()->modify('last day of next month');
		$keterangan = NULL;

		if ($now > $periode_sekarang && $now < $periode_selanjutnya) {
			if ($status_sekarang == NULL) {
				$keterangan = KeteranganPeriode::BELUM_PERIODE_INI;
			} elseif ($status_sekarang == StatusTransaksi::TRANSAKSI_BERHASIL) {
				$keterangan = KeteranganPeriode::SUCCESS;
			} else {
				$keterangan = KeteranganPeriode::PENDING_PERIODE_INI;
			}
		} elseif ($now > $periode_sekarang && $now > $periode_selanjutnya) {
			if ($status_sekarang == NULL) {
				$keterangan  = KeteranganPeriode::BELUM_PERIODE_SEBELUMNYA;
			}
			elseif ($status_sekarang == StatusTransaksi::TRANSAKSI_BERHASIL) {
				if ($now < $batas_periode_selanjutnya) {
					$keterangan  = KeteranganPeriode::BELUM_PERIODE_INI;
				} else {
					$keterangan  = KeteranganPeriode::BELUM_PERIODE_SEBELUMNYA;
				}
			} else {
				
				$keterangan  = KeteranganPeriode::PENDING_PERIODE_SEBELUMNYA;
			}
		} else {
			$keterangan = KeteranganPeriode::BELUM_SAATNYA;
		}

		return $keterangan;



	}

	private function getClient($client) {

		unset($client->lastTransaksi);
		// $clientt =  $client->toArray();
		// $clientt['periode'] = $client->periode;
		return $client;
	}

	private function getStatusReadable($client) {
		$status = $this->getStatus($client);
		if ($status == NULL) {
			return;
		}
		$status = $this->CI->M_Status->findOrFail($status);
		return $status->status;
	}
	private function getStatus($client) {
		$status = NULL;
		if ($client->lastTransaksi != NULL) {
			$status = $client->lastTransaksi->status_transaksi_id;
		}
		return $status;
	}

	private function getPeriodeReadable($client) {
		$periode = $this->getPeriode($client)->copy();
		$periode = indoDate($periode->format('Y-m-d'), 'F Y');
		return $periode;
	}
	private function getPeriode($client) {
		$periode = $client->periode->copy();
		if ($client->lastTransaksi != NULL) {
			$periode = $client->lastTransaksi->periode->copy();
		}
		return $periode->modify('last day of this month')->endOfDay();
	}

	private function getLast()
	{
		$obj_client = $this->CI->M_Client->with('lastTransaksi.dataStatusTransaksi')->findOrFail($this->client_id);

		return $obj_client;

	}


}

/* End of file TrackTransaksi.php */
/* Location: ./application/libraries/TrackTransaksi.php */