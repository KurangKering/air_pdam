<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Illuminate\Support\Carbon;

class ClientProfile extends MY_Controller
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
		$client    = $this->M_Client->findOrFail($client_id);
		$pengguna  = $this->M_Pengguna->where(['role_id' => '3', 'foreign_id' => $client_id])->first();

		$this->response['client']   = $client;
		$this->response['pengguna'] = $pengguna;
		return view('client_profile.index', $this->response);
	}

	public function updateDataPerusahaan()
	{
		$post = $this->input->post();
		$periode_bulan = $post['input-periode_bulan'];
		$periode_tahun = $post['input-periode_tahun'];
		$periode               = Carbon::createFromFormat('Y-m', "{$periode_tahun}-{$periode_bulan}")->endOfMonth()->format('Y-m-d');
		$post['input-periode'] = $periode;
		$form_data             = [
			'nama_perusahaan'           => $post['input-nama_perusahaan'],
			'periode'                   => $post['input-periode'],
			'meteran_akhir'             => $post['input-meteran_akhir'],
			'kap_mesin_produksi'        => $post['input-kap_mesin_produksi'],
			'satuan_kap_mesin_produksi' => $post['input-satuan_kap_mesin_produksi'],
			'kap_prod_produksi'         => $post['input-kap_prod_produksi'],
			'kap_prod_operasional'      => $post['input-kap_prod_operasional'],
			'kap_prod_hari_operasional' => $post['input-kap_prod_hari_operasional'],
			'kap_prod_jumlah_produksi'  => $post['input-kap_prod_jumlah_produksi'],
			'water_meter_no_seri'       => $post['input-water_meter_no_seri'],
			'water_meter_kondisi'       => $post['input-water_meter_kondisi'],
		];

		$client_id = $this->account['id'];
		$client = $this->M_Client->findOrFail($client_id);
		$is_success =  $client->update($form_data);
		
		if (!$is_success){
			$this->response['success'] = false;
		}

		unset($this->response['account']);
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($this->response));
	}

	public function updateDataPengguna()
	{
		$post = $this->input->post();
		$form_data             = [
			'nama'           => $post['input-nama_pengguna'],
			'email'                   => $post['input-email'],
			'username'             => $post['input-username'],
		];

		if (!empty($post['input-password'])) {
			$form_data['password'] = $post['input-password'];
		}
		$client_id = $this->account['id'];
		$pengguna = $this->M_Pengguna->where(['role_id' => '3', 'foreign_id' => $client_id])->first();

		$is_success = false;
		try {
			$is_success =  $pengguna->update($form_data);
		} catch (Exception $e) {
			
		}
		
		if (!$is_success){
			$this->response['success'] = false;
		}

		unset($this->response['account']);
		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($this->response));
	}

}
/* End of file Transaksi.php */
/* Location: ./application/controllers/ClientTransaksi.php */
