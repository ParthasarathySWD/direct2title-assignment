<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderInfo extends MY_Controller {

	public function index()
	{
		$data['content'] = 'index';
		$this->load->view('page', $data);
	}
}
