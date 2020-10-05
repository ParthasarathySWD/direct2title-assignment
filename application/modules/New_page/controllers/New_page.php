<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class New_page extends MY_Controller {

	public function index()
	{
		$data['content'] = 'index';
		$this->load->view('page', $data);
	}
}
