<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public $auth = array();
	public $response = array();
	public function __construct()
	{
		parent::__construct();
		$account = $this->session->userdata('account');

		if ($account == NULL) {
			redirect('login');
			return;
		} 

		$this->response['account'] = $account;
		$this->response['success'] = true;
		$this->response['messages'] = array();

	}

}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */