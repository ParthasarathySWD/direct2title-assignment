<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
class Order_attachment extends MY_Controller {


	protected $mpropertyroles = [];
	protected $mroles = [];

	function __construct()
	{
		parent::__construct();
		$this->load->model('Attachments_model');
		$this->load->model('Common_model');
		// $this->load->model('emailsignature/emailsignature_model');
		// $this->load->model('vendor/Vendor_Model');
		$this->lang->load('keywords');
		$this->load->library(array('form_validation')); 
		$this->load->helper(array('form', 'url'));
		// $this->load->library('session');
		// $this->load->library("PDFMerger1");


		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }
		// else{

			$this->load->library('PDFtoImage');
			// $this->load->model('users/Mlogin');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
			date_default_timezone_set('US/Eastern');
		// }
	}	
	public function index()
	{
		// $data['content'] = 'index';
		// $this->load->view('page', $data);
		if(isset($_GET['OrderUID']))
		{
			$OrderUID = $_GET['OrderUID'];

			$OrderID = $this->Common_model->GetOrderByID($OrderUID);
			$RelationalOrders = $this->Common_model->GetRelationalOrdersByID($OrderUID);
			$data['RelationalOrders'] = $RelationalOrders;
			$OrderNumber = $OrderID->OrderUID;
			if($OrderNumber == 0)
			{
				redirect(base_url().'my_orders');
			}

			$UserUID = $this->session->userdata('UserUID');

			$OrderrUID = $_GET['OrderUID'];
			$OrderUID = str_replace('/', '', $OrderrUID);
			$data['OrderUID'] = $OrderUID;

			$this->db->select('mCustomers.*');
			$this->db->from('mCustomers');
			$this->db->join('tOrders', 'tOrders.CustomerUID = mCustomers.CustomerUID', 'inner');
			$this->db->where('tOrders.OrderUID', $OrderUID);
			$query = $this->db->get();
			$customer = $query->row_array();

			//$data['CustomerEmail'] = $customer['CustomerOrderAckEmailID'] . ';';
			$data['order_details'] = $this->Common_model->get_orderdetails($OrderUID);
			$data['CustomerEmail'] = $data['order_details']->EmailReportTo . ';';
			/* NF158 - Attorney PTO - pdf import & doc type as others */

			/*D2TINT-44 - @author Yagavi G <yagavi.g@avanzegroup.com> @purpose Select DocType Values for Final Reports*/
			$DefaultDocTypes = $this->config->item('DefaultDoctypes');
			/* Starts - Doctypes added for Keystone Orders */

			// $OrderSourceName = $this->common_model->GetApiSourceName($OrderUID); 
			// $SourceName = $OrderSourceName->OrderSourceName;

			$KeystoneDocTypes = $this->config->item('KeystoneDocTypes');
			if(trim($SourceName) == 'Keystone'){
				$data['TypeOfDocument'] = array_merge($DefaultDocTypes, $KeystoneDocTypes);
			} else {
				$data['TypeOfDocument'] = $DefaultDocTypes;
			}
			
			/* Ends - Doctypes added for Keystone Orders */
						
			$data['AttorneyAccept'] = $this->Attachments_model->getApioutboundorders($OrderUID);
			// $this->load->model('order_search/ordersearch_model');
			// $data['document'] = $this->ordersearch_model->Getdocumenttypes(); 
		   	 $data['site'] = $this->Attachments_model->Getsearchsites($OrderUID);
			// $data['DocumentPermissions']=$this->ordersearch_model->GetDocumentPermissions();
			// $data['ScheduledBorrower'] = $this->getScheduledBorrowers($OrderUID);
			// @auth Ubakarasamy 
			// $data['GeneralSignatures'] = $this->emailsignature_model->GetEmailSignatures($OrderUID);
			$data['content'] = 'index';
			$data['Action']="ADD";
			$data['CaseSen'] = $this->common_model->get_CaseSen($OrderUID)->CaseSensitivity;
			$data['data']=array('menu'=>'Address','title'=>'Address','link'=>array('Address'));
			//$data['AttachmentDetails']= $this->Attachments_model->GetAttachmentDetailsByID($OrderUID);
			$data['SearchDocumentDetails']= $this->Attachments_model->GetSearchDocumentDetailsByID($OrderUID);
			$data['Role_permissions'] = $this->common_model->role_workflows();
			$data['mailtemplates'] = $this->Attachments_model->getorderlevel_mailtemplates();

			$data['Name'] = $this->UserName;
			// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
			// $data['EmailSelections'] = $this->Attachments_model->getOrderMails($OrderUID);
			$data['UserSignature'] =$this->common_model->GetUserSignature($this->loggedid);
			$this->load->view('page', $data);
		}
		else
		{
			redirect(base_url().'my_orders');	
		}
	}


	  function Document_upload()
    {
        try
        {
            /**
                * @description Add XSS-Clean Input Security
                * @author Mohindarkumar <mohindar.kumar@avanzegroup.com>
                * @since 23-07-2020 
                * @version CustomerPortal
            */
            $documentUpload = $this->input->post();
            $documentUpload = $this->security->xss_clean($documentUpload);
            /** end */

            $SearchModeUID = $documentUpload['SearchModeUID'];
            $DocumentTypeUID = $documentUpload['DocumentTypeUID'];
            $TypeOfDocument = $documentUpload['TypeOfDocument'];
            $DisplayFileName = $documentUpload['DisplayFileName'];
            $TypeOfPermissions = $documentUpload['TypeOfPermissions'];
            //add comments @auth shruti.vs@avanzegroup.com
            $Comments = $documentUpload['Comments'];
            //print_r($documentUpload['Comments'))
            $Upload_Success_Count=0;
            $Upload_Fail_Count=0;
            $data['OrderUID'] = $documentUpload['OrderUID'];
            $OrderUID = $documentUpload['OrderUID'];
            $data['UploadedUserUID'] = $this->loggedid;
            $UploadedUserUID = $this->loggedid;
            $data['UploadedDate'] = date('Y-m-d  H:i:s');
            $UploadedDate = date('Y-m-d  H:i:s');

            $date = date('Ymd');

            $torders = $this->Common_model->DocumentGettOrders($data);
				
            $OrderDocs_Path = $torders['OrderDocsPath'];
			
            if(empty($OrderDocs_Path))
            {

                $query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$date."/".$torders['OrderNumber']."/"."' Where OrderUID=".$data['OrderUID']);

                $OrderDocs_Path = SEARCHDOCSPATH . $date.'/'.$torders['OrderNumber']."/";
            }
            else
            {
                $OrderDocs_Path = PARENTBASEPATH . $OrderDocs_Path;
            }

            $count_image = count($_FILES['image']['name']);
            $image=$_FILES['image'];
            $ReswareFiles = array();
            $AutoSendSearchDocs = array();
            for($i=0; $i < $count_image; $i++)
            {
                $dotposition=strripos($image['name'][$i], '.');
                $documentname=substr($image['name'][$i], 0, $dotposition);


                $this->db->select('*');
                $this->db->from('torderdocuments');
                $this->db->where('torderdocuments.OrderUID', $data['OrderUID']);
                $this->db->like('torderdocuments.DocumentFileName', $documentname);
                $result=$this->db->get();
                $documentcount=$result->num_rows();
                $data['DocumentFileName'] = $image['name'][$i];
                $data['Comments'] = $Comments[$i];
                $data['DisplayFileName'] = $DisplayFileName[$i];
                if ($TypeOfPermissions[$i]==NULL || !isset($TypeOfPermissions[$i])) {
                    $TypeOfPermissions[$i] = 3;
                }
                $data['TypeOfPermissions'] = $TypeOfPermissions[$i];

                $data['extension'] = pathinfo($image['name'][$i], PATHINFO_EXTENSION);

                $data['DocumentFileName'] = $this->Attachments_model->GetAvailFileName($documentname, '.'.$data['extension'], $documentcount, $data['OrderUID']);

                $image = $_FILES['image'];
                $image_tmp_name = $image['tmp_name'][$i];
                $data['Position'] = $documentUpload['position'.$i];

                if (!is_dir($OrderDocs_Path)) {
                    mkdir($OrderDocs_Path, 0777, true);
                }
                if(move_uploaded_file($image_tmp_name, $OrderDocs_Path.$data['DocumentFileName']))
                {
                    if($TypeOfDocument[$i] == 'Search'){
                        array_push($AutoSendSearchDocs,array('path'=>$OrderDocs_Path.$data['DocumentFileName'],'filename'=>$data['DocumentFileName']));
                    }
                    $data['SearchModeUID'] = $SearchModeUID[$i];
                    $data['DocumentTypeUID'] = isset($DocumentTypeUID[$i]) ? $DocumentTypeUID[$i] : 0;

                    if($data['DocumentTypeUID'] == 8){
                        array_push($AutoSendSearchDocs,array('path'=>$OrderDocs_Path.$data['DocumentFileName'],'filename'=>$data['DocumentFileName']));
                    }

                    $checkdates= $this->Common_model->CheckSearchDateExists($data['OrderUID']);
                    $checkdates = $checkdates->CheckDates;

                    if ($checkdates == 0) {
                        $data['SearchAsOfDate'] = "0000-00-00 00:00:00";
                        $data['SearchFromDate'] = "0000-00-00 00:00:00";
                    }
                    else
                    {
                        $this->db->select('*, Max(SearchAsOfDate) As SearchAsOfDate, Max(SearchFromDate) AS SearchFromDate');
                        $result=$this->db->get_where('torderdocuments', array('OrderUID' => $data['OrderUID']));
                        $torderdocuments=$result->row();

                        $data['SearchAsOfDate']=$torderdocuments->SearchAsOfDate;
                        $data['SearchFromDate']=$torderdocuments->SearchFromDate;
                    }

                    $data['IsReport'] = 0;
                    $data['TypeOfDocument'] = isset($TypeOfDocument[$i]) ? $TypeOfDocument[$i] : "Search";
                    $data['DocumentCreatedDate'] = date('Y-m-d H:i:s');
                    if ($this->session->userdata('RoleType')==8) {
                        $data['TypeOfPermissions'] = 1;/*Show for Customer*/
                        $data['IsDocumentApproval'] = 0;
                        $objdata['DocumentFileName'] =$data['DocumentFileName'];
                        $objdata['TypeOfDocument'] = $data['TypeOfDocument'];
                        $objdata['DocumentTypeUID'] =  $data['DocumentTypeUID'];
                        $objdata['OrderUID']=$OrderUID;
                        $objdata['UploadedUserUID']=$UploadedUserUID;
                        $objdata['UploadedDate']= $UploadedDate;
                        $objdata['SearchModeUID']= $data['SearchModeUID'] ;
                        $objdata['ApprovalFunction']='Document Approval';
                        $objdata['RaisedByUserUID']=$UploadedUserUID;
                        $objdata['RaisedDatetime']=$UploadedDate;
                        $query = $this->db->insert('torderapprovals', $objdata);

                        array_push($ReswareFiles,array('path'=>$OrderDocs_Path.$data['DocumentFileName'],'filename'=>$data['DocumentFileName']));

                    }
                    elseif (in_array($this->session->userdata('RoleType'), array(13,14))){
                        $data['TypeOfPermissions'] = 3;/*Show for Vendor*/
                    }
                 

                    $retdata = $this->Attachments_model->StoreDocuments($data);


                    if($retdata>0)
                    {

                        $Upload_Success_Count++;
                    }
                    else
                    {
                        $Upload_Fail_Count++;
                    }
                }
                else
                {
                    $Upload_Fail_Count++;
                }
            }

            if(!empty($ReswareFiles)){
                $ReswareMailSent = $this->Common_model->Resware_client_mail($OrderUID,' Client Document Approval ',NULL,$ReswareFiles,TRUE);
            }
            if(!empty($AutoSendSearchDocs)){
                $ReswareMailSent = $this->Common_model->Resware_client_mail($OrderUID,' Search Package Added ',NULL,$AutoSendSearchDocs,TRUE);
            }

            $data1['Content']='Abstractor Document: <b>'.$data['DocumentFileName'].'</b> is added';
            $data1['ModuleName']='Abstractor Document';
            $data1['IpAddreess']=$_SERVER['REMOTE_ADDR'];
            $data1['DateTime']=date('Y-m-d H:i:s');
            $data1['TableName']='torderdocuments2';
            $data1['OrderUID']=$torders['OrderUID'];
            $data1['UserUID']=$this->session->userdata('UserUID');
            $this->Common_model->Audittrail_insert($data1);
            $AssignOrderUID = $data['OrderUID'];

            echo json_encode(array('Status' => 'Success', 'success_msg'=>$Upload_Success_Count . ' Documents Uploaded Successfully.', 'fail_msg'=>$Upload_Fail_Count.' Documents Upload Failed.', 'failure'=>$Upload_Fail_Count));
        }
        catch(Exception $e)
        {
            echo json_encode($e->getMessage());
        }
    }

}
