<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('login');
	}
	
	public function logincheck()
	{
		if($_POST['username']=='admin' && $_POST['password']=='admin'){
			
			$userdata=array(
				"scope_user_id"=>1,
				"scope_user_name"=>"Admin",
				"scope_logged_in"=>1
				);
			
			$this->session->set_userdata($userdata);
			if($this->session->userdata("scope_user_id"))
				redirect(base_url().'dashboard/');
			else
				redirect(base_url());	
		}
		else{
			redirect(base_url());
		}
	}
	
	public function logout(){

		$this->session->sess_destroy();
		redirect(base_url());
	}


	public function order_session($OrderUID)
	{
			
		$userdata=array(
			"scope_order_id"=>$OrderUID
			);
		
		$this->session->set_userdata($userdata);
		if($this->session->userdata("scope_order_id"))
			redirect(base_url().'order_summary/');
		else
			redirect(base_url());	
		
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
