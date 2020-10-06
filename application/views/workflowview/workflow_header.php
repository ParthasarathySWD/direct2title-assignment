            <style type="text/css">
                body.theme-red .summary_header_table span.icon{
                    color: #fff;
                    margin-right: 3px;
                    top: 2px;
                    font-size: 17px;
                    position: relative;
                    font-weight:normal;
                }
                body.light_version.theme-red .summary_header_table tbody tr td{
                    font-weight: normal;
                    color: #fff;
                    background: #e15858;
                }
                .nav.nav-tabs3>li>a {
                    color:#666;
                }
            </style>
            <?php
            $this->load->model('Common_model');  
            
            // global $is_api_order;

$OrderUID = $order_details->OrderUID;
$order_details = $this->Common_model->get_orderdetails($OrderUID);

// $apiOrders = $this->Common_model->apiOrdersCount($OrderUID);


// $Orderdetailsss = $order_details;
// $Borrower=$this->Common_model->Get_borrowers_by_OrderUID($OrderUID);
// // echo '<pre>';print_r($Borrower);exit;
// $CustomerUID = $Orderdetailsss->CustomerUID;
//     //$IsAdverseConditions = $this->Common_model->GetIsAdverseConditions($CustomerUID);

// $SubProductUID = $Orderdetailsss->SubProductUID;
// $MenuBarDetails = $this->Common_model->GetMenuBarDetails($CustomerUID,$SubProductUID,$OrderUID);
// $is_vendor_login = $this->Common_model->is_vendorlogin();

// $ProductUID = $this->Common_model->GetProductUIDBySubProductUID($SubProductUID);
// $Product = $ProductUID->ProductUID;
// /**********@author praveen kumar - 10 JAN 2019**********/
//     //fetch order details based on product
// $Ordertypes_Det = $this->Common_model->GetOrderTypeDetails(['ProductUID'=>$ProductUID->ProductUID]);

// $ProductDetails = $this->Common_model->CheckAdverseConditionsEnabledForProduct($Product);
// $IsAdverseConditions = $ProductDetails->AdverseConditionEnable;
// $IsClosingProduct = $ProductDetails->IsClosingProduct;
// $DynamicProduct = $this->Common_model->CheckIsDynamicProduct($Product);
// $IsDynamicProduct = $DynamicProduct->IsDynamicProduct;
// $IsRMSProduct = $order_details->RMS;
// $Prioritysheader = $this->Common_model->GetPriorityDetails();
// $UserUID = $this->session->userdata('UserUID');
// $UserRole = $this->Common_model->GetUserRoleTypeDetails($UserUID);
// $Userdetails = $this->Common_model->GetUserDetailsByUser($UserUID);
// // echo '<pre>';print_r($Userdetails->IsOrderInfo);exit;
// $UserRoleType = $UserRole->RoleType;

// // $Sub_Pro_Details = $this->Common_model->GetSub_productDetails($ProductUID = '1');
// $ExceptionList = $this->Common_model->GetExceptionList($OrderUID);



// // $ApiRequestStatus = $this->Common_model->GetApiRequestStatus($OrderUID);

// $CheckValue = $this->Common_model->GetOrderAbstractorDetails($OrderUID);
// $CheckFeePricing = $CheckValue->CheckFeePricing;
// $Permissions='';
// $RoleUID = $this->session->userdata('RoleUID');
// $FeesPermission=$this->Common_model->GetRolePermissions($RoleUID);


// if($FeesPermission->CustomerPricing!=0 || $FeesPermission->AbstractorFee!=0){
//   $Permissions='1';
// }else{
//   $Permissions='0';
// }
// if($Userdetails->IsOrderInfo=='1'){
//   $dynamic="panel-collapse collapse in";
// }else{
//   $dynamic="panel-collapse collapse";
// }
// $Roles = array('1','2','3','4','5','6','7','12');

// $IsAPIReportSend = $Orderdetailsss->IsAPIReportSend;
// $APIOrder = $Orderdetailsss->APIOrder;

// if($IsAPIReportSend == 0 && $APIOrder == 1){
//   $ReportNotSend = 1;
// } else {
//   $ReportNotSend = 0;
// }

// $OrderSourceUID = $Orderdetailsss->OrderSourceUID; 
// $OrderSourceDetails = $this->Common_model->GetOrderSourceDetails($OrderSourceUID);
// $FloodOrder = 0;
// if(!empty($OrderSourceDetails) && $OrderSourceDetails->OrderSourceCode === 'Flood'){
//   $FloodOrder = 1;
// }

// $orderdetails = $this->Common_model->get_orderdetails($OrderUID);
// $CustomerUID = $orderdetails->CustomerUID;
// $SubProductUID = $orderdetails->SubProductUID;
// $CheckPrintingWorkFlow = $this->Common_model->CheckPrintingWorkFlow($CustomerUID,$SubProductUID,$OrderUID);


// $AttachmentDetails = $this->Common_model->GetFinalReportsList($OrderUID);
// $InternalExceptionList = $this->Common_model->GetInternalExceptionList($OrderUID);

/*$IsPrintingWorkflow = $CheckPrintingWorkFlow->Count;*/

// $OrderSourceName = $this->Common_model->GetApiSourceName($OrderUID);

// $IsTitleClosingOrder = 0;
// $TabName = '';
// $tab_url = '';
// $tab_segment = '';

// if($Orderdetailsss->APIOrder == 1) {
//   if($Orderdetailsss->OrderSourceUID == 1) {
//     $tab_url = base_url().'order_detail?OrderUID='.$order_details->OrderUID.'/';
//     $tab_segment = 'order_detail';
//     if ($Orderdetailsss->IsClosingProduct) {
//       $IsTitleClosingOrder = 1;
//       $TabName = 'API Events';
//     } 
//     if ($Orderdetailsss->IsTitleProduct) {
//       $IsTitleClosingOrder = 1;
//       $TabName = 'API Events';
//     } 
//   }

//   if($OrderSourceName->OrderSourceName == 'Keystone') {
//     $tab_segment = 'title_closing';
//     if ($Orderdetailsss->IsClosingProduct) {
//       $IsTitleClosingOrder = 1;
//       $TabName = 'API Events';
//       $tab_url = 'title_closing/Closing/'.$OrderUID;
//     } 

//     if ($Orderdetailsss->IsTitleProduct) {
//       $IsTitleClosingOrder = 1;
//       $TabName = 'API Events';
//       $tab_url = 'title_closing/Title/'.$OrderUID;
//     } 
//   }
// }

// if($is_api_order) { 
//   $OrderSourceName = $this->Common_model->GetApiSourceName($OrderUID); 
//   $SourceName = '<span style="background-color: #ffb236;padding: 5px;font-family: Montserrat,Helvetica Neue,Arial,sans-serif;">'.$OrderSourceName->OrderSourceName.'</span>';
// } else { 
//   $SourceName = ''; 
// }

// $is_eventlog_order = 0;
// $IsEventLogCleared = isset($Orderdetailsss->IsEventLogCleared) ? $Orderdetailsss->IsEventLogCleared : 0;
// $EventLogOrders = $this->Common_model->CheckEventLogOrders($OrderUID);
// if(!empty($EventLogOrders)){
//   $is_eventlog_order = 1;
//   $EventLogWorkflowUID = $EventLogOrders->WorkflowUID;
// }
?>

            <div class="card-header pb-0 pt-0 pl-3 pr-3 order-sum-header">
                <table class="table table-vcenter text-nowrap table_custom mb-0 order-summary-head">
                    <tbody>
                        <tr>
                            <td>
                                <div class="small text-muted">Order Number</div>
                                <div><?php echo $order_details->OrderNumber; ?></div>
                            </td>
                            <td>
                                <div class="small text-muted">Loan Number</div>
                                <div><?php echo $order_details->LoanNumber; ?></div>
                            </td>
                            <td>
                                <div class="small text-muted">Product/SubProduct</div>
                                <div><?php echo $order_details->ProductCode . '-' . $order_details->SubProductName; ?></div>
                            </td>
                            <td>
                                <div class="small text-muted">Property Address</div>
                                <div><?php echo $order_details->PropertyAddress1 . ', ' . $order_details->PropertyCityName . ', ' . $order_details->PropertyStateCode . ', ' . $order_details->PropertyZipcode; ?></div>
                            </td>
                            <td>
                                <div class="small text-muted">Borrower Name</div>
                                <div><?php echo $order_details->PRName; ?></div>
                            </td>
                            <td>
                                <span class="tag tag-cyan"><?php echo  $order_details->StatusName ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
