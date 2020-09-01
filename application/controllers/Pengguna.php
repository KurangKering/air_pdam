<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Ozdemir\Datatables\Datatables;
use Ozdemir\Datatables\DB\CodeigniterAdapter;

class Pengguna extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $this->response['data']['roles'] = $this->M_Role->get();

        return view('pengguna.index', $this->response);
    }

    public function getData()
    {
        $this->jGetDataTable();
    }
    private function jGetDataTable()
    {
        $dt = new Datatables(new CodeigniterAdapter);
        $dt->query('SELECT
           pengguna.id,
           pengguna.email,
           pengguna.username,
           pengguna.nama,
           pengguna.role_id,
           role.nama as role_nama
           FROM pengguna
           JOIN role ON pengguna.role_id = role.id

           ');

        $dt->add('action', function ($data) {
            $isBoleh = !in_array($data['role_id'], [1,3]);
            $html = '   
            <a href="javascript:void(0)" class="btn btn-sm btn-outline-warning" onClick="showModal(' . $data['id'] . ',1)"><i class="fas fa-edit"></i> Ubah</a>';
            $html .= "
            <button type=\"button\" class=\"btn  btn-sm btn-outline-danger\"";  
            if (!$isBoleh) {
                $html .= "disabled";
            }
            $html .= " onClick=\"showModal('{$data['id']}',2)\">
            <i class=\"fas fa-trash\"></i> Hapus</button>
            ";

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
        $this->form_validation->set_rules('role_id', 'Role', 'trim|required');
        $this->form_validation->set_rules('nama', 'Nama Pengguna', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|is_unique[pengguna.email]');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[pengguna.username]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if (!$this->form_validation->run()) {
            $this->response['messages'] = array(
                'input-role_id'     => form_error('role_id', '<p class="mt-3 text-danger">', '</p>'),
                'input-nama'     => form_error('nama', '<p class="mt-3 text-danger">', '</p>'),
                'input-email'     => form_error('email', '<p class="mt-3 text-danger">', '</p>'),
                'input-username' => form_error('username', '<p class="mt-3 text-danger">', '</p>'),
                'input-password' => form_error('password', '<p class="mt-3 text-danger">', '</p>'),
            );
            $this->response['messages'] = array_filter($this->response['messages']);
            $this->response['success']  = 0;
        } else {

            $post_pengguna = array(
                'role_id'     => $post['role_id'],
                'nama'     => $post['nama'],
                'email'     => $post['email'],
                'username' => $post['username'],
                'password' => $post['password'],
            );

            $insert_data = $this->M_Pengguna->insert($post_pengguna);
        }

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
        $auth = $this->auth;
        $this->form_validation->set_rules('email', 'Email', 'trim|required');
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('nama', 'Nama Pengguna', 'trim|required');

        if (!$this->form_validation->run()) {
            $this->response['messages'] = array(
                'input-email'     => form_error('email', '<p class="mt-3 text-danger">', '</p>'),
                'input-nama'     => form_error('nama', '<p class="mt-3 text-danger">', '</p>'),
                'input-username' => form_error('username', '<p class="mt-3 text-danger">', '</p>'),
            );
            $this->response['messages'] = array_filter($this->response['messages']);
            $this->response['success']  = 0;

        } else {
            $post_pengguna = array(
                'nama'     => $post['nama'],
                'email' => $post['email'],
                'username' => $post['email'],

            );
            if ($post['role_id'] != "null" && $post['role_id'] != null) {
                $post_pengguna['role_id'] = $post['role_id'];
            }

            if ($post['password']) {
                $post_pengguna['password'] = ($post['password']);
            }

            $data       = $this->M_Pengguna->findOrFail($post['id']);
            $data_update = $data->update($post_pengguna);
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
        $data        = $this->M_Pengguna->findOrFail($id);
        $delete_data = $data->delete();

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
        $data = $this->M_Pengguna->findOrFail($id);
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($data));
    }

}

/* End of file Pengguna.php */
/* Location: ./application/controllers/Pengguna.php */
