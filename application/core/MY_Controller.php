<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends MX_Controller {
	public $parameters = [];
	protected $helpers =  array('url','d2t','tatcalculate');

	function __construct()
	{
		parent::__construct();
		$thisUrl = base_url().$this->uri->uri_string();
		/* State State Management Starts*/
		$this->loggedid 	= $this->session->set_userdata('UserUID', 66);
		$this->RoleUID 		= $this->session->set_userdata('RoleUID', 1);
		$this->UserName 	= $this->session->set_userdata('UserName', "Techinical User");
		$this->RoleType 	= $this->session->set_userdata('RoleType', 1);
		$this->LoginID 		= $this->session->set_userdata('LoginID', "tech1");
		$this->Avatar 		= $this->session->set_userdata('Avatar', "");

		/* State State Management Ends*/

		$logged_in = $this->session->userdata('UserUID');
		$this->output->enable_profiler($this->config->item('profiler'));
		if ($logged_in != TRUE || empty($logged_in)){

			$allowed = array('login');
			if ( ! in_array(strtolower($this->router->fetch_class()), $allowed))
			{
				if ($this->input->is_ajax_request()) {
					?>
					<script>
						window.location.href='<?php echo base_url("Login?url=$thisUrl"); ?>';
					</script>
					<?php
					exit;
				}
				else{
					redirect(base_url("Order_entry?url=$thisUrl"));
				}
			}
		}
		else
		{			
			$this->load->library(array('form_validation'));
			$this->load->helper('form');
			$this->lang->load('keywords');

			$this->load->helper('serverconfig');
			$this->load->helper('customer_pricing');
			$this->load->helper('d2t');
			// $this->load->model('Login/Login_model');
			$this->load->model('Common_model');

			$this->loggedid 	= $this->session->userdata('UserUID');
			$this->RoleUID 		= $this->session->userdata('RoleUID');
			$this->UserName 	= $this->session->userdata('UserName');
			$this->RoleType 	= $this->session->userdata('RoleType');
			$this->LoginID 		= $this->session->userdata('LoginID');
			$this->Avatar 		= $this->session->userdata('Avatar');
			$this->parameters['CustomerUID'] = $this->session->userdata('CustomerUID');
		}
	}

}
