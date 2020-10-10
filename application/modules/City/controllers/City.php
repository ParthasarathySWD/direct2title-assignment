<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City extends MY_Controller {

	public function index()
	{
		$data['content'] = 'index';
		$this->load->view('page', $data);
	}
	public function add()
	{
		$data['content'] = 'add';
		$this->load->view('page', $data);
	}
}
