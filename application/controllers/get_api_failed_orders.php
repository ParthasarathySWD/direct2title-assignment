<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Get_api_failed_orders extends CI_Controller {

	function __construct()
	{ 
		parent::__construct();
		$this->load->config('keywords');
		$this->load->model('Common_model');
	}

	function index(){
		$this->db->select("*");
		$this->db->from('tApiOrders');
		$this->db->where('tApiOrders.Status', 'New');
		$query = $this->db->get();
		$tApiOrders = $query->result();

		if(!empty($tApiOrders)){
			foreach ($tApiOrders as $key => $value) {
				if($value->OrderRequestUID){

					/*$this->load->library('../controllers/api_orders');
					$obj = new $this->api_orders();
					$obj->insert_order_entry($value->OrderRequestUID); */
					
					require_once(APPPATH.'controllers/api_orders.php'); 
					$oHome =  new Api_orders();
					echo $oHome->insert_order_entry();
					exit;


					//$this->api_orders->insert_order_entry($value->OrderRequestUID);
					echo '<pre>';print_r($value->OrderRequestUID);exit;
					//echo modules::run('api_orders/insert_order_entry',$value->OrderRequestUID); 
				}
			}
		}
	}
}
?>
