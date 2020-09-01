<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

	public function index()
	{

	}

	public function login_page()
	{	
		$auth = $this->session->userdata('auth');
		if ($auth) {
			redirect('');
		}
		return view('login');
	}

	public function login()
	{
		

		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'required');


		if ($this->form_validation->run() == false) {
			$this->response['messages'] = array(
				'input-username' => form_error('username', '<p class="mt-3 text-danger">', '</p>'),
				'input-password' => form_error('password', '<p class="mt-3 text-danger">', '</p>'),
			);
			$this->response['messages'] = array_filter($this->response['messages']);
			$this->response['success']  = 0;
		} else {
			$post = $this->input->post();

			$is_user_exist = $this->M_Pengguna->where('username', $post['username'])
			->where('password', $post['password'])->first();

			if ($is_user_exist) {


				$post_account = array(
					'account' => array(
						'role_id' => $is_user_exist->role_id,
						'id' => $is_user_exist->foreign_id,
						'nama' => $is_user_exist->nama,
						'role_name' => hRole($is_user_exist->role_id),
					)

				);

				$this->session->set_userdata($post_account);

				$this->response['messages'] = 'Berhasil Login';
				$this->response['success']  = 1;
			} else {
				$this->response['success'] = false;
				$this->response['messages'] = 'Username/Password salah';
			}

		}

		$this->output
		->set_content_type('application/json')
		->set_output(json_encode($this->response));
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('login');
	}
}

/* End of file Auth.php */
/* Location: ./application/controllers/Auth.php */
