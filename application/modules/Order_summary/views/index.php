<?php   

global $is_workflowcomplete_enabled;
global $is_save_enabled;
global $is_review_enabled;
global $is_exception_enabled;
global $is_summary_enabled;
global $is_copy_enabled;
global $is_api_order;
global $IsUpper;


$this->load->model('Common_model');
$this->load->model('Order_summary_model');


$OrderrUID = rtrim($OrderUID,'/');

$UserUID = $this->session->userdata('UserUID');
$RoleUID = $this->session->userdata('RoleUID');
$UserRole = $this->Common_model->GetUserRoleTypeDetails($UserUID);
$UserRoleType = $UserRole->RoleType;
$Pricing = $this->Common_model->GetRolePricingdetails($RoleUID);

$OrderUID = $order_details->OrderUID;
$order_details = $this->Common_model->get_orderdetails($OrderUID);
$ExceptionList = $this->Common_model->GetExceptionList($OrderUID);


$Orderdetailsss = $this->Common_model->get_orderdetails($OrderrUID);

$SubProductUID = $Orderdetailsss->SubProductUID;
$OrderSourceUID = $Orderdetailsss->OrderSourceUID;
$ProductUID = $this->Common_model->GetProductUIDBySubProductUID($SubProductUID);
$Product = $ProductUID->ProductUID;



$currentqueue='';
$currentqueue = $this->Common_model->GetCurrentQueueStatus($OrderrUID);

$billedqueue ='';
$search=$this->Order_summary_model->SearchCheckWorkflowStatus($OrderUID);
$type=$this->Order_summary_model->TypingCheckWorkflowStatus($OrderUID);
$tax=$this->Order_summary_model->TaxingCheckWorkflowStatus($OrderUID);
$Orderstatus=$this->Order_summary_model->SearchStatus($OrderUID);
$billedqueue=$this->Order_summary_model->BilledStatus($OrderrUID);
if($billedqueue)
{
  $billedqueue = '<span class="btn btn-sm btn-rounded btn-sm pull-right" style="font-size: 10px; color:#fff; background: #34a853;">Billed</span>';
}

$is_stewart = 0;
if($OrderSourceUID == 2){
  $is_stewart = 1;
}


$is_customer_login = $this->Common_model->is_customerlogin();


$pricheck =0;
if($order_details->PriorityUID == '1'){

 $pricheck ='checked';
 


 

}



?>
<!-- start body header -->
<div id="page_top" class="section-body" style="">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Order Summary</h1> 
            </div>
            <div class="right">
                <div class="notification d-flex">
                    <button type="button" class="btn btn-facebook"><i class="fa fa-info-circle mr-2"></i>Need Help</button>
                    <button type="button" class="btn btn-facebook"><i class="fa fa-file-text mr-2"></i>Data export</button>
                    <button type="button" class="btn btn-facebook"><i class="fa fa-power-off mr-2"></i>Sign Out</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="section-body">
    <div class="container-fluid">
    <form action="#" name="frm_OrderSummary" id="frm_OrderSummary" onsubmit="return false;">
        <input class="mdl-textfield__input  input-xs" type="hidden" id="OrderUID" name="OrderUID" value="<?php echo $order_details->OrderUID ?>">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                   <?php $this->load->view('workflowview/workflow_header'); ?>
                    <div class="card-header pb-0 pt-0 pl-3 pr-3 order-sum-tab">

                      <?php $this->load->view('workflowview/workflow_menu'); ?>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 pl-0">
                                    <h3 class="card-title pt-2">Product Info</h3>
                                </div>

                                <div class="col-md-6 col-lg-6 text-right pr-0 pt-1">
                                     <?php if($is_exception_enabled == 1 && !$is_vendor_login) { ?>
                                    <a href="" class="btn btn-secondary  btn-sm" id="RaiseException"> Raise Request</a>
                                     <?php }?>
                                    <a href="" id="EntryCopySummary" class="btn btn-info btn-sm"> Quick Order Entry</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="col-md-12 col-lg-12 add-product-div p-0">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">

                                    <div class="form-group">
                                        <label class="form-label">Client</label>
                                       <select class="form-control mdl-textfield__input input-xs mdl-select2 select2" id="customer" name="customer">
                              <?php foreach ($Customers as $key => $customer) {
                                if($customer->CustomerUID == $order_details->CustomerUID) {?>
                                  <option value="<?php echo $customer->CustomerUID; ?>" selected><?php echo $customer->CustomerNumber.' / '.$customer->CustomerName; ?></option>';
                                <?php }
                              } ?>
                            </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label">Product</label>
                                        <select class="form-control mdl-textfield__input input-xs select2 mdl-select2" id="ProductUID" name="ProductUID">
                                            <?php $prod = $this->Order_Summary_Model->GetProductBySubproducts($order_details->SubProductUID);?>
                                            <?php foreach ($CustProducts as $key => $product) { ?>
                                              <?php if($product->ProductUID == $prod->ProductUID) { ?>
                                                <option value="<?php echo $product->ProductUID; ?>" selected><?php echo $product->ProductName;?></option>;
                                            <?php } 
                                        } ?>
                                    </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label">SubProduct</label>
                                        <select class="form-control mdl-textfield__input input-xs select2 mdl-select2" id="SubProductUID" name="SubProductUID" <?php if($is_vendor_login){ echo 'disabled="disabled"'; } ?> >
                              <?php foreach ($Sub_products as $key => $Sub_product) {
                                if($Sub_product->SubProductUID == $order_details->SubProductUID) {?>
                                  <option value="<?php echo $Sub_product->SubProductUID; ?>" selected><?php echo $Sub_product->SubProductName;?></option>';
                                <?php }else{ ?>
                                  <option value="<?php echo $Sub_product->SubProductUID; ?>"><?php echo $Sub_product->SubProductName;?></option>';
                                <?php }
                              } ?>
                            </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label class="form-label">Priority </label>
                                        <select class="form-control mdl-textfield__input input-xs select2 mdl-select2 " id="PriorityUID" name="PriorityUID" <?php if($is_vendor_login){ echo 'disabled="disabled"'; } ?>>

                              <?php foreach ($Prioritys as $key => $priority) {
                                if($priority->PriorityUID == $order_details->PriorityUID) {?>
                                  <option value="<?php echo $priority->PriorityUID; ?>" selected><?php echo $priority->PriorityName;?></option>';
                                <?php }else{ ?>
                                  <option value="<?php echo $priority->PriorityUID; ?>"><?php echo $priority->PriorityName;?></option>
                                <?php }

                              } ?>

                            </select>
                                    </div>
                                </div>
                                <div class="col-md-1 col-lg-1 text-right">
                                    <div class="form-group mt-4 pt-1">
                                        <a href="Order_entry" class="btn btn-primary btn-sm"><i class="fe fe-plus"></i></a>
                                    </div>
                                </div>

                            </div>
                            <!-- <div class="row ">
                                <div class="col-md-3 col-lg-3">

                                    <div class="form-group">
                                        <label class="form-label">Client</label>
                                        <select name="beast" class="form-control select2">
                                            <option></option>
                                            <option value="1">60245101 / Zions Bancorporation, N.A.</option>
                                            <option value="4">12345 / Test Customer</option>
                                            <option value="3">99903780 / Builder Finance Inc.</option>
                                            <option value="3">99903808 / Community Financial Credit Union</option>
                                            <option value="3">6025381 / First Atlantic Federal CU</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label">Product</label>
                                        <select name="beast" class="form-control select2">
                                            <option></option>
                                            <option value="1">Property Report</option>
                                            <option value="4">Flood Cert</option>
                                            <option value="3">Recording</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label">SubProduct</label>
                                        <select name="beast" class="form-control select2">
                                            <option></option>
                                            <option value="1">Property Report</option>
                                            <option value="4">Copies</option>
                                            <option value="3">EZ Prop</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label class="form-label">Priority </label>
                                        <select name="beast" class="form-control select2">
                                            <option></option>
                                            <option value="1">Rush</option>
                                            <option value="4">Normal</option>
                                            <option value="3">ASAP</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 col-lg-1 text-right">
                                    <div class="form-group mt-4 pt-1">
                                        <a href="Order_entry" class="btn btn-danger btn-sm"><i class="fe fe-minus"></i></a>
                                    </div>
                                </div>

                            </div> -->
                        </div>
                    </div>
                    <div class="card-header">
                        <h3 class="card-title">Loan Info</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Loan Number</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">#</span>
                                        </div>
                                        <input class="form-control mdl-textfield__input  input-xs" type="text" id="LoanNumber" name="LoanNumber" value="<?php echo $order_details->LoanNumber ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Loan Amount</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                        </div>
                                        <input type="text" class="form-control currency   input-xs" id="LoanAmount" name="LoanAmount" placeholder="LoanAmount" value="<?php echo $order_details->LoanAmount ?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Client Ref Number</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">#</span>
                                        </div>
                                        <input class="form-control mdl-textfield__input  input-xs" type="text" id="CustomerRefNum" name="CustomerRefNum" value="<?php echo $order_details->CustomerRefNum ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">APN</label>
                                   <input class="form-control mdl-textfield__input  input-xs <?php if($is_vendor_login){ echo 'vendor_disabled'; } ?>" type="text" id="APN" name="APN" value="<?php echo $order_details->APN ?>" <?php if($is_vendor_login){ echo 'disabled="disabled"'; } ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Borrower Type</label>
                                   <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs BorrowerType" id="BorrowerType" name="BorrowerType" >
                                  <option value=""></option>
                                  <?php 
                                  foreach ($BorrowerDetails as $key => $value) { if($value->BorrowerTypeUID == $order_details->BorrowerType){?>

                                    <option value="<?php echo $value->BorrowerTypeUID;?>" selected><?php echo $value->BorrowerTypeName;?></option>
                                  <?php } else{?>
                                    <option value="<?php echo $value->BorrowerTypeUID;?>"><?php echo $value->BorrowerTypeName;?></option>
                                  <?php } }?>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Property Type </label>
                                     <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs PropertyType" id="PropertyType" name="PropertyType" >
                                  <option value=""></option>
                                  <?php 
                                  foreach ($PropertyTypeDetails as $key => $value) { if($value->PropertyUseUID == $order_details->PropertyType){?>

                                    <option value="<?php echo $value->PropertyUseUID;?>" selected><?php echo $value->PropertyUseName;?></option>
                                  <?php } else{?>
                                    <option value="<?php echo $value->PropertyUseUID;?>"><?php echo $value->PropertyUseName;?></option>
                                  <?php } }?>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Transaction Type </label>
                                   <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs TransactionType" id="TransactionType" name="TransactionType" >
                                  <option value=""></option>
                                  <?php 
                                  foreach ($TransactionTypeDetails as $key => $value) { if($value->TransactionTypeUID == $order_details->TransactionType){?>

                                    <option value="<?php echo $value->TransactionTypeUID;?>" selected><?php echo $value->TransactionTypeName;?></option>
                                  <?php } else{?>
                                    <option value="<?php echo $value->TransactionTypeUID;?>"><?php echo $value->TransactionTypeName;?></option>
                                  <?php } }?>
                                </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-header">
                        <h3 class="card-title">Property Address</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Address Line 1</label>
                                    <input class="form-control ClassForClientBranch mdl-textfield__input  input-xs dup_bg_color <?php if($is_vendor_login){ echo 'vendor_disabled'; } ?>" type="text" id="PropertyAddress1" name="PropertyAddress1" value="<?php echo $order_details->PropertyAddress1 ?>" <?php if($is_vendor_login){ echo 'disabled="disabled"'; } ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Address Line 2</label>
                                    <input class="form-control mdl-textfield__input  input-xs dup_bg_color <?php if($is_vendor_login){ echo 'vendor_disabled'; } ?>" type="text" id="PropertyAddress2" name="PropertyAddress2" value="<?php echo $order_details->PropertyAddress2 ?>" <?php if($is_vendor_login){ echo 'disabled="disabled"'; } ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Zipcode</label>
                                   <input class="form-control mdl-textfield__input  input-xs dup_bg_color <?php if($is_vendor_login){ echo 'vendor_disabled'; } ?>" maxlength="10" type="text" id="PropertyZipcode" name="PropertyZipcode" value="<?php echo $order_details->PropertyZipcode ?>" <?php if($is_vendor_login){ echo 'disabled="disabled"'; } ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">City</label>
                                   <input class="form-control ClassForClientBranch mdl-textfield__input  input-xs dup_bg_color form-control select dropdown-toggle size <?php if($is_vendor_login){ echo 'vendor_disabled'; } ?>" type="text" value="<?php echo $order_details->PropertyCityName ?>" id="PropertyCityName" name="PropertyCityName" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php if($is_vendor_login){ echo 'disabled="disabled"'; } ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">County</label>
                                   <input class="form-control ClassForClientBranch mdl-textfield__input  input-xs dup_bg_color form-control select dropdown-toggle size <?php if($is_vendor_login){ echo 'vendor_disabled'; } ?>" type="text" value="<?php echo $order_details->PropertyCountyName ?>" id="PropertyCountyName" name="PropertyCountyName" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php if($is_vendor_login){ echo 'disabled="disabled"'; } ?>>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">State</label>
                                     <input class="form-control ClassForClientBranch mdl-textfield__input  input-xs dup_bg_color form-control select dropdown-toggle size <?php if($is_vendor_login){ echo 'vendor_disabled'; } ?>" type="text" value="<?php echo $order_details->PropertyStateCode ?>" id="PropertyStateCode" name="PropertyStateCode" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" <?php if($is_vendor_login){ echo 'disabled="disabled"'; } ?>>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-header">
                        <h3 class="card-title">Additional Info</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Send Report To</label>
                                    <input type="text" class="form-control" name="" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Email Report To</label>
                                  <input class="form-control mdl-textfield__input  input-xs" type="text" id="EmailReportTo" name="EmailReportTo" value="<?php echo $order_details->EmailReportTo ?>">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-header pb-0">

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 pl-0">
                                    <h3 class="card-title">Property Roles</h3>
                                </div>

                                <div class="col-md-6 col-lg-6 text-right pr-0">
                                    <a class="btn btn-info btn-sm add_property_role" style="margin-top:-15px;">Add New</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12 col-lg-12">

                                <div class="table-responsive">
                                    <table class="table table-hover table-vcenter table-new" cellspacing="0" id="propertyRoleTable">
                                        <thead>
                                            <tr>
                                                <th>Role Details</th>
                                                <th>Telephone</th>
                                                <th>Work No</th>
                                                <th>Cell No</th>
                                                <th>Email</th>
                                                <th class="text-center" style="width:120px">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                       <!--      <tr>
                                                <td>
                                                    <p class="mb-0">Name</p>
                                                    <span class="text-muted font-13">Borrowers</span>
                                                </td>
                                                <td>(236) 565-6565</td>
                                                <td>(656) 562-6652</td>
                                                <td>(626) 626-2626</td>
                                                <td>test@mail.com</td>
                                                <td class="actions text-center">
                                                    <button class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></button>
                                                    <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                </td>
                                            </tr> -->
                                            <?php foreach ($prop_details as $key => $prop) { 

                                      $prop->PRSocialNumber = $encrypt->decrypt($prop->PRSocialNumber,$this->config->item('encryption_key'),256);
                                    ?>
                                      <tr>
                                        <input type="hidden" class="OrderPropertyRoleUID" name="OrderPropertyRoleUID[]" value="<?php echo $prop->Id; ?>">
                                        <input type="hidden" class="PropertyRoleUID" name="PropertyRoleUID[]" value="<?php echo $prop->PropertyRoleUID; ?>">
                                        <input type="hidden" class="PropertyRoleName" name="PropertyRoleName[]" value="<?php echo $prop->PropertyRoleName; ?>">
                                        <input type="hidden" class="PRName" name="PRName[]" value="<?php echo $prop->PRName; ?>">
                                        <input type="hidden" class="PRTitle" name="PRTitle[]" value="<?php echo $prop->PRTitle; ?>">
                                        <input type="hidden" class="PRHomeNumber" name="PRHomeNumber[]" value="<?php echo $prop->PRHomeNumber; ?>">
                                        <input type="hidden" class="PRWorkNumber" name="PRWorkNumber[]" value="<?php echo $prop->PRWorkNumber; ?>">
                                        <input type="hidden" class="PRCellNumber" name="PRCellNumber[]" value="<?php echo $prop->PRCellNumber; ?>">
                                        <input type="hidden" class="PRSocialNumber" name="PRSocialNumber[]" value="<?php echo $prop->PRSocialNumber; ?>">
                                        <input type="hidden" class="PREmailID" name="PREmailID[]" value="<?php echo $prop->PREmailID; ?>">
                                         <input type="hidden" class="chk_mailing" name="chk_mailing[]" value="<?php echo $prop->IsMailingAddress; ?>">
                                         <input type="hidden" class="MailingAddress1" name="MailingAddress1[]" value="<?php echo $prop->MailingAddress1; ?>">
                                         <input type="hidden" class="MailingAddress2" name="MailingAddress2[]" value="<?php echo $prop->MailingAddress2; ?>">
                                         <input type="hidden" class="MailingZipCode" name="MailingZipCode[]" value="<?php echo $prop->MailingZipCode; ?>">
                                         <input type="hidden" class="MailingCityName" name="MailingCityName[]" value="<?php echo $prop->MailingCityName; ?>">
                                         <input type="hidden" class="MailingStateCode" name="MailingStateCode[]" value="<?php echo $prop->MailingStateCode; ?>">
                                         <input type="hidden" class="MailingCountyName" name="MailingCountyName[]" value="<?php echo $prop->MailingCountyName; ?>">
                                         <input type="hidden" class="MailingAddressNotes" name="MailingAddressNotes[]" value="<?php echo $prop->MailingAddressNotes; ?>">

                                        <?php if ($order_details->IsClosingProduct == 1) { ?>
                                         <input type="hidden" class="chk_Signing" name="chk_Signing[]" value="<?php echo $prop->IsSigningAddress; ?>">
                                         <input type="hidden" class="SigningAddress1" name="SigningAddress1[]" value="<?php echo $prop->SigningAddress1; ?>">
                                         <input type="hidden" class="SigningAddress2" name="SigningAddress2[]" value="<?php echo $prop->SigningAddress2; ?>">
                                         <input type="hidden" class="SigningZipCode" name="SigningZipCode[]" value="<?php echo $prop->SigningZipCode; ?>">
                                         <input type="hidden" class="SigningCityName" name="SigningCityName[]" value="<?php echo $prop->SigningCityName; ?>">
                                         <input type="hidden" class="SigningStateCode" name="SigningStateCode[]" value="<?php echo $prop->SigningStateCode; ?>">
                                         <input type="hidden" class="SigningCountyName" name="SigningCountyName[]" value="<?php echo $prop->SigningCountyName; ?>">
                                         <input type="hidden" class="SigningAddressNotes" name="SigningAddressNotes[]" value="<?php echo $prop->SigningAddressNotes; ?>">
                                         <input type="hidden" class="SigningSpecialInstruction" name="SigningSpecialInstruction[]" value="<?php echo $prop->SigningSpecialInstruction; ?>">
                                       <?php } ?>

                                      <td>
                                        <div class="d-flex align-items-center">
                                            <div class="">
                                                <a href="javascript:void(0);" title="Name" data-toggle="tooltip" data-placement="top"><?php echo $prop->PRName; ?></a>
                                                <p class="mb-0" title="Role" data-toggle="tooltip" data-placement="top"><?php echo $prop->PropertyRoleName; ?></p>
                                            </div>
                                        </div>
                                      </td>

                                       <td><?php echo $prop->PRHomeNumber; ?></td>
                                       <td><?php echo $prop->PRWorkNumber; ?></td>
                                       <td><?php echo $prop->PRCellNumber; ?></td>
                                       <td><?php echo $prop->PREmailID; ?></td>
                                       <td>
                                       <!--  <button type="button" class="btn btn-sm btn-default text-primary edit_property_info" title="View" data-toggle="tooltip" data-id="<?php echo $prop->Id; ?>" data-placement="top" data-type="confirm"><i class="icon-eye"></i></button> -->
                                        <button class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></button>
                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                       </td>

                                     </tr>
                                   <?php } ?>

                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                          <!--   <div class="col-md-12 col-lg-12">
                                <h3 class="card-title pb-4 pt-2">Add Property Role</h3>
                            </div>
                            <div class="col-md-3 col-lg-3">

                                <div class="form-group">
                                    <label class="form-label">Roles</label>
                                    <select name="beast" class="form-control select2">
                                        <option></option>
                                        <option value="1">Attorney in Fact</option>
                                        <option value="4">Non Borrower</option>
                                        <option value="3">Payer</option>
                                        <option value="3">Borrowers</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Title</label>
                                    <select name="beast" class="form-control select2">
                                        <option></option>
                                        <option value="1">MR</option>
                                        <option value="4">MRS</option>
                                        <option value="3">Ms</option>
                                        <option value="3">Dr</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">

                                <div class="form-group">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="example-text-input" placeholder="">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3">
                                <label class="form-label">Phone</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-mobile-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control mobile-phone-number" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <label class="form-label">Work No</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control phone-number" placeholder="">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3">
                                <label class="form-label">Cell No</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-mobile-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control mobile-phone-number" placeholder="">
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-3">
                                <label class="form-label">Social No</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                                    </div>
                                    <input type="text" class="form-control credit-card" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                                <label class="form-label">Email</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-envelope-o"></i></span>
                                    </div>
                                    <input type="text" class="form-control email" placeholder="">
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-9 pt-4">
                                <label class="form-label" style="display:inline-block;">Is the Mailing Address same as Property Address?</label>
                                
                                <div class="custom-controls-stacked ml-3" style="display:inline-block;">
                                    <label class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" name="mailing-address-same" value="option1" checked>
                                        <span class="custom-control-label">Yes</span>
                                    </label>
                                    <label class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" name="mailing-address-same" value="option2">
                                        <span class="custom-control-label">No</span>
                                    </label>
                                </div>
                            </div> -->
                             <div class="col-md-12 col-lg-12 multiplePropertyAddTable" id="multiplePropertyAddTable">
                            </div>

                            <div class="row MailingContentAdd" id="MailingDetailsAdd" style="display: none;">

                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing Address 1</label>
                                    <input type="text" class="form-control" name="" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing Address 2</label>
                                    <input type="text" class="form-control" name="" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing Zipcode</label>
                                    <input type="text" class="form-control" name="" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing City</label>
                                    <input type="text" class="form-control" name="" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing County</label>
                                    <input type="text" class="form-control" name="" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing State</label>
                                    <input type="text" class="form-control" name="" placeholder="">
                                </div>
                            </div>
                        </div>
                           <!--  <div class="col-md-12 col-lg-12 mb-4">
                                <div class="btn-list  text-right">
                                    <a href="#" class="btn btn-secondary">Cancel</a>
                                    <a href="#" class="btn btn-primary">Save</a>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-lg-12 mb-3 mt-4">
                    <div class="btn-list text-right">
                        <a href="#" class="btn btn-success single_submit" id="UpdateRoles" value="1">Update</a>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>


<script type="text/javascript">
    
/**
        * @author Sathis Kannn P
        * @purpose Add Property Role Onchange to View a Add Columns
        * @param Header, Button, ProductName, GetRole, Count
        * change event for .add_property_role
        * @date 14-09-2020
        * @version New Assignment
        */
        var Header,Button;
        Count =0;
        $(document).on('click', '.add_property_role', function(event)
        {
          event.preventDefault();
         /* Act on the event */             
          var id  = $(this).attr('id');
           Header  = 'Add New Property Roles';
          // var Button = 'Delete';
           Button = 'Save';
          $('.multiplePropertyAddTable').append(multiplePropertyTable(Button,Header));

          $('.select2').select2();
          $('.ProductUID1:last').val($('.ProductUID1:last option:first').val()).trigger('change');

        });
        $(document).on('click', '.add_cancel', function(event)
        {
          event.preventDefault();
          /* Act on the event */
          // var id = $(this).attr('data-attr');

          // $("#rowID_" + id).remove();
          $('.multiplePropertyAddTable').html(''); 
                  $('.SaveUpdate').hide();
                  $('.select2').select2();
        });

        function multiplePropertyTable(Button,Header)
        {
            var id  = 1;
            var multiplePropertyFields;

             multiplePropertyFields  = '<div class="row" id="rowID_'+id+'">';

             multiplePropertyFields  += '<div class="col-md-12 col-lg-12">';
             multiplePropertyFields  += '<div class="row">';
             multiplePropertyFields  += '<div class="col-md-6">';
             multiplePropertyFields  += '<h3 class="card-title pb-4 pt-2">'+Header+'</h3>';
             multiplePropertyFields  += '</div>';
             //  multiplePropertyFields  += '<div class="col-md-6 text-right">';
             // multiplePropertyFields  += '<i class="btn btn-danger btn-md fe fe-minus propertycancel_button" button-id="'+id+'"></i>';
             // multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

             multiplePropertyFields  += '<div class="col-md-3 col-lg-3">';
             multiplePropertyFields  += '<div class="form-group">';
             multiplePropertyFields  += '<label class="form-label">Roles</label>';
             multiplePropertyFields  += '<select class="form-control select2 roles" name="roles" id="roles">';
             <?php 
             foreach ($Prop_roles as $value)
             {                                                              
                ?>
             multiplePropertyFields += '<option value="<?php echo $value->PropertyRoleName; ?>"><?php echo $value->PropertyRoleName; ?></option>';
            
            <?php } ?>
             multiplePropertyFields  += ' </select>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';
 multiplePropertyFields  += '<input type="hidden" class="rolesname" name="rolesname" id="rolesname" value="<?php echo $value->PropertyRoleName; ?>"  />';
             multiplePropertyFields  += '<div class="col-md-3 col-lg-3">';
             multiplePropertyFields  += ' <div class="form-group">';
             multiplePropertyFields  += ' <label class="form-label">Title</label>';
             multiplePropertyFields  += '<select class="form-control select2 title" id="title" name="title">';
             multiplePropertyFields  += ' <option></option>';
             multiplePropertyFields  += ' <option value="1">MR</option>';
             multiplePropertyFields  += ' <option value="2">MRS</option>';
             multiplePropertyFields  += ' <option value="3">Ms</option>';
             multiplePropertyFields  += ' <option value="4">Dr</option>';
             multiplePropertyFields  += ' </select>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

             multiplePropertyFields  += '<div class="col-md-6 col-lg-6">';
             multiplePropertyFields  += '<div class="form-group">';
             multiplePropertyFields  += '<label class="form-label">Name</label>';
             multiplePropertyFields  += '<input type="text" class="form-control name" name="name" id="name" name="example-text-input" placeholder="">';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

             multiplePropertyFields  += '<div class="col-lg-3 col-md-3">';
             multiplePropertyFields  += '<label class="form-label">Phone</label>';
             multiplePropertyFields  += '<div class="input-group mb-3">';
             multiplePropertyFields  += '<div class="input-group-prepend">';
             multiplePropertyFields  += '<span class="input-group-text"><i class="fa fa-mobile-phone"></i></span>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '<input type="text" class="form-control mobile-phone-number telephone" id="telephone" name="telephone" placeholder="">';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

             multiplePropertyFields  += '<div class="col-lg-3 col-md-3">';
             multiplePropertyFields  += '<label class="form-label">Work No</label>';
             multiplePropertyFields  += '<div class="input-group mb-3">';
             multiplePropertyFields  += '<div class="input-group-prepend">';
             multiplePropertyFields  += '<span class="input-group-text"><i class="fa fa-phone"></i></span>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '<input type="text" class="form-control phone-number workNo" id="workNo" value="" name="workNo" placeholder="">';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

             multiplePropertyFields  += '<div class="col-lg-3 col-md-3">';
             multiplePropertyFields  += '<label class="form-label">Cell No</label>';
             multiplePropertyFields  += '<div class="input-group mb-3">';
             multiplePropertyFields  += '<div class="input-group-prepend">';
             multiplePropertyFields  += '<span class="input-group-text"><i class="fa fa-mobile-phone"></i></span>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '<input type="text" class="form-control mobile-phone-number cellNo phone_mask" name="cellNo" placeholder="">';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

             multiplePropertyFields  += '<div class="col-lg-3 col-md-3">';
             multiplePropertyFields  += '<label class="form-label">Social No</label>';
             multiplePropertyFields  += '<div class="input-group mb-3">';
             multiplePropertyFields  += '<div class="input-group-prepend">';
             multiplePropertyFields  += '<span class="input-group-text"><i class="fa fa-credit-card"></i></span>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '<input type="text" class="form-control credit-card ssn socialNo ssn_mask" name="socialNo" data-mask="ssn" id="ssn" placeholder="">';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

             multiplePropertyFields  += '<div class="col-lg-3 col-md-3">';
             multiplePropertyFields  += '<label class="form-label">Email</label>';
             multiplePropertyFields  += '<div class="input-group mb-3">';
             multiplePropertyFields  += '<div class="input-group-prepend">';
             multiplePropertyFields  += '<span class="input-group-text"><i class="fa fa-envelope-o"></i></span>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '<input type="text" class="form-control email email_validation" name="email" id="email" placeholder="">';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

             multiplePropertyFields  += '<div class="col-lg-9 col-md-9 pt-4">';
             multiplePropertyFields  += '<label class="form-label" style="display:inline-block;">Is the Mailing Address same as Property Address?</label>';  
             multiplePropertyFields  += '<div class="custom-controls-stacked ml-3" style="display:inline-block;">';
             multiplePropertyFields  += '<label class="custom-control custom-radio custom-control-inline">';
             multiplePropertyFields  += '<input type="radio" class="custom-control-input IsMailing" id="IsMailingYes" name="mailing-address-same" value="Yes" checked>';
             multiplePropertyFields  += '<span class="custom-control-label">Yes</span>';
             multiplePropertyFields  += '</label>';
             multiplePropertyFields  += '<label class="custom-control custom-radio custom-control-inline">';
             multiplePropertyFields  += '<input type="radio" class="custom-control-input IsMailing" id="IsMailingNo" name="mailing-address-same" value="No">';
             multiplePropertyFields  += '<span class="custom-control-label">No</span>';
             multiplePropertyFields  += '</label>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

             if(Button == 'Save'){
               multiplePropertyFields  += '<div class="col-md-12 col-lg-12 mb-3">';
               multiplePropertyFields  += '<div class="btn-list  text-right">';
               multiplePropertyFields  += '<a href="#" class="btn btn-secondary add_cancel" id="add_cancel">Cancel</a>';
               multiplePropertyFields  += '<a href="#" class="btn btn-primary add_role" id="add_role">Save</a>';
               multiplePropertyFields  += '</div>';
               multiplePropertyFields  += '</div>';
             }
             else{
               multiplePropertyFields  += '<div class="col-md-12 col-lg-12 mb-3">';
               multiplePropertyFields  += '<div class="btn-list  text-right">';
               multiplePropertyFields  += '<a href="#" class="btn btn-secondary add_cancel" id="add_cancel">Cancel</a>&nbsp;&nbsp;';
               multiplePropertyFields  += '<a href="#" class="btn btn-success edit_role" id="edit_role" data-rowID="'+Count+'">Update</a>';
               multiplePropertyFields  += '</div>';
               multiplePropertyFields  += '</div>';

             }


            
            $('.select2').select2();
            return multiplePropertyFields;
        }



        /* @author Sathis Kannn P
        * @purpose IsMailing Fields Show/Hide
        * change event for .IsMailing
        * @date 14-09-2020
        * @version New Assignment
        */

        $(document).off('change', '.IsMailing').on('change', '.IsMailing', function(e)
        {
            e.preventDefault(); 

            var mailingvalue =$(this).val();

            var mailCheck = mailingvalue;
            if (mailCheck == 'Yes')
            {
                $('.MailingContentAdd').hide();
            }
            else
            {
              $('.MailingContentAdd').show();
              // $('#MailingDetailsAdd .mailingAddressFields').html(mailingAddressFields);

            }

        });

  /**
        * @author Sathis Kannan P
        * @purpose Property Role Save Button Click to Add Values In Table
        * @param nothing
        * click event for .add_role
        * @date 14-09-2020
        * @version New Assignment
        */

        var btnCount = 0;
        $(document).off('click', '.add_role').on('click', '.add_role', function(event) {
            event.preventDefault();
            
            /* Act on the event */
            btnCount++;
            var productname = $('.ProductUID option:selected').text();



            var roles,title,name,telephone,workNo,cellNo,socialNo,email,mailCheck,mailingaddress1,mailingaddress2,mailingzipcode,mailingcity,mailingcounty,mailingstate,mailingnotes,signinCheck,signingaddress1,signingaddress2,signingzipcode,signingcity,signingcounty,signingstate,signingAddressnotes,signingnotes,roles1,title1;     

            
            roles               = $('.multiplePropertyAddTable').find(".roles :selected").val();
            title               = $('.multiplePropertyAddTable').find(".title :selected").val();
            roles1              = $('.multiplePropertyAddTable').find(".roles :selected").text();
            title1              = $('.multiplePropertyAddTable').find(".title :selected").text();
            name                = $('.multiplePropertyAddTable').find(".name").val();
            rolesname           = $(".rolesname:selected").val();
            telephone           = $('.multiplePropertyAddTable').find(".telephone").val();
            workNo              = $('.multiplePropertyAddTable').find(".workNo").val();
            cellNo              = $('.multiplePropertyAddTable').find(".cellNo").val();
            socialNo            = $('.multiplePropertyAddTable').find("input[name=socialNo]").val();
            email               = $('.multiplePropertyAddTable').find(".email").val();
            mailingaddress1     = $('.multiplePropertyAddTable').find('.mailingaddress1').val();
            mailingaddress2     = $('.multiplePropertyAddTable').find('.mailingaddress2').val();
            mailingzipcode      = $('.multiplePropertyAddTable').find('.mailingzipcode').val();
            mailingcity         = $('.multiplePropertyAddTable').find('.mailingcity').val();
            mailingcounty       = $('.multiplePropertyAddTable').find('.mailingcounty').val();
            mailingstate        = $('.multiplePropertyAddTable').find('.mailingstate').val();
            mailingnotes        = $('.multiplePropertyAddTable').find('.mailingnotes').val();

            // signingaddress1     = $('.multiplePropertyAddTable').find('.signingaddress1').val();
            // signingaddress2     = $('.multiplePropertyAddTable').find('.signingaddress2').val();
            // signingzipcode      = $('.multiplePropertyAddTable').find('.signingzipcode').val();
            // signingcity         = $('.multiplePropertyAddTable').find('.signingcity').val();
            // signingcounty       = $('.multiplePropertyAddTable').find('.signingcounty').val();
            // signingstate        = $('.multiplePropertyAddTable').find('.signingstate').val();
            // signingAddressnotes = $('.multiplePropertyAddTable').find('.signingAddressnotes').val();
            // signingnotes        = $('.multiplePropertyAddTable').find('.signingnotes').val(); 

            var mailingAddressRadioBtnValue = [];
            $.each($("input[name='IsMailing']:checked"), function()
            {
              mailingAddressRadioBtnValue.push($(this).val());
            });
            mailCheck = mailingAddressRadioBtnValue;

            // var signingAddressRadioBtnValue = [];
            // $.each($("input[name='signingAddress']:checked"), function()
            // {
            //   signingAddressRadioBtnValue.push($(this).val());
            // });
            // signinCheck = signingAddressRadioBtnValue;

            if(mailingaddress1 == undefined){
              mailingaddress1 = "";
            }
            if(mailingaddress2 == undefined){
              mailingaddress2 = "";
            }
            if(mailingzipcode == undefined){
              mailingzipcode = "";
            }
            if(mailingcity == undefined){
              mailingcity = "";
            }
            if(mailingcounty == undefined){
              mailingcounty = "";
            }
            if(mailingstate == undefined){
              mailingstate = "";
            }
            // if(mailingnotes == undefined){
            //   mailingnotes = "";
            // }
            // if(signingaddress1 == undefined){
            //   signingaddress1 = "";
            // }
            // if(signingaddress2 == undefined){
            //   signingaddress2 = "";
            // }
            // if(signingzipcode == undefined){
            //   signingzipcode = "";
            // }
            // if(signingcity == undefined){
            //   signingcity = "";
            // }
            // if(signingcounty == undefined){
            //   signingcounty = "";
            // }
            // if(signingstate == undefined){
            //   signingstate = "";
            // }
            // if(signingAddressnotes == undefined){
            //   signingAddressnotes = "";
            // }
            // if(signingnotes == undefined){
            //   signingnotes = "";
            // }
            if(mailCheck == undefined){
              mailCheck = "";
            }
            // if(signinCheck == undefined){
            //   signinCheck = "";
            // }

            var table;
           
            table  = '<tr id="rowID_'+btnCount+'">';
            table  += '<td>';
            table  +='<div class="d-flex align-items-center">';
            table  +='<div class="">';
            // table  += '<p class="mb-0" title="'+name+'" id="personName">'+name+'</p>';
            // table  += '<span class="text-muted font-13" id="personRole" title="'+roles+'">'+roles+'</span>';
            table  +='<p href="javascript:void(0);" title="Name" data-toggle="tooltip"  id="personName"data-placement="top">'+name+'</p>';
            table  +='<p class="mb-0" title="Role" data-toggle="tooltip" id="personRole" data-placement="top">'+roles+'</p>';
            table  += '</div>';
            table  += '</div>';
            table  += '</td>';
            table  += '<td id="personTelePhone">'+telephone+'</td>';
            table  += '<td id="personWorkNo">'+workNo+'</td>';
            table  += '<td id="personCellNo">'+cellNo+'</td>';
            table  += '<td id="personEmail">'+email+'</td>';
            table  += '<td class="actions text-center">';
            table  += '<button class="btn btn-sm btn-icon button-edit text-primary edit_property_info" title="Edit" id="'+btnCount+'"><i class="icon-pencil"></i></button>';
            table  += '<button class="btn btn-sm btn-icon text-danger propery_row_delete" title="Delete"><i class="icon-trash" id="'+btnCount+'"></i></button>';
            table  += '</td>';
            // table  += '</tr>';

            // table  = '<tr id="rowID_'+btnCount+'">';
            // table += '<td>';
            // table += '<div class="d-flex align-items-center">';
            // table += '<div class="">';
            // table += '<a title="'+name+'" id="personName">'+name+'</a>';
            // table += '<p class="mb-0" id="personRole" title="'+roles1+'">'+roles1+'</p>';
            // table += '</div>';
            // table += '</div>';
            // table += '</td>';
            // table += '<td id="personTelePhone">'+telephone+'</td>';
            // table += '<td id="personWorkNo">'+workNo+'</td>';
            // table += '<td id="personCellNo">'+cellNo+'</td>';
            // table += '<td id="personEmail">'+email+'</td>';
            // table += '<td>';
            // table += '<button type="button" class="btn btn-sm btn-default text-primary edit_property_info" title="Edit" data-toggle="tooltip" id="'+btnCount+'" data-placement="top" data-type="confirm"><i class="icon-note"></i></button>';
            // table += '<button type="button" class="btn btn-sm btn-default js-sweetalert text-danger propery_row_delete" title="Delete" data-toggle="tooltip" data-placement="top" id="'+btnCount+'"><i class="icon-trash"></i></button>';
            // table += '</td>';

            table += '<td display="display:none;">';

            table += '<input type="hidden" class="form-control propertyRole" data-name="'+roles1+'" id="propertyRole" name="PropertyRoleUID[]" value="'+roles+'" readonly>';

            table += '<input type="hidden" class="form-control propertytitle"  id="propertytitle" name="PRTitle[]" value="'+title+'" readonly>';

            table += '<input type="hidden" class="form-control propertyName"  id="propertyName" name="PRName[]" value="'+name+'" readonly>';
            table += '<input type="hidden" class="form-control propertyTelephone"  id="propertyTelephone" name="PRHomeNumber[]" value="'+telephone+'" readonly>';
            table += '<input type="hidden" class="form-control propertyWorkNO"  id="propertyWorkNO" name="PRWorkNumber[]" value="'+workNo+'" readonly>';
            table += '<input type="hidden" class="form-control propertyCellNo"  id="propertyCellNo" name="PRCellNumber[]" value="'+cellNo+'" readonly>';
            table += '<input type="hidden" class="form-control propertySocialNO"  id="propertySocialNO" name="PRSocialNumber[]" value="'+socialNo+'" readonly>';
            table += '<input type="hidden" class="form-control propertyEmail"  id="propertyEmail" name="PREmailID[]" value="'+email+'" readonly>';
            table += '<input type="hidden" class="form-control propertyMailCheck"  id="propertyMailCheck" name="propertyMailCheck" value="'+mailCheck+'" readonly>';

            table += '<input type="hidden" class="form-control propertymailingaddress1"  id="propertymailingaddress1" name="MailingAddress1[]" value="'+mailingaddress1+'" readonly>';
            table += '<input type="hidden" class="form-control propertymailingaddress2"  id="propertymailingaddress2" name="MailingAddress2[]" value="'+mailingaddress2+'" readonly>';
            table += '<input type="hidden" class="form-control propertymailingzipcode"  id="propertymailingzipcode" name="MailingZipCode[]" value="'+mailingzipcode+'" readonly>';
            table += '<input type="hidden" class="form-control propertymailingcity"  id="propertymailingcity" name="MailingCityName[]" value="'+mailingcity+'" readonly>';
            table += '<input type="hidden" class="form-control propertymailingcounty"  id="propertymailingcounty" name="MailingCountyName[]" value="'+mailingcounty+'" readonly>';
            table += '<input type="hidden" class="form-control propertymailingstate"  id="propertymailingstate" name="MailingStateCode[]" value="'+mailingstate+'" readonly>';

            table += '<input type="hidden" class="form-control propertymailingnotes"  id="propertymailingnotes" name="MailingAddressNotes[]" value="'+mailingnotes+'" readonly>';

            table += '<input type="hidden" class="form-control propertyMailCheck"  id="propertyMailCheck" name="chk_mailing[]" value="'+mailCheck+'" readonly>';

            // Siging Values
            // table += '<input type="hidden" class="form-control propertysigningcheck"  id="propertysigningcheck" name="chk_Signing[]" value="'+signinCheck+'" readonly>';
            // table += '<input type="hidden" class="form-control propertysigningaddress1"  id="propertysigningaddress1" name="SigningAddress1[]" value="'+signingaddress1+'" readonly>';
            // table += '<input type="hidden" class="form-control propertysigningaddress2"  id="propertysigningaddress2" name="SigningAddress2[]" value="'+signingaddress2+'" readonly>';
            // table += '<input type="hidden" class="form-control propertysigningzipcode"  id="propertysigningzipcode" name="SigningZipCode[]" value="'+signingzipcode+'" readonly>';
            // table += '<input type="hidden" class="form-control propertysigningcity"  id="propertysigningcity" name="SigningCityName[]" value="'+signingcity+'" readonly>';
            // table += '<input type="hidden" class="form-control propertysigningcounty"  id="propertysigningcounty" name="SigningCountyName[]" value="'+signingcounty+'" readonly>';
            // table += '<input type="hidden" class="form-control propertysigningstate"  id="propertysigningstate" name="SigningStateCode[]" value="'+signingstate+'" readonly>';
            // table += '<input type="hidden" class="form-control propertysigningAddressnotes"  id="propertysigningAddressnotes" name="SigningAddressNotes[]" value="'+signingAddressnotes+'" readonly>';
            // table += '<input type="hidden" class="form-control propertysigningnotes"  id="propertysigningnotes" name="SigningSpecialInstruction[]" value="'+signingnotes+'" readonly>';
            table += '</td>'; 
            table += '</tr>';           

            // isInvalid = validationPropertyRole(productname);

            // if (isInvalid) 
            // {
            //   return false;
            // }else{
                  $('.tableDisplay').show();
                  $("#propertyRoleTable tbody").append(table);            
                  // propertyroleTableCheck();              
                  $('.multiplePropertyAddTable').html(''); 
                  $('.SaveUpdate').hide();
                  $('.select2').select2();
            // }
        });
 $('#UpdateRoles').click(function(event) {

    event.preventDefault();

    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();


    var OrderUID = $('#OrderUID').val();

    var LoanAmount = $('#LoanAmount').val();

    if(LoanAmount){
      LoanAmount = LoanAmount.replace(/[,$]/g , '');
      var LoanAmount = Number(LoanAmount);
    }
    else{

      var LoanAmount = 0;
    }

    var formData=$('#frm_OrderSummary').serialize()+'&'+$.param({ 'LoanAmount': LoanAmount });
    $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>Order_summary/save_roles',
     data: formData,
     dataType:'json',
     cache: false,
     beforeSend: function(){
      $('.loading').show()
      button.attr("disabled", true);
      button.html('Loading ...');
    },
    success: function(data)
    {
      $('.loading').hide()
      if(data['validation_error'] == 0){
        
        toastr["success"]("", data['message']);


      }else{
        
        toastr["error"]("", data['message']);
        button.html(button_text);
        button.removeAttr("disabled");


        $.each(data, function(k, v) {
              // $('#'+k).nextAll('span:first').html(v);
              $('#'+ k +'.select2').next().find('span.select2-selection').addClass('errordisplay');

              $('#'+k).closest('div.is_error').addClass('is-invalid');

            });

        if (data.MailingAddressValidation) {

          var MailingAddressValidation = data.MailingAddressValidation;
          for(var key1 in MailingAddressValidation){
            if (!MailingAddressValidation.hasOwnProperty(key1)) continue;
            var obj = MailingAddressValidation[key1];

            for(var key2 in obj){
              if (!obj.hasOwnProperty(key2)) continue;
              $('.other_address').eq(Number(key1)).find('.'+key2).closest('div.is_error').addClass('is-invalid');
              /*console.log("$('.other_address').eq("+key1+").find(."+key2+").closest('div.is_error').addClass('is-invalid')");*/

            }
          }
        }

        if (data.SigningAddressValidation) {

          var SigningAddressValidation = data.SigningAddressValidation;
          for(var key1 in SigningAddressValidation){
            if (!SigningAddressValidation.hasOwnProperty(key1)) continue;
            var obj = SigningAddressValidation[key1];

            for(var key2 in obj){
              if (!obj.hasOwnProperty(key2)) continue;
              $('.signing_other_address').eq(Number(key1)).find('.'+key2).closest('div.is_error').addClass('is-invalid');
              /*console.log("$('.other_address').eq("+key1+").find(."+key2+").closest('div.is_error').addClass('is-invalid')");*/

            }
          }
        }
      }


    },
    error: function (jqXHR, textStatus, errorThrown) {

      console.log(errorThrown);

    },
    failure: function (jqXHR, textStatus, errorThrown) {

      console.log(errorThrown);

    },
  });
  });

</script>