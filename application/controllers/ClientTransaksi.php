<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Carbon;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\CodeigniterAdapter;

class ClientTransaksi extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->account = $this->response['account'];
		if ($this->account['role_id'] != 3) {
			redirect('/');
		}

	}
	public function index()
	{
		$client_id = $this->account['id'];

		return view('client_transaksi.index', $this->response);
	}
	public function getData()
	{
		$this->jGetDataTable();
	}
	private function jGetDataTable()
	{
		$dt = new Datatables(new CodeigniterAdapter);
		$dt->query("
			SELECT tr.id, tr.periode, tr.biaya, tr.meteran_awal, tr.meteran_akhir,
			cl.nama_perusahaan,
			st.status,
			st.id as status_id
			FROM transaksi AS tr JOIN status st ON tr.status_transaksi_id = st.id
			JOIN client AS cl ON tr.client_id = cl.id
			WHERE cl.id = {$this->account['id']}
			");
		$dt->edit('periode', function ($data) {
			return indoDate($data['periode'], 'F Y');
		});
		$dt->edit('biaya', function ($data) {
			return hRupiah($data['biaya']);
		});
		$dt->add('action', function ($data) {
			$html = "
			<button type=\"button\" class=\"btn  btn-sm btn-outline-primary\" onClick=\"showDetailTransaksi('{$data['id']}',1)\">
			<i class=\"fas fa-eye\"></i> Detail</button>

			<button type=\"button\" class=\"btn  btn-sm btn-outline-primary\" onClick=\"showDetailTransaksi('{$data['id']}',1)\">
			<i class=\"fas fa-print\"></i> Cetak</button>
			";
			
			return $html;
		});
		echo $dt->generate();
	}

	public function formatLastTransaction($clientId)
	{

		$last_transaction = $this->M_Transaksi->getLast($clientId);

		$output              = array();
		$output['is_ada']    = (bool) $last_transaction;
		$output['client_id'] = $clientId;
		$dataClient          = null;
		$now                 = Carbon::now();
		if ($last_transaction != null) {
			$dataClient = $last_transaction->dataClient;
		} else {
			$dataClient = $this->M_Client->findOrFail($clientId);
		}

		$output['data_perusahaan'] = array(
			'client_id'       => $dataClient->id,
			'nama_perusahaan' => $dataClient->nama_perusahaan,
			'meteran_akhir'   => $dataClient->meteran_akhir,
		);
		if ($last_transaction) {
			$biaya_transaksi            = $last_transaction->biaya;
			$periode_transaksi          = new Carbon($last_transaction->periode);
			$status_transaksi           = $last_transaction->status_transaksi_id;
			$periode_selanjutnya        = $periode_transaksi->copy()->modify('last day of next month');
			$batas_periode_selanjutnya  = $periode_selanjutnya->copy()->modify('last day of next month');
			$output['last_transaction'] = array(
				'id'             => $last_transaction->id,
				'bulan'          => $periode_transaksi->format('n'),
				'bulan_huruf'    => hBulan($periode_transaksi->format('n')),
				'tahun'          => $periode_transaksi->format('Y'),
				'biaya'          => $biaya_transaksi,
				'status'         => $status_transaksi,
				'status_message' => $last_transaction->dataStatusTransaksi->status,
			);
			$output['next_transaction'] = array(
				'bulan'       => $periode_selanjutnya->format('n'),
				'bulan_huruf' => hBulan($periode_selanjutnya->format('n')),
				'tahun'       => $periode_selanjutnya->format('Y'),
			);

			if ($now > $periode_transaksi && $now < $periode_selanjutnya) {
				if ($status_transaksi == StatusTransaksi::TRANSAKSI_BERHASIL) {
					$output['status']   = 'success';
					$output['messsage'] = "Periode " . indoDate($periode_transaksi->format('Y-m-d'), 'F Y') . " telah lunas";
				} else {
					$output['status']   = 'pending_periode_ini';
					$output['messsage'] = 'Anda belum menyelesaikan transaksi untuk periode ini';
				}
			} else if ($now > $periode_transaksi && $now > $periode_selanjutnya) {
				if ($status_transaksi == StatusTransaksi::TRANSAKSI_BERHASIL) {
					if ($now < $batas_periode_selanjutnya) {
						$output['status']  = 'belum_periode_ini';
						$output['message'] = "Anda belum melakukan transaksi untuk periode {$periode_selanjutnya->format('F Y')}";
					} else {
						$output['status']  = 'belum_periode_sebelumnya';
						$output['message'] = "Anda belum melakukan transaksi untuk periode {$periode_selanjutnya->format('F Y')}";
					}
				} else {
					$output['status']  = 'pending_periode_sebelumnya';
					$output['message'] = "Anda belum menyelesaikan transaksi untuk periode {$periode_transaksi->format('F Y')}";
				}
			}
		} else {
			$periode_transaksi          = $dataClient->periode->modify('last day of this month');
			$batas_periode_transaksi    = $periode_transaksi->copy()->modify('last day of next month');
			$output['next_transaction'] = array(
				'bulan'       => $periode_transaksi->format('n'),
				'bulan_huruf' => hBulan($periode_transaksi->format('n')),
				'tahun'       => $periode_transaksi->format('Y'),
			);
			if ($now < $periode_transaksi) {
				$output['status'] = 'belum_saatnya';
			} elseif ($now > $periode_transaksi) {

				if ($now < $batas_periode_transaksi) {
					$output['status']  = 'belum_periode_ini';
					$output['message'] = "Anda belum melakukan transaksi untuk periode {$periode_transaksi->format('F Y')}";
				} else {
					$output['status']  = 'belum_periode_sebelumnya';
					$output['message'] = "Anda belum melakukan transaksi untuk periode {$periode_transaksi->format('F Y')}";
				}

			}

		}
		if (!$last_transaction) {
			$output['periode_']                  = "{$output['next_transaction']['bulan_huruf']} {$output['next_transaction']['tahun']}";
			$output['status_']                   = "-";
			$output['status_transaksi_terakhir'] = null;
		} else {
			$output['periode_']                  = "{$output['last_transaction']['bulan_huruf']} {$output['last_transaction']['tahun']}";
			$output['status_']                   = $last_transaction->dataStatusTransaksi->status;
			$output['status_transaksi_terakhir'] = $output['last_transaction']['status'];

		}
		return $output;

	}
	public function outputTrack($client_id = 1)
	{
		$this->load->library('TrackTransaksi', ['client_id' => $client_id]);
		$track_transaksi = $this->tracktransaksi->generate();
		echo '<pre>';
		die(var_dump($track_transaksi));
	}
	public function jGetFormatLastTransaction()
	{

		$client_id = $this->account['id'];
		$this->load->library('TrackTransaksi', ['client_id' => $client_id]);
		$track_transaksi = $this->tracktransaksi->generate();
		$html            = view('client_transaksi.history-content', $track_transaksi, true);
		$output['html']  = $html;

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($output));
	}

	public function forward()
	{
		$client_id = $this->account['id'];
		$this->load->library('TrackTransaksi', ['client_id' => $client_id]);
		$track_transaksi = $this->tracktransaksi->generate();
		$keterangan_selesai = [KeteranganPeriode::BELUM_PERIODE_INI, KeteranganPeriode::BELUM_PERIODE_SEBELUMNYA];
		$status_tertentu    = [StatusTransaksi::INPUT_DATA, StatusTransaksi::KONFIRMASI_PEMBAYARAN, StatusTransaksi::TRANSAKSI_BERHASIL];
		if (!in_array($track_transaksi['keterangan_periode'], $keterangan_selesai)) {

			if (in_array($track_transaksi['status_transaksi'], $status_tertentu)) {
				redirect('client-transaksi');
			}
		}

		$tahapan             = null;
		$data_transaction    = null;
		$current_transaction = null;
		if ($track_transaksi['current'] == null) {
			$current_transaction = $track_transaksi['next'];
		} else {
			if ($track_transaksi['current']['status'] != StatusTransaksi::TRANSAKSI_BERHASIL) {
				$tahapan             = $track_transaksi['current']['status'];
				$current_transaction = $track_transaksi['current'];
			} else {
				$current_transaction = $track_transaksi['next'];
			}
		}
		$this->response['tahapan'] = $tahapan;
		$this->response['client']  = $track_transaksi['client'];
		$this->response['current'] = $current_transaction;
		return view('client_transaksi.forward', $this->response);
	}

	public function showImage($value='')
	{
		# code...
	}
	public function calculateTahapPembayaran()
	{
		$client_id             = $this->account['id'];
		$data_transaction      = $this->M_Transaksi->getLast($client_id);
		$tanggal_mulai_periode = $data_transaction->periode->copy()->modify('first day of next month');
		$waktu_input           = $data_transaction->waktu_input;
		$waktu_verifikasi      = $data_transaction->waktu_verifikasi;
		$config                = $this->M_Config->first();
		$tanggal_batas_input   = $tanggal_mulai_periode->copy()->addDays($config->batas_input);
		$now                   = Carbon::now();
		$jumlah_hari_bayar     = $now->diffInDays($waktu_verifikasi);
		$jumlah_pemakaian      = $data_transaction->meteran_akhir - $data_transaction->meteran_awal;
		$biaya_bersih          = $jumlah_pemakaian * $config->harga_per_watt;
		$denda_input           = $tanggal_batas_input < $waktu_input ? ($biaya_bersih * $config->denda_input) : 0;
		$kali_denda_bayar      = round($jumlah_hari_bayar / $config->batas_bayar);
		$denda_bayar           = ($config->denda_bayar * $biaya_bersih) * $kali_denda_bayar;
		$biaya_seluruhnya      = $biaya_bersih + $denda_input + $denda_bayar;
		$output                = array(
			'success'                 => 1,
			'client_id'               => $client_id,
			'periode'                 => $tanggal_mulai_periode->copy()->modify('last day of previous month'),
			'waktu_input'             => $waktu_input,
			'waktu_verifikasi'        => $waktu_verifikasi,
			'tanggal_batas_input'     => $tanggal_batas_input,
			'jumlah_hari_bayar'       => $jumlah_hari_bayar,
			'jumlah_pemakaian'        => $jumlah_pemakaian,
			'biaya_bersih'            => $biaya_bersih,
			'denda_input'             => $denda_input,
			'jumlah_kali_denda_bayar' => $kali_denda_bayar,
			'denda_bayar'             => $denda_bayar,
			'total_biaya'             => $biaya_seluruhnya,
			'persen_denda_input'      => $config->denda_input,
			'persen_denda_bayar'      => $config->denda_bayar,
			'harga_per_watt'          => $config->harga_per_watt,
			'hari_batas_input'        => $config->batas_input,
			'hari_batas_bayar'        => $config->batas_bayar,
		);
		return $output;
	}
	public function generateTahapPembayaran()
	{
		$output                        = $this->calculateTahapPembayaran();
		$client                        = $this->M_Client->findOrFail($output['client_id']);
		$output['nama_perusahaan']     = $client->nama_perusahaan;
		$output['denda_input']         = hRupiah($output['denda_input']);
		$output['denda_bayar']         = hRupiah($output['denda_bayar']);
		$output['biaya_bersih']        = hRupiah($output['biaya_bersih']);
		$output['periode']             = indoDate($output['periode'], 'm Y');
		$output['waktu_input']         = indoDate($output['waktu_input']->format('Y-m-d H:i:s'), 'j-m-Y H:i:s');
		$output['waktu_verifikasi']    = indoDate($output['waktu_verifikasi']->format('Y-m-d H:i:s'), 'j-m-Y H:i:s');
		$output['tanggal_batas_input'] = indoDate($output['tanggal_batas_input']->format('Y-m-d H:i:s'), 'j-m-Y H:i:s');
		$output['total_biaya']         = hRupiah($output['total_biaya']);

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($output));
	}
	public function generateTahapInput()
	{
		$client_id          = $this->account['id'];
		$this->load->library('TrackTransaksi', ['client_id' => $client_id]);
		$track_transaksi = $this->tracktransaksi->generate();
		$current_transaction   = $track_transaksi['current'];
		$next_transaction = $track_transaksi['next'];
		$client             = $track_transaksi['client'];
		$detail_transaksi   = null;
		$flag               = null;
		if ($current_transaction != NULL) {
			if ($current_transaction['status'] != StatusTransaksi::TRANSAKSI_BERHASIL) {
				$detail_transaksi = $current_transaction->detailTransaksi()->get();
				
				$flag = 'edit';
			} else {
				$bulan      = $next_transaction['bulan'];
				$tahun      = $next_transaction['tahun'];
				$total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
				for ($i = 1; $i <= $total_days; $i++) {
					$detail_transaksi[$i - 1]['tanggal_dmy'] = "{$i}-{$bulan}-{$tahun}";
					if ($i == 1) {
						$detail_transaksi[$i - 1]['awal'] = $current_transaction->meteran_akhir;
					}
					$detail_transaksi[$i - 1]['akhir']     = "";
					$detail_transaksi[$i - 1]['pemakaian'] = "";
				}
				$flag = 'tambah';
			}
		} else {
			$periode    = $client['periode'];
			$bulan      = $periode->format('n');
			$tahun      = $periode->format('Y');
			$total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
			for ($i = 1; $i <= $total_days; $i++) {
				$detail_transaksi[$i - 1]['tanggal_dmy'] = "{$i}-{$bulan}-{$tahun}";
				if ($i == 1) {
					$detail_transaksi[$i - 1]['awal'] = $client['meteran_akhir'];
				}
				$detail_transaksi[$i - 1]['akhir']     = "";
				$detail_transaksi[$i - 1]['pemakaian'] = "";
			}
			$flag = 'tambah';
		}
		$output = array(
			'detail_transaksi' => $detail_transaksi,
			'flag'             => $flag,
		);
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($output));
	}
	public function actionInputData()
	{
		$post = $this->input->post();
		switch ($post['flag']) {
			case 'tambah':
			$this->actionTambahInputData($post);
			break;
			case 'edit':
			$this->actionEditInputData($post);
			break;
		}
	}
	public function actionPembayaran()
	{
		$auth = $this->auth;
		$this->load->library('form_validation');
		$client_id = $this->account['id'];
		$data_transaction        = $this->M_Transaksi->getLast($client_id);
		$content_pembayaran      = $this->calculateTahapPembayaran();
		$post                    = $this->input->post();
		$post['input-status']    = StatusTransaksi::KONFIRMASI_PEMBAYARAN;
		$post_transaction        = array(
			'harga_per_watt'      => $content_pembayaran['harga_per_watt'],
			// 'biaya'               => $content_pembayaran['total_biaya'],
			'status_transaksi_id' => $post['input-status'],
			'waktu_pembayaran'	  => Carbon::now(),
		);
		$periode_filename = $data_transaction->periode->copy()->format('Y-m');

		$filename = "file-pembayaran-{$client_id}-{$periode_filename}";
		$file_pembayaran = $this->uploadmanager->uploadPembayaran('file_pembayaran',$filename, $data_transaction->file_pembayaran);
		
		if ($file_pembayaran['success'] == 'Y') {
			$post_transaction['file_pembayaran'] = $file_pembayaran['upload_data']['file_name'];
		}
		DB::beginTransaction();
		try {
			$transaksi_id            = DB::table('transaksi')->where('id', $data_transaction->id)->update($post_transaction);
			$post_transaction_status = array(
				'transaksi_id' => $transaksi_id,
				'status_id'    => $post['input-status'],
				'waktu'        => Carbon::now(),
			);
			$insert_transaction_status = DB::table('transaksi_status')->insert($post_transaction_status);
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			echo '<pre>';
			die(var_dump($e->getMessage()));
		}
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($this->response));
	}
	protected function actionTambahInputData($post)
	{
		$auth = $this->auth;
		$this->load->library('form_validation');
		$total_days            = count($post['input-tanggal']);
		$meteran_awal          = $post['input-meter_awal'][0];
		$post['meteran_awal']  = $meteran_awal;
		$meteran_akhir         = $post['input-meter_akhir'][$total_days - 1];
		$post['meteran_akhir'] = $meteran_akhir;
		$client_id             = $this->account['id'];
		$this->load->library('TrackTransaksi', ['client_id' => $client_id]);
		$track_transaksi = $this->tracktransaksi->generate();
		$client = $track_transaksi['client'];
		$current = $track_transaksi['current'];
		$next = $track_transaksi['next'];
		if ($track_transaksi['current'] != NULL) {
			$periode         = $next['periode'];
			$post_month      = $periode->format('n');
			$post_year       = $periode->format('Y');
			$post['periode'] = $periode;
		} else {
			$post_month      = $client->periode->format('n');
			$post_year       = $client->periode->format('Y');
			$post['periode'] = Carbon::createFromFormat('Y-m', "{$post_year}-{$post_month}")->endOfMonth();
		}
		$post_transaction = array(
			'client_id'           => $client_id,
			'periode'             => $post['periode'],
			'meteran_awal'        => $post['meteran_awal'],
			'meteran_akhir'       => $post['meteran_akhir'],
			'status_transaksi_id' => StatusTransaksi::INPUT_DATA,
			'waktu_input'         => Carbon::now(),
		);


		$periode_filename = $post['periode']->copy()->format('Y-m');

		$filename = "file-meteran-{$client_id}-{$periode_filename}";
		$file_meteran = $this->uploadmanager->uploadMeteran('file_meteran',$filename, 'file-meteran-1-2020-07.jpg');
		
		if ($file_meteran['success'] == 'Y') {
			$post_transaction['file_meteran'] = $file_meteran['upload_data']['file_name'];
		}
		DB::beginTransaction();
		try {

			$transaksi_id            = DB::table('transaksi')->insertGetId($post_transaction);

			$post_transaction_status = array(
				'transaksi_id' => $transaksi_id,
				'status_id'    => StatusTransaksi::INPUT_DATA,
				'waktu'        => Carbon::now(),
			);
			$insert_transaction_status = DB::table('transaksi_status')->insert($post_transaction_status);
			$post_detail_transaction   = array();
			foreach ($post['input-tanggal'] as $k => $v) {
				$tanggal                   = $post['input-tanggal'][$k];
				$tanggal                   = Carbon::createFromFormat('d-m-Y', $tanggal);
				$post_detail_transaction[] = array(
					'transaksi_id' => $transaksi_id,
					'tanggal'      => $tanggal->format('Y-m-d'),
					'awal'         => $post['input-meter_awal'][$k],
					'akhir'        => $post['input-meter_akhir'][$k],
					'pemakaian'    => $post['input-pemakaian'][$k],
				);
			}
			DB::table('detail_transaksi')->insert($post_detail_transaction);
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			echo '<pre>';
			die(var_dump($e->getMessage()));
			$this->response['success'] = 0;
		}
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($this->response));
	}
	protected function actionEditInputData($post)
	{

		$auth = $this->auth;
		$this->load->library('form_validation');
		$post                  = $this->input->post();
		$config                = $this->M_Config->first();
		$client_id             = $this->account['id'];
		$data_transaction      = $this->M_Transaksi->getLast($client_id);
		$transaksi_id          = $data_transaction->id;
		$total_days            = count($post['input-tanggal']);
		$meteran_awal          = $post['input-meter_awal'][0];
		$post['meteran_awal']  = $meteran_awal;
		$meteran_akhir         = $post['input-meter_akhir'][$total_days - 1];
		$post['meteran_akhir'] = $meteran_akhir;
		$post['input-status']  = StatusTransaksi::INPUT_DATA;
		$postTransaksi         = array(
			'meteran_awal'        => $post['meteran_awal'],
			'meteran_akhir'       => $post['meteran_akhir'],
			'status_transaksi_id' => $post['input-status'],
		);

		if ($_FILES['file_meteran']['name'] != "") {
			$periode_filename = $data_transaction->periode->copy()->format('Y-m');
			$filename = "file-meteran-{$client_id}-{$periode_filename}";
			$file_meteran = $this->uploadmanager->uploadMeteran('file_meteran',$filename, $data_transaction->file_meteran);

			if ($file_meteran['success'] == 'Y') {
				$postTransaksi['file_meteran'] = $file_meteran['upload_data']['file_name'];
			}

		}

		
		DB::beginTransaction();
		try {
			$post_transaction_status = array(
				'transaksi_id' => $transaksi_id,
				'status_id'    => $post['input-status'],
				'waktu'        => Carbon::now(),
			);
			// $data_transaction_status = $this->M_TransaksiStatus->where('transaksi_id', $transaksi_id)
			// ->orderBy('id', 'desc')->first();
			// if ($data_transaction_status) {
			// 	if ($data_transaction_status->status_id != $post['input-status']) {
			// 		$insert_transaction_status = DB::table('transaksi_status')->insert($post_transaction_status);
			// 	}
			// } else {
			// 	$insert_transaction_status = DB::table('transaksi_status')->insert($post_transaction_status);
			// }
			$update_transaction = DB::table('transaksi')->where('id', $transaksi_id)->update($postTransaksi);
			foreach ($post['input-tanggal'] as $k => $v) {
				$tanggal                 = $post['input-tanggal'][$k];
				$tanggal                 = Carbon::createFromFormat('d-m-Y', $tanggal);
				$id_detail_transaction   = $post['input-detail_transaksi_id'][$k];
				$post_detail_transaction = array(
					'awal'      => $post['input-meter_awal'][$k],
					'akhir'     => $post['input-meter_akhir'][$k],
					'pemakaian' => $post['input-pemakaian'][$k],
				);
				DB::table('detail_transaksi')->where('id', $id_detail_transaction)->update($post_detail_transaction);
			}
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			$this->response['success'] = false;
		}
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($this->response));
	}

	private function upload_image($fileInput, $fieldName = null, $id = null) {
		$config['upload_path'] = './media/';
		$config['allowed_types'] = 'jpg|png|jpeg|gif';
		$config['max_size'] = 0;
		$config['encrypt_name'] = true;
		$this->load->library('upload');
		$this->upload->initialize($config);
		if (!$this->upload->do_upload($fileInput)) {
			$output['status'] = 'error';
			$output['message'] = $this->upload->display_errors();
		} else {
			$file = $this->upload->data();
			// chmood new file
			@chmod(FCPATH.'media/'.$file['file_name'], 0777);
			// resize new image
			$output['status'] = 'success';
			$output['file_name'] = $file['file_name'];
			if ( _hIsNaturalNumber($id) ) {
				$query = $this->M_data_dokumen->where('data_pegawai_id', $id)->first();
				// chmood old file
				@chmod(FCPATH.'media/'.$query[$fieldName], 0777);
				// unlink old file
				@unlink(FCPATH.'media/'.$query[$fieldName]);
			}
		}
		return $output;
	}

	public function getDetailTransaksi()
	{
		$transaksi_id = $this->input->post('transaksi_id');
		$client_id = $this->account['id'];
		$transaksi = $this->M_Transaksi->with('dataClient', 'dataStatusTransaksi', 'detailTransaksi')->where(['id' => $transaksi_id, 'client_id' => $client_id])->first()->toArray();
		$html            = view('client_transaksi.detail', compact('transaksi'), true);
		$output['html']  = $html;

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($output));

		

	}
}
/* End of file Transaksi.php */
/* Location: ./application/controllers/ClientTransaksi.php */
