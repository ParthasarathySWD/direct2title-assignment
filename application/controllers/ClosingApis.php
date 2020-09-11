<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ClosingApis extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('Aes');
        $this->load->library('AesCtr');
        $this->load->config('RoleTypes');
        $this->load->helper(array('url','string'));
        $this->load->model('apis_model');
        $this->load->model('common_model');
        date_default_timezone_set("America/New_York");
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    //Login
    function login()
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST')
        {
            $params = json_decode(file_get_contents('php://input'), TRUE);
            $Username = $params['UserName'];
            $Password = md5($params['Password']);
            $DeviceName = $params['deviceName'];
            $Platform = $params['platform'];
            $Version = $params['version'];
            $IpAddreess = $params['IpAddreess'];
            $Location = $params['Location'];
            
            $result = $this->apis_model->login($Username,$Password);
            


/*            if ($result['AbstractorUID'] != 0) {
*/            
    $salt_key = md5(microtime().rand());
    $secret = 'zdfghtuOdfPKJL2551*^$#$()k';
    $encrypt = new AesCtr();
    $encrptval = $encrypt->encrypt($salt_key, $secret, 256); 
    $InputPassword = md5($Password.$encrptval);
    $DatabasePassword = md5($result['Password'].$encrptval);   
    if($InputPassword == $DatabasePassword)
    { 
        $RoleType = $result['RoleType'];
        if (in_array($RoleType, $this->config->item('Abstractor'))) 
        {
          $RoleType = "Vendor";
      }
      else
      {
        $RoleType = "Organization";
    }

    $res = array("status" => 200,'message' => 'Success','UserUID' => $result['UserUID'],'LoginID' => $result['LoginID'],'Name' =>$result['UserName'],'EmailID' => $result['EmailID'],'ContactNo' => $result['ContactNo'],'AbstractorUID' => $result['AbstractorUID'],'RoleName' => $result['RoleName'],'Key' => $result['AuthenticationKey'],'Session' => $result['SessionValue'], 'RoleType'=>$RoleType);

                    //AuditTrail Added Code Begin 
    $InsetData = array(
      'UserUID' => $result['LoginID'],
      'ModuleName' => 'Login',
      'TableName' => 'musers',
      'Feature' => $result['UserUID'],
      'Devicename' =>$DeviceName,
      'Platform' =>$Platform,
      'PlatformVersion' =>$Version,
      'IpAddreess' =>$IpAddreess,
      'Location' =>$Location,
      'DateTime' => date('Y-m-d H:i:s'));
    $this->common_model->InsertAuditTrail($InsetData);
                    //AuditTrail Added Code End
} 
else 
{
    $res = array("status" => 401,'message' => 'Invalid Credentials');
}

echo json_encode($res);   
/*            }else{
                $res = array("status" => 401,'message' => 'Not An Abstractor',null);
                echo json_encode($res);   
            }     
*/        }
            else{

                $this->respond("400", 'Bad Request', null);
            }
        }
    // Login Ends

    //Orders List
        function getOrdersByUserID()
        {
            if($this->input->server('REQUEST_METHOD') === 'POST'){
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $AuthenticationKey = $params['AuthenticationKey'];
                if($this->apis_model->CheckAuthKey($AuthenticationKey))
                {
                    $AbstractorUID = $params['AbstractorUID'];
                    $result = $this->apis_model->GetOrdersByUserID($AbstractorUID,$AuthenticationKey);


                    foreach ($result as $key => $value) {

                        $ClosingQueue = $this->getCurrentStatus($value['OrderUID'], $value['OrderStatus']);
                        $result[$key]['StatusName'] = $ClosingQueue->StatusName;
                        $result[$key]['StatusColor']= $ClosingQueue->StatusColor;

                    }


                    if (!empty($result)) {
                       echo json_encode($result);
                   }else{
                    $this->respond("200", 'No Orders Found', null);
                }
                

            }else{
                $this->respond("404", 'Page Not Found', null);
            }

        }
        else{
            $this->respond("400", 'Bad Request', null);
        }
    }
    //Orders List ends

    //Order Details/Description Based on OrderUID
    function getOrderDetailsByOrderUID()
    {
        if($this->input->server('REQUEST_METHOD') === 'POST'){

            $params = json_decode(file_get_contents('php://input'), TRUE);
            $AuthenticationKey = $params['AuthenticationKey'];
            if($this->apis_model->CheckAuthKey($AuthenticationKey))
            { 
                $OrderUID = $params['OrderUID'];
                $OrderUIDMain =$params['MainOrderUID'];
                $AbstractorUID = $params['AbstractorUID'];

                if(!empty($OrderUID)){
                $result=$this->apis_model->GetOrderDetailsByOrderUID($OrderUID,$AbstractorUID,$OrderUIDMain);
                 }
                 else{
                    $result=$this->apis_model->GetOrderDetailsByOrderUID($OrderUID,$AbstractorUID,$OrderUIDMain);
                 }

                


                if (!empty($result)) {
                    $ClosingQueue = $this->getCurrentStatus($OrderUID, $result['SummaryDetails']->OrderStatus);
                    $result['SummaryDetails']->StatusName = $ClosingQueue->StatusName;
                    $result['SummaryDetails']->StatusColor= $ClosingQueue->StatusColor;
                    echo json_encode($result);
                }else{
                    $this->respond("200", 'No Orders Found', null);
                }
            }else{
                $this->respond("404", 'Page Not Found', null);
            }
        }
        else{
            $this->respond("400", 'Bad Request', null);
        }
    }

    //Order Details/Description Based on OrderUID

    //Completed Orders List
    function completedOrderListByUserId()
    {
        if($this->input->server('REQUEST_METHOD') === 'POST'){
         $params = json_decode(file_get_contents('php://input'), TRUE);
         $AuthenticationKey = $params['AuthenticationKey'];
         if($this->apis_model->CheckAuthKey($AuthenticationKey))
         {
            $AbstractorUID = $params['AbstractorUID'];
            $result = $this->apis_model->GetCompletedOrderListByUserID($AbstractorUID);

            foreach ($result as $key => $value) {

                $ClosingQueue = $this->getCurrentStatus($value['OrderUID'], $value['OrderStatus']);
                $result[$key]['StatusName'] = $ClosingQueue->StatusName;
                $result[$key]['StatusColor']= $ClosingQueue->StatusColor;

            }

            if ($result) {
                echo json_encode($result);
            }else{
                $this->respond("200", 'No Orders Found', null);
            }

        }else{
            $this->respond("404", 'Page Not Found', null);
        }
    }else{
        $this->respond("400", 'Bad Request', null);
    }
}
    //Completed Orders List

    //Pending Orders List
function pendingOrderListByUserId()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){


     $params = json_decode(file_get_contents('php://input'), TRUE);
     $AuthenticationKey = $params['AuthenticationKey'];
     if($this->apis_model->CheckAuthKey($AuthenticationKey))
     {
        $loggedid = $params['loggedid'];
        $result = $this->apis_model->GetPendingOrdersByUserID($loggedid,$AuthenticationKey);
        if ($result) {
            echo json_encode($result);
        }else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}else{
    $this->respond("400", 'Bad Request', null);
}
}
    //Pending Orders List


    // Accept Order
function AcceptOrder()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $AbstractorOrderUID = $params['OrderUID'];
            $orderUID = $this->getOrderUID($AbstractorOrderUID);
            $UserUID = $params['UserUID'];
            $status = 3;
            $result = $this->apis_model->Change_order_status($orderUID,$status,$UserUID);
            
            if($result)
            {
                $this->respond("200", 'Order Accepted', null);
            }else{
                $this->respond("406", 'Error While Accepting Order', null);
            }

        }else{
         $this->respond("404", 'Page Not Found', null);
     } 
 }else{
    $this->respond("400", 'Bad Request', null);
}
}
    // Accept Order ends

    // Reject order    
function submitrejectorder()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $orderUID = $params['OrderUID'];
            $UserUID = $params['UserUID'];
            $rejection=array('Remarks'=>$params['remarkstext'],'Reason'=>$params['Reason'], 'OrderFlag'=>2);

            $result = $this->apis_model->rejectorder_in_queue($orderUID,$UserUID,$rejection);
            
            if($result)
            {
                $this->respond("200", 'Abstractor Order Rejected Successfully', null);
            }else{
                $this->respond("406", 'Error While Rejecting Order', null);
            }

        }else{
         $this->respond("404", 'Page Not Found', null);
     } 
 }else{
    $this->respond("400", 'Bad Request', null);
}
}
    // Reject order ends

    // Shipping Reasons
function GetShippingReasons()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];
        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            if($this->apis_model->GetShippingReasons())
            {
               echo json_encode($this->apis_model->GetShippingReasons());
           }else{

            $this->respond("200", 'No System Notes Found', null);
        }

    }else{
     $this->respond("404", 'Page Not Found', null);
 }

}else{
    $this->respond("400", 'Bad Request', null);
}
}

    // Shipping reasons ends

public function SigningProcess()
{
    $params = json_decode(file_get_contents('php://input'), TRUE);
    $SendType = $params['SendType'];
    $SignedUID = $params['SignedUID'];
    $OrderUID = $params['OrderUID'];
    $loggedid = $params['loggedid'];
    $SchedulingDate = $this->apis_model->GetScheduleDate($OrderUID);

    $fieldArray = array(
        "OrderUID"=>$OrderUID,
        "SigningFeedback"=>$params['signingfeedback'],
        "IsSigningDone"=>$params['IsSigningDone'],
        "IsShipped"=>$params['shipping'],
        "SigningComment"=>$params['Comments'],
        "ShippedComment"=>$params['ShippimgCommends'],
        "IsSigningAccepted"=>$params['IsSigningAccepted'],
        "SigningAcceptedComment"=>$params['SigningAcceptedComment'],
        "IsDayOfSigning"=>$params['IsDayOfSigning'],
        "DayOfSigningComment"=>$params['DayOfSigningComment'],
        "Is2hoursCheck"=>$params['Is2hoursCheck'],
        "2hoursCheckComments"=>$params['_2hoursCheckComments'],
        "IsPostSigning"=>$params['IsPostSigning'],
        "PostSigningComment"=>$params['PostSigningComment'],
        'ModifiedUserUID'=>$loggedid,
        'ModifiedDateTime'=>date('Y-m-d H:i:s'),
                // "CreatedDateTime"=>date('Y-m-d H:i:s'),
                // "ModifiedUserUID"=>$this->loggedid,
                // "SignedDateTime"=> Date('Y-m-d',strtotime($SchedulingDate->SigningDateTime))
    ); 
    


    $result =  $this->apis_model->updatesignDetails($fieldArray,$SignedUID,$OrderUID,$loggedid);
    $Message = "Signing Updated";


            // if ($this->db->trans_status()) {
            //     if ($SendType == 1) {
            //         $returndata = $this->Schedule_model->SMPTEmail($post);
            //         if ($returndata['status'] == 1) {
            //             /*Commit DB Changes*/
            //             $this->db->trans_commit();
    echo json_encode(array('error' => 0,'message'=>"Signing Updated & Send"));exit;
            //         }
            //         else{
            //             /*Rollback DB Changes*/
            //             $this->db->trans_rollback();
            //             echo json_encode(array('error' => 1,'message'=>'Failed'));exit;
            //         }
            //     }
            //     else{
            //         /*Commit DB Changes*/
            //         $this->db->trans_commit();
            //         echo json_encode(array('error' => 0,'message'=>$Message));exit;
            //     }
            // }
            // else{
            //     /*Rollback DB Changes*/
            //     $this->db->trans_rollback();
            //     echo json_encode(array('error' => 1,'message'=>'Failed'));exit;
            // }

    
}


function GetDueOrders()
{ 
    if($this->input->server('REQUEST_METHOD') === 'POST'){

     $params = json_decode(file_get_contents('php://input'), TRUE);
     $AuthenticationKey = $params['AuthenticationKey'];
     if($this->apis_model->CheckAuthKey($AuthenticationKey))
     {
        $AbstractorUID = $params['AbstractorUID'];
        $DueOrders = $this->apis_model->getScheduleData($AbstractorUID);
        $result = [];
        if (!empty($DueOrders)) {
         foreach ($DueOrders as $key => $value) 
         {
            $data['id'] = $value->OrderUID;
            $data['start'] = $value->AssignedDateTime;
            $data['end'] = $value->SigningDateTime;
            $data['title'] = $value->OrderNumber;
            $currentdate=date("Y-m-d h:i:sa");
            if( (date("Y-m-d h:i:sa",strtotime($value->SigningDateTime))>= date("Y-m-d h:i:sa")) || (date('Y-m-d h:i:sa', strtotime('+1 day', $currentdate)) >= date("Y-m-d h:i:sa",strtotime($value->SigningDateTime))))
            {
                $data['backgroundColor'] = '#008000';
            }
            else
            {
                $data['backgroundColor'] = '#ff3300';
            }
            $data['url'] = base_url().'users/order_session/'.$value->OrderUID;
            $result[] = $data;
        }
        echo json_encode($result);
    }else{
        $this->respond("200", 'No Due Orders Found', null);
    }

}else{
    $this->respond("404", 'Page Not Found', null);
}
}else{
    $this->respond("400", 'Bad Request', null);
}      
}


public function SaveShippingDet()
{

    if ($this->input->server('REQUEST_METHOD') === 'POST') 
    {
        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];
        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $UserUID = $params['UserUID'];
            $ShippingUID = $params['ShippingUID'];
            $OrderUID = $params['OrderUID'];
            $SenderType = $params['SenderType'];
            $SenderCustomerUID = $params['SenderCustomerUID'];
            $SenderBorrowerUID = $params['SenderBorrowerUID'];
            $SenderCompanyName = $params['SenderCompanyName'];
            $SenderAddress1 = $params['SenderAddress1'];
            $SenderAddress2 = $params['SenderAddress2'];
            $SenderCityName = $params['SenderCityName'];
            $SenderStateCode = $params['SenderStateCode'];
            $SenderZipCode = $params['SenderZipCode'];
            $SenderContact = $params['SenderContact'];
            $RecipientType = $params['RecipientType'];
            $RecipientCustomerUID = $params['RecipientCustomerUID'];
            $RecipientBorrowerUID = $params['RecipientBorrowerUID'];
            $RecipientCompanyName = $params['RecipientCompanyName'];
            $RecipientAddress1 = $params['RecipientAddress1'];
            $RecipientAddress2 = $params['RecipientAddress2'];
            $RecipientCityName = $params['RecipientCityName'];
            $RecipientStateCode = $params['RecipientStateCode'];
            $RecipientZipCode = $params['RecipientZipCode'];
            $RecipientContact = $params['RecipientContact'];
            $ServiceType = $params['ServiceType'];
            $ShippingStatus = $params['ShippingStatus'];
            $TrackingNumber = $params['TrackingNumber'];
            $IsShipped = $params['IsShipped'];
            $ShippingReasonUID = $params['ShippingReasonUID'];
            $Notes = $params['Notes'];
            $IsShippedCheckBox = $params['IsShippedCheckBox'];
            $ReasonName = $params['ReasonName'];
            $post = $params;

            $result = $this->apis_model->SaveShippingDet($post,$UserUID);

            if( $result== 1)
            {

                $res = array('Status' => 1,'message'=>'Shipping Details added Successsfully');                                      
            }
            else if($result == 2){

                $res = array('Status' => 1,'message'=>'Shipping Details Update Successsfully');

            }
            else{

                $res = array('Status' => 0,'message'=>'Failed');

            }
            echo json_encode($res);exit();
        }else{
            $this->respond("404", 'Page Not Found', null);
        }
    }else{
        $this->respond("400", 'Bad Request', null);
    }
}


        //Search Based On Keywords
function searchList()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];
        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $keywords = $params['keywords'];
            echo json_encode($this->apis_model->GetSearchListResults($keywords));
        }else{
            $this->respond("404", 'Page Not Found', null);
        }
    }else{
        $this->respond("400", 'Bad Request', null);
    }
}
    //Search Based on Keywords

    //Upload Document
function upload()
{

  if($this->input->server('REQUEST_METHOD') === 'POST'){

     $AuthenticationKey = $this->input->post('AuthenticationKey');

     if($this->apis_model->CheckAuthKey($AuthenticationKey))
     {

        $data['OrderUID'] = $this->input->post('OrderUID');
        $data['UploadedUserUID'] = $this->input->post('UserUID');
        $data['UploadedDate'] = date('Y-m-d  H:i:s');
        $torders = $this->apis_model->Gettorders($data);
        $OrderDocs_Path = $torders['OrderDocsPath'];
        $date = date('Ymd');
        if(empty($OrderDocs_Path))
        {
            $query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$date."/".$torders['OrderNumber']."/"."' Where OrderUID=".$data['OrderUID']);
            $OrderDocs_Path = 'uploads/searchdocs/'.$date.'/'.$torders['OrderNumber']."/";
        }

        $upload_path = $OrderDocs_Path;
        $upload_url = FCPATH.'/'.$upload_path; 
        if (isset($_FILES['file'])) 
        {   
         $extension = pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
         $extensionArray = array('png','jpg','docx','doc','pdf');
         $DotPosition =  strripos($_FILES['file']['name'], '.');
         $DocumentName = substr($_FILES['file']['name'], 0, $DotPosition);
         $this->db->select('*');
         $this->db->from('torderdocuments');
         $this->db->where('torderdocuments.OrderUID', $data['OrderUID']);
         $this->db->like('torderdocuments.DocumentFileName', $DocumentName);
         $result=$this->db->get();
         $DocumentCount = $result->num_rows();

         $data['DocumentFileName'] = $_FILES['file']['name'];
         $data['DisplayFileName'] = $_FILES['file']['name'];
         if($DocumentCount>0)
         {
            $data['DocumentFileName'] = $DocumentName . "_" . $DocumentCount . "." . $extension;    
        }
        $data['Position'] = $DocumentCount + 1; 

        $this->common_model->CreateDirectoryToPath($OrderDocs_Path);

        $data['SearchModeUID'] = 5;
        $data['DocumentTypeUID'] = 9;
        $CheckDate= $this->apis_model->CheckSearchDateExists($data['OrderUID']);
        $CheckDate = $CheckDate->CheckDates;
        if ($CheckDate == 0) 
        {
          $data['SearchAsOfDate'] = "0000-00-00 00:00:00";
          $data['SearchFromDate'] = "0000-00-00 00:00:00";
      }
      else
      {
          $result=$this->db->get_where('torderdocuments', array('OrderUID' => $data['OrderUID']));
          $torderdocuments=$result->row();
          $data['SearchAsOfDate']=$torderdocuments->SearchAsOfDate;
          $data['SearchFromDate']=$torderdocuments->SearchFromDate;
      }
      $data['IsReport'] = 0;
      $data['TypeOfDocument'] = "Search";
      $data['DocumentCreatedDate'] = date('Y-m-d H:i:s');

      if(in_array($extension, $extensionArray))
      {

          if($_FILES['file']['size'] < 10485760)
          {

              if(is_uploaded_file($_FILES['file']['tmp_name']))
              {

                  $file_name = $_FILES['file']['name'];
                  $sourcePath = $_FILES['file']['tmp_name'];
                  $destination_path = $upload_url.$data['DocumentFileName'];          
                  try 
                  {

                      if(move_uploaded_file($sourcePath, $destination_path)) 
                      {

                          $result = $this->apis_model->UploadDocuments($data);
                          if($result)
                          {
                              $this->respond("200", "Uploaded Successfully", $file_name);
                          }
                      }                
                  }
                  catch (Exception $e) 
                  {
                      $this->respond("201", $e->getMessage(), null);
                  }            
              }                             
          }else{
           $this->respond("406",'File Size Must Not Exceed 10 MB', null);
       }
   }else{
      $this->respond("406",'File Format Not Supported', null);
  }
}
else 
{
    $this->respond("201", 'File Not Selected', null);
} 
}
else
{
 $this->respond("404", 'Page Not Found', null);
} 
}else{
    $this->respond("400", 'Bad Request', null);
}
}
    //Upload Document

    //Profile Picture
function profilepictureUpload()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];
        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $UserUID = $this->input->post("UserUID");
            $upload_path = 'uploads/avatar/';
            $upload_url = base_url().$upload_path;
            if (!is_dir($upload_url)) 
            {
                mkdir($upload_url, 0777, true);
            }  
            if (isset($_FILES['profilepic'])) 
            {   
                $extension = pathinfo($_FILES['profilepic']['name'],PATHINFO_EXTENSION);
                $extensionArray = array('png','jpg');
                if(in_array($extension, $extensionArray))
                {
                    if($_FILES['document']['size'] < 10485760)
                    {
                        if(is_uploaded_file($_FILES['profilepic']['tmp_name']))
                        {
                            $file_name = $_FILES['profilepic']['name'];
                            $sourcePath = $_FILES['profilepic']['tmp_name'];
                            $destination_path = $upload_url.$UserUID.'.'.$extension;          
                            try 
                            {
                                if(move_uploaded_file($sourcePath, $destination_path)) 
                                {
                                    if($this->apis_model->UpdateProfileImage($UserUID,$destination_path))
                                    {
                                        $this->respond("200", "Uploaded Successfully", $file_name);
                                    }
                                }                
                            }
                            catch (Exception $e) 
                            {
                                $this->respond("201", $e->getMessage(), null);
                            }            
                        }   
                    }else{
                     $this->respond("406",'File Size Must Not Exceed 10 MB', null);
                 }
             }else{
                $this->respond("406",'File format Not Acceptable', null);
            }

        }
        else 
        {
            $this->respond("201", 'File Not Selected', null);
        } 
    }
    else
    {
     $this->respond("404", 'Page Not Found', null);
 } 
}else{
    $this->respond("400", 'Bad Request', null);
}
}
    //Profile Picture

    //Forgot Password
function checkloginexist()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $params = json_decode(file_get_contents('php://input'), TRUE);
            //Login ID or Email 
        $loginid = $params['loginid'];
        $result = $this->apis_model->checkloginexist($loginid);
        
        if($result)
        {
            foreach($result as $data)
            {
                $Email = $data->UserEmailID;
            }
            $DynamicAccessCode = random_string('numeric', 8);
            $this->apis_model->SaveDynamicAccessCode($Email,$DynamicAccessCode);
            $this->ForgetPasswordVerification($Email,$DynamicAccessCode,$loginid);
        }
        else
        {
         $this->respond("401", 'Login or Email does not exist', null);
     }
 }
 else
 {
     $this->respond("400", 'Bad Request', null);
 }   
}

function ForgetPasswordVerification($Email,$DynamicAccessCode,$loginid)
{
    $UserName = $this->apis_model->GetUserName($loginid);
    $email = $this->load->library('email');
    $email->from('notifications@direct2title.com')
    ->to($Email)
    ->subject('Your Dynamic Access Code')
    ->message('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> <html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> </head> <body> <div class="row" style="border:2px solid #ccc;width:750px;margin:0 auto;"> <div class="row" > <p style="background-color:#f5f3f3;color:#808080;text-align:center;line-height:50px;font-size:20px;margin:10px;"><img src="https://staging.direct2title.com/assets/img/logo.png" alt="logo" class="logo-img" style="margin-top: 15px;margin-bottom: -10px;width: 21%;"></p></div><br/> <table style="max-width: 620px; margin: 0 auto;font-size:15px;line-height:22px;"> <tbody> <tr style="height: 23px;"> <td style="height: 23px;"><span style="font-weight: bold;">Hi '.$loginid.',</span></td></tr><tr style="height: 43px;"> <td style="height: 43px;">You recently requested to reset your password for your Direct2Title Account- Click the button below to reset it.<strong> Your Access Code is '.$DynamicAccessCode.'.</strong></td></tr><tr style="height: 29px;"> <td style="text-align: center; height: 29px;"><a href="https://staging.direct2title.com/users/updatepassword" style="background-color: red; color: #fff; display: inline-block; padding: 10px 10px 10px 10px; font-weight: bold; border-radius: 5px; text-align: center;font-size: 12px;text-decoration:none;border:px solid #FFFFFF; -webkit-border-radius: px; -moz-border-radius: px;border-radius: px;width:px;font-size:px;font-family:arial, helvetica, sans-serif; padding: 5px 10px 5px 10px;margin: 7px 0; text-decoration:none; display:inline-block; color: #FFFFFF;background-color: #ff9a9a; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff9a9a), to(#ff4040));background-image: -webkit-linear-gradient(top, #ff9a9a, #ff4040);background-image: -moz-linear-gradient(top, #ff9a9a, #ff4040);background-image: -ms-linear-gradient(top, #ff9a9a, #ff4040);background-image: -o-linear-gradient(top, #ff9a9a, #ff4040);background-image: linear-gradient(to bottom, #ff9a9a, #ff4040);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff9a9a, endColorstr=#ff4040);" class="btn button_example"><span style="padding:10px,10px,10px,10px;">Reset Your Password</style></a></td></tr><tr style="height: 43px;"> <td style="height: 43px;">If you did not request a password reset. please ignore this email or reply to let us know. This password reset is only valid for the next 30 minutes.</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 23px;"> <td style="height: 23px;">Thanks,</td></tr><tr style="height: 23px;"> <td style="height: 23px;">Direct2Title Team</td></tr><tr style="height: 23px;"> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"><span style="font-weight: bold;">P.S.</span> We also love hearing from you and helping you with any issues you have. Please reply to this email if you want to ask a question or just say hi.</td></tr><tr style="height: 23px;"> <td style="height: 23px;border-bottom: 1px solid #ccc;"></td></tr><tr style="height: 23px; "> <td style="height: 23px;"></td></tr><tr style="height: 43px;"> <td style="height: 43px;"> If you are having trouble clicking the password reset button, copy and paste the URL below into your web browser and click forgot password link.</td></tr><tr style="height: 43px;"> <td style="height: 43px;font-size: 12px;text-decoration: underline;"> <a href="https://staging.direct2title.com"> https://staging.direct2title.com</a></td></tr></tbody> </table> <div class="row" style="margin:10px,10px,10px,10px;margin-left:10px;margin-right:10px;"> <p style="padding:10px,10px,10px,10px;background-color:#f5f3f3;color:#907f7f;text-align:center;line-height:50px"><strong> Direct2Title Team. All Rights Reserved.</strong></p></div></div></body> </html> <style type="text/css"> #main{max-width: 600px; margin: 0 auto;}.button_example{border:px solid #FFFFFF; -webkit-border-radius: px; -moz-border-radius: px;border-radius: px;width:px;font-size:px;font-family:arial, helvetica, sans-serif; padding: 10px 10px 10px 10px; text-decoration:none; display:inline-block; color: #FFFFFF; background-color: #ff9a9a; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff9a9a), to(#ff4040)); background-image: -webkit-linear-gradient(top, #ff9a9a, #ff4040); background-image: -moz-linear-gradient(top, #ff9a9a, #ff4040); background-image: -ms-linear-gradient(top, #ff9a9a, #ff4040); background-image: -o-linear-gradient(top, #ff9a9a, #ff4040); background-image: linear-gradient(to bottom, #ff9a9a, #ff4040);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff9a9a, endColorstr=#ff4040);}.button_example:hover{border:px solid #FFFFFF; background-color: #ff6767; background-image: -webkit-gradient(linear, left top, left bottom, from(#ff6767), to(#ff0d0d)); background-image: -webkit-linear-gradient(top, #ff6767, #ff0d0d); background-image: -moz-linear-gradient(top, #ff6767, #ff0d0d); background-image: -ms-linear-gradient(top, #ff6767, #ff0d0d); background-image: -o-linear-gradient(top, #ff6767, #ff0d0d); background-image: linear-gradient(to bottom, #ff6767, #ff0d0d);filter:progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=#ff6767, endColorstr=#ff0d0d);}</style>');
    if(!$email->send())
    {
        $this->respond("201", $email->print_debugger(), null);
    }
    else
    {
        $this->respond("200", 'Check Your Mail To reset Your Password', null);
    }
}
    //Forgot Password
    //Update Password
function updatePassword()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $params = json_decode(file_get_contents('php://input'), TRUE);
        $accesscode = $params['accesscode'];
        $result = $this->apis_model->CheckAccessCode($accesscode);
        if($result)
        {
            $password = $params['password'];
            $cpassword = md5($params['cpassword']);
            if($password == $params['cpassword'])
            {
                if (strlen($password) > 8 && preg_match('/[0-9]/', $password)
                  && preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password))
                {
                    $result = $this->apis_model->updatePassword($accesscode,$cpassword);
                    if($result){
                        $this->respond("200", 'Password Updated Successfully', null);
                    }
                    else{
                        $this->respond("406", 'Error While Updating Password', null);
                    } 
                } 
                else {
                    $this->respond("202", 'Password must contain Uppercase, Lowercase and Numeric !!', null);
                }
            }else{
             $this->respond("202", 'Confirm Password field does not match', null);
         }
     }
     else{
         $this->respond("401", 'Entered Access Code is Wrong', null);
     }
 }
 else{
    $this->respond("400", 'Bad Request', null);
}
}
    //Update Password 
    //Change Password
function changePassword()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {

      $params = json_decode(file_get_contents('php://input'), TRUE);
      $AuthenticationKey = $params['AuthenticationKey'];
      if(!empty($AuthenticationKey))
      {
          $UserUID = $params['UserUID'];
          $oldpassword = $params['oldpassword'];
          $result = $this->apis_model->CheckOldPassword($oldpassword,$UserUID);
          if($result)
          {
            $cpassword = md5($params['cpassword']); 
            $password = $params['password'];
            if($password == $params['cpassword'])
            {
                if(strlen($password) > 8 && preg_match('/[0-9]/', $password) && preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password))
                {
                    $result = $this->apis_model->changePassword($UserUID,$cpassword);
                    if($result)
                    {
                        $this->respond("200", 'Password Changed Successfully', null);
                    }
                    else{
                        $this->respond("406", 'Error While Updating Password', null);
                    }                           
                } 
                else {
                    $this->respond("202", 'Password must contain Uppercase, Lowercase and Numeric !!', null);
                }
            }else{
                $this->respond("202", 'Confirm Password field does not match', null);
            }
        }
        else{
            $this->respond("401", 'Old Password is Incorrect', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}
    //Change Password
    //Profile Information Update
function updateProfileInformation()
{
   if($this->input->server('REQUEST_METHOD') === 'POST')
   {
    $params = json_decode(file_get_contents('php://input'), TRUE);
    $AuthenticationKey = $params['AuthenticationKey'];
    if(!empty($AuthenticationKey))
    {
      $UserUID = $params['UserUID'];
      $UserName = $params['UserName'];
      $LoginID = $params['LoginID'];
      $EmployeeUID = $params['EmployeeUID'];
      $UserEmailID = $params['UserEmailID'];
      $UserContactNo = $params['UserContactNo'];
      $UserFaxNo = $params['UserFaxNo'];
      if($this->apis_model->Checkloginidexist($LoginID,$UserUID)){

         $this->respond("401", 'Login already exist', null);
     }
     else
     {
        $Profile = array('UserName' => $UserName, 'LoginID' => $LoginID, 'EmployeeUID' => $EmployeeUID,'UserEmailID' => $UserEmailID,'UserContactNo' =>$UserContactNo,'UserFaxNo' => $UserFaxNo,'UserUID' => $UserUID);
        if($this->apis_model->UpdateProfileInformation($Profile))
        {
            $this->respond("200", 'Profile Updated Successfully', null); 
        }
        else
        {
            $this->respond("406", 'Error While Updating Information', null);
        }
    }  
}else{
    $this->respond("404", 'Page Not Found', null);
}
}
else
{
    $this->respond("400", 'Bad Request', null);
}
}
    //Profile Information Update
    //Get Profile Information


function GetProfileInformation()
{
   if($this->input->server('REQUEST_METHOD') === 'POST')
   {

     $params = json_decode(file_get_contents('php://input'), TRUE);

     $AuthenticationKey = $params['AuthenticationKey'];

     if($this->apis_model->CheckAuthKey($AuthenticationKey))
     {
      $UserUID = $params['UserUID'];

      echo json_encode($this->apis_model->GetProfileInformation($UserUID));

  }else{
    $this->respond("404", 'Page Not Found', null);
}
}
else
{
    $this->respond("400", 'Bad Request', null);
}
}




function OrderComplete()
{
   if($this->input->server('REQUEST_METHOD') === 'POST')
   {
    $params = json_decode(file_get_contents('php://input'), TRUE);
    $AuthenticationKey = $params['AuthenticationKey'];
    if($this->apis_model->CheckAuthKey($AuthenticationKey))
    {
      $UserUID = $params['UserUID'];
      $OrderUID = $params['OrderUID'];
      if($this->apis_model->OrderComplete($UserUID,$OrderUID))
      {
        $this->respond("200", 'Signing Completed', null);
    }else{

        $this->respond("406", 'Error While Completing Signing', null);
    }

}else{
 $this->respond("404", 'Page Not Found', null);
}
}
else{
    $this->respond("400", 'Bad Request', null);
}
}


   // public function getDocumentOrderUID($MainOrderUID)
   //  {
   //      return $this->db->select('AbstractorOrderUID')->from('torderabstractor')->where('OrderUID',$MainOrderUID)->get()->row()->AbstractorOrderUID;
   //  }

function GetAbstractorDocuments()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];
        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $AbstractorOrderUID = $params['OrderUID'];
            $OrderUID = $this->getOrderUID($AbstractorOrderUID); 
            $UserUID = $params['UserUID'];
            $RoleType = $params['RoleType'];
            $MainOrderUID = $params['MainOrderUID'];
            $DocumentOrderUID =$this->apis_model->getDocumentOrderUID($MainOrderUID);
            $AbstractorMainUID =$DocumentOrderUID->AbstractorOrderUID;

            $filterDocument='';

            if(!empty($MainOrderUID)){
                $filterDocument=$MainOrderUID;

            }
            elseif(!empty($AbstractorOrderUID)){
                $DocumentOrderUID =$this->apis_model->getDocumentOrderUID($AbstractorOrderUID);
                $AbstractorMainUID =$DocumentOrderUID->OrderUID;
                $filterDocument= $AbstractorMainUID;

            }
            
            $result =$this->apis_model->GetAbstractorDocuments($filterDocument);

            if($result)
            {
             echo json_encode($result);
         }
         else
         {

            $this->respond("200", 'No Documents Found', null);
        }

    }else{
       $this->respond("404", 'Page Not Found', null);
   }

}else{
    $this->respond("400", 'Bad Request', null);
}
}

public function getNotesOrderUID($AbstractorOrderUID)
{

    return $this->db->select('OrderUID')->from('torderabstractor')->where('AbstractorOrderUID',$AbstractorOrderUID)->get()->row()->OrderUID;
}


function GetOrderInfoNotes()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $AbstractorOrderUID = $params['OrderUID'];
            $UserUID = $params['UserUID'];
            $MainOrderUID = $params['MainOrderUID'];
            $OrderUID = $this->getNotesOrderUID($AbstractorOrderUID);


                // $AbstractorUID = $params['AbstractorUID'];

            if(!empty($AbstractorOrderUID)){
                echo json_encode($this->apis_model->GetOrderInfoNotes($OrderUID));
            }
            else{
               echo json_encode($this->apis_model->GetOrderInfoNotes($MainOrderUID));
           }    

       }else{
         $this->respond("404", 'Page Not Found', null);
     }

 }else{
    $this->respond("400", 'Bad Request', null);
}
}


    // public function getOrderUID($MainOrderUID)
    // {
    //     return $this->db->select('AbstractorOrderUID')->from('torderabstractor')->where('OrderUID',$MainOrderUID)->get()->row()->AbstractorOrderUID;
    // }

function SaveAbstractorNotes()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $AbstractorOrderUID = $params['OrderUID'];
            $MainOrderUID = $params['MainOrderUID'];
            $OrderUID = $this->getOrderUID($AbstractorOrderUID);
            $SectionUID = $params['SectionUID'];
            $SystemNotesUID = $params['SystemNotesUID'];
            $Note = $params['Comments'];
            $RoleType = 15;
            $CreatedByUserUID = $params['UserUID'];
            $CreatedByAPI = $params['CreatedByAPI'];
            $AbstractorUID = $params['AbstractorUID'];
            $CreatedOn = Date('Y-m-d H:i:s',strtotime("now"));
            if(!empty($OrderUID)){
                $NotesArray = array('OrderUID' => $OrderUID,'Note' => $Note,'SectionUID' => $SectionUID,'SystemNotesUID' => $SystemNotesUID,'RoleType' => $RoleType,'AbstractorUID' => $AbstractorUID,'CreatedByUserUID' => $CreatedByUserUID,'CreatedOn' => $CreatedOn,'CreatedByAPI' => $CreatedByAPI );
            }
            else{
                $NotesArray = array('OrderUID' => $MainOrderUID,'Note' => $Note,'SectionUID' => $SectionUID,'SystemNotesUID' => $SystemNotesUID,'RoleType' => $RoleType,'AbstractorUID' => $AbstractorUID,'CreatedByUserUID' => $CreatedByUserUID,'CreatedOn' => $CreatedOn,'CreatedByAPI' => $CreatedByAPI );     
            }
            if($this->apis_model->SaveAbstractorInfoNotes($NotesArray))
            {
                $this->respond("200", 'Notes Added', null);
            }
            else
            {
                $this->respond("406", 'Error While Adding Notes', null);
            }

        }else{
         $this->respond("404", 'Page Not Found', null);
     }

 }else{
    $this->respond("400", 'Bad Request', null);
}
}


public function OrderShippingDetails()
{
    $params = json_decode(file_get_contents('php://input'), TRUE);
    $AuthenticationKey = $params['AuthenticationKey'];
    if($this->apis_model->CheckAuthKey($AuthenticationKey))
    {
        $ShippingUID = $params['ShippingUID'];
        $Details = $this->db->select('*')->from('tOrderShipping')->where('ShippingUID',$ShippingUID)->get()->row();
        if($Details){
            echo json_encode($Details);
        }else{
            $this->respond("200", 'No Shipping Details Found', null);
        }
    }else{
        $this->respond("400", 'Bad Request', null);
    }

}

function respond($status, $status_message, $data)
{
    $response['status'] = $status;
    $response['message'] = $status_message;
    $response['data'] = $data;
    echo json_encode($response);
}


public function uploadProfile()
{

        // if($this->input->server('REQUEST_METHOD') === 'POST')
        // {

    $params = json_decode(file_get_contents('php://input'), TRUE);

    $UserUID = $params['UserUID'];
    $image = $params['profilepic'];

    $image=explode('base64,', $image);
    $image = base64_decode($image[1]);

    $imageName =  $UserUID;

    $path = "uploads/avatar/".$imageName.".jpg";
    file_put_contents($path,$image);
    $this->apis_model->UpdateProfileImage($UserUID,$path);
    $dataDB['status'] = 'success';       
    $dataDB['filename'] = $path;
        // }
    $this->respond( "200",$dataDB,'');

}

public function getOrderUID($AbstractorOrderUID)
{
    return $this->db->select('OrderUID')->from('torderabstractor')->where('AbstractorOrderUID',$AbstractorOrderUID)->get()->row()->OrderUID;
}

function getCurrentStatus($OrderUID, $OrderStatus)
{
    $Statuses = [
        0 => (Object)['StatusName'=>'New', 'StatusColor'=>'#03A9F4'],
        1 => (Object)['StatusName'=>'New', 'StatusColor'=>'#03A9F4'],
        2 => (Object)['StatusName'=>'New', 'StatusColor'=>'#03A9F4'],
        3 => (Object)['StatusName'=>'Work In Progress', 'StatusColor'=>'#0259e8'],
        5 => (Object)['StatusName'=>'Signing Completed', 'StatusColor'=>'#0ca931'],

    ];

    if (is_null($OrderStatus)) 
    {
        return $Statuses[0];
    }


    return $Statuses[$OrderStatus];


}

    /**
        *@description Function to getOrderDetails
        *
        * @param $query
        * 
        * @throws no exception
        * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
        * @return JSON Output <string> 
        * @since 26/6/2020 
        * @version Closign API Search Functionality 
        *
    */ 
    function getOrderDetails(){
        if($this->input->server('REQUEST_METHOD') === 'POST'){

            $params = json_decode(file_get_contents('php://input'), TRUE);
            $AuthenticationKey = $params['AuthenticationKey'];

            /*Authenticated*/
            if($this->apis_model->CheckAuthKey($AuthenticationKey))
            {
                $post = [];
                $OrderUID = $params['OrderUID'];
                $AbstractorUID = $params['AbstractorUID'];
                $RoleType = $params['RoleType'];
                $post['search_value'] = $params['search_value'];

                $Getpost =$post['search_value'];
                $GetOrderNumber =$this->apis_model->GetOrderDetailsByOrderNumber($Getpost);
                $GetAbstractorUID =$GetOrderNumber->OrderUID;


                if (empty($post['search_value']) || empty($RoleType) ) {
                    $this->respond("400", 'Bad Request', null);exit;
                }

                $post['column_search'] = array('CustomerName', 'msubproducts.SubProductName','LoanNumber','OrderNumber','PRName','PropertyAddress1','tOrderClosing.SigningStateCode','DATE_FORMAT(tOrderSign.SignedDateTime,"%m/%d/%Y %H:%i:%s")','DATE_FORMAT(torders.OrderEntryDatetime, "%m/%d/%Y %H:%i:%s")','DATE_FORMAT(torders.OrderDueDatetime, "%m/%d/%Y %H:%i:%s")');


                
                if ($RoleType == "Vendor")
                {
                  $RoleType = "Vendor";
                  $post['AbstractorUID'] = $AbstractorUID;
                  $result = $this->apis_model->GetOrdersForQuery($post, $AbstractorUID);                  
                  foreach ($result as $key => $value) {

                    $ClosingQueue = $this->getCurrentStatus($value['OrderUID'], $value['OrderStatus']);
                    $result[$key]['StatusName'] = $ClosingQueue->StatusName;
                    $result[$key]['StatusColor']= $ClosingQueue->StatusColor;

                }
                $this->output->set_content_type('application/json');
                $this->output->set_output(json_encode($result))->_display();exit;
                /*Todo*/
            }
            else
            {  
                $RoleType = "Organization";
                $result = $this->apis_model->GetOrdersForQuery($post); 

                if(!empty($result)) {           
                    foreach ($result as $key => $value) {
                        $ClosingQueue = $this->getCurrentStatus($value['OrderUID'], $value['OrderStatus']);
                        $result[$key]['StatusName'] = $ClosingQueue->StatusName;
                        $result[$key]['StatusColor']= $ClosingQueue->StatusColor;

                    }
                }
                else{
                   $post['column_search'] = array('CustomerName', 'msubproducts.SubProductName','LoanNumber','OrderNumber','PropertyAddress1','DATE_FORMAT(torders.OrderEntryDatetime, "%m/%d/%Y %H:%i:%s")','DATE_FORMAT(torders.OrderDueDatetime, "%m/%d/%Y %H:%i:%s")');

                   $result =$this->apis_model->GetOrderDetaillsByNew($GetAbstractorUID,$post);
               }

               $this->output->set_content_type('application/json');
               $this->output->set_output(json_encode($result))->_display();exit;

           }
       }
       else{
        $this->respond("403", 'Not Authorized', null);
    }

}
}








    //@DEC: CANCEL ORDER @AUTH: shruti.vs@avanzegroup.com 
function cancelorder()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $UserUID = $params['UserUID'];

            $musers = $this->common_model->get_row('musers', ['AuthKey'=>$AuthenticationKey]);
            if (!empty($musers) ) {
                $UserUID = $musers->UserUID;
            }
            $orderUID = $params['OrderUID'];

             //   $rejection=array('Remarks'=>$params['remarkstext'],'Reason'=>$params['Reason'], 'OrderFlag'=>2);

            $result = $this->apis_model->cancelorders($orderUID,$UserUID,$rejection);
            
            if($result)
            {
                $this->respond("200", 'Cancel Order Successfully', null);
            }else{
                $this->respond("406", 'Error While Canceling Order', null);
            }

        }else{
         $this->respond("404", 'Page Not Found', null);
     } 
 }else{
    $this->respond("400", 'Bad Request', null);
}
}

  /**
        *@Description Function to Canceled Order List Process
        *
        * @param $query
        * 
        * @throws no exception
        * @author Shruti <shruti.vs@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Closign API Search Functionality 
        *
    */ 
  function cancelorderlist()
  {

    if($this->input->server('REQUEST_METHOD') === 'POST'){
        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];
        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        {
            $AbstractorUID = $params['AbstractorUID'];

            $musers = $this->common_model->get_row('musers', ['AbstractorUID'=>$AbstractorUID],['AuthKey'=>$AuthenticationKey]);
            if (!empty($musers) ) {                  
                $UserUID = $musers->UserUID;
                $AbstractorUID = $musers->AbstractorUID;
            }

            $result = $this->apis_model->GetCancelOrdersListByUserID($AbstractorUID,$AuthenticationKey);

            if (!empty($result)) {
                echo json_encode($result);
            }else{
                $this->respond("200", 'No Cancel Orders Found', null);
            }


        }else{
            $this->respond("404", 'Page Not Found', null);
        }

    }
    else{
        $this->respond("400", 'Bad Request', null);
    }

}





    //
    /**
     * @Dec: FUNCTION RESCHEDULE
     * @Auth: shruti.vs@avanzegroup.com
     */
    function rescheduleApi()
    {

        if($this->input->server('REQUEST_METHOD') === 'POST')
        {

            $params = json_decode(file_get_contents('php://input'), TRUE);
            $AuthenticationKey = $params['AuthenticationKey'];

            /*Authenticated*/
            if($this->apis_model->CheckAuthKey($AuthenticationKey))
            {

                $AbstractorOrderUID = $params['OrderUID'];

                $UserDetails = $this->apis_model->getLoginDetails($AuthenticationKey);
                if (empty($UserDetails) || empty($UserDetails['AbstractorUID']) ) 
                {
                 $this->respond("400", 'Bad Request', null); exit;
             }
             $AbstractorUID = $UserDetails['AbstractorUID'];
             $this->loggedid = $UserDetails['UserUID'];


             $tOrderSchedule = $this->common_model->get_row('tOrderSchedule', ['AbstractorOrderUID'=>$AbstractorOrderUID]);


             $OrderUID = "";


             if (!empty($tOrderSchedule)) 
             {
                $ScheduleUID = $tOrderSchedule->ScheduleUID;
                $OrderUID = $tOrderSchedule->OrderUID;
            }
            else
            {
                $this->respond("400", 'Invalid Order', null); exit;
            }


            if (!empty($tOrderSchedule)) 
            {
                $ScheduleUID = $tOrderSchedule->ScheduleUID;
            }
            else
            {
                $this->respond("400", 'Invalid Order', null); exit;
            }

            $SigningDateTime = $params['SigningDateTime'];
            $SigningDateTime = date('Y-m-d H:i:s', strtotime($SigningDateTime));

            if (empty($SigningDateTime) && $SigningDateTime < date('Y-m-d H:i:s', strtotime("1970-02-05 00:00:00"))) 
            {
                $this->respond("400", 'Invalid ScheduleDateTime', null); exit;   
            }


            $SigningDuration = $params['SigningDuration'];
            $SignStatus = "Reschedule";

            $mabstractor_check = $this->common_model->get_row('mabstractor', ['AbstractorUID'=>$AbstractorUID]);

            $torders = $this->common_model->get_row('torders', ['OrderUID'=>$OrderUID]);
            $torderabstractor = $this->common_model->get_row('torderabstractor', ['AbstractorOrderUID'=>$AbstractorOrderUID]);



            if (date('Y-m-d H:i:s', strtotime($tOrderSchedule->SigningDateTime)) !=  $SigningDateTime) {
                $IsSigningDateTimeChanged = true;
            }


            if ($IsSigningDateTimeChanged) {


                $tOrderSigning = array(

                    "OrderUID"=>$OrderUID,
                    'AbstractorUID' => !empty($AbstractorUID) ? $AbstractorUID : NULL,
                    'ScheduleUID' => $ScheduleUID,
                    "OrderTypeUID"=>$torderabstractor->OrderTypeUID,
                    'AbstractorOrderUID'=>$AbstractorOrderUID,
                    "SigningStatus"=>'Reschedule',
                    "ModifiedUserUID"=>$this->loggedid,
                    "SignedDateTime"=> $SigningDateTime,
                );
                /*Save tOrderSigning*/
                $this->common_model->save('tOrderSign', $tOrderSigning, ['ScheduleUID'=>$ScheduleUID, 'OrderUID'=>$OrderUID]);


                if ($IsSigningDateTimeChanged) {

                    $tOrderSchedule_update['SigningDateTime'] = $SigningDateTime;
                    $tOrderSchedule_update['SigningDuration'] = $post['SigningDuration'];
                }


                $tOrderSchedule_update['OrderUID'] = $OrderUID;
                $tOrderSchedule_update['IsPackageSentToAttorney'] = $tOrderSchedule_old->IsPackageSentToAttorney;
                $tOrderSchedule_update['ScheduleStatus'] = $tOrderSchedule_old->ScheduleStatus;
                $tOrderSchedule_update['AssignedDateTime'] = $tOrderSchedule_old->AssignedDateTime;
                $tOrderSchedule_update['AssignedByUserUID'] = $tOrderSchedule_old->AssignedByUserUID;
                $tOrderSchedule_update['SigningType'] = $tOrderSchedule_old->SigningType;
                $tOrderSchedule_update['AllowOverride'] = $tOrderSchedule_old->AllowOverride;
                $tOrderSchedule_update['TimeZoneUID'] = $tOrderSchedule_old->TimeZoneUID;

                /* Save in tOrderSchedule*/
                $ScheduleUIDUpdated = $this->common_model->save('tOrderSchedule', $tOrderSchedule_update, ['ScheduleUID'=>$ScheduleUID]);



                $this->load->model('title_closing/Closing_model');


                $Borrowers = $this->apis_model->getScheduleBorrowers($ScheduleUID);


                $ContactList = [];
                foreach ($Borrowers as $key => $Id) {
                    $BorrowerUID = $Id->Id;
                    $GetBorrowerDetails = $this->apis_model->GetOrderPropertyRolesByID($BorrowerUID);
                    $ContactList[] = $GetBorrowerDetails->PRName;
                }
                $BorrowerNames = implode(', ', $ContactList);

                $ReScheduleInfo['SigningTime'] = $SigningTime;
                $ReScheduleInfo['SigningDate'] = $SigningDate;
                $ReScheduleInfo['BorrowerName'] = $BorrowerNames;
                $ReScheduleInfo['ScheduleUID'] = $ScheduleUID;
                $ReScheduleInfo['ContactList'] = $mabstractor;

                $this->load->model('title_closing/Closing_model');
                $Event_310 = $this->Closing_model->SendEventstoAPI($OrderUID,'310',$ReScheduleInfo);
            }

            /* End -- API Event - Closing -- End */


            $this->respond("200", 'Order Reschedule Successfully', null);
        }
        else{
            $this->respond("404", 'Page Not Found', null);
        } 
    }else{
        $this->respond("400", 'Bad Request', null);
    }
}

     /**
        *@Description Function to Cancel Sigining process
        *
        * @param $query
        * 
        * @throws no exception
        * @author Shruti <shruti.vs@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Closign API Search Functionality 
        *
    */ 

     function cancelsigningprocess()
     {
        if($this->input->server('REQUEST_METHOD') === 'POST')
        {

            $params = json_decode(file_get_contents('php://input'), TRUE);
            $AuthenticationKey = $params['AuthenticationKey'];

            /*Authenticated*/
            if($this->apis_model->CheckAuthKey($AuthenticationKey))
            {

                $OrderUID = $params['OrderUID'];
                $AbstractorUID = $params['AbstractorUID'];


                $musers = $this->common_model->get_row('musers',['AbstractorUID'=>$AbstractorUID], ['AuthKey'=>$AuthenticationKey]);
                if (!empty($musers) ) {
                 $UserUID = $musers->UserUID;
                 $AbstractorUID = $musers->AbstractorUID;
             }


             $rejection=array('Reason'=>$params['Reason'], 'OrderFlag'=>2);

             $result = $this->apis_model->cancelSigningProcess($OrderUID,$UserUID,$rejection);

             $this->respond("200", 'Cancel Signing Process Successfully', null);

         }
         else{
            $this->respond("404", 'Page Not Found', null);
        } 
    }else{
        $this->respond("400", 'Bad Request', null);
    }
}

function APIScheduleSigningEvent($OrderUID, $ScheduleUID, $vendorindividualcontactuid, $IsEditContact = false)
{   
    $this->load->model('title_closing/Closing_model');
    $ScheduleDetails = $this->Schedule_model->getSchedulingDetailsByScheduleUID($ScheduleUID);
    $mabstractor = $this->common_model->get_row('mabstractor', ['AbstractorUID'=>$ScheduleDetails->AbstractorUID]);

    $SigningDate = date('Y-m-d', strtotime($ScheduleDetails->SigningDateTime));
    $SigningTime = date('h:i A', strtotime($ScheduleDetails->SigningDateTime));
    $ScheduleBorrowers = $this->apis_model->getScheduleBorrowers($ScheduleUID);
    $Borrowers =[];

    foreach ($ScheduleBorrowers as $key => $borrower) {
        $Borrowers[] = $borrower->Id;
    }

    foreach ($Borrowers as $key => $Id) {
        if (!empty($Id)) {
            $BorrowerUID = trim($Id);
            $GetBorrowerDetails = $this->Schedule_model->GetOrderPropertyRolesByID($BorrowerUID);
            $ContactList[] = $GetBorrowerDetails->PRName;
        }
    }

    $ClosingInsertData = (array)$this->common_model->get_row('tOrderClosing', ['ScheduleUID'=> $ScheduleUID]);
    $BorrowerNames = implode(', ', $ContactList);
    $GetSigningAddressByBorrrowerID = $this->Schedule_model->getSigningAddress($OrderUID,$Borrowers);
    $GetSigningAddressByBorrrowerID['SigningDate'] = $SigningDate;
    $GetSigningAddressByBorrrowerID['SigningTime'] = $SigningTime;
    $GetSigningAddressByBorrrowerID['ContactList'] = $mabstractor;
    $GetSigningAddressByBorrrowerID['BorrowerName'] = $BorrowerNames;
    $GetSigningAddressByBorrrowerID['Address1'] = $ClosingInsertData['SigningAddress1'];
    $GetSigningAddressByBorrrowerID['Address2'] = $ClosingInsertData['SigningAddress2'];
    $GetSigningAddressByBorrrowerID['ZipCode'] = $ClosingInsertData['SigningZipCode'];
    $GetSigningAddressByBorrrowerID['CityName'] = $ClosingInsertData['SigningCityName'];
    $GetSigningAddressByBorrrowerID['CountyName'] = $ClosingInsertData['SigningCountyName'];
    $GetSigningAddressByBorrrowerID['StateCode']= $ClosingInsertData['SigningStateCode'];
    $GetSigningAddressByBorrrowerID['ScheduleUID']= $ScheduleUID;
    $mabstractor->ScheduleUID = $ScheduleUID;


    $mabstractorcontact = $this->common_model->get_row('mabstractorcontact', ['AbstractorContactUID'=>$vendorindividualcontactuid]);
    if (!empty( $mabstractorcontact )){

        $NotaryDetails = array(
            'NotaryName' => $mabstractorcontact->ContactName,
            'NotaryEmail' =>  $mabstractorcontact->Email,
            'NotaryPhoneNumber' => $mabstractorcontact->OfficeNo,
            'NotaryAddress1' => '',
            'NotaryZipcode' => '',
            'NotaryCity' => '',
            'NotaryStateCode' => '',
            'NotaryCountyName' => '',
        );

        if (!$IsEditContact){
            $realec_mabstractor->AbstractorFirstName  = $NotaryDetails['NotaryName'];
            $realec_mabstractor->OfficePhoneNo  = $NotaryDetails['NotaryPhoneNumber'];
            $realec_mabstractor->CarPhoneNo  = '';
            $this->load->model('title_closing/Closing_model');
            $Event_301 = $this->Closing_model->SendEventstoAPI($OrderUID,'301',$realec_mabstractor);
            if($Event_301) {
                $Event_303 = $this->Closing_model->SendEventstoAPI($OrderUID,'303',$GetSigningAddressByBorrrowerID);
            }
        }

        $closing_key_data['SigningDate'] = $GetSigningAddressByBorrrowerID['SigningDate'];
        $closing_key_data['SigningTime'] = $GetSigningAddressByBorrrowerID['SigningTime'];
        $closing_key_data['ContactList'] = $GetSigningAddressByBorrrowerID['ContactList'];
        $closing_key_data['BorrowerName'] = $GetSigningAddressByBorrrowerID['BorrowerName'];
        $closing_key_data['SigningAddress1'] = $GetSigningAddressByBorrrowerID['Address1'];
        $closing_key_data['SigningAddress2'] = $GetSigningAddressByBorrrowerID['Address2'];
        $closing_key_data['SigningZipCode'] = $GetSigningAddressByBorrrowerID['ZipCode'];
        $closing_key_data['SigningCityName'] = $GetSigningAddressByBorrrowerID['CityName'];
        $closing_key_data['SigningCountyName'] = $GetSigningAddressByBorrrowerID['CountyName'];
        $closing_key_data['SigningStateCode'] = $GetSigningAddressByBorrrowerID['StateCode'];
        $closing_key_data['NotaryDetails'] = $NotaryDetails;
        $closing_key_data['OrderUID'] = $OrderUID;
        $closing_key_data['ScheduleUID'] = $ScheduleUID;
        $closing_key_data['BorrowerUID'] = $Borrowers[0];
        $closing_key_data['SigningLocationDetails'] = $this->Keystone_model->CheckSigningAddress($closing_key_data);

        $Details = $this->Closing_model->GetInBoundTransactionDetails($OrderUID);
        $TitleApiVersion = $Details->TitleApiVersion;

        $NotaryEmail = '';
        if($NotaryDetails['NotaryEmail']){
            $NotaryEmail = '; Notary Email: '.$NotaryDetails['NotaryEmail'];
        }
        if($TitleApiVersion == '1'){
            $closing_key_data['OrderUID'] = $OrderUID;
            $closing_key_data['EventCode'] = 'ScheduledSigning';
            $closing_key_data['EventComments'] = 'Schedule Confirmed. Notary Name: '.$NotaryDetails['NotaryName'].'; Notary Phone: '.$NotaryDetails['NotaryPhoneNumber'].$NotaryEmail;
            $Key_ScheduleConfirm = $this->Closing_model->SendClosingEventstoAPI($closing_key_data);
        } else if($TitleApiVersion == '2'){
            $closing_key_data['OrderUID'] = $OrderUID;
            $closing_key_data['EventCode'] = 'NotaryAssigned';
            $closing_key_data['EventComments'] = 'Schedule Confirmed. Notary Name: '.$NotaryDetails['NotaryName'].'; Notary Phone: '.$NotaryDetails['NotaryPhoneNumber'].$NotaryEmail;
            $Key_AssignNotary = $this->Closing_model->SendClosingEventstoAPI($closing_key_data);        
            if($Key_AssignNotary) {
                $closing_key_data['EventCode'] = '';
                $closing_key_data['EventCode'] = 'ScheduleConfirmed';
                if ($IsEditContact == true) 
                {
                    $closing_key_data['EventCode'] = 'RescheduleConfirmed';                 
                }
                $closing_key_data['EventComments'] = 'Schedule Confirmed. Notary Name: '.$NotaryDetails['NotaryName'].'; Notary Phone: '.$NotaryDetails['NotaryPhoneNumber'].$NotaryEmail;
                $Key_ScheduleConfirm = $this->Closing_model->SendClosingEventstoAPI($closing_key_data);
            }
        }
    }

    /* Update borrower signing details  */
                    /*$tApiOrderSchedule = array(
                        'OrderUID' => $OrderUID,
                        'ClosingAddress1'    => $closing_key_data['SigningAddress1'],
                        'ClosingAddress2'    => $closing_key_data['SigningAddress2'],
                        'ClosingCityName'    => $closing_key_data['SigningCityName'],
                        'ClosingCountyName'  => $closing_key_data['SigningCountyName'],
                        'ClosingStateCode'   => $closing_key_data['SigningStateCode'],
                        'ClosingZipcode'     => $closing_key_data['SigningZipCode'],
                        'SpecialInstruction' => (!empty($post['SigningSpecialInstruction']) ? $post['SigningSpecialInstruction'] : ''),
                    );

                    $Borrowers = $this->Schedule_model->getScheduleBorrowers($ScheduleUID);
                    foreach ($Borrowers as $key => $Id) {
                        $BorrowerUID = $Id->Id;
                        $this->Keystone_model->UpdateSigningDetails($OrderUID,$tApiOrderSchedule,$BorrowerUID);
                    }*/
                    /* End Update borrower signing details  */

                    /*========= Keystone Assign Schedule ============ */

                    return true;

                }

/**
        *@Description Function to Roletype Based Order Details
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Role Based API Functionality 
        * @since 08/08/2020 
    */ 

function getOrderDetailsByRole()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];
        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $AbstractorUID = $params['AbstractorUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];
 
            
                // $GetOrderDetails = $this->apis_model->GetOrderDetailsByRoleOrderUID($GetOrderUID);
                // print_r(json_encode($GetOrderDetails));exit();

            // if($RoleType == "Organization"){
                if(!empty($OrderUID)){
                    $result=$this->apis_model->GetOrderDetailsByRole($OrderUID);
                }else{
                   $result = $this->apis_model->GetOrderDetailsByRoleOrderUID($GetOrderUID);
               }
           // }

                // if(empty($result)){

                // }

           if (!empty($result)) {
            $ClosingQueue = $this->getCurrentStatus($OrderUID, $result['SummaryDetails']->OrderStatus);
            $result['SummaryDetails']->StatusName = $ClosingQueue->StatusName;
            $result['SummaryDetails']->StatusColor= $ClosingQueue->StatusColor;
            echo json_encode($result);
        }else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}



 /**
        *@Description Function to GetDeed Based Order Details
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Deed Based API Functionality 
        * @since 14/08/2020 
    */ 

 function getOrderDetailsByDeed()
 {
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];

            $OrderDetails = $this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID',$OrderUID)->get()->row();
            $OrderMainUID = $OrderDetails->OrderUID;

            if($RoleType == "Organization"){

                if(!empty($OrderUID)){
                    $result1=$this->apis_model->GetDeedDetailsByOrderUID($OrderMainUID);
                        // echo json_encode($result1);
                    if(empty($result1)){
                       $resultMain = $this->apis_model->GetDeedDetailsByOrderUID($GetOrderUID);
                       $result = $resultMain;
                   }else{
                    $result = $result1;
                }
                echo json_encode($result);
            }
            else{
                $resultMain = $this->apis_model->GetDeedDetailsByOrderUID($GetOrderUID);
                $result = $resultMain;
                echo json_encode($result);

            }

        }
        else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}



 /**
        *@Description Function to GetMortage OrderList Based on Order
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Mortage Based API Functionality 
        * @since 14/08/2020 
    */ 


 function getOrderDetailsByDeedlist()
 {
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];



            if($RoleType == "Organization" && (!empty($OrderUID))){


                $result=$this->apis_model->GetDeedDetailsByID($OrderUID);
                echo json_encode($result);

            }
            else{
                $this->respond("200", 'No Orders Found', null);
            }
        }else{
            $this->respond("404", 'Page Not Found', null);
        }
    }
    else{
        $this->respond("400", 'Bad Request', null);
    }
}


 /**
        *@Description Function to GetDeedOrderList Based on Order
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Deed Based API Functionality 
        * @since 14/08/2020 
    */ 

 function getOrderDetailsByMortgagelist()
 {
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];

                // $OrderDetails = $this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID',$OrderUID)->get()->row();
                // $OrderMainUID = $OrderDetails->OrderUID;

            if($RoleType == "Organization" && (!empty($OrderUID))){

                    // if(!empty($OrderUID)){
                $result=$this->apis_model->GetMortgageDetailsByID($OrderUID);
                echo json_encode($result);
                     // }
                        //else{
                   //     $result = $this->apis_model->GetDeedDetailsByOrderUID($GetOrderUID);
                   // }
            }
            else{
                $this->respond("200", 'No Orders Found', null);
            }
        }else{
            $this->respond("404", 'Page Not Found', null);
        }
    }
    else{
        $this->respond("400", 'Bad Request', null);
    }
}


/**
        *@Description Function to GetMortgageDetails Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Mortgage Based API Functionality 
        * @since 14/08/2020 
    */ 

function getOrderDetailsByMortgage()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];


            $OrderDetails = $this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID',$OrderUID)->get()->row();
            $OrderMainUID = $OrderDetails->OrderUID;

            if($RoleType == "Organization"){

                if(!empty($OrderUID)){
                    $result1=$this->apis_model->GetMortgageDetails($OrderMainUID);
                        // echo json_encode($result1);
                    if(empty($result1)){
                       $resultMain = $this->apis_model->GetMortgageDetails($GetOrderUID);
                       $result = $resultMain;
                   }else{
                    $result = $result1;
                }
                echo json_encode($result);
            }
            else{
                $resultMain = $this->apis_model->GetMortgageDetails($GetOrderUID);
                $result = $resultMain;
                echo json_encode($result);
            }
        }
        else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}


/**
        *@Description Function to GetPropertyRoles Details Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version PropertyRoles Based API Functionality 
        * @since 24/08/2020 
    */ 


function getPropertyRolesDetailsByOrderUID()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];

            $OrderDetails = $this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID',$OrderUID)->get()->row();
            $OrderMainUID = $OrderDetails->OrderUID;

            if($RoleType == "Organization"){

                if(!empty($OrderUID)){
                    $result1=$this->apis_model->GetPropertyRoleDetails($OrderMainUID);
                        // echo json_encode($result1);
                    if(empty($result1)){
                       $resultMain = $this->apis_model->GetPropertyRoleDetails($GetOrderUID);
                       $result = $resultMain;
                   }else{
                    $result = $result1;
                }
                echo json_encode($result);
            }
            else{
                $resultMain = $this->apis_model->GetPropertyRoleDetails($GetOrderUID);
                $result = $resultMain;
                echo json_encode($result);

            }

        }
        else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}

/**
        *@Description Function to GetHistory Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version History Based API Functionality 
        * @since 14/08/2020 
    */ 

function getHistoryByOrderUID()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];



        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];


            $OrderDetails = $this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID',$OrderUID)->get()->row();
            $OrderMainUID = $OrderDetails->OrderUID;

            if($RoleType == "Organization"){

                if(!empty($OrderUID)){
                    $result=$this->apis_model->GetAllAuditdetails($OrderMainUID);
                    echo json_encode($result);
                }else{
                   $result = $this->apis_model->GetAllAuditdetails($GetOrderUID);
                   echo json_encode($result);
               }
           }
           else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}


/**
        *@Description Function to GetJudgementList Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version GetJudgementList Based API Functionality 
        * @since 14/08/2020 
    */ 

function getJudgementDetailsListByOrderUID()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];

            $OrderDetails = $this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID',$OrderUID)->get()->row();
            $OrderMainUID = $OrderDetails->OrderUID;
            $result;
            if($RoleType == "Organization"){

                if(!empty($OrderUID)){
                    $result1=$this->apis_model->GetJudgementListByOrderUID($OrderMainUID);
                        // echo json_encode($result1);
                    if(empty($result1)){

                       $resultMain = $this->apis_model->GetJudgementListByOrderUID($GetOrderUID);
                       $result = $resultMain;
                   }else{
                    $result = $result1;
                }
                echo json_encode($result);
            }
            else{

                $resultMain = $this->apis_model->GetJudgementListByOrderUID($GetOrderUID);
                $result = $resultMain;

                echo json_encode($result);

            }

        }
        else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}


/**
        *@Description Function to GetJudmentDetails Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version JudmentDetails Based API Functionality 
        * @since 14/08/2020 
    */ 

function getJudgementByOrderUID()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];



        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 

            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];



            // $OrderDetails = $this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID',$OrderUID)->get()->row();
            // $OrderMainUID = $OrderDetails->OrderUID;


            if($RoleType == "Organization"){

                if(!empty($OrderUID)){
                    $result=$this->apis_model->getJudgementDetailsByOrderUID($OrderMainUID);
                    echo json_encode($result);
                }else{
                   $result = $this->apis_model->getJudgementDetailsByOrderUID($GetOrderUID);
                   echo json_encode($result);
               }
           }
           else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}


/**
        *@Description Function to GetLinesList Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version GetLinesList Based API Functionality 
        * @since 14/08/2020 
    */ 

function getLiensDetailsListByOrderUID()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];

            $OrderDetails = $this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID',$OrderUID)->get()->row();
            $OrderMainUID = $OrderDetails->OrderUID;
            $result;
            if($RoleType == "Organization"){

                if(!empty($OrderUID)){
                    $result1=$this->apis_model->GetLinesListByOrderUID($OrderMainUID);
                        // echo json_encode($result1);
                    if(empty($result1)){

                       $resultMain = $this->apis_model->GetLinesListByOrderUID($GetOrderUID);
                       $result = $resultMain;
                   }else{
                    $result = $result1;
                }
                echo json_encode($result);
            }
            else{

                $resultMain = $this->apis_model->GetLinesListByOrderUID($GetOrderUID);
                $result = $resultMain;
                echo json_encode($result);

            }

        }
        else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}

/**
        *@Description Function to GetLinesList Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version GetLinesList Based API Functionality 
        * @since 14/08/2020 
    */ 

function GetLinesDetailsByID()
{
    if($this->input->server('REQUEST_METHOD') === 'POST'){

        $params = json_decode(file_get_contents('php://input'), TRUE);
        $AuthenticationKey = $params['AuthenticationKey'];

        if($this->apis_model->CheckAuthKey($AuthenticationKey))
        { 
            $OrderUID = $params['OrderUID'];
            $RoleType = $params['RoleType'];
            $GetOrderUID = $params['MainOrderUID'];


            // $OrderDetails = $this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID',$OrderUID)->get()->row();
            // $OrderMainUID = $OrderDetails->OrderUID;

            if($RoleType == "Organization"){

               $result = $this->apis_model->GetLinesDetailsByLinesNo($GetOrderUID);
               echo json_encode($result);
           }
           else{
            $this->respond("200", 'No Orders Found', null);
        }
    }else{
        $this->respond("404", 'Page Not Found', null);
    }
}
else{
    $this->respond("400", 'Bad Request', null);
}
}















}




?>