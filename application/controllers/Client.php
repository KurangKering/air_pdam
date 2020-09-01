<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Illuminate\Database\Capsule\Manager as DB;
use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\CodeigniterAdapter;
use Illuminate\Support\Carbon;

class Client extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        return view('client.index', $this->response);
    }
    public function getData()
    {
        $this->jGetDataTable();
    }
    private function jGetDataTable()
    {
        $dt = new Datatables(new CodeigniterAdapter);
        $dt->query('SELECT
           client.id,
           client.nama_perusahaan as nama,
           pengguna.email
           FROM client
           JOIN pengguna ON client.id = pengguna.foreign_id
           WHERE pengguna.role_id = 3
           ');
        $dt->add('action', function ($data) {
            $html = '
            <a href="javascript:void(0)" class="btn btn-sm btn-outline-warning" onClick="showModal(' . $data['id'] . ',1)"><i class="fas fa-edit"></i> Ubah</a>
            <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger" onClick="showModal(' . $data['id'] . ',2)"><i class="fas fa-trash"></i> Hapus</a>';
            return $html;
        });
        echo $dt->generate();
    }
    public function insert()
    {
        $this->jInsert();
    }
    private function jInsert()
    {
        $auth = $this->auth;
        $this->load->library('form_validation');
        $post = $this->input->post();
        $this->form_validation->set_rules('input-nama_perusahaan', 'Nama Perusahaan', 'trim|required');
        $this->form_validation->set_rules('input-satuan_kap_mesin_produksi', 'Satuan Mesin Produksi', 'trim|required');
        $this->form_validation->set_rules('input-kap_mesin_produksi', 'Kapasitas Mesin Produksi', 'trim|required');
        $this->form_validation->set_rules('input-kap_prod_produksi', 'Produksi', 'trim|required');
        $this->form_validation->set_rules('input-kap_prod_operasional', 'Operasional', 'trim|required');
        $this->form_validation->set_rules('input-kap_prod_hari_operasional', 'Hari Operasional', 'trim|required');
        $this->form_validation->set_rules('input-kap_prod_jumlah_produksi', 'Jumlah Produksi', 'trim|required');
        $this->form_validation->set_rules('input-water_meter_no_seri', 'No Seri', 'trim|required');
        $this->form_validation->set_rules('input-water_meter_kondisi', 'Kondisi', 'trim|required');
        $this->form_validation->set_rules('input-meteran_akhir', 'Meteran Mulai', 'trim|required');
        $this->form_validation->set_rules('input-periode_bulan', 'Periode Bulan', 'trim|required');
        $this->form_validation->set_rules('input-periode_tahun', 'Periode Tahun', 'trim|required');
        $this->form_validation->set_rules('input-email', 'Email', 'trim|required|is_unique[pengguna.email]');
        $this->form_validation->set_rules('input-username', 'Username', 'trim|required|is_unique[pengguna.username]');
        $this->form_validation->set_rules('input-password', 'Password', 'trim|required');

        if (!$this->form_validation->run()) {
            $this->response['messages'] = array(
                'input-nama_perusahaan'           => form_error('input-nama_perusahaan', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_mesin_produksi'        => form_error('input-kap_mesin_produksi', '<p class="mt-3 text-danger">', '</p>'),
                'input-satuan_kap_mesin_produksi' => form_error('input-satuan_kap_mesin_produksi', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_prod_produksi'         => form_error('input-kap_prod_produksi', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_prod_operasional'      => form_error('input-kap_prod_operasional', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_prod_hari_operasional' => form_error('input-kap_prod_hari_operasional', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_prod_jumlah_produksi'  => form_error('input-kap_prod_jumlah_produksi', '<p class="mt-3 text-danger">', '</p>'),
                'input-water_meter_no_seri'       => form_error('input-water_meter_no_seri', '<p class="mt-3 text-danger">', '</p>'),
                'input-water_meter_kondisi'       => form_error('input-water_meter_kondisi', '<p class="mt-3 text-danger">', '</p>'),
                'input-meteran_akhir'             => form_error('input-meteran_akhir', '<p class="mt-3 text-danger">', '</p>'),
                'input-periode_bulan'             => form_error('input-periode_bulan', '<p class="mt-3 text-danger">', '</p>'),
                'input-periode_tahun'             => form_error('input-periode_tahun', '<p class="mt-3 text-danger">', '</p>'),
                'input-email'                     => form_error('input-email', '<p class="mt-3 text-danger">', '</p>'),
                'input-username'                  => form_error('input-username', '<p class="mt-3 text-danger">', '</p>'),
                'input-password'                  => form_error('input-password', '<p class="mt-3 text-danger">', '</p>'),
            );
            $this->response['messages'] = array_filter($this->response['messages']);
            $this->response['success']  = 0;
        } else {
            $periode_bulan         = $post['input-periode_bulan'];
            $periode_tahun         = $post['input-periode_tahun'];
            $periode               = Carbon::createFromFormat('Y-m', "{$periode_tahun}-{$periode_bulan}")->endOfMonth()->format('Y-m-d');
            $post['input-periode'] = $periode;

            $post_client = array(
                'nama_perusahaan'           => $post['input-nama_perusahaan'],
                'satuan_kap_mesin_produksi' => $post['input-satuan_kap_mesin_produksi'],
                'kap_mesin_produksi'        => $post['input-kap_mesin_produksi'],
                'kap_prod_produksi'         => $post['input-kap_prod_produksi'],
                'kap_prod_operasional'      => $post['input-kap_prod_operasional'],
                'kap_prod_hari_operasional' => $post['input-kap_prod_hari_operasional'],
                'kap_prod_jumlah_produksi'  => $post['input-kap_prod_jumlah_produksi'],
                'water_meter_no_seri'       => $post['input-water_meter_no_seri'],
                'water_meter_kondisi'       => $post['input-water_meter_kondisi'],
                'meteran_akhir'             => $post['input-meteran_akhir'],
                'periode'                   => $post['input-periode'],
            );
            $post_pengguna = array(
                'nama'     => $post_client['nama_perusahaan'],
                'email'    => $post['input-email'],
                'username' => $post['input-username'],
                'password' => $post['input-password'],
                'role_id'  => 3,
            );
            DB::beginTransaction();
            try {
                $client_id                   = DB::table('client')->insertGetId($post_client);
                $post_pengguna['foreign_id'] = $client_id;
                $info_pemilik                = DB::table('pengguna')->insert($post_pengguna);
                DB::commit();

            } catch (Exception $e) {
                DB::rollback();
                $this->response['success'] = 0;
            }
        }
        unset($this->response['account']);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($this->response));
    }
    public function update()
    {
        $this->jUpdate();
    }
    private function jUpdate()
    {
        $this->load->library('form_validation');
        $post = $this->input->post();
        $this->form_validation->set_rules('input-nama_perusahaan', 'Nama Perusahaan', 'trim|required');
        $this->form_validation->set_rules('input-satuan_kap_mesin_produksi', 'Satuan Mesin Produksi', 'trim|required');
        $this->form_validation->set_rules('input-kap_mesin_produksi', 'Kapasitas Mesin Produksi', 'trim|required');
        $this->form_validation->set_rules('input-kap_prod_produksi', 'Produksi', 'trim|required');
        $this->form_validation->set_rules('input-kap_prod_operasional', 'Operasional', 'trim|required');
        $this->form_validation->set_rules('input-kap_prod_hari_operasional', 'Hari Operasional', 'trim|required');
        $this->form_validation->set_rules('input-kap_prod_jumlah_produksi', 'Jumlah Produksi', 'trim|required');
        $this->form_validation->set_rules('input-water_meter_no_seri', 'No Seri', 'trim|required');
        $this->form_validation->set_rules('input-water_meter_kondisi', 'Kondisi', 'trim|required');
        $this->form_validation->set_rules('input-meteran_akhir', 'Meteran Mulai', 'trim|required');
        $this->form_validation->set_rules('input-periode_bulan', 'Periode Bulan', 'trim|required');
        $this->form_validation->set_rules('input-periode_tahun', 'Periode Tahun', 'trim|required');
        if (!$this->form_validation->run()) {
            $this->response['messages'] = array(
                'input-nama_perusahaan'           => form_error('input-nama_perusahaan', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_mesin_produksi'        => form_error('input-kap_mesin_produksi', '<p class="mt-3 text-danger">', '</p>'),
                'input-satuan_kap_mesin_produksi' => form_error('input-satuan_kap_mesin_produksi', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_prod_produksi'         => form_error('input-kap_prod_produksi', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_prod_operasional'      => form_error('input-kap_prod_operasional', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_prod_hari_operasional' => form_error('input-kap_prod_hari_operasional', '<p class="mt-3 text-danger">', '</p>'),
                'input-kap_prod_jumlah_produksi'  => form_error('input-kap_prod_jumlah_produksi', '<p class="mt-3 text-danger">', '</p>'),
                'input-water_meter_no_seri'       => form_error('input-water_meter_no_seri', '<p class="mt-3 text-danger">', '</p>'),
                'input-water_meter_kondisi'       => form_error('input-water_meter_kondisi', '<p class="mt-3 text-danger">', '</p>'),
                'input-meteran_akhir'             => form_error('input-meteran_akhir', '<p class="mt-3 text-danger">', '</p>'),
                'input-periode_bulan'             => form_error('input-periode_bulan', '<p class="mt-3 text-danger">', '</p>'),
                'input-periode_tahun'             => form_error('input-periode_tahun', '<p class="mt-3 text-danger">', '</p>'),
            );
            $this->response['messages'] = array_filter($this->response['messages']);
            $this->response['success']  = 0;
        } else {
            $periode_bulan         = $post['input-periode_bulan'];
            $periode_tahun         = $post['input-periode_tahun'];
            $periode               = Carbon::createFromFormat('Y-m', "{$periode_tahun}-{$periode_bulan}")->endOfMonth()->format('Y-m-d');
            $post['input-periode'] = $periode;


            $post_client = array(
                'nama_perusahaan'           => $post['input-nama_perusahaan'],
                'satuan_kap_mesin_produksi' => $post['input-satuan_kap_mesin_produksi'],
                'kap_mesin_produksi'        => $post['input-kap_mesin_produksi'],
                'kap_prod_produksi'         => $post['input-kap_prod_produksi'],
                'kap_prod_operasional'      => $post['input-kap_prod_operasional'],
                'kap_prod_hari_operasional' => $post['input-kap_prod_hari_operasional'],
                'kap_prod_jumlah_produksi'  => $post['input-kap_prod_jumlah_produksi'],
                'water_meter_no_seri'       => $post['input-water_meter_no_seri'],
                'water_meter_kondisi'       => $post['input-water_meter_kondisi'],
                'meteran_akhir'             => $post['input-meteran_akhir'],
                'periode'                   => $post['input-periode'],
            );
            $data_client   = $this->M_Client->findOrFail($post['input-id']);
            $update_client = $data_client->update($post_client);
        }
        unset($this->response['account']);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($this->response));
    }
    public function delete()
    {
        $id = $this->input->post('id');
        $this->jDelete($id);
    }
    private function jDelete($id)
    {
        DB::beginTransaction();
        try {
            $data_client     = $this->M_Client->findOrFail($id);
            $id              = $data_client->id;
            $delete_pengguna = DB::table('pengguna')->where('role_id', 3)->where('foreign_id', $id)->delete();
            $delete_client   = DB::table('client')->delete($id);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($this->response));
    }
    public function detail()
    {
        $id = $this->input->post('id');
        $this->jGetDetail($id);
    }
    private function jGetDetail($id)
    {
        $data = $this->M_Client->findOrFail($id);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }
}
/* End of file Client.php */
/* Location: ./application/controllers/Client.php */
