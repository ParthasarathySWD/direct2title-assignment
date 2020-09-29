<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends MY_Controller {

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
	public function contacts()
	{
		$data['content'] = 'contacts';
		$this->load->view('page', $data);
	}
	public function pricing()
	{
		$data['content'] = 'pricing';
		$this->load->view('page', $data);
	}
	public function products()
	{
		$data['content'] = 'products';
		$this->load->view('page', $data);
	}
	public function workflows()
	{
		$data['content'] = 'workflows';
		$this->load->view('page', $data);
	}
	public function task_management()
	{
		$data['content'] = 'task-management';
		$this->load->view('page', $data);
	}
	public function priority_tat()
	{
		$data['content'] = 'priority-tat';
		$this->load->view('page', $data);
	}
	public function pass_through_cost()
	{
		$data['content'] = 'pass-through-cost';
		$this->load->view('page', $data);
	}
	public function billing()
	{
		$data['content'] = 'billing';
		$this->load->view('page', $data);
	}
	public function audit_log()
	{
		$data['content'] = 'audit-log';
		$this->load->view('page', $data);
	}
}
