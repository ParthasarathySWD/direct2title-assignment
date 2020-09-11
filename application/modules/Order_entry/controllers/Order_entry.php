<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_entry extends MY_Controller {

	public function index()
	{
		$data['content'] = 'index';
		$this->load->view('page', $data);
	}
	public function bulk_entry()
	{
		$data['content'] = 'bulkentry';
		$this->load->view('page', $data);
	}
}
