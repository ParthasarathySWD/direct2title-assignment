<?php defined('BASEPATH') OR exit('No direct script access allowed');

class barcode {


	function __construct()

	{

		include_once APPPATH.'libraries/barcode/barcode.php';

		if ($params == NULL)

		{

			$param = '"en-GB-x","A4","","",10,10,10,10,6,3';

		}

		return new barcode_generator();

	}

}









// require_once(dirname(__FILE__) . '/dompdf/dompdf_config.inc.php');
// class Pdf extends DOMPDF
// {
// 	/**
// 	 * Get an instance of CodeIgniter
// 	 *
// 	 * @access	protected
// 	 * @return	void
// 	 */
// 	protected function ci()
// 	{
// 		return get_instance();
// 	}
// 	*
// 	 * Load a CodeIgniter view into domPDF
// 	 *
// 	 * @access	public
// 	 * @param	string	$view The view to load
// 	 * @param	array	$data The view data
// 	 * @return	void
	 
// 	public function load_view($view, $data = array())
// 	{
// 		$html = $this->ci()->load->view($view, $data, TRUE);
// 		$this->load_html($html);
// 	}
// }