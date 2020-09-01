<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		
	}
	public function index()
	{

		return view('dashboard', $this->response);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */