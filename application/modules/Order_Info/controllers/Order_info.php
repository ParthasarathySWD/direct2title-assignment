<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
class Order_Info extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Order_info_model');
		$this->load->model('Common_model');
		$this->load->library(array('form_validation'));
    	$this->load->helper(array('form', 'url'));
    	$this->load->library('session');
    	$this->lang->load('keywords');

		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }
		// else
		// {
			// $this->load->model('users/Mlogin');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
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
			$OrderNumber = $OrderID->OrderUID;
			if($OrderNumber == 0){
			 	redirect(base_url().'my_orders');
			}
			
			$UserUID = $this->session->userdata('UserUID');		
			$OrderrUID = $_GET['OrderUID'];
			$OrderUID = str_replace('/', '', $OrderrUID);
			$data['OrderUID'] = $OrderUID;			
			$data['order_details'] = $this->Common_model->get_orderdetails($OrderUID);
			$data['content'] = 'index';
			$torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
            $subproductuid=$torders[0]->SubProductUID;
            $countyuid=$torders[0]->PropertyCountyUID;
            $stateuid=$torders[0]->PropertyStateUID;

            $data['Orderinfo'] = $this->db->query('SELECT * FROM torderinfo WHERE OrderUID='.$OrderUID.'')->row();
			$data['MappedFieldBySubProduct'] = $this->Order_info_model->GetMappedFieldBySubProduct($subproductuid);
            $data['TemplateMappingList']=$this->Order_info_model->TemplatemappingList($torders);
            $data['TemplateDetails']=$this->Order_info_model->GetTemplateMappingByOrderUID($torders);

            $data['DynTemplateUID'] = $data['TemplateDetails']->TemplateUID;
            $data['FieldRow'] = $data['TemplateDetails']->FieldRow;

            //$UpdateTOrderTable = $this->UpdateTOrderTable($OrderUID,$DynTemplateUID,$FieldRow);

			//$data['Fields'] = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);
			$data['Name'] = $this->UserName;
			$data['Action']='Mapping_EDIT';
  			// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);

  			$data['AttachmentDetails'] = $this->Order_info_model->GetAttachmentDetailsForCopy($OrderUID);

			$this->load->view('page', $data);
		}
		else
		{
			redirect(base_url().'my_orders');	
		}
	}

public function previewtemplate()
	{
	    $TemplateFileName=$this->input->post('TemplateFileName');
	    $OrderUID=$this->input->post('OrderUID');    
        $torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
        $subproductuid=$torders[0]->SubProductUID;
        $countyuid=$torders[0]->PropertyCountyUID;
        $stateuid=$torders[0]->PropertyStateUID;
		$Fields = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);

		$OrderDetails=$this->db->query('SELECT FilePathName FROM  torderinfo WHERE OrderUID='.$OrderUID.'')->row();
		$order_file = $OrderDetails->FilePathName;

		if(file_exists(FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php'))
		{
			$OrderUID     =$this->input->post('OrderUID');
            $OrderNumber = $this->Order_info_model->get_orderdetails($OrderUID);
			$OrderNumber = $OrderNumber->OrderNumber;
			$FilePathName = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php' ;
			$filecontents=file_get_contents($FilePathName);

			$ReportContent = $filecontents;

			foreach($Fields as $f){
	            $value=$f->FieldName;
				$OrderDetails_new=$this->db->query('SELECT '.$f->FieldName.' FROM '.$f->TableName.' WHERE OrderUID='.$OrderUID.'')->row();

				if($f->FieldDataType === 'radio' || $f->FieldDataType === 'checkbox'){
					$FieldName_radio = $OrderDetails_new->$value;
					if($FieldName_radio == 1)
					{
						$fv= "Yes"; 
					}
					else
					{
						$fv= "No"; 
					}
				}
				
				elseif($f->FieldDataType === 'date'){
					$FieldName_radio = $OrderDetails_new->$value;
					$datetime = new DateTime();
					$newDate = $datetime->createFromFormat('Y-m-d', $FieldName_radio);
					$fv = $newDate->format('m/d/Y');
				}
				else
				{
	                $fv=(isset($OrderDetails_new->$value)?$OrderDetails_new->$value :' ');                 
				}	

				//$fv=(isset($OrderDetails_new->$value)?$OrderDetails_new->$value :' '); 

				$find = '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$fv.'</span>';  
				$replace =  '%%'.$f->FieldName.'%%';
				//$filecontents = $filecontents;

				$ReportContent = str_replace($find,$replace,$ReportContent);
			} 

			foreach($Fields as $f){
	            $value=$f->FieldName;
	            $fv= ''; 
				$OrderDetails_new=$this->db->query('SELECT '.$f->FieldName.' FROM '.$f->TableName.' WHERE OrderUID='.$OrderUID.'')->row();
				if($f->FieldDataType === 'radio' || $f->FieldDataType === 'checkbox'){
					//$fv=(isset($_POST[$f->FieldName]) ? 1 : 0);
					$FieldName_radio = $OrderDetails_new->$value;
					if($FieldName_radio == 1)
					{
						$fv= "Yes"; 
					}
					else
					{
						$fv= "No"; 
					}
				}
				elseif($f->FieldDataType === 'date'){
					/*$FieldName_radio = $OrderDetails_new->$value;
					$fv = date('m/d/Y',strtotime($FieldName_radio));*/

					$FieldName_radio = $OrderDetails_new->$value;
					$datetime = new DateTime();
					$newDate = $datetime->createFromFormat('Y-m-d', $FieldName_radio);
					$fv = $newDate->format('m/d/Y');
				}

				else
				{
	                $fv=(isset($OrderDetails_new->$value)?$OrderDetails_new->$value :' ');                 
				}	

		
				$ReportContent = str_replace('%%'.$f->FieldName.'%%',  '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$fv.'</span>' , $ReportContent);
	    		
			} 

			echo $ReportContent;


		}

		else
		{
			$TemplateFileName=$this->input->post('TemplateFileName');
		    $OrderUID=$this->input->post('OrderUID');       
	        $file=$TemplateFileName;  
	        $filecontents=file_get_contents($file);


	        foreach($Fields as $f){
	            $value=$f->FieldName;
				$OrderDetails_new=$this->db->query('SELECT '.$f->FieldName.' FROM '.$f->TableName.' WHERE OrderUID='.$OrderUID.'')->row();
				//$fv=(isset($OrderDetails_new->$value)?$OrderDetails_new->$value :' ');  

				if($f->FieldDataType === 'radio' || $f->FieldDataType === 'checkbox'){
					//$fv=(isset($_POST[$f->FieldName]) ? 1 : 0);
					$FieldName_radio = $OrderDetails_new->$value;
					if($FieldName_radio == 1)
					{
						$fv= "Yes"; 
					}
					else
					{
						$fv= "No"; 
					}

				}

				elseif($f->FieldDataType === 'date'){
					/*$FieldName_radio = $OrderDetails_new->$value;
					$fv = date('m/d/Y',strtotime($FieldName_radio));*/

					$FieldName_radio = $OrderDetails_new->$value;

					if(isset($FieldName_radio)){
						$datetime = new DateTime();
						$newDate = $datetime->createFromFormat('Y-m-d', $FieldName_radio);
						$fv = $newDate->format('m/d/Y');
					}
					else{
						$fv = '';
					}

				}
				
				else
				{
	                $fv=(isset($OrderDetails_new->$value)?$OrderDetails_new->$value :' ');                 
				}	
				


				$filecontents = str_replace('%%'.$f->FieldName.'%%',  '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$fv.'</span>' , $filecontents);
			} 

	    	echo $filecontents;
		}
    }

    public function reset_template(){

    	$OrderUID     =$this->input->post('OrderUID');
        $OrderNumber = $this->Order_info_model->get_orderdetails($OrderUID);
		$OrderNumber = $OrderNumber->OrderNumber;

		$files = glob(FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/*.php'); //get all file names
		foreach($files as $file){
		    if(is_file($file))
		    unlink($file); //delete file
		}

    	/*if(file_exists(FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php'))
		{
			unlink(FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php');
			unlink(FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithOut_Key_Report.php');

	    	$TemplateFileName = $_POST['OrderDocs_Path'];
		    $OrderUID=$this->input->post('OrderUID');       
	        $file=$TemplateFileName;        
	        $filecontents=file_get_contents($file);

	        $torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
	        $subproductuid=$torders[0]->SubProductUID;
	        $countyuid=$torders[0]->PropertyCountyUID;
	        $stateuid=$torders[0]->PropertyStateUID;
			$Fields = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);

	      	$test = "UPDATE torderinfo SET FilePathName = '' WHERE  OrderUID='$OrderUID'";
	      	$res = $this->db->query($test);        

	        if($res){
	        	foreach($Fields as $f){
		            $value=$f->FieldName;
					$OrderDetails_new=$this->db->query('SELECT '.$f->FieldName.' FROM '.$f->TableName.' WHERE OrderUID='.$OrderUID.'')->row();
					$fv=(isset($OrderDetails_new->$value)?$OrderDetails_new->$value :' ');           
					$filecontents = str_replace('%%'.$f->FieldName.'%%',  '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$fv.'</span>' , $filecontents);
				} 
	    		echo $filecontents;
	        }
	    } */
    }

    public function ReplaceContent()
    {
		$OrderDocs_Path = $_POST['OrderDocs_Path'];
		$OrderUID = $_POST['OrderUID'];

		$torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
        $subproductuid=$torders[0]->SubProductUID;
        $countyuid=$torders[0]->PropertyCountyUID;
        $stateuid=$torders[0]->PropertyStateUID;

		$Fields = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);
		$GetData=$this->Order_info_model->GettorderdetailsByUID($OrderUID);
		$GetDynamic=$this->Order_info_model->GetDynamicDetails($OrderUID);

		$ProjectUID = $torders[0]->ProjectUID;
		$IsUppercase = $this->Order_info_model->GetProjectDetailsForUpper($ProjectUID);


		/*$OrderDetails=$this->db->query('SELECT FilePathName FROM  torderinfo WHERE OrderUID='.$OrderUID.'')->row();
		$order_file = $OrderDetails->FilePathName;*/

		$OrderUID     =$this->input->post('OrderUID');
        $OrderNumber = $this->Order_info_model->get_orderdetails($OrderUID);
		$OrderNumber = $OrderNumber->OrderNumber;

		if(file_exists(FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php'))
		{
			$OrderUID     =$this->input->post('OrderUID');
            $OrderNumber = $this->Order_info_model->get_orderdetails($OrderUID);
			$OrderNumber = $OrderNumber->OrderNumber;
			$FilePathName = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php' ;
			$filecontents=file_get_contents($FilePathName);
			$output = $filecontents;
		}
		else
		{
			$TemplateFileName = $_POST['OrderDocs_Path'];
		    $OrderUID=$this->input->post('OrderUID');       
	        $file=$TemplateFileName;      
	        $fp = fopen ( $file, 'r' );
		    $output = fread( $fp, filesize( $file ) );			  
		    fclose ( $fp );
		}

    	$Field=$this->Order_info_model->GetDynamicField($GetDynamic->SubProductUID); 
		$output = str_replace( '%%Witness1%%' , '<span style="color: blue;" data-keyword="Witness1">&nbsp;</span>', $output );
		$output = str_replace( '%%Witness2%%' , '<span style="color: blue;" data-keyword="Witness2">&nbsp;</span>', $output );
		$output = str_replace( '%%Signor%%' , '<span style="color: blue;" data-keyword="Signor">&nbsp;</span>', $output );
		$output = str_replace( '%%Notary%%' , '<span style="color: blue;" data-keyword="Notary">&nbsp;</span>', $output );
		$output = str_replace( '%%PrintDate%%' , '<span style="color: blue;" data-keyword="PrintDate">&nbsp;</span>', $output );
		$output = str_replace( '%%NotaryDateOfExpiration%%' , '<span style="color: blue;" data-keyword="NotaryDateOfExpiration">&nbsp;</span>', $output );
		$output = str_replace( '%%NotaryAck%%' , '<span style="color: blue;" data-keyword="NotaryAck">&nbsp;</span>', $output );
		$output = str_replace( '%%IncorporatedState%%' , '<span style="color: blue;" data-keyword="IncorporatedState">&nbsp;</span>', $output );
		
		$output = str_replace( '%%NotaryStateName%%' , '<span style="color: blue;" data-keyword="NotaryStateName">&nbsp;</span>', $output );
		$output = str_replace( '%%NotaryCountyName%%' , '<span style="color: blue;" data-keyword="NotaryCountyName">&nbsp;</span>', $output );
		$output = str_replace( '%%SignorTitle%%' , '<span style="color: blue;" data-keyword="SignorTitle">&nbsp;</span>', $output );
		$output = str_replace( '%%EndorserPrintName%%' , '<span style="color: blue;" data-keyword="EndorserPrintName">&nbsp;</span>', $output );
		$output = str_replace( '%%MERSAddress%%' , '<span style="color: blue;" data-keyword="MERSAddress">&nbsp;</span>', $output );
		$output = str_replace( '%%Barcode%%' , '<span style="color: blue;" data-keyword="Barcode">&nbsp;</span>', $output );

        foreach($Field as $f)
        {  
        	if($IsUppercase->IsUpperCase=='1') {
        		$post_name = strtoupper($_POST[$f->FieldName]);
        	} else {
        		$post_name = $_POST[$f->FieldName];
        	}

        	if($f->FieldName === 'MIN' )
            {
            	if($post_name){
        			$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'"> MIN:'.$post_name.'	</span>' , $output );
            	} else {
            		$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$post_name.'	</span>' , $output );
            	}
        	}

        	else if($f->FieldName === 'MERSPhoneNo' )
            {
            	if($post_name){
	        		$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'"> MERS Phone :'.$post_name.'</span>' , $output );
	        	} else {
	        		$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $output );
	        	}
        	} 

        	else if($f->FieldName === 'Comments' )
            {
            	if($post_name){
	        		$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'"> Comments :'.$post_name.'</span>' , $output );
	        	} else {
	        		$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $output );
	        	}
        	} 

        	else if($f->FieldName === 'LegalDescription' )
            {
            	if($post_name){
	        		$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $output );
	        	} else {
	        		$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $output );
	        	}
        	} 

        	else if($f->FieldDataType === 'radio' || $f->FieldDataType === 'checkbox')
          	{
				$FieldName_radio = $_POST[$f->FieldName];
				if($FieldName_radio == 'Yes')
				{
					$output = str_replace( $f->FieldKeyword , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">Yes</span>', $output );
				}
				else
				{
					$output = str_replace( $f->FieldKeyword , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">No</span>', $output );
				}
			}

            elseif($f->FieldDataType == 'textarea')
            {
               
                    $output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $output );                
            }

            elseif($f->FieldDataType == 'input')
            {
                $output = str_replace('%%'.$f->FieldName.'%%',  '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $output );
            }

            elseif($f->FieldDataType == 'date'){

            	$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $output );
        	}

            else
            {
        		$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $output );
        	}

        } 

        echo $output;
	}

	public function EditContentMCE()
    {
		$OrderDocs_Path = $_POST['OrderDocs_Path'];
		$OrderUID = $_POST['OrderUID'];

		$torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
        $subproductuid=$torders[0]->SubProductUID;
        $countyuid=$torders[0]->PropertyCountyUID;
        $stateuid=$torders[0]->PropertyStateUID;
		$Fields = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);

		$GetData=$this->Order_info_model->GettorderdetailsByUID($OrderUID);
		$GetDynamic=$this->Order_info_model->GetDynamicDetails($OrderUID);

		/*$OrderDetails=$this->db->query('SELECT FilePathName FROM  torderinfo WHERE OrderUID='.$OrderUID.'')->row();
		$order_file = $OrderDetails->FilePathName;*/

		$OrderUID     =$this->input->post('OrderUID');
        $OrderNumber = $this->Order_info_model->get_orderdetails($OrderUID);
		$OrderNumber = $OrderNumber->OrderNumber;

		if(file_exists(FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php'))
		{
			$OrderUID     =$this->input->post('OrderUID');
            $OrderNumber = $this->Order_info_model->get_orderdetails($OrderUID);
			$OrderNumber = $OrderNumber->OrderNumber;
			$FilePathName = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php' ;
			$filecontents=file_get_contents($FilePathName);
			$output = $filecontents;
		}
		else
		{
			$TemplateFileName = $_POST['OrderDocs_Path'];
		    $OrderUID=$this->input->post('OrderUID');       
	        $file=$TemplateFileName;      
	        $fp = fopen ( $file, 'r' );
		    $output = fread( $fp, filesize( $file ) );			  
		    fclose ( $fp );

		    $FilePath = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber;            
	        mkdir($FilePath, 0777, true); 

	        $FilePathName = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php' ;
	        file_put_contents($FilePathName, $output);
		}

    	/*$Field=$this->Order_info_model->GetDynamicField($GetDynamic->SubProductUID); 
        foreach($Field as $f)
        { 
          	if($f->FieldDataType === 'radio' || $f->FieldDataType === 'checkbox')
          	{
				$FieldName_radio = $_POST[$f->FieldName];
				if($FieldName_radio == 'Yes')
				{
					$output = str_replace( $f->FieldKeyword , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">Yes</span>', $output );
				}
				else
				{
					$output = str_replace( $f->FieldKeyword , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">No</span>', $output );
				}
			}

            elseif($f->FieldDataType == 'textarea')
            {
               
                    $output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$_POST[$f->FieldName].'</span>' , $output );                
            }

            elseif($f->FieldDataType == 'input')
            {
                $output = str_replace('%%'.$f->FieldName.'%%',  '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$_POST[$f->FieldName].'</span>' , $output );
            }

            elseif($f->FieldDataType == 'date'){

            	$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$_POST[$f->FieldName].'</span>' , $output );
        	}

            else
            {
        		$output = str_replace( $f->FieldKeyword  , '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$_POST[$f->FieldName].'</span>' , $output );
        	}

        } */
        echo $output;
	}

	function clean($string){
		$text = filter_var($string, FILTER_SANITIZE_STRING);
		$contents = str_replace('"','\"',$text);
		return $contents;
	}

    function savetemplate(){

        $file = $_POST['OrderDocs_Path'];
        $filecontents = $this->input->post('content');

        $OrderUID = $this->input->post('OrderUID');

        $torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
        $subproductuid=$torders[0]->SubProductUID;
        $countyuid=$torders[0]->PropertyCountyUID;
        $stateuid=$torders[0]->PropertyStateUID;
		$Fields = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);


		$ProjectUID = $torders[0]->ProjectUID;
		$IsUppercase = $this->Order_info_model->GetProjectDetailsForUpper($ProjectUID);

        $content=$filecontents;
      
        $output='';

        $OrderUID     =$this->input->post('OrderUID');
        $OrderNumber = $this->Common_model->get_orderdetails($OrderUID);
		$OrderNumber = $OrderNumber->OrderNumber;

        $FilePath = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber;            

        mkdir($FilePath, 0777, true); 

        $FilePathName = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithOut_Key_Report.php' ;
        file_put_contents($FilePathName, $content);  

		foreach($Fields as $f){

			if($IsUppercase->IsUpperCase=='1') {
        		$post_name = strtoupper($_POST[$f->FieldName]);
        	} else {
        		$post_name = $_POST[$f->FieldName];
        	}

			$OrderDetails=$this->db->query('SELECT * FROM  torderinfo WHERE OrderUID='.$OrderUID.'')->row();

			if($f->FieldDataType === 'radio' || $f->FieldDataType === 'checkbox'){
				$FieldName_radio = $post_name;
				if($FieldName_radio == 'Yes')
				{
					$fv = 1;
				}
				else
				{
					$fv = 0;
				}
			}
			elseif($f->FieldDataType === 'date'){
				$fv_format = $post_name;

				$res = explode("/", $fv_format);
				if (!empty($res) && count($res) == 3) {
					$fv = $res[2]."-".$res[0]."-".$res[1];
				}
				else{
					$fv = NULL;
				}
			}
			else
			{
                $fv=(isset($post_name) ? $post_name:'0');                     
			}	

			$fv_sanitized = $this->clean($fv);

			if(count($OrderDetails)>0){
		        $this->db->query('UPDATE torderinfo  SET '.$f->FieldName.' = "'.$fv_sanitized.'"  WHERE  OrderUID='.$OrderUID.' ');
		        $FilePath = 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithOut_Key_Report.php' ;
    			$this->db->query('UPDATE torderinfo SET FilePathName = "'.$FilePath.'"  WHERE  OrderUID='.$OrderUID.' ');	
		    }
		    else
		    {
		   	    $this->db->query('insert into torderinfo (OrderUID,'.$f->FieldName.') values("'.$OrderUID.'","'.$fv_sanitized.'")');
		   	    $FilePath = 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithOut_Key_Report.php' ;
    			$this->db->query('UPDATE torderinfo SET FilePathName = "'.$FilePath.'"  WHERE  OrderUID='.$OrderUID.' ');		  
		    }     

			$OrderDetails_new=$this->db->query('SELECT * FROM torderinfo  WHERE OrderUID='.$OrderUID.'')->row();
			//$content = str_replace('%%'.$f->FieldName.'%%',  '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$OrderDetails_new->FieldName.'</span>' , $content );

			$FilePathName = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php' ;
			$filecontents=file_get_contents($FilePathName);
			$outputs = $filecontents;

			if($f->FieldDataType === 'date'){

				$find = '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$fv_format.'</span>';  
				$replace =  '%%'.$f->FieldName.'%%';
			}
			elseif($f->FieldDataType === 'radio' || $f->FieldDataType === 'checkbox'){
				if($fv == 1){
					$find = '<span style="color: blue;" data-keyword="'.$f->FieldName.'">Yes</span>';  
					$replace =  '%%'.$f->FieldName.'%%';						
				}
				else{
					$find = '<span style="color: blue;" data-keyword="'.$f->FieldName.'">No</span>';  
					$replace =  '%%'.$f->FieldName.'%%';
				}
			}
			else{
				$find = '<span style="color: blue;" data-keyword="'.$f->FieldName.'">'.$fv.'</span>';  
				$replace =  '%%'.$f->FieldName.'%%';
			}

			$contents = str_replace($find,$replace,$outputs);
			file_put_contents($FilePathName, $contents);  
        } 

       /* $OrderDetails = $this->Common_model->get_orderdetails($OrderUID);
        $OrderDetails_info=$this->db->query('SELECT * FROM torderinfo  WHERE OrderUID='.$OrderUID.'')->row();
        $IsMERSAssignee = $OrderDetails_info->IsMERSAssignee;
        $IsMERSAssignor = $OrderDetails_info->IsMERSAssignor;
        $IsMERSEndorser = $OrderDetails_info->IsMERSEndorser;
        $IsMERSLender = $OrderDetails_info->IsMERSLender;

        if($IsMERSAssignee == 1 || $IsMERSAssignor == 1 || $IsMERSEndorser == 1 || $IsMERSLender == 1) {
        	$this->db->query('UPDATE torders SET IsMERS = "1"  WHERE  OrderUID='.$OrderUID.'');
        }*/
		$this->db->where(array("OrderUID"=>$OrderUID));   
	    $this->db->update('torders', array("IsTemplateSaved"=>1));

	    /*Release 2 D2T-18 - @author Parthasarathy G <parthasarathy.m@avanzegroup.com> @purpose Log Process Updates Date 21.4.2020*/
	    $this->common_model->saveOrderEditProcessLogs($OrderUID, $this->config->item('ProcessesUID')['AOM']);


    }	

	function Edittemplate(){

        $file = $_POST['OrderDocs_Path'];
        $filecontents = $this->input->post('content');

        $OrderUID = $this->input->post('OrderUID');

        $torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
        $subproductuid=$torders[0]->SubProductUID;
        $countyuid=$torders[0]->PropertyCountyUID;
        $stateuid=$torders[0]->PropertyStateUID;
		$Fields = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);

        $content=$filecontents;
      
        $output='';

        $OrderUID     =$this->input->post('OrderUID');
        $OrderNumber = $this->Order_info_model->get_orderdetails($OrderUID);
		$OrderNumber = $OrderNumber->OrderNumber;

        $FilePath = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber;            

        mkdir($FilePath, 0777, true); 

        $FilePathName = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithOut_Key_Report.php' ;
        file_put_contents($FilePathName, $content);  
         
    }	


    function GetAttachments(){

    	$OrderUID = $this->input->post('OrderUID');
    	$DocumentFileName = $this->input->post('DocumentFileName');

    	$this->db->select("*");
		$this->db->from('torderdocuments');
		$this->db->join('torders','torderdocuments.OrderUID=torders.OrderUID','left');
		$this->db->where(array("torders.OrderUID"=>$OrderUID, "torderdocuments.DocumentFileName"=>$DocumentFileName));  
		$query = $this->db->get();
		$AttachmentDetails =  $query->row();

		$OrderPath = $AttachmentDetails->OrderDocsPath;
		$DocumentFileName = $AttachmentDetails->DocumentFileName;

		$pdf_file = $OrderPath.$DocumentFileName;

		echo $pdf_file;
    }

    function GetMasterProjectDetailsForSelect2(){

    	$OrderUID = $this->input->post('OrderUID');
    	$PrimaryColUID = $this->input->post('PrimaryColUID');
    	$PrimaryColName = $this->input->post('PrimaryColName');
    	$TableUsed = $this->input->post('TableUsed');
    	$IsMERS = $this->input->post('IsMERS');
    	$Keyword = $this->input->post('Keyword');
    	$PrintName = $this->input->post('PrintName');
    	$FieldName = $this->input->post('FieldName');
    	$OtherName = $this->input->post('OtherName');
    	$Val = $this->input->post('Val');

    	$Orderinfo = $this->db->query('SELECT * FROM torderinfo WHERE OrderUID='.$OrderUID.'')->row();


    	$this->db->select("*");
		$this->db->from($TableUsed);
		$this->db->where(array($PrimaryColName=>$PrimaryColUID));  
		$query = $this->db->get();
		$res = $query->row();

		$GetOrderInfoStyle=$this->Order_info_model->GetOrderInfoStyle($this->loggedid);
		if($GetOrderInfoStyle == 1)
		{
			$rowtest = 'col-sm-12';
			$colsm3 = 'col-sm-4';
			$colsm12 = 'col-sm-8';
			$style="<style>
			label.col-sm-4 {
				padding: 0px;
			}
			.input-group.datepicker.col-sm-8 {
				padding-left: 15px;
				padding-right: 15px;
			}
			.LoanAmountDiv
			{
				padding-left: 15px !important;
				padding-right: 15px !important;
			}
			.col-sm-12.form-group.input_style {
				padding: 0px !important;
			}
			</style>";
		}
		else
		{
			$rowtest = '';
			$colsm3 = '';
			$colsm12 = '';
			$style="";
		}
		$torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
    	$ProjectUID =$torders[0]->ProjectUID;
		$NonEditableField = $this->db->select('mfields.FieldName')->from('mProjectStaticFields')->join('mfields','mfields.FieldUID=mProjectStaticFields.FieldUID')->where('mProjectStaticFields.ProjectUID',$ProjectUID)->get()->result();
    	$NonEditableFields = [];
    	foreach ($NonEditableField as $row) {
    		array_push($NonEditableFields, $row->FieldName);
    	}

    	if($Val === 'OTHERS' || $Val === 'Others' || $Val === 'others'){

    		$str="";

    		$dispaly_cols = array('AssigneePrintName','AssigneeAddress1','AssigneeAddress2','AssigneeStateName','AssigneeCountyName','AssigneeCityName','AssigneeZipCode','AssignorPrintName','AssignorAddress1','AssignorAddress2','AssignorStateName','AssignorCountyName','AssignorCityName','AssignorZipCode','EndorserPrintName','EndorserAddress1','EndorserAddress2','EndorserStateName','EndorserCountyName','EndorserCityName','EndorserZipCode','LenderPrintName');

    		$result = $this->GetTableColumnFields($TableUsed,$Keyword);


    		$OrderData = $Orderinfo->$IsMERS;

    		if(in_array($IsMERS,$NonEditableFields))
    		{
    			$Disable = 'disabled';
    		}
    		else
    		{
    			$Disable = '';
    		}

    		if($OrderData == 1){
    			$checkbox = '<input type="checkbox" id="'.$IsMERS.'1" data-mers="'.$IsMERS.'" name="'.$IsMERS.'1" class="IsCheckMers" checked  '.$Disable.'>';
    		} else {
    			$checkbox = '<input type="checkbox" id="'.$IsMERS.'1" data-mers="'.$IsMERS.'" name="'.$IsMERS.'1" class="IsCheckMers" '.$Disable.'>';
    		}



	 		$str.= '  <div class="'.$rowtest.' form-group input_style" id="Div_IsMers">
	          			<label style="font-weight:bold" class="'.$colsm3.'">'.$IsMERS.'</label>
	          			<div class="'.$colsm12.'">
						<div class="be-checkbox">'.$checkbox.'
	      					<label for="'.$IsMERS.'1"></label>
	    				</div></div>
	    			</div>';

	    	$str.='<input type="hidden" name="'.$IsMERS.'"  id="'.$IsMERS.'"  value="0"/>';
	    	//echo '<pre>';print_r($result);exit;
			foreach ($result as $row) 
			{
				if(in_array($row,$NonEditableFields))
				{
					$Disable = 'readonly';
				}
				else
				{
					$Disable = '';
				}
				if($row != $FieldName){

					if (in_array($row,$dispaly_cols) == True){

						$Orderinfo = $this->db->query('SELECT * FROM torderinfo WHERE OrderUID='.$OrderUID.'')->row();
						$col_value = $Orderinfo->$row;

						/*$str.= '<div class="form-group input_style" id="'.trim($OtherName).'">
			       				<label for="'.trim($row).'" style="font-weight:bold" >'.trim($row).'</label>
	                            <input type="text" class="form-control input-xs" name="'.trim($row).'"  id="'.trim($row).'" value="'.$col_value.'"/>
			        		</div>';*/

			        	$str.= '<div class="'.$rowtest.' form-group input_style" id="'.trim($OtherName).'">
						        <label for="'.trim($row).'" style="font-weight:bold" class="'.$colsm3.'">'.trim($row).'</label>
						        <div class="'.$colsm12.'">
						         <textarea style="padding: 0px 9px !important;" class="form-control input-xs"  name="'.trim($row).'" id="'.trim($row).'" value="'.$col_value.'" '.$Disable.'>'.$col_value.'</textarea></div>

						        </div>';
			        }

				} 
			}

    	} else {

			$str="";

			$TableIsMERS = $res->$IsMERS;

			if($TableIsMERS == 1){
				$Data = $this->Common_model->get_orderdetails($OrderUID);
			    $PropertyStateCode = $Data->PropertyStateCode;
			    $StateDetails = $this->Order_info_model->GetStateDetails($PropertyStateCode);

			    $StateTableUsed = 'mstates';
			    $StateKeyword = 'MERS';

			    $result = $this->GetTableColumnFields($StateTableUsed,$StateKeyword);
				foreach ($res as $key => $value) {
					foreach ($result as $row) 
					{ 
						$ReplaceKeyword = str_replace($StateKeyword, $Keyword, $row);
						if($ReplaceKeyword == $key)
						$str.='<input type="hidden" name="'.$ReplaceKeyword.'"  id="'.$ReplaceKeyword.'"  value="'.$StateDetails->$row.'"/>'; 
					}

					if($PrintName === $key){
						$str.='<input type="hidden" name="'.$PrintName.'"  id="'.$PrintName.'"  value="'.$value.'"/>'; 
					}

					if($PrimaryColName === $key){
						$str.='<input type="hidden" name="'.$PrimaryColName.'"  id="'.$PrimaryColName.'"  value="'.$value.'"/>'; 
					}

					if($IsMERS === $key){
						$str.='<input type="hidden" name="'.$IsMERS.'"  id="'.$IsMERS.'"  value="'.$value.'"/>'; 
					}
				}	

			} else {
				/*if ($TableUsed !== 'mEndorser'){*/
					$result = $this->GetTableColumnFields($TableUsed,$Keyword);
					foreach ($res as $key => $value) {
						foreach ($result as $row) 
						{ 
							if($row != $FieldName){
								if($row == $key){
									$str.='<input type="hidden" name="'.$row.'"  id="'.$row.'"  value="'.$value.'"/>'; 
								}
							} 
						}
					}	
				/*}*/
			}
		}

		echo json_encode(array('Append' => $str, 'Keyword' => $Keyword));
    }


    function GetTableColumnFields($table_name,$Keyword){
		if($table_name){
			$fields =  $this->db->list_fields($table_name);
			$ColumnName =[];

			foreach ($fields as $field)
			{
				if (strpos($field, $Keyword) !== false) {
			  		$ColumnName[] = $field;
				}
			}
			return $ColumnName;
		}
    }


    function UpdateFieldsTemplateMapping(){

    	$OrderUID = $this->input->post('OrderUID');
    	$TemplateFieldRow = $this->input->post('TemplateUID');
    	$DefaultTemplatemapping=$this->db->get_where('mtemplatemapping', array('FieldRow' => $TemplateFieldRow,'IsDefault' => 1))->row();
    	$Templatemapping=$this->db->get_where('mtemplatemapping', array('FieldRow' => $TemplateFieldRow))->row();
    	
    	if($DefaultTemplatemapping){
    		$TemplateUID = $DefaultTemplatemapping->TemplateUID;
    	} else {
    		$TemplateUID = $Templatemapping->TemplateUID;
    	}

    	$fieldArray = array("TemplateUID"=>$TemplateUID,"TemplateFieldRow"=>$TemplateFieldRow); 
		$this->db->where(array("OrderUID"=>$OrderUID));   
	    $res = $this->db->update('torders', $fieldArray);

	    $DynamicFields = $this->GetFieldsDynamic($OrderUID);
	    $FieldSectionTableView = $this->GetFieldSectionTableView($OrderUID);

	    if($res){
	    	$data=array("success"=>1,"msg"=>"Template Updated Successfully","type"=>"color success","DynamicFields"=>$DynamicFields,"FieldSectionTableView"=>$FieldSectionTableView);
	    } else {
	    	$data=array("success"=>0,"msg"=>"error","type"=>"error");
	    }

	    echo json_encode($data);
    }
    function GetFieldsDynamic($OrderUID){

    	$Orderinfo = $this->db->query('SELECT * FROM torderinfo WHERE OrderUID='.$OrderUID.'')->row();
    	$order_details = $this->Common_model->get_orderdetails($OrderUID);
    	$torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
    	$ProjectUID =$torders[0]->ProjectUID;
    	//$ProjectUID ='4';
    	$NonEditableField = $this->db->query('SELECT `FieldUID` FROM mProjectStaticFields WHERE ProjectUID='.$ProjectUID.'')->result();
    	$NonEditableFields = [];
    	foreach ($NonEditableField as $row) {
    		array_push($NonEditableFields, $row->FieldUID);
    	}
    	//echo '<pre>';print_r($NonEditableFields);exit;
		$Fields = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);
		$Template=$this->Order_info_model->GetTemplateMappingByOrderUIDFieldRow($torders);
		$GetOrderInfoStyle=$this->Order_info_model->GetOrderInfoStyle($this->loggedid);
		if($GetOrderInfoStyle == 1)
		{
			$rowtest = 'col-sm-12';
			$colsm3 = 'col-sm-4';
			$colsm12 = 'col-sm-8';
			$style="<style>
			label.col-sm-4 {
				padding: 0px;
			}
			.input-group.datepicker.col-sm-8 {
				padding-left: 15px;
				padding-right: 15px;
			}
			.LoanAmountDiv
			{
				padding-left: 15px !important;
				padding-right: 15px !important;
			}
			.Div_legal
			{
				padding-right:29px;
			}
			.ui-widget.ui-widget-content {
				border: 1px solid #c5c5c5;
				width: 170px !important;
			}
			.col-sm-12.form-group.input_style {
				padding: 0px !important;
			}
			</style>";
		}
		else
		{
			$rowtest = '';
			$colsm3 = '';
			$colsm12 = '';
			$style="";
		}
    	$field_value='';
    	$str ='';
    	$GetCountyList = $this->db->select('CountyName')->from('mcounties')->group_by('CountyName')->get()->result();
    	$AvailableCounty ='';
    	foreach ($GetCountyList as $County) {
    		if($County->CountyName != '')
    		{
	    		$AvailableCounty .= '"'.$County->CountyName.'",';
    			
    		}
    	}
    	$GetCityList = $this->db->select('CityName')->from('mcities')->group_by('CityName')->get()->result();
    	$AvailableCityName ='';
    	foreach ($GetCityList as $CityNames) {
    		if($CityNames->CityName != '')
    		{
	    		$AvailableCityName .= '"'.$CityNames->CityName.'",';
    			
    		}
    	}
    	$GetStateCodeList = $this->db->select('StateCode')->from('mstates')->group_by('StateCode')->get()->result();
    	$PropertyStateCode ='';
    	foreach ($GetStateCodeList as $StateCodes) {
    		if($StateCodes->StateCode != '')
    		{
	    		$PropertyStateCode .= '"'.$StateCodes->StateCode.'",';
    			
    		}
    	}
    	$GetStateNameList = $this->db->select('StateName')->from('mstates')->group_by('StateName')->get()->result();
    	$PropertyStateName ='';
    	foreach ($GetStateNameList as $StateNames) {
    		if($StateNames->StateName != '')
    		{
	    		$PropertyStateName .= '"'.$StateNames->StateName.'",';
    			
    		}
    	}
    	//echo '<pre>';print_r($AvailableCounty);exit;
    	$str .='<script>
  $( function() {
    var AvailableCounty = [
      '.$AvailableCounty.'
    ];
     $("#PropertyCountyName").keyup(function(){
    	var my_txt = $(this).val();
      	var len = my_txt.length;
      	
    	if(len > 2)
    	{
    		$("#PropertyCountyName").autocomplete({
    			source: AvailableCounty
    			});
    	}
    	else
    	{
    		$("#PropertyCountyName").autocomplete("destroy");
    	}
    	});
   
  } );
  $( function() {
    var AvailableCityName = [
      '.$AvailableCityName.'
    ];
    $("#PropertyCityName").keyup(function(){
    	var my_txt = $(this).val();
      	var len = my_txt.length;
      	
    	if(len > 2)
    	{
    		$("#PropertyCityName").autocomplete({
    			source: AvailableCityName
    			});
    	}
    	else
    	{
    		$("#PropertyCityName").autocomplete("destroy");
    	}
    	});
  } );
  $( function() {
    var PropertyStateCode = [
      '.$PropertyStateCode.'
    ];

    $("#PropertyStateCode").autocomplete({
      source: PropertyStateCode
    });
  } );
  $( function() {
    var PropertyStateName = [
      '.$PropertyStateName.'
    ];

     $("#PropertyStateName").keyup(function(){
    	var my_txt = $(this).val();
      	var len = my_txt.length;
      	
    	if(len > 2)
    	{
    		$("#PropertyStateName").autocomplete({
    			source: PropertyStateName
    			});
    	}
    	else
    	{
    		$("#PropertyStateName").autocomplete("destroy");
    	}
    	});

    
  } );
  </script>';
    	$str.= $style.'<input type="hidden" id="OrderDocs_Path" name="OrderDocs_Path" value="'.$Template->TemplateFileName.'">';

		foreach ($Fields as $key => $row){
			$value=$row->FieldName;
			//echo '<pre>';print_r($row->FieldUID);exit;
			if(in_array($row->FieldUID,$NonEditableFields))
			{
				$Disable = 'readonly';
			}
			else
			{
				$Disable = '';
			}
			//$Disable = 'readonly';
			if($row->FieldDataType == 'date')
		    {
		    	$NonEditableField = $this->db->select('mfields.FieldName')->from('mProjectStaticFields')->join('mfields','mfields.FieldUID=mProjectStaticFields.FieldUID')->where('mProjectStaticFields.ProjectUID',$ProjectUID)->get()->result();
		    	$NonEditableFields = [];
		    	foreach ($NonEditableField as $row1) {
		    		array_push($NonEditableFields, $row1->FieldName);
		    	}
		    	if(in_array($row->FieldName,$NonEditableFields))
		    	{
		    		$Disable = 'readonly';
		    	}
		    	else
		    	{
		    		$Disable = '';
		    	}
		      if (in_array($row->FieldName,array('NotaryDateOfExpiration','PrintDate')) == false){

		      	if($Disable == 'readonly')
		      	{
		      		$str.= '<div class="'.$rowtest.' form-group input_style">
		      		<label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'" >'. $row->FieldName.'</label>
		      		<div  class=" '.$colsm12.'">';

		      		if($Orderinfo->$value)
		      		{                                                    
		      			if( in_array($Orderinfo->$value, ["0000-00-00", "", " ", "--", "__", "0000-00-00 00:00:00",NULL]) ) 
		      			{
		      				$str.= '<input size="16" type="text" name="'. $row->FieldName.'" id="'. $row->FieldName.'" value=" " class="form-control input-xs" '.$Disable.'>';
		      			} 
		      			else
		      			{
		      				$str.= '<input size="16" type="text" name="'. $row->FieldName.'" id="'. $row->FieldName.'" value="'. date('m/d/Y',strtotime($Orderinfo->$value)) .'" class="form-control input-xs"  '.$Disable.'>';
		      			}
		      		}
		      		else
		      		{
		      			$str.= '<input size="16" type="text" name="'. $row->FieldName.'" id="'. $row->FieldName.'" value=" " class="form-control input-xs" '.$Disable.'>';
		      		}
		      		$str.= '</div>
		      		</div>';
		      	}
		      	else
		      	{
		      		$str.= '<div class="'.$rowtest.' form-group input_style">
		      		<label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'" >'. $row->FieldName.'</label>
		      		<div  class="input-group datepicker '.$colsm12.'">';

		      		if($Orderinfo->$value)
		      		{                                                    
		      			if( in_array($Orderinfo->$value, ["0000-00-00", "", " ", "--", "__", "0000-00-00 00:00:00",NULL]) ) 
		      			{
		      				$str.= '<input size="16" type="text" name="'. $row->FieldName.'" id="'. $row->FieldName.'" value=" " class="form-control input-xs" '.$Disable.'><span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar" style="font-size:15px;margin-top:-6px;"></i></span>';
		      			} 
		      			else
		      			{
		      				$str.= '<input size="16" type="text" name="'. $row->FieldName.'" id="'. $row->FieldName.'" value="'. date('m/d/Y',strtotime($Orderinfo->$value)) .'" class="form-control input-xs"  '.$Disable.'><span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar" style="font-size:15px;margin-top:-6px;"></i></span>';
		      			}
		      		}
		      		else
		      		{
		      			$str.= '<input size="16" type="text" name="'. $row->FieldName.'" id="'. $row->FieldName.'" value=" " class="form-control input-xs" '.$Disable.'><span class="input-group-addon btn btn-primary"><i class="icon-th mdi mdi-calendar" style="font-size:15px;margin-top:-6px;"></i></span>';
		      		}
		      		$str.= '</div>
		      		</div>';
		      	}
		       
		      }
		    }
		    else if($row->FieldDataType == 'checkbox')
		    {
		      $field_value = $Orderinfo->$value;

		       if($field_value == 1)
		       {
		        $str.= '<div class="class="'.$row.' form-group input_style">
		              <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		              <div class="'.$colsm12.'">
		                <input type="radio" name="'. $row->FieldName.'" value="Yes" checked '.$Disable.'/> Yes
		                <input type="radio" name="'. $row->FieldName.'" value="No"  '.$Disable.'/> No
		                 </div>
		              </div>
		        ';
		       }
		       else
		       {
		        $str.= '<div class="class="'.$row.' form-group input_style">
		              <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		              <div class="'.$colsm12.'">
		                <input type="radio" name="'. $row->FieldName.'" value="Yes" '.$Disable.' /> Yes
		                <input type="radio" name="'. $row->FieldName.'" value="No"  '.$Disable.' checked/> No
		               </div>
		            </div>
		        ';
		       }
		    }
		    else if($row->FieldDataType == 'radio')
		    {
		      $field_value = $Orderinfo->$value;
		        
		        if($field_value == 1)
		        {
		          $str.= '<div class="'.$rowtest.' form-group input_style">
		        
		           <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		           <div class="'.$colsm12.'">
		           <input type="radio" name="'. $row->FieldName.'" value="Yes" checked '.$Disable.'/> Yes
		           <input type="radio" name="'. $row->FieldName.'" value="No"  '.$Disable.'/> No
		           </div>

		          </div>
		          ';
		        }
		        else
		        {
		          $str.= '<div class="'.$rowtest.' form-group input_style">
		        
		           <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		           <div class="'.$colsm12.'">
		           <input type="radio" name="'. $row->FieldName.'" value="Yes" '.$Disable.'/> Yes
		           <input type="radio" name="'. $row->FieldName.'" value="No" '.$Disable.' checked/> No
		           </div>

		          </div>
		          ';
		        }
		    }
		    else if($row->FieldDataType == 'textarea')
		    {
		    	$NonEditableField = $this->db->select('mfields.FieldName')->from('mProjectStaticFields')->join('mfields','mfields.FieldUID=mProjectStaticFields.FieldUID')->where('mProjectStaticFields.ProjectUID',$ProjectUID)->get()->result();
		    	$NonEditableFields = [];
		    	foreach ($NonEditableField as $row1) {
		    		array_push($NonEditableFields, $row1->FieldName);
		    	}
		    	if(in_array($row->FieldName,$NonEditableFields))
		    	{
		    		$Disable = 'readonly';
		    	}
		    	else
		    	{
		    		$Disable = '';
		    	}
		    	$field_value = $Orderinfo->$value;
		    	$InputFieldName = $row->FieldName;

		    	$LegalDescriptionDetails = array('See Exhibit A','OTHERS');
		       
		        if(in_array($InputFieldName,array('LegalDescription')) == True){

		        	if($Disable == 'readonly')
		        	{
		        		$str.= '<div class="'.$rowtest.' form-group input_style">
		        		<label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		        		<div class="'.$colsm12.'"">
		        		<input type="text" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$field_value.'" '.$Disable.'/></div>
		        		</div>';
		        	}
		        	else
		        	{
		        		$str.= '<div class="'.$rowtest.' form-group input_style">
		        		<label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		        		<div class="'.$colsm12.'"">
		        		<select class=" mdl-select2 select2 legal_change input-xs" name="LegDesc"  id="LegDesc" '.$Disable.'>
		        		';  



		        		if(!empty($field_value))
		        		{
		        			$str.= "<option value='' ></option>";
		        			foreach ($LegalDescriptionDetails as $key => $ele) {
		        				if(strtolower($ele)==strtolower($field_value)){
		        					$str.= "<option value='".$ele."' selected='selected'>".$ele."</option>";
		        				}
		        				else if(strtolower($field_value) != 'see exhibit a')
		        				{
		        					$str.= "<option value='".$ele."' selected='selected'>".$ele."</option>";
		        				}
		        				else {
		        					$str.= "<option value='".$ele."'>".$ele."</option>";
		        				}
		        			}


		        		}
		        		else{
		        			
		        			$str.= "<option value='' selected='selected'></option>";
		        			$str.= "<option value='See Exhibit A'>See Exhibit A</option>";
		        			$str.= "<option value='OTHERS'>OTHERS</option>";
		        			
		        		}
		        		


		        		$str.= '</select></div>

		        		</div>';
		        		if($field_value == "SEE EXHIBIT A" || $field_value == '')
		        		{
		        			$str.= '<div class="form-group input_style Div_legal" style="display:none;">
		        			<textarea class="form-control input-xs"  name="'. $row->FieldName.'" id="'. $row->FieldName.'" value="'.$Orderinfo->$value.'" '.$Disable.'>'.$field_value.'</textarea>

		        			</div>
		        			';
		        		}
		        		else
		        		{
		        			$str.= '<div class="form-group input_style Div_legal">
		        			<textarea class="form-control input-xs"  name="'. $row->FieldName.'" id="'. $row->FieldName.'" value="'.$Orderinfo->$value.'" '.$Disable.'>'.$field_value.'</textarea>

		        			</div>
		        			';
		        		}
		        	}
		        	

		        } else {
		        	$NonEditableField = $this->db->select('mfields.FieldName')->from('mProjectStaticFields')->join('mfields','mfields.FieldUID=mProjectStaticFields.FieldUID')->where('mProjectStaticFields.ProjectUID',$ProjectUID)->get()->result();
		        	$NonEditableFields = [];
		        	foreach ($NonEditableField as $row1) {
		        		array_push($NonEditableFields, $row1->FieldName);
		        	}
		        	if(in_array($row->FieldName,$NonEditableFields))
		        	{
		        		$Disable = 'readonly';
		        	}
		        	else
		        	{
		        		$Disable = '';
		        	}
		        	$str.= '<div class="'.$rowtest.' form-group input_style">
		        	<label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		        	<div class="'.$colsm12.'">
		        	<textarea class="form-control input-xs"  name="'. $row->FieldName.'" value="'.$Orderinfo->$value.'" '.$Disable.'>'.$field_value.'</textarea>
		        	</div>
		        	</div>
		        	';
		        }		        

		    }else if($row->FieldDataType == 'AmountInWords')
		    {
		    }
		    else if($row->FieldDataType == 'list')
		    {
		        $field_value = $Orderinfo->$value;

		        $ParentTable = $row->ParentTable;

		        $ProjectUID = $order_details->ProjectUID;
		        $ParentTableFields = $row->ParentTableFields;

		        $MasterTable = array('massignor','massignee','mlender','mendorser');
		        $LowerParentTable = strtolower($ParentTable);

		        if (in_array($LowerParentTable,$MasterTable) == True){
		          if($LowerParentTable == 'massignor'){
		            $ParentTableDetails = $this->Order_info_model->GetAssignorData($ProjectUID);
		            //$PrintName = $this->Order_info_model->GetAssignorDataPrintName($ProjectUID);
		            $PrimaryUID = 'AssignorUID';
		            $TableUsed = 'mAssignor';
		            $Keyword = 'Assignor';
		            $IsMERS = 'IsMERSAssignor';
		            $PrintName = 'AssignorPrintName';
		            $OtherName = 'AssignorOtherName';
		            
		          } else if($LowerParentTable == 'massignee'){
		            $ParentTableDetails = $this->Order_info_model->GetAssigneeData($ProjectUID);
		            //$PrintName = $this->Order_info_model->GetAssigneeDataPrintName($ProjectUID);
		            $PrimaryUID = 'AssigneeUID';
		            $TableUsed ='mAssignee';
		            $Keyword ='Assignee';
		            $IsMERS = 'IsMERSAssignee';
		            $PrintName = 'AssigneePrintName';
		            $OtherName = 'AssigneeOtherName';

		          }else if($LowerParentTable == 'mlender'){
		            $ParentTableDetails = $this->Order_info_model->GetLenderData($ProjectUID);
		            //$PrintName = $this->Order_info_model->GetLenderDataPrintName($ProjectUID);
		            $PrimaryUID = 'LenderUID';
		            $TableUsed ='mLender';
		            $Keyword ='Lender';
		            $IsMERS = 'IsMERSLender';
		            $PrintName = 'LenderPrintName';
		            $OtherName = 'LenderOtherName';

		          }else if($LowerParentTable == 'mendorser'){
		            $ParentTableDetails = $this->Order_info_model->GetEndorserData($ProjectUID);
		            //$PrintName = $this->Order_info_model->GetEndorserDataPrintName($ProjectUID);
		            $PrimaryUID = 'EndorserUID';
		            $TableUsed ='mEndorser';
		            $Keyword ='Endorser';
		            $IsMERS = 'IsMERSEndorser';                                    
		            $PrintName = 'EndorserPrintName'; 
		            $OtherName = 'EndorserOtherName';                                   
		          }
		          //echo '<pre>';print_r($Disable);exit;
		          $NonEditableField = $this->db->select('mfields.FieldName')->from('mProjectStaticFields')->join('mfields','mfields.FieldUID=mProjectStaticFields.FieldUID')->where('mProjectStaticFields.ProjectUID',$ProjectUID)->get()->result();
		          $NonEditableFields = [];
		          foreach ($NonEditableField as $row1) {
		          	array_push($NonEditableFields, $row1->FieldName);
		          }
		          if(in_array($row->FieldName,$NonEditableFields))
		          {
		          	$Disable = 'readonly';
		          }
		          else
		          {
		          	$Disable = '';
		          }
		          if($Disable == 'readonly')
		          {
		          	
		          			$str.= '<div class="'.$rowtest.' form-group input_style">
		          			<label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		          			<div class="'.$colsm12.'"">
		          			<input type="text" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$field_value.'" '.$Disable.'/></div>
		          			</div>';
		          	
		          }
		          else
		          {
		          	$str.= '<div class="'.$rowtest.' form-group input_style">
		          	<label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		          	<div class="'.$colsm12.'">
		          	<select class=" mdl-select2 select2 projectselect2 input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" '.$Disable.'>
		          	';  
		          	$str.= "<option value='' data-keyword='".$Keyword."'></option>";

		          	foreach ($ParentTableDetails as $key => $ele) {

		          		if(strtolower($ele->$ParentTableFields)==strtolower($field_value)){
		          			$str.= "<option value='".$ele->$ParentTableFields."' data-id='".$ele->$PrimaryUID."' data-primary='".$PrimaryUID."' data-table='".$TableUsed."' data-mers='".$IsMERS."' data-ismers='".$ele->$IsMERS."' data-keyword='".$Keyword."' data-printname='".$PrintName."' data-filename='".$row->FieldName."' data-othername='".$OtherName."'  selected>".$ele->$ParentTableFields."</option>";
		          		}  else {
		          			$str.= "<option value='".$ele->$ParentTableFields."' data-id='".$ele->$PrimaryUID."' data-primary='".$PrimaryUID."' data-table='".$TableUsed."' data-mers='".$IsMERS."' data-ismers='".$ele->$IsMERS."' data-keyword='".$Keyword."' data-printname='".$PrintName."' data-filename='".$row->FieldName."' data-othername='".$OtherName."' >".$ele->$ParentTableFields."</option>";
		          		}

		          	}

		          	/*if(strtolower($row->FieldName) === strtolower('LenderName')){ */

		          		if(strtolower($field_value) == strtolower('OTHERS')){
		          			$str.= "<option value='OTHERS' data-primary='".$PrimaryUID."' data-table='".$TableUsed."' data-mers='".$IsMERS."' data-keyword='".$Keyword."' data-printname='".$PrintName."' data-filename='".$row->FieldName."' data-othername='".$OtherName."' selected >OTHERS</option>";
		          		} else {
		          			$str.= "<option value='OTHERS' data-primary='".$PrimaryUID."' data-table='".$TableUsed."' data-mers='".$IsMERS."' data-keyword='".$Keyword."' data-printname='".$PrintName."' data-filename='".$row->FieldName."' data-othername='".$OtherName."' >OTHERS</option>";
		          		}
		          		/*}*/


		          		$str.= '</select></div>

		          		</div>';
		          $str.= '<div id="'.trim($OtherName).'Append"></div>';
		          }
		          


		        } else if (in_array($LowerParentTable, array('mstates')) == true){

		          $ParentTableFields = $row->ParentTableFields;
		          $ParentTableDetails = $this->Order_info_model->GetParentMasterData($ParentTable);
		          $Details = $this->Common_model->get_orderdetails($OrderUID);
		          $StateCode = $Details->PropertyStateCode;
		      
		          $str.= '<div class="'.$rowtest.' form-group input_style">
		          <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		          <div  class="'.$colsm12.'">
		          <select class=" mdl-select2 select2 input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" '.$Disable.'>
		           ';  

		            $str.= "<option value=''></option>";

		            foreach ($ParentTableDetails as $key => $ele) {
		              if(strtolower($ele->$ParentTableFields)==strtolower($StateCode))
		                $str.= "<option value='".$ele->$ParentTableFields."' selected>".$ele->$ParentTableFields."</option>";
		              else
		                $str.= "<option value='".$ele->$ParentTableFields."'>".$ele->$ParentTableFields."</option>";
		            }

		          $str.= '</select></div>

		          </div>
		          ';

		        } else if (in_array($LowerParentTable, array('msignor','mnotary')) == false){

		          $ParentTableFields = $row->ParentTableFields;
		          $ParentTableDetails = $this->Order_info_model->GetParentMasterData($ParentTable);
		         
		      
		          $str.= '<div class="'.$rowtest.' form-group input_style">
		          <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		          <div class="'.$colsm12.'">
		          <select class=" mdl-select2 select2 input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" '.$Disable.'>
		           ';  

		            $str.= "<option value=''></option>";

		            foreach ($ParentTableDetails as $key => $ele) {
		              if(strtolower($ele->$ParentTableFields)==strtolower($field_value))
		                $str.= "<option value='".$ele->$ParentTableFields."' selected>".$ele->$ParentTableFields."</option>";
		              else
		                $str.= "<option value='".$ele->$ParentTableFields."'>".$ele->$ParentTableFields."</option>";
		            }

		          $str.= '</select></div>

		          </div>
		          ';

		        } 
		    }
		    else
		    {
		        $InputFieldName = $row->FieldName;
		        $ProjectUID = $order_details->ProjectUID;
		        $NonEditableField = $this->db->select('mfields.FieldName')->from('mProjectStaticFields')->join('mfields','mfields.FieldUID=mProjectStaticFields.FieldUID')->where('mProjectStaticFields.ProjectUID',$ProjectUID)->get()->result();
		        $NonEditableFields = [];
		        foreach ($NonEditableField as $row1) {
		        	array_push($NonEditableFields, $row1->FieldName);
		        }
		        if(in_array($InputFieldName,$NonEditableFields))
				{
					$Disable = 'readonly';
				}
				else
				{
					$Disable = '';
				}
		        $PropertyDetails = array('PropertyAddress1','PropertyAddress2','PropertyCityName','PropertyCountyName','PropertyZipCode','APN');

		        $ProjectDetails = array('ReturnAddress1','ReturnAddress2','ReturnStateCode','ReturnCountyName','ReturnCityName','ReturnZipCode','ReturnToOrg','PreparedByOrg','PreparedByAddress1','PreparedByAddress2','PreparedByStateCode','PreparedByCountyName','PreparedByCityName','PreparedZipCode','PreparedByPhoneNo');

		        if (in_array($InputFieldName,$PropertyDetails) == True){

		          if($Orderinfo->$value != ''){
		            $OrderData = $Orderinfo->$value;
		          } else {
		            $OrderData = $this->Order_info_model->GetPropertyAddressDetails($OrderUID,$InputFieldName);
		          }

		          $str.= '<div class="'.$rowtest.' form-group input_style">
		           <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		           <div class="'.$colsm12.'">
		           <input type="text" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$OrderData.'" '.$Disable.' /></div>
		          </div>';

		          //$str.= ' <input type="hidden" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'"  value="'.$OrderData.'"/>';                                         

		        } else if (in_array($InputFieldName,$ProjectDetails) == True){

		          if($Orderinfo->$value != ''){
		            $OrderData = $Orderinfo->$value;
		          } else {
		            $OrderData = $this->Order_info_model->GetProjectDetails($ProjectUID,$InputFieldName);
		          }

		          $str.= '<input type="hidden" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'"  value="'.$OrderData.'"/>'; 

		        } else if (in_array($InputFieldName,array('Witness1','Witness2','NotaryStateName','NotaryCountyName','SignorTitle','AssigneePrintName','AssigneeAddress1','AssigneeAddress2','AssigneeStateName','AssigneeCountyName','AssigneeCityName','AssigneeZipCode','AssignorPrintName','AssignorAddress1','AssignorAddress2','AssignorStateName','AssignorCountyName','AssignorCityName','AssignorZipCode','EndorserPrintName','EndorserAddress1','EndorserAddress2','EndorserStateName','EndorserCountyName','EndorserCityName','EndorserZipCode','LenderPrintName','LenderAddress1','LenderAddress2','LenderStateName','LenderCountyName','LenderCityName','LenderZipCode','IsMERSAssignee','IsMERSAssignor','IsMERSEndorser','IsMERSLender','AssigneeUID','AssignorUID','EndorserUID','LenderUID','ParcelID','MERSAddress','NotaryAck','IncorporatedState','Barcode')) == true){

		        } else if(in_array($InputFieldName,array('MERSPhoneNo')) == True){

		            $Data = $this->Common_model->get_orderdetails($OrderUID);
		            $PropertyStateCode = $Data->PropertyStateCode;
		            $IsMERS = $Data->IsMERS;
		            $StateDetails = $this->Order_info_model->GetStateDetails($PropertyStateCode);
		            if($IsMERS == 1){
		              $OrderData = $StateDetails->MERSPhoneNo;
		            } else {
		              $OrderData = '';                                              
		            }

		          $str.= '<div id="Div_MERS"></div>'; 

		        } else if(in_array($InputFieldName,array('MIN')) == True){

		          $str.= '<div class="'.$rowtest.' form-group input_style" id="Div_MIN">';
                                             
		          $Data = $this->Common_model->get_orderdetails($OrderUID);
		          $IsMERS = $Data->IsMERS;
		          if($IsMERS == 1){
		            $str.= '<label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		            <div class="'.$colsm12.'">
                            <input type="text" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" maxlength="18" value="'.$Orderinfo->$value.'" '.$Disable.' /></div>';
		          }    

		          $str.='</div>';                              

		        } else if(in_array($InputFieldName,array('OrderNumber')) == True){

		          if($Orderinfo->$value != ''){
		            $OrderData = $Orderinfo->$value;
		          } else {
		            $Data = $this->Common_model->get_orderdetails($OrderUID);
		            $OrderData = $Data->OrderNumber;
		          }

		          $str.= '<input type="hidden" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'"  value="'.$OrderData.'"/>'; 

		        } else if(in_array($InputFieldName,array('TaxID')) == True){

		          if($Orderinfo->PreparedBy != ''){
		            $OrderData = $Orderinfo->$value;
		          } else {
		            $Data = $this->Common_model->get_orderdetails($OrderUID);
		            $OrderData = $Data->APN;
		          }

		          //$str.= '<input type="hidden" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'"  value="'.$OrderData.'"/>'; 
		          $str.= '<div class="'.$rowtest.' form-group input_style">
		           <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		           <div class="'.$colsm12.'"">
		           <input type="text" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$OrderData.'" '.$Disable.'/></div>
		          </div>';

		        } else if(in_array($InputFieldName,array('LoanNumber')) == True){

		          if($Orderinfo->$value != ''){
		            $OrderData = $Orderinfo->$value;
		          } else {
		            $Data = $this->Common_model->get_orderdetails($OrderUID);
		            $OrderData = $Data->LoanNumber;
		          }

		          $str.= '<input type="hidden" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'"  value="'.$OrderData.'"/>'; 

		        } else if(in_array($InputFieldName,array('PropertyStateCode')) == True){

		          if($Orderinfo->$value != ''){
		            $OrderData = $Orderinfo->$value;
		          } else {
		            $Data = $this->Common_model->get_orderdetails($OrderUID);
		            $PropertyStateCode = $Data->PropertyStateCode;
		            $StateDetails = $this->Order_info_model->GetStateDetails($PropertyStateCode);
		            $OrderData = $StateDetails->StateCode;
		          }

		          $str.= '<div class="'.$rowtest.' form-group input_style">
		           <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		           <div class="'.$colsm12.'"">
		           <input type="text" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$OrderData.'" '.$Disable.'/></div>
		          </div>';

		          //$str.= '<input type="hidden" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'"  value="'.$OrderData.'"/>'; 

		        } else if(in_array($InputFieldName,array('PropertyStateName')) == True){

		          if($Orderinfo->$value != ''){
		            $OrderData = $Orderinfo->$value;
		          } else {
		            $Data = $this->Common_model->get_orderdetails($OrderUID);
		            $PropertyStateCode = $Data->PropertyStateCode;
		            $StateDetails = $this->Order_info_model->GetStateDetails($PropertyStateCode);
		            $OrderData = $StateDetails->StateName;
		          }

		           $str.= '<div class="'.$rowtest.' form-group input_style">
			           <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
			           <div class="'.$colsm12.'">
			           <input type="text" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$OrderData.'" '.$Disable.'/></div>
			          </div>';

		          //$str.= '<input type="hidden" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'"  value="'.$OrderData.'"/>'; 

		        } else if(in_array($InputFieldName,array('PreparedBy')) == True){

		          if($Orderinfo->$value != ''){
		            $OrderData = $Orderinfo->$value;
		          } else {
		            $UserUID = $this->session->userdata('UserUID');
		            $Data = $this->Order_info_model->GetUserDetails($UserUID);
		            $OrderData = $Data->UserName;
		          }

		          $str.= '<input type="hidden" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'"  value="'.$OrderData.'" '.$Disable.'/>'; 

		        } else if(in_array($InputFieldName,array('LoanAmount')) == True){

	               if($Orderinfo->$value != ''){
		            $OrderData = $Orderinfo->$value;
		          } else {
		            $Data = $this->Common_model->get_orderdetails($OrderUID);
		            $OrderData = $Data->LoanAmount;
		          }
		          $padding = '';
		          	if($Disable == "readonly" && $GetOrderInfoStyle == 1)
		          	{
		          		$padding = 'style="padding-left: 15px;padding-right: 15px;font-size:10px;"';
		          	}
		            $str.= '<div class="'.$rowtest.' form-group input_style">
		                  <label for="exampleInputuname" style="font-weight:bold;"  class="'.$colsm3.'">'. $row->FieldName.'</label>
		                  <div class="input-group '.$colsm12.' LoanAmountDiv" '.$padding.'><span class="input-group-addon input-xs" style="border-color:#59595973;">$</span>
		                    <input type="text" class="form-control currency input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$OrderData.'" data-in-words="'. $row->AmountInWords.'" '.$Disable.'>
		                  </div>
		                </div>';

		            $str.= '<input type="hidden" class="form-control input-xs" name="'. $row->AmountInWords.'"  id="'. $row->AmountInWords.'"  value=""/>';

	            }
	            else if($row->IsAmount == 1){
	            	 $padding = '';
		          	if($Disable == "readonly" && $GetOrderInfoStyle == 1)
		          	{
		          		$padding = 'style="padding-left: 15px;padding-right: 15px;font-size:10px;"';
		          	}
		          $str.= '<div class="'.$rowtest.' form-group input_style" >
		                  <label for="exampleInputuname" style="font-weight:bold;" class="'.$colsm3.'">'. $row->FieldName.'</label>
		                  <div class="input-group '.$colsm12.'" '.$padding.'><span class="input-group-addon input-xs" style="border-color:#59595973;">$</span>
		                    <input type="text" class="form-control currency input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$Orderinfo->$value.'" data-in-words="'. $row->AmountInWords.'" '.$Disable.'>
		                  </div>
		                </div>';

		          $str.= '<input type="hidden" class="form-control input-xs" name="'. $row->AmountInWords.'"  id="'. $row->AmountInWords.'"  value=""/>'; 

		        } else if(in_array($InputFieldName,array('BorrowerName')) == True){

	                if($Orderinfo->$value != ''){
	                  $OrderData = $Orderinfo->$value;
	                } else {
	                  $OrderData = $this->GetBorrowers($OrderUID);
	                }

		            $str.= '<div class="'.$rowtest.' form-group input_style">
								<label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
								<div class="'.$colsm12.'">
								<input type="text" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$OrderData.'" '.$Disable.' /></div>
							</div>';


	            }  else {
		          $str.= '<div class="'.$rowtest.' form-group input_style">
		           <label for="exampleInputuname" style="font-weight:bold" class="'.$colsm3.'">'. $row->FieldName.'</label>
		           <div class="'.$colsm12.'">
		           <input type="text" class="form-control input-xs" name="'. $row->FieldName.'"  id="'. $row->FieldName.'" value="'.$Orderinfo->$value.'" '.$Disable.' /></div>
		          </div>';
		        }
		    } 

		}

		return $str;

    }

    function GetBorrowers($OrderUID){

        $this->load->model('reports/Report_model');

    	$borrowers = $this->Report_model->getBorrowers($OrderUID);
		$Count =  count($borrowers);
		if($Count > 1){
		    $borrower ='';
		    foreach($borrowers as $value) 
		    {
		        if($value->PRName !=''){
		            $borrower .=  trim($value->PRName.',');
		        }
		    }
		    $borrowers =  rtrim($borrower,",");
		    $Array = explode(",", $borrowers);
		    $LastArrayValue =  end($Array);
		    
		    if(count($Array) > 1 ){
		        array_pop($Array);
		        $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;
		    }else{
		        $BorrowerName = implode(", &nbsp;", $Array);
		    }
		}
		else{

		    $borrower ='';
		    foreach($borrowers as $value) 
		    {
		        $borrower .=  $value->PRName.',';
		    }
		    $BorrowerName =  rtrim($borrower,",");
		}

		return $BorrowerName;
    }

    function GetFieldSectionTableView($OrderUID){

    	$torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
		$Fields = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);

		$str ='';

		foreach ($Fields as $row){
			$str.='<tr style="background-color: #cdf7bf66;">
				<td>'.$row->FieldName.'</td>
				<td class="text-center">
				<button class="btn btn-space btn-social btn-color choose btn-xs" style="background-color: #7da74ffa;color: #fff;border: 1px solid #54a03b;" data-value="'.$row->FieldUID.'"  data-display-name="'.$row->FieldName.'" data-keyword="'.$row->FieldKeyword.'" > <i class="fa fa-forward" aria-hidden="true"></i></button>
				</td>
			</tr>';
		}

		return $str;
    }


    function EditMainTemplate(){

        $file = $_POST['OrderDocs_Path'];
        $filecontents = $this->input->post('content');
        $OrderUID = $this->input->post('OrderUID');
        $torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
        $content=$filecontents;
      
        $OrderUID     =$this->input->post('OrderUID');
        $OrderNumber = $this->Common_model->get_orderdetails($OrderUID);
		$OrderNumber = $OrderNumber->OrderNumber;

        $FilePathName = FCPATH . 'uploads/SampleTemplate/'.$OrderNumber.'/'.$OrderNumber.'-WithKey_Report.php' ;
		file_put_contents($FilePathName, $content);
    }


















	
}
