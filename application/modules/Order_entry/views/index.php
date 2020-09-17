
<?php


$IsUpper = $this->common_model->GetOrganizations();
$UserUID = $this->session->userdata('UserUID');
$RoleUID = $this->session->userdata('RoleUID');
$UserRole = $this->common_model->GetRoleTypeDetails($RoleUID);
$CustomerDetails = $this->common_model->GetCustomerByUserUID($UserUID);
$Products = $this->Common_model->getCustomerProducts(1);
$Sub_products = $this->Common_model->GetSub_productDetails();
$BorrowerDetails = $this->Common_model->GetBorrowerDetailsDescription();
$PropertyTypeDetails = $this->Common_model->GetPropertyTypeDetails();
$Prop_roles=$this->Common_model->GetPropertyrolesDetails();


// if($UserRole->RoleType==8){
//     $CustomerDetails = $this->common_model->GetCustomerByUserUID($UserUID);
//             // echo '<pre>';print_r($CustomerDetails->CustomerUID);exit;    
//     if($CustomerDetails->CustomerUID!=12){
//         $CustomerUID =0;
//     }else{
//         $CustomerUID =1;
//     }
// }else{
//     $CustomerUID =0;
// }



?>


<!-- start body header -->
<div id="page_top" class="section-body">
    <div class="container-fluid">
        <form id="frmOrderEntry" enctype="multipart/form-data">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Place Order</h1> 
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
        <h5 class="pb-1 inner-page-head">Single Order</h5>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 pl-0">
                                    <h3 class="card-title pt-3">Product Info</h3>
                                </div>

                                <div class="col-md-6 col-lg-6 text-right pr-0 pt-2">

                                    <a href="Order_entry/bulk_entry" class="btn btn-info btn-sm">Bulk Order</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12 col-lg-12 add-product-div p-0">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">

                                    <div class="form-group">
                                        <label class="form-label" for="customer">Client</label>
                                        <select name="customer" id="customer" class="form-control select2">
                                            <?php 
                                            $selected = '';
                                            $cust_no = '';
                                            foreach ($Customers as $key => $customer) { 
                                                if($customer->CustomerUID == $CustomerUIDEntry){
                                                    echo '<option value="'.$customer->CustomerUID.'" selected>'.$customer->CustomerNumber.' / '. $customer->CustomerName.'</option>';
                                                }
                                                else{
                                                    echo '<option value="'.$customer->CustomerUID.'">'.$customer->CustomerNumber.' / '. $customer->CustomerName.'</option>';
                                                }
                                            } ?>   
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label" for="ProductUID">Product</label>
                                        <select name="ProductUID[] " class="form-control select2 ProductUID">
                                           <?php foreach ($Products as $key => $value) { ?>
                                            <option value="<?php echo $value->ProductUID  ?>"><?php echo $value->ProductName ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                                <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label" for="SubProductUID">SubProduct</label>
                                        <select name="SubProductUID[]" class="form-control select2 SubProductUID">
                                            <?php foreach ($SubProduct as $key => $value) { print_r($value);  ?>
                                            <option value="<?php echo $value->ProductUID  ?>"><?php echo $value->ProductName ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-lg-2">
                                    <div class="form-group">
                                        <label class="form-label" for="PriorityUID">Priority </label>
                                        <select name="PriorityUID[]" class="form-control select2 PriorityUID" id="PriorityUID">
                                            <option></option>
                                            <option value="1">Rush</option>
                                            <option value="4">Normal</option>
                                            <option value="3">ASAP</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1 col-lg-1 text-right">
                                    <div class="form-group mt-4 pt-1">
                                        <i class="btn btn-primary btn-md fe fe-plus productadd_button"></i>
                                    </div>
                                </div>

                            <div class="col-md-12 col-lg-12 multipleProductAddTable" id="multipleProductAddTable">

                            </div>


                            </div>
                            
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
                                        <input type="text" class="form-control" id="LoanNumber" name="LoanNumber" placeholder="">
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
                                        <input type="text" class="form-control money-dollar" id="LoanAmount" name="LoanAmount"placeholder="">
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
                                        <input type="text" class="form-control" id="CustomerRefNum" name="CustomerRefNum" placeholder="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">APN</label>
                                    <input type="text" class="form-control" id="APN" name="APN" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Borrower Type</label>
                                    <select  class="form-control select2" id="BorrowerType" name="BorrowerType">
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
                                    <select  class="form-control select2" id="PropertyType" name="PropertyType">
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
                                    <select  class="form-control select2" id="TransactionType" name="TransactionType">
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
                                    <input type="text" class="form-control PropertyAddress1" id="PropertyAddress1" name="PropertyAddress1" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Address Line 2</label>
                                    <input type="text" class="form-control PropertyAddress2" id="PropertyAddress2" name="PropertyAddress2" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Zipcode</label>
                                    <input type="text" class="form-control PropertyZipcode"  id="PropertyZipcode" name="PropertyZipcode" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control PropertyCityName" id="PropertyCityName" name="PropertyCityName" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">County</label>
                                    <input type="text" class="form-control PropertyCountyName" id="PropertyCountyName" name="PropertyCountyName" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">State</label>
                                    <input type="text" class="form-control PropertyStateCode" id="PropertyStateCode" name="PropertyStateCode" name="" placeholder="">
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
                                    <input type="text" class="form-control" id="SendReportTo" name="SendReportTo" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Email Report To</label>
                                    <input type="text" class="form-control" id="AttentionName" name="AttentionName" placeholder="">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header pb-0">

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 pl-0">
                                    <h3 class="card-title pt-3">Property Roles</h3>
                                </div>

                                <div class="col-md-6 col-lg-6 text-right pr-0 pt-2">

                                    <a class="btn btn-info btn-sm add_property_role">Add New</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12 col-lg-12">

                                <div class="table-responsive tableDisplay" style="display: none;">
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
                                           
                                        </tbody>
                                    </table>
                                </div>
                                <!-- <div class="row clearfix p-2 add_property_div" id="add_property_div">
                                   
                                </div> -->
                           
                        
                            <div class="col-md-12 col-lg-12 multiplePropertyAddTable" id="multiplePropertyAddTable">
                            </div>

                            <div class="row MailingContentAdd" id="MailingDetailsAdd" style="display: none;">
                                <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing Address 1</label>
                                    <input type="text" class="form-control mailingaddress1" id="mailingaddress1" name="mailingaddress1" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing Address 2</label>
                                    <input type="text" class="form-control mailingaddress2" id="mailingaddress2" name="mailingaddress2" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing Zipcode</label>
                                    <input type="text" class="form-control mailingzipcode" id="mailingzipcode" name="mailingzipcode" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing City</label>
                                    <input type="text" class="form-control mailingcity" id="mailingcity" name="mailingcity" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing County</label>
                                    <input type="text" class="form-control mailingcounty" id="mailingcounty" name="mailingcounty" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label class="form-label">Mailing State</label>
                                    <input type="text" class="form-control mailingstate" id="mailingstate" name="mailingstate" placeholder="">
                                </div>
                            </div>
                            </div>

                           
                           <!--  <div class="col-md-12 col-lg-12 mb-3 SaveUpdate" style="display: none">
                                <div class="btn-list  text-right">
                                    <a href="#" class="btn btn-secondary add_cancel" id="add_cancel" data-attr="1" >Cancel</a>
                                    <a href="#" class="btn btn-primary add_role" id="add_role">Save</a>
                                </div>
                            </div> -->
                        </div>

</form>
                    </div>
                </div>

                <div class="col-md-12 col-lg-12 mb-3 mt-4">
                    <div class="btn-list text-right">
                        <a href="#" class="btn btn-success saveandneworder" value="1" id="saveandneworder">Place Order</a>
                    </div>
                </div>
            </div>
        
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">

     /**
        * @author Sathis Kannan P
        * @purpose Customer Onchange to ProductGet
        * Click event for # customer
        * @date 12-09-2020
        * @version D2T Assignment
        */

        $('#customer').on('change',function() {
            ajax_load_cutomer_data();
        });

        function ajax_load_cutomer_data()
        {
            customerid = $('#customer').val(); 
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>Order_entry/getproductsubproduct',
                data: {'CustomerUID':customerid},
                dataType:'json',
                beforeSend: function(){
                    $('.spinnerclass').addClass("be-loading-active"); 
                },
                success: function(data)
                { 
                    $('.ProductUID').empty();
                    $('.SubProductUID').empty(); 
                    $('#EmailReportTo').val(''); 
                    $('.PriorityUID').empty(); 
                    if(data['success'] == 1)
                    {
                        Products = data['data']['Products'];

                        <?php 
                        if(in_array($this->session->userdata('RoleType'), array(1,2,3,4,5,6)))
                        { 
                            ?>
                            <?php } ?>
                              $.each(data['data']['Products'], function(k, v) {
                                $('.ProductUID').append('<option value="' + v['ProductUID'] + '" data-IsRMSProduct="'+v['IsRMSProduct']+'" >' + v['ProductName'] + '</option>');
                            });

                            $('.ProductUID').val($('.ProductUID option:first-child').val()).trigger('change');
                            $('.select2').select2();


                            <?php 
                            if(in_array($this->session->userdata('RoleType'), array(1,2,3,4,5,6))) { ?>

                            <?php } ?>


                                if(data['data']['CustomerDetails']['PriorityUID'] == '0')
                                {
                                    $.each(data['priority'], function(k, v) {

                                        if(v['PriorityName'] == 'Normal')
                                        {
                                            $('.PriorityUID').append('<option value="' + v['PriorityUID'] + '" selected>' + v['PriorityName'] + '</option>');

                                        }
                                        else{
                                            $('.PriorityUID').append('<option value="' + v['PriorityUID'] + '" >' + v['PriorityName'] + '</option>');
                                        }
                                    });


                                }else{

                                $.each(data['priority'], function(k, v) {

                                    if(v['PriorityUID'] == data['data']['CustomerDetails']['PriorityUID'])
                                    {
                                        $('.PriorityUID').append('<option value="' + v['PriorityUID'] + '" selected>' + v['PriorityName'] + '</option>');

                                    }
                                    else{
                                        $('.PriorityUID').append('<option value="' + v['PriorityUID'] + '" >' + v['PriorityName'] + '</option>');
                                    }
                                });

                            }

                            //$('#EmailReportTo').val(data['data']['CustomerDetails']['CustomerOrderAckEmailID']);

                            var EmailReportTo ='';

                            if(data['data']['CustomerDetails']['CustomerOrderAckEmailID'] != ''){
                                EmailReportTo = data['data']['CustomerDetails']['CustomerOrderAckEmailID'];
                            }
                            else{
                                EmailReportTo = data['data']['UserDetails']['UserEmailID'];
                            }

                        $('#EmailReportTo').val(EmailReportTo);
                                                
                        <?php
                        if($this->session->userdata('RoleType') != 8)
                        {?>
                            $('#AttentionName').val(data['data']['CustomerDetails']['CustomerPContactName']);
                            <?php } ?>

                            <?php
                            if(in_array($this->session->userdata('RoleType'),array(8)))
                            { 
                                ?>
                                // $('#PropertyZipcode').val(data['data']['CustomerDetails']['CustomerZipCode']).trigger('change');
                                // $('#PropertyAddress1').val(data['data']['CustomerDetails']['CustomerAddress1']);
                                // $('#PropertyZipcode').parent().addClass('is-dirty');
                                // $('#PropertyAddress1').parent().addClass('is-dirty');
                                <?php 
                            }
                            ?>
                            $('#EmailReportTo').parent().addClass('is-dirty'); 
                            $('#AttentionName').parent().addClass('is-dirty'); 
                            $('.ProductUID').parent().addClass('is-dirty');
                            $('.SubProductUID').parent().addClass('is-dirty');
                            $('.PriorityUID').parent().addClass('is-dirty');

                            /*-------- Add Customer BraNCEHS -------*/
                            var BranchUID = $('#BranchUID').val();
                            var Branches = data['data'].CustomerBranches.reduce(function (accumulator, elem, index) {
                                return accumulator + '<option value="'+elem.BranchUID+'">'+elem.BranchName+'</option>';
                            }, '<option></option>');

                            $('#BranchUID').html(Branches);
                            if (BranchUID) {
                                $('#BranchUID').val(BranchUID);        
                            }
                            $('#modal_add_customer_branch input').val('');
                            $('#modal_add_customer_branch select').val('');
                            $('#modal_add_customer_branch').modal('hide');
                            $('.select2').select2({
                                theme: "bootstrap",
                            });
                    }else{
                        $('#EmailReportTo').parent().removeClass('is-dirty');
                        $('#AttentionName').parent().addClass('is-dirty'); 
                        $('.ProductUID').parent().removeClass('is-dirty');
                        $('.ProductUID').trigger('change');
                        $('.SubProductUID').parent().removeClass('is-dirty') 
                        $('.SubProductUID').trigger('change'); 
                        $('#PropertyZipcode').parent().removeClass('is-dirty');
                        $('.PriorityUID').parent().removeClass('is-dirty');
                        $('#LoanNumber').parent().removeClass('is-dirty');
                    } 
                        $('.spinnerclass').removeClass("be-loading-active"); 

                        if(window.location.hash=='#help') 
                        {
                            if($('#joyRideTipContent').joyride())
                            {
                                $('#joyRideTipContent').joyride('destroy');
                                joyride_tour(1);
                                <?php 
                                if(in_array($this->session->userdata('RoleType'), array(1,2,3,4,5,6)))
                                { 
                                    ?>
                                    $('#ProductUID').select2('open'); 
                                    <?php } ?>
                                }
                            }
                        
                        //To display rms div if rms product selected
                        // displayrmspanel();

                        },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown); 
                    },
                    failure: function (jqXHR, textStatus, errorThrown) { 
                        console.log(errorThrown); 
                    },
                })

    }
     /**
        * @author Sathis Kannan P
        * @purpose mailing zipcode change
        * @param nothing
        * blur event for .mailingzipcode
        * @date 14-09-2020
        * @version New Assignment
        */

         $(document).off('blur', '.mailingzipcode').on('blur', '.mailingzipcode', function(e){
            e.preventDefault();

            zip_val = $(this).val().replace(/[^a-z0-9\s]/gi, '');
            
            // Check Zipcode less then 5 digits
            if(zip_val.length != 5 && zip_val.length != 9 && zip_val != '' )
            {
                toastr['error']("", "Zipcode Invalid!");
                $('#spnInvalidZipcode').show();
                $('.PropertyCityName, .PropertyCountyName, .PropertyStateCode').empty();
                $('#PropertyCityName, #PropertyCountyName, #PropertyStateCode').val('');
                return true;  
            } else {
                  $('#spnInvalidZipcode').hide(); 
            }

            // var OtherAddressContext = $(this).closest('.mailingAddressFields');
            $.ajax({
              type: "GET",
              url: 'Order_entry/GetZipCodeDetails',
              data: {'Zipcode':zip_val}, 
              dataType:'json',
              cache: false,
              beforeSend: function () {
                       $('.spinnerclass').addClass("be-loading-active");
                    },
              success: function(data)
                    {                        
                        $('.spinnerclass').removeClass("be-loading-active");
                        $('#spnInvalidState').hide();
                        $('.mailingcity').empty();
                        $('.mailingcounty').empty();
                        $('.mailingstate').empty();
                        $('.mailingcity').html(' ');
                        $('.mailingcounty').html(' ');
                        $('.mailingstate').html(' ');

                        if(data != ''){

                            if(data['success'] == 1)
                            {

                              if(data['City'].length > 1){
                                $('.mailingcity').html(' ');
                                $('.mailingcity').append('<span class="badge badge-danger cus-badge" style="background: #eb6357;color: #fff; z-index: 9999; top: -16px; right: -20px;">'+data['City'].length+'</span>');
                            }

                            if(data['County'].length > 1){
                                $('.mailingcounty').html(' ');
                                $('.mailingcounty').append('<span class="badge badge-danger cus-badge" style="background: #eb6357;color: #fff; z-index: 9999; top: -16px; right: -20px;">'+data['County'].length+'</span>');
                            } 

                            if(data['State'].length > 1){
                                $('.mailingstate').html(' ');
                                $('.mailingstate').append('<span class="badge badge-danger cus-badge" style="background: #eb6357;color: #fff; z-index: 9999; top: -16px; right: -20px;">'+data['State'].length+'</span>');
                            } 


                            $.each(data['City'], function(k, v) {
                                
                                $('#mailingcity').val(v['CityUID']);
                                $('#mailingcity').val(v['CityName']);
                                $('.mailingcity').append('<li><a href="javascript:(void);" data-value="' + v['CityName'] + '">' + v['CityName'] + '</a></li>');
                                $('#mailingcity').parent().parent().addClass('is-dirty');
                                // zipcode_select();
                            });

                            $.each(data['County'], function(k, v) {
                                
                                $('#mailingcounty').val(v['CountyUID']);
                                $('#mailingcounty').val(v['CountyName']);
                                $('.mailingcounty').append('<li><a href="javascript:(void);" data-value="' + v['CountyName'] + '">' + v['CountyName'] + '</a></li>');
                                $('#mailingcounty').parent().parent().addClass('is-dirty'); 
                                  // zipcode_select();
                            });

                            $.each(data['State'], function(k, v) {
                                
                                $('#mailingstate').val(v['StateUID']);
                                $('#mailingstate').val(v['StateCode']);
                                $('.mailingstate').append('<li><a href="javascript:(void);" data-value="' + v['StateCode'] + '">' + v['StateCode'] + '</a></li>');
                                $('#mailingstate').parent().parent().addClass('is-dirty');
                                  // zipcode_select();
                            });
                            $('#PropertyStateCode').trigger('change');

                            }
                            else
                            {
                                toastr['error']("", "Zipcode Invalid!"); 

                                $('#PropertyCityName').val('');
                                $('#PropertyCityName').parent().parent().removeClass('is-dirty');

                                $('#PropertyCountyName').val('');
                                $('#PropertyCountyName').parent().parent().removeClass('is-dirty'); 

                                $('#PropertyStateCode').val('');
                                $('#PropertyStateCode').parent().parent().removeClass('is-dirty');                

                                $('#spnInvalidZipcode').show();
                                

                            }              
                      // $('#joyRideTipContent').joyride('destroy');
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
        /** END */

      /**
        * @author Sathis Kannan P
        * @purpose ProductName Change to Get Subproduct 
        * change event for .ProductUID
        * @date 12-09-2020
        * @version D2T Assignment
        */
        $(document).off('change', '.ProductUID').on('change', '.ProductUID', function(event)
        {
            event.preventDefault();
            productOnChange();

        });

        function productOnChange()
        {
            var CustomerUID  = $('#customer :selected').val(); 
            var ProductUID = $('.ProductUID :selected').val();
            var ProductName = $('.ProductUID :selected').text();
            // if (ProductName == 'Closing') 
            // { 
            //   $('.closingProductInfo').show(); 
            //   $('.closingProductInputs').show();
            // }
            // else
            // {
            //     $('.closingProductInfo').hide();
            //     $('.closingProductInputs').hide();
            // } 

            $.ajax({
              url:      '<?php echo base_url().'Order_entry/get_subproduct' ?>',
              type:     'GET',
              dataType: 'JSON',
              data:     {ProductUID: ProductUID,CustomerUID:CustomerUID},
              beforeSend: function () {
                    $('.spinnerclass').addClass('be-loading-active');
                },
                success: function (data) {
                    $('.spinnerclass').removeClass('be-loading-active');
                   

                     $.each(data.SubProduct, function(k, v) {
                                $('.SubProductUID').append('<option value="' + v['SubProductUID'] + '" data-SubProduct="'+v['SubProduct']+'" >' + v['SubProductName'] + '</option>'); });
                      $.each(data.priority, function(k, v) {
                                $('.PriorityUID').append('<option value="' + v['PriorityUID'] + '" data-Priority="'+v['Priority']+'" >' + v['PriorityName'] + '</option>'); });
                    // $('.SubProductUID').html(data.SubProduct); 
                    // $('.ProjectUID').html(data['ProjectDetails']);  
                    // $('.PriorityUID').html(data.priority);               
                    $('.SubProductUID').val($('.SubProductUID option:first-child').val()).trigger('change');
                },
            });
        }
        /** END */



        $(document).on('click', '.productadd_button', function(event)
        {
          event.preventDefault();
          /* Act on the event */             
          var id  = $(this).attr('id');
          var Button = 'Delete';
          $('.multipleProductAddTable').append(multipleProductTable(Button));

          $('.select2').select2();
          $('.ProductUID1:last').val($('.ProductUID1:last option:first').val()).trigger('change');
        });

        $(document).on('click', '.productremove_button', function(event)
        {
          event.preventDefault();

          /* Act on the event */
          var id = $(this).attr('id');
          $("#rowID_" + id).remove();
        });

        var id  = 1;
        function multipleProductTable(Button)
        {
            id++;
            var multipleProductFields;

            multipleProductFields  = '<div class="row" id="rowID_'+id+'">';

            multipleProductFields +='<div class="col-md-3 col-lg-3">';
            multipleProductFields +='<div class="form-group">';
            multipleProductFields +='<label class="form-label">Product</label>';
            multipleProductFields +='<select name="beast ProductUID[] " class="form-control select2 ProductUID1">';
            <?php foreach ($Products as $key => $value) { ?>
                multipleProductFields +='<option value="<?php echo $value->ProductUID  ?>"><?php echo $value->ProductName ?></option>';
            <?php } ?>
            multipleProductFields +='</select>';
            multipleProductFields +='</div>';
            multipleProductFields +='</div>';    

             multipleProductFields += '<div class="col-md-3 col-lg-3">';
             multipleProductFields += '<div class="form-group">';
             multipleProductFields += '<label class="form-label">SubProduct</label>';
             multipleProductFields += '<select name="beast SubProductUID[]" class="form-control Subproduct1">'; 
             multipleProductFields += '</select>';
             multipleProductFields += '</div>';
             multipleProductFields += '</div>';
             multipleProductFields += '<div class="col-md-3 col-lg-3">';
             multipleProductFields += '<div class="form-group">';
             multipleProductFields += '<label class="form-label">Priority </label>';
             multipleProductFields += '<select name="beast PriorityUID[]" class="form-control select2 Priority1" id="PriorityUID">';
             multipleProductFields += '</select>';
             multipleProductFields += '</div>';
             multipleProductFields += '</div>';        

            
            if (Button == 'Add') 
            {
                multipleProductFields += '<div class="col-lg-1 col-md-12">';
                multipleProductFields += '<i class="fa fa-plus-circle productadd_button" id="'+id+'" style="padding: 27px 0px;margin: 5px 5px;font-size: 30px;position: relative;top: -7px;"></i>';
                multipleProductFields += '</div>';
                multipleProductFields += '</div>';
            }
            else 
            {
                multipleProductFields += '<div class="col-lg-1 col-md-12">';
               
               multipleProductFields += ' <i class="btn btn-danger btn-md fe fe-minus productremove_button" id="'+id+'" style="margin: 29px 0px 0px 0px"></i>';
                multipleProductFields += '</div>';
                multipleProductFields += '</div>';            
            }              

            $('.select2').select2();
            return multipleProductFields;
        }
        /**
        * @author Sathis Kannan P
        * @purpose Add New Property event
        * @param nothing
        * change event for .add_property_role
        * @date 14-09-2020
        * @version New Assignment
        */
        $(document).off('click', '.add_property_role').on('click', '.add_property_role', function(e){
            e.preventDefault(); 
            $('.SaveUpdate').show();
        });

        /**
        * @author Sathis Kannan
        * @purpose Product onchange event
        * @param nothing
        * change event for .ProductUID1
        * @date 13-09-2020
        * @version Client Portal New Theme
        */

        $(document).off('change', '.ProductUID1').on('change', '.ProductUID1', function(event) {

            event.preventDefault();
            var CustomerUID  = $('#customer :selected').val(); 
            var ProductUID = $(this).children("option:selected").val();
            var ProductName = $(this).children("option:selected").text();
            var crow = $(this).closest('.row');

            // if (ProductName == 'Closing') 
            // { 
            //   $('.closingProductInfo').show(); 
            //   $('.closingProductInputs').show();
            // }
            // else
            // {
            //     $('.closingProductInfo').hide();
            //     $('.closingProductInputs').hide();
            // } 

            $.ajax({
                url:      '<?php echo base_url().'Order_entry/get_subproduct' ?>',
                type:     'GET',
                dataType: 'JSON',
                data:     {ProductUID: ProductUID,CustomerUID:CustomerUID},
                beforeSend: function () {
                  $('.spinnerclass').addClass("be-loading-active");
              },
              success: function (data) {
               var subproductoption = "";
               var priorityoption = "";

                $.each(data.SubProduct, function(k, v) {
                    subproductoption += '<option value="' + v['SubProductUID'] + '" data-SubProduct="'+v['SubProduct']+'" >' + v['SubProductName'] + '</option>'; });

                $.each(data.priority, function(k, v) {
                    priorityoption += '<option value="' + v['PriorityUID'] + '" data-Priority="'+v['Priority']+'" >' + v['PriorityName'] + '</option>'; });
               
                crow.find('.Subproduct1').html(subproductoption);  
                crow.find('.Priority1').html(priorityoption);      
                crow.find('.Subproduct1').select2();
                crow.find('.Priority1').select2();

                $('.spinnerclass').removeClass("be-loading-active");
                crow.find('.SubProductUID1').val(crow.find('.SubProductUID1 option:first-child').val()).trigger('change');
            },
        });
        });
        /** END */

        /**
        * @author Sathis Kannan P
        * @purpose Order entry Zipcode KeyUp function
        * @param nothing
        * onkeyup event for #PropertyStateCode
        * @date 13-September-2020
        * @version D2T Assignment
        */

        $('#PropertyZipcode').keyup(function() 
        {
            var foo = $(this).val().split("-").join(""); // remove hyphens
            if (foo.length > 0) {
              foo = foo.match(new RegExp('.{1,5}', 'g')).join("-");
            }
            $(this).val(foo);
        });

        $('#PropertyZipcode').blur(function(event) 
        {
            zip_val = $(this).val().replace(/[^a-z0-9\s]/gi, '');
            
            // Check Zipcode less then 5 digits
            if(zip_val.length != 5 && zip_val.length != 9 && zip_val != '' )
            {
                toastr['error']("", "Zipcode Invalid!");
                $('#spnInvalidZipcode').show();
                $('.PropertyCityName, .PropertyCountyName, .PropertyStateCode').empty();
                $('#PropertyCityName, #PropertyCountyName, #PropertyStateCode').val('');
                return true;  
            } else {
                  $('#spnInvalidZipcode').hide(); 
            }
            if(zip_val!='')
            {
                
                $.ajax({
                    type: "GET",
                    url: 'Order_entry/GetZipCodeDetails',
                    data: {'Zipcode':zip_val}, 
                    dataType:'json',
                    cache: false,
                    beforeSend: function () {
                       $('.spinnerclass').addClass("be-loading-active");
                    },
                    success: function(data)
                    {                        
                        $('.spinnerclass').removeClass("be-loading-active");
                        $('#spnInvalidState').hide();
                        $('.PropertyCityName').empty();
                        $('.PropertyStateCode').empty();
                        $('.PropertyCountyName').empty();
                        $('.MultiOrderedcity').html(' ');
                        $('.MultiOrderedcounty').html(' ');
                        $('.MultiOrderedstate').html(' ');

                        if(data != ''){

                            if(data['success'] == 1)
                            {

                              if(data['City'].length > 1){
                                $('.MultiOrderedcity').html(' ');
                                $('.MultiOrderedcity').append('<span class="badge badge-danger cus-badge" style="background: #eb6357;color: #fff; z-index: 9999; top: -16px; right: -20px;">'+data['City'].length+'</span>');
                            }

                            if(data['County'].length > 1){
                                $('.MultiOrderedcounty').html(' ');
                                $('.MultiOrderedcounty').append('<span class="badge badge-danger cus-badge" style="background: #eb6357;color: #fff; z-index: 9999; top: -16px; right: -20px;">'+data['County'].length+'</span>');
                            } 

                            if(data['State'].length > 1){
                                $('.MultiOrderedstate').html(' ');
                                $('.MultiOrderedstate').append('<span class="badge badge-danger cus-badge" style="background: #eb6357;color: #fff; z-index: 9999; top: -16px; right: -20px;">'+data['State'].length+'</span>');
                            } 


                            $.each(data['City'], function(k, v) {
                                $('#PropertyCityName').val(v['CityName']);
                                $('#PropertyCity').val(v['CityUID']);

                                $('.PropertyCityName').append('<li><a href="javascript:(void);" data-value="' + v['CityName'] + '">' + v['CityName'] + '</a></li>');
                                $('#PropertyCityName').parent().parent().addClass('is-dirty');
                                // zipcode_select();
                            });

                            $.each(data['County'], function(k, v) {
                                $('#PropertyCountyName').val(v['CountyName']);
                                $('#PropertyCountyUID').val(v['CountyUID']);

                                $('.PropertyCountyName').append('<li><a href="javascript:(void);" data-value="' + v['CountyName'] + '">' + v['CountyName'] + '</a></li>');
                                $('#PropertyCountyName').parent().parent().addClass('is-dirty'); 
                                  // zipcode_select();
                            });

                            $.each(data['State'], function(k, v) {
                                $('#PropertyStateCode').val(v['StateCode']);
                                $('#PropertyStateUID').val(v['StateUID']);

                                $('.PropertyStateCode').append('<li><a href="javascript:(void);" data-value="' + v['StateCode'] + '">' + v['StateCode'] + '</a></li>');
                                $('#PropertyStateCode').parent().parent().addClass('is-dirty');
                                  // zipcode_select();
                            });
                            $('#PropertyStateCode').trigger('change');

                            }
                            else
                            {
                                toastr['error']("", "Zipcode Invalid!"); 

                                $('#PropertyCityName').val('');
                                $('#PropertyCityName').parent().parent().removeClass('is-dirty');

                                $('#PropertyCountyName').val('');
                                $('#PropertyCountyName').parent().parent().removeClass('is-dirty'); 

                                $('#PropertyStateCode').val('');
                                $('#PropertyStateCode').parent().parent().removeClass('is-dirty');                

                                $('#spnInvalidZipcode').show();
                                

                            }              
                      // $('#joyRideTipContent').joyride('destroy');
                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        console.log(errorThrown);

                    },
                    failure: function (jqXHR, textStatus, errorThrown) {

                        console.log(errorThrown);

                    },
                });
            }
            else
            {
              $('#PropertyCityName').val('');
              $('#PropertyCityName').parent().parent().removeClass('is-dirty');

              $('#PropertyCountyName').val('');
              $('#PropertyCountyName').parent().parent().removeClass('is-dirty'); 

              $('#PropertyStateCode').val('');
              $('#PropertyStateCode').parent().parent().removeClass('is-dirty');
              toastr["error"]("", "Zipcode Invalid!");
            }     
        });
        /** END */


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
          var id = $(this).attr('data-attr');

          $("#rowID_" + id).remove();
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
             multiplePropertyFields += '<option value="<?php echo $value->PropertyRoleUID; ?>"><?php echo $value->PropertyRoleName; ?></option>';
            <?php } ?>
             multiplePropertyFields  += ' </select>';
             multiplePropertyFields  += '</div>';
             multiplePropertyFields  += '</div>';

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
            table  += '<p class="mb-0" title="'+name+'" id="personName">'+name+'</p>';
            table  += '<span class="text-muted font-13" id="personRole" title="'+roles+'">'+roles+'</span>';
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

         $(document).on('click', '.add_cancel', function(event) {
            event.preventDefault();
            /* Act on the event */
            // $('.multiplePropertyAddTable').html('');
            // $('.add_property_role').val('0').prop('selected', true);
             $('.multiplePropertyAddTable').html(''); 
                  $('.SaveUpdate').hide();
                  $('.select2').select2();

            $('.select2').select2();
        });

    /**
        * @author Sathis Kannan P
        * @purpose Property Role Delete
        * @param nothing
        * click event for .propery_row_delete
        * @date 14-09-2020
        * @version Client Portal New Theme
        */
        $('#propertyRoleTable').on('click', '.propery_row_delete', function(e){
            e.preventDefault();
            var id          = $(this).attr('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you delete a Property Role!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                  $(this).closest("tr").remove(); 
                  propertyroleTableCheck();
              }else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled',
                    'Your Property Roles Details is safe :)',
                    'error'
                    );
            }
        });
        });
        /** END */

        function propertyroleTableCheck()
        {
            var isEmpty = document.querySelectorAll("#propertyRoleTable tr").length <= 1;
            if (isEmpty == true)
            {
              $('.tableDisplay').hide();
          }
          else
          {
              $('.tableDisplay').show();
          }
        }


         /**
        * @author Sathis Kannan P
        * @purpose Property Role Inputs Validation
        * @param nothing
        * @date 14-09-2020
        * @version New Assignment
        */

        function validationPropertyRole(productname)
        {
            var isValidate = false;

            var roles = $("#roles").val();
            var name  = $("#name").val();
            var ssn   = $("#ssn").val();

            var mailing = $('input[name="mailingAddress"]:checked');
            var signing = $('input[name="signingAddress"]:checked');


            if (productname == 'Closing')
            {
                if (ssn == '' || ssn == 0 || ssn == null)
                {
                    $('#ssn').css({'border': '1px solid red'});
                    isValidate = true;
                }
            }

            if (roles == '' || roles == 0 || roles == null)
            {
              $('#roles').closest('.form-group').find('select').addClass('errordisplay');
              isValidate = true;
            }

            if (name == '' || name == 0 || name == null)
            {
              $('#name').css({'border': '1px solid red'});
              isValidate = true;
            }

            if (mailing && mailing.val() == 'No') 
            {                
                var validate_mailingAddress = $('#mailingaddress1').val();
                var validate_mailingZipCode = $('#mailingzipcode').val();
                var validate_mailingCityName = $('#mailingcity').val();
                var validate_mailingCountyName = $('#mailingcounty').val();
                var validate_mailingstatecode = $('#mailingstate').val();

                if(validate_mailingAddress == '')
                {
                    $('#mailingaddress1').css({'border': '1px solid red'});
                    isValidate = true;
                }
                if(validate_mailingZipCode == '')
                {
                    $('#mailingzipcode').css({'border': '1px solid red'});
                    isValidate = true;
                }
                if(validate_mailingCityName == '')
                {
                    $('#mailingcity').css({'border': '1px solid red'});
                    isValidate = true;
                }
                if(validate_mailingCountyName == '')
                {
                    $('#mailingcounty').css({'border': '1px solid red'});
                    isValidate = true;
                }
                if(validate_mailingstatecode == '')
                {
                    $('#mailingstate').css({'border': '1px solid red'});
                    isValidate = true;
                }

            }



            // if (signing && signing.val() == 'Other') 
            // {                
            //     var validate_signingAddress = $('#signingaddress1').val();
            //     var validate_signingZipCode = $('#signingzipcode').val();
            //     var validate_signingCityName = $('#signingcity').val();
            //     var validate_signingCountyName = $('#signingcounty').val();
            //     var validate_signingstatecode = $('#signingstate').val();

            //     if(validate_signingAddress == '')
            //     {
            //         $('#signingaddress1').css({'border': '1px solid red'});
            //         isValidate = true;
            //     }
            //     if(validate_signingZipCode == '')
            //     {
            //         $('#signingzipcode').css({'border': '1px solid red'});
            //         isValidate = true;
            //     }
            //     if(validate_signingCityName == '')
            //     {
            //         $('#signingcity').css({'border': '1px solid red'});
            //         isValidate = true;
            //     }
            //     if(validate_signingCountyName == '')
            //     {
            //         $('#signingcounty').css({'border': '1px solid red'});
            //         isValidate = true;
            //     }
            //     if(validate_signingstatecode == '')
            //     {
            //         $('#signingstate').css({'border': '1px solid red'});
            //         isValidate = true;
            //     }
            // }

            return isValidate;

        }


         /**
        * @author Sathis Kannan P
        * @purpose Edit Property Role
        * @param nothing
        * @date 14-09-2020
        * @version New Assignment
        */

        function clearProp() {
            $('.multiplePropertyAddTable').find('input[type=text]').val('');
            $('.multiplePropertyAddTable').find('select').val('');
            $('.multiplePropertyAddTable').find('textarea').val('');

            $('.mailingAddress').prop('checked', false);
            // $('.signingAddress').prop('checked', false);

        }

           count  = 0;
        $('#propertyRoleTable').on('click', '.edit_property_info', function(event) {
            event.preventDefault();
            /* Act on the event */
            count++;
            clearProp();
            var table = $(this).closest("tr");
            var id =$(this).attr('id');
           

            var id, editroles, edittitle, editname, edittelephone, editworkNo, editcellNo, editsocialNo, editemail, editmailingaddress1, editmailingaddress2, editmailingzipcode, editmailingcity, editmailingcounty, editmailingstate, editmailCheck,editsigningaddress1,editsigningaddress2,editsigningzipcode,editsigningcity,editsigningcounty,editmailingstate,editsigningAddressnotes,editsigningnotes,editsignincheck;

            $(table).each(function() {
               
              editroles           = table.find('td #propertyRole').val();
              editroles1          = table.find('td #propertyRole').attr('data-name');
              edittitle           = $(this).find('#propertytitle').val();
              editname            = $(this).find('#propertyName').val();
              edittelephone       = $(this).find('#propertyTelephone').val();
              editworkNo          = $(this).find('#propertyWorkNO').val();
              editcellNo          = $(this).find('#propertyCellNo').val();
              editsocialNo        = $(this).find('#propertySocialNO').val();
              editemail           = $(this).find('#propertyEmail').val();

              editmailingaddress1 = $(this).find('#propertymailingaddress1').val();
              editmailingaddress2 = $(this).find('#propertymailingaddress2').val();
              editmailingzipcode  = $(this).find('#propertymailingzipcode').val();
              editmailingcity     = $(this).find('#propertymailingcity').val();
              editmailingcounty   = $(this).find('#propertymailingcounty').val();
              editmailingstate    = $(this).find('#propertymailingstate').val();
              editmailingnotes    = $(this).find('#propertymailingnotes').val();
              editmailCheck       = $(this).find('#propertyMailCheck').val();

              // Signing Values
              // editsigningaddress1     = $(this).find('#propertysigningaddress1').val();
              // editsigningaddress2     = $(this).find('#propertysigningaddress2').val();
              // editsigningzipcode      = $(this).find('#propertysigningzipcode').val();
              // editsigningcity         = $(this).find('#propertysigningcity').val();
              // editsigningcounty       = $(this).find('#propertysigningcounty').val();
              // editsigningstate        = $(this).find('#propertysigningstate').val();
              // editsigningAddressnotes = $(this).find('#propertysigningAddressnotes').val();
              // editsigningnotes        = $(this).find('#propertysigningnotes').val();
              // editsignincheck         = $(this).find('#propertysigningcheck').val();
          });
            id = $(this).attr('id');  
            console.log(editroles+edittitle+editname+edittelephone+editworkNo+editcellNo+editsocialNo+editemail+editmailingaddress1+editmailingaddress2+editmailingzipcode+editmailingcity+editmailingcounty+editmailingstate+editmailCheck+id+"/*/*/*/*/*"+editsigningaddress1+editsigningaddress2+editsigningzipcode+editsigningcity+editsigningcounty+editmailingstate+editsigningAddressnotes+editsigningnotes+editsignincheck);

            var Count,Header,Button,ProductName,GetRole;

            var ProductUID = $('.ProductUID :selected').val();
            var ProductName = $('.ProductUID :selected').text();
             alert(ProductUID);
            Count = id;
            Header = 'Edit Property Role';
            GetRole = editroles1;
            Button = 'Edit';

              
             // $('.tableDisplay').show();
                  $(".multiplePropertyAddTable").append(multiplePropertyTable(Button,Header));           
                  // propertyroleTableCheck();              
                  // $('.multiplePropertyAddTable').html(''); 
                  
                // $('.add_property_div').show();

                  // $('.SaveUpdate').hide();
                  $('.select2').select2(); 
            fieldReload();    


            $('.multiplePropertyAddTable').find(".name").val(editname);
            $('.multiplePropertyAddTable').find(".telephone").val(edittelephone);
            $('.multiplePropertyAddTable').find(".workNo").val(editworkNo);
            $('.multiplePropertyAddTable').find(".cellNo").val(editcellNo);
            $('.multiplePropertyAddTable').find(".socialNo").val(editsocialNo);
            $('.multiplePropertyAddTable').find("input[name=socialNo]").val(editsocialNo);
            $('.multiplePropertyAddTable').find(".email").val(editemail);

            var getValueTitle = $.trim(edittitle);
            $("#title option").each(function() {

                if ($(this).val() == getValueTitle)
                {
                    $(this).attr('selected', 'selected');
                    $('.select2').select2();
                }
            });

            if (editmailCheck == 'No') 
            {
                var mailingadd = editmailCheck;
                if (mailingadd && mailingadd != "") {                    
                    $('.mailingAddress:radio[value="'+mailingadd+'"]').prop('checked', true);
                }
                $('#mailingNo').prop('checked', true).trigger('change');                

                // $(this).find('.mailingNo').val().trigger('change');
                $('.multiplePropertyAddTable').find('.mailingaddress1').val(editmailingaddress1);
                $('.multiplePropertyAddTable').find('.mailingaddress2').val(editmailingaddress2);
                $('.multiplePropertyAddTable').find('.mailingzipcode').val(editmailingzipcode);
                $('.multiplePropertyAddTable').find('.mailingcity').val(editmailingcity);
                $('.multiplePropertyAddTable').find('.mailingcounty').val(editmailingcounty);
                $('.multiplePropertyAddTable').find('.mailingstate').val(editmailingstate);
                $('.multiplePropertyAddTable').find('.mailingnotes').val(editmailingnotes);
            }

            // if (editsignincheck == 'Other') 
            // {
            //     var signingadd = editsignincheck;
            //     if (signingadd && signingadd != "") {
            //         $('.signingAddress:radio[value="'+signingadd+'"]').prop('checked', true);
            //     }
            //     $('#signingOther').prop('checked', true).trigger('change');  
                
            //     $('.signingAddressInput').val(editsignincheck).trigger('change');
            //     $('.multiplePropertyAddTable').find('.signingaddress1').val(editsigningaddress1);
            //     $('.multiplePropertyAddTable').find('.signingaddress2').val(editsigningaddress2);
            //     $('.multiplePropertyAddTable').find('.signingzipcode').val(editsigningzipcode);
            //     $('.multiplePropertyAddTable').find('.signingcity').val(editsigningcity);
            //     $('.multiplePropertyAddTable').find('.signingcounty').val(editsigningcounty);
            //     $('.multiplePropertyAddTable').find('.signingstate').val(editmailingstate);
            //     $('.multiplePropertyAddTable').find('.signingAddressnotes').val(editsigningAddressnotes);
            //     $('.multiplePropertyAddTable').find('.signingnotes').val(editsigningnotes);
            // } 
            // else
            // {
            //     var signingadd = editsignincheck;
            //     if (signingadd && signingadd != "") {
            //         $('.signingAddress:radio[value="'+signingadd+'"]').prop('checked', true);
            //     }
            // }             
            // $('.ssn_mask').unmask().maskSSN('999-99-9999', { maskedChar: 'X', maskedCharsLength: 5 });

        });

        function fieldReload(){
            $('.multiplePropertyAddTable').find(".name").val('');
            $('.multiplePropertyAddTable').find(".telephone").val('');
            $('.multiplePropertyAddTable').find(".workNo").val('');
            $('.multiplePropertyAddTable').find(".cellNo").val('');
            $('.multiplePropertyAddTable').find(".socialNo").val('');
            $('.multiplePropertyAddTable').find(".email").val('');    
            $('.multiplePropertyAddTable').find('.mailingaddress1').val('');
            $('.multiplePropertyAddTable').find('.mailingaddress2').val('');
            $('.multiplePropertyAddTable').find('.mailingzipcode').val('');
            $('.multiplePropertyAddTable').find('.mailingcity').val('');
            $('.multiplePropertyAddTable').find('.mailingcounty').val('');
            $('.multiplePropertyAddTable').find('.mailingstate').val('');
            // $('.multiplePropertyAddTable').find('.mailingnotes').val('');
        }

        /** END */

        /**
        * @author Sathis Kannan P
        * @purpose Property Role Update Button Click to Add Values In Table
        * @param nothing
        * click event for .edit_role
        * @date 15-09-2020
        * @version New Assignment
        */

        $(document).on('click', '.edit_role', function(event) {
            event.preventDefault();
            /* Act on the event */
            // $('#edit_role').prop('checked', true).trigger('change');
            var rowID = $(this).attr('data-rowID');

            var roles,title,name,telephone,workNo,cellNo,socialNo,email,mailCheck,mailingaddress1,mailingaddress2,mailingzipcode,mailingcity,mailingcounty,mailingstate,mailingnotes,signinCheck,signingaddress1,signingaddress2,signingzipcode,signingcity,signingcounty,signingstate,signingAddressnotes,signingnotes,roles1,title1; 

            
            roles               = $('.multiplePropertyAddTable').find(".roles :selected").val();
            title               = $('.multiplePropertyAddTable').find(".title :selected").val();
            roles1              = $('.multiplePropertyAddTable').find(".roles :selected").text();
            title1              = $('.multiplePropertyAddTable').find(".title :selected").text();
            name                = $('.multiplePropertyAddTable').find(".name").val();
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
            $.each($("input[name='mailingAddress']:checked"), function()
            {
              mailingAddressRadioBtnValue.push($(this).val());
          });
            mailCheck = mailingAddressRadioBtnValue;

          //   var signingAddressRadioBtnValue = [];
          //   $.each($("input[name='signingAddress']:checked"), function()
          //   {
          //     signingAddressRadioBtnValue.push($(this).val());
          // });
          //   signinCheck = signingAddressRadioBtnValue;

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
          if(mailingnotes == undefined){
              mailingnotes = "";
          }
          // if(signingaddress1 == undefined){
          //     signingaddress1 = "";
          // }
          // if(signingaddress2 == undefined){
          //     signingaddress2 = "";
          // }
          // if(signingzipcode == undefined){
          //     signingzipcode = "";
          // }
          // if(signingcity == undefined){
          //     signingcity = "";
          // }
          // if(signingcounty == undefined){
          //     signingcounty = "";
          // }
          // if(signingstate == undefined){
          //     signingstate = "";
          // }
          // if(signingAddressnotes == undefined){
          //     signingAddressnotes = "";
          // }
          // if(signingnotes == undefined){
          //     signingnotes = "";
          // }
          if(mailCheck == undefined){
              mailCheck = "";
          }
          // if(signinCheck == undefined){
          //     signinCheck = "";
          // }

          $("#propertyRoleTable #rowID_"+rowID+"").each(function() {
            $row =$(this);                   

            $row.find("td #personName").text(name);
            $row.find("td #personRole").text(roles1);
            $row.find("#personTelePhone").text(telephone);
            $row.find("#personWorkNo").text(workNo);
            $row.find("#personCellNo").text(cellNo);
            $row.find("#personEmail").text(email);

            $row.find("td #propertyRole").val(roles);
            $row.find("td #propertytitle").val(title);
            $row.find("td #propertyName").val(name);
            $row.find("td #propertyTelephone").val(telephone);
            $row.find("td #propertyWorkNO").val(workNo);
            $row.find("td #propertyCellNo").val(cellNo);
            $row.find("td #propertySocialNO").val(socialNo);
            $row.find("td #propertyEmail").val(email);
            $row.find("td #propertyMailCheck").val(mailCheck);



            $row.find("td #propertymailingaddress1").val(mailingaddress1);
            $row.find("td #propertymailingaddress2").val(mailingaddress2);
            $row.find("td #propertymailingzipcode").val(mailingzipcode);
            $row.find("td #propertymailingcity").val(mailingcity);
            $row.find("td #propertymailingcounty").val(mailingcounty);
            $row.find("td #propertymailingstate").val(mailingstate);
            $row.find("td #propertymailingnotes").val(mailingnotes);  


            // $row.find("td #propertysigningcheck").val(signinCheck);

            // $row.find("td #propertysigningaddress1").val(signingaddress1);
            // $row.find("td #propertysigningaddress2").val(signingaddress2);
            // $row.find("td #propertysigningzipcode").val(signingzipcode);
            // $row.find("td #propertysigningcity").val(signingcity);
            // $row.find("td #propertysigningcounty").val(signingcounty);
            // $row.find("td #propertysigningstate").val(signingstate);                
            // $row.find("td #propertysigningAddressnotes").val(signingAddressnotes);
            // $row.find("td #propertysigningnotes").val(signingnotes);

        });            

          $('.multiplePropertyAddTable').html(''); 
          $('.select2').select2();
        });       

        /** END */

  /**
        * @author Sathis Kannan P
        * @purpose Order entry front end data validation
        * @param nothing
        * check event for order entry
        * @date 14-09-2020
        * @version New Assignment
        */
        var ProductUID,SubProductUID,LoanNumber,LoanAmount,CustomerRefNum,AltORderNumber,APN,PropertyAddress1,PropertyAddress2,PropertyZipcode,PropertyCityName,PropertyCountyName,PropertyStateCode,PropertyAddress2,EmailReportTo,BorrowerType,PropertyType,TransactionType,roles,title,name,telephone,workNo,cellNo,socialNo,email,optionyes,propertymailingaddress1,propertymailingaddress2,propertymailingzipcode,propertymailingcity,propertymailingcounty,propertymailingstate,propertysigningcheck,propertysigningaddress1,propertysigningaddress2,propertysigningzipcode,propertysigningcity,propertysigningcounty,propertysigningstate,propertysigningAddressnotes,propertysigningnotes;

        function frontEndValidation() 
        {
            ProductUID          = $(".ProductUID :selected").val().trim();
            SubProductUID       = $(".SubProductUID :selected").val().trim();
            PropertyAddress1    = $("#PropertyAddress1").val().trim();
            PropertyZipcode     = $("#PropertyZipcode").val().trim();
            PropertyCityName    = $("#PropertyCityName").val().trim();
            PropertyCountyName  = $("#PropertyCountyName").val().trim();
            PropertyStateCode   = $("#PropertyStateCode").val().trim();

            const isEmpty = document.querySelectorAll("#propertyRoleTable tr").length <= 1;
            var status = true;
            if (ProductUID == '' || ProductUID == 0 || ProductUID == null)
            {                
                status = false;
                $('.ProductUID').css({'border': '1px solid red'});
            }
            else
            {
                status = true;
                $('.ProductUID').css({'border': 'inherit'});

            }
            if (SubProductUID == '' || SubProductUID == 0 || SubProductUID == null)
            {
              status = false;
              $('.SubProductUID').css({'border': '1px solid red'});
          }
          else
          {
              status = true;
              $('.SubProductUID').css({'border': 'inherit'});

          }
          if (PropertyAddress1 == '' || PropertyAddress1 == 0 || PropertyAddress1 == null)
          {
              status = false;
              $('#PropertyAddress1').css({'border': '1px solid red'});
          }
          else
          {
              status = true;
              $('#PropertyAddress1').css({'border': 'inherit'});

          }
          if (PropertyZipcode == '' || PropertyZipcode == 0 || PropertyZipcode == null)
          {
            status = false;
            $('#PropertyZipcode').css({'border': '1px solid red'});
        }
        else
        {
            status = true;
            $('#PropertyZipcode').css({'border': 'inherit'});

        }
        if (PropertyCityName == '' || PropertyCityName == 0 || PropertyCityName == null)
        {
          status = false;
          $('#PropertyCityName').css({'border': '1px solid red'});
      }
      else
      {
          status = true;
          $('#PropertyCityName').css({'border': 'inherit'});

      }
      if (PropertyCountyName == '' || PropertyCountyName == 0 || PropertyCountyName == null)
      {
          status = false;
          $('#saveandneworder').attr('disabled', false); 
          $('#PropertyCountyName').css({'border': '1px solid red'});
      }
      else
      {
          status = true;
          $('#PropertyCountyName').css({'border': 'inherit'});

      }
      if (PropertyStateCode == '' || PropertyStateCode == 0 || PropertyStateCode == null)
      {
          status = false;
          $('#PropertyStateCode').css({'border': '1px solid red'});
      }
      else
      {
          status = true;
          $('#PropertyStateCode').css({'border': 'inherit'});     

      }

      if (isEmpty == true)
      {
          status = false;
          toastr['error']("", "Please Add a Property Roles");             
      }

      if (status == true)
      {
          return 1;
      }
      else
      {
        toastr['error']("", "Please Fill All Mandatory Information");             
    }

        }

/**
        * @author Sathis Kannan P
        * @purpose Order Entry Save Function
        * @param nothing
        * click event for .saveandneworder
        * @date 14-09-2020
        * @version New Assignment
        */
        var button;
        var button_val;
        var button_text;
        var Datas = "";

        $(".saveandneworder").on('click', function(event) {

            /* Act on the event */
            button      = $(this);
            button_val  = $(this).val();
            button_text = $(this).html();

            if (frontEndValidation() == 1)
            {
              // alert("Success");
              event.preventDefault();

              // LOAN AMOUNT CONVERT STRING
              var LoanAmount = $("#LoanAmount").val();
              // LoanAmount = LoanAmount.replace(/[,$]/g , ''); 
              // var LoanAmount = Number(LoanAmount);     

              var csrf_token = {};
              csrf_token[$('.txt_csrfname').attr('name')] = $('.txt_csrfname').val();

              var formOrderentryData = new FormData($("#frmOrderEntry")[0]);
              formOrderentryData.append($('.txt_csrfname').attr('name'), $('.txt_csrfname').val());

            //   for (var i = 0; i < fileData.length; i++) 
            //   {
            //     formOrderentryData.append("file[]", fileData[i].file);
            //     formOrderentryData.append("filename[]", fileData[i].filename);
            //     formOrderentryData.append("postition[]", fileData[i].ID);
            // }

            console.log(formOrderentryData); 

            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>Order_entry/insert',
                data: formOrderentryData, 
                dataType:'json',
                cache: false,
                processData: false,
                contentType: false,
                beforeSend: function(){
                  $('.spinnerclass').addClass("be-loading-active");
                  button.attr("disabled", true);
                  button.html('Loading ...'); 
                  $('#duplicate-modal').modal('hide');
              },
              success: function(data)
              {
                   
                // console.log(data);            

                if(data['validation_error'] == 0)
                {
                    Swal.fire({
                        title: data['message'], 
                        icon: 'success',
                        html: '<div style="display:flex;" id="SuccessOrderNumber"> <input type="text" class="form-control" name="OrderNumber" id="OrderNumber" value="'+data['OrderNumber']+'" readonly> <i class="fa fa-clone btncopyOrder" data-toggle="tooltip" title="Copy Order Number" data-placement="top" style="position: relative;top: 15px;"></i> </div> <hr>' +
                        '<div id="orderEntrySuccess">'+'<button type="button" class="btn btn-info redirectBtn" id="home" value="gotoHome" data-orderuid="'+data['id']+'" style="margin:3px;">' + '<i class="fa fa-home"></i> Go Dashboard' + '</button>' + '<button type="button" class="btn btn-success redirectBtn" id="ordersummary" value="gotoOrderSummary" data-orderuid="'+data['id']+'" style="margin:3px;">' + '<i class="fa fa-list-alt"></i> Stay back' + '</button>' +'<button type="button" class="btn btn-danger redirectBtn" id="neworder" value="gotoNewOrder" data-orderuid="'+data['id']+'" style="margin:3px;">' + '<i class="fa fa-plus-circle"></i> Place new order' + '</button>'+'</div>',  
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false, 
                    });
                    
                }
                else if(data['validation_error'] == 1)
                {                        
                     $('.spinnerclass').removeClass("be-loading-active");
                      toastr["error"]("", data['message']);
                      
                      button.html(button_text);
                      button.removeAttr("disabled");

                      $.each(data, function(k, v) {
                            // $('#'+k).nextAll('span:first').html(v);
                            $('#'+ k +'.select2').next().find('span.select2-selection').addClass('errordisplay'); 
                            $('#'+k).closest('div.is_error').addClass('is-invalid');
                        });

                      /*---- mailing address validation message ----*/
                      if (data.MailingAddressValidation) {

                        var MailingAddressValidation = data.MailingAddressValidation;
                        for(var key1 in MailingAddressValidation){
                          if (!MailingAddressValidation.hasOwnProperty(key1)) continue;
                          var obj = MailingAddressValidation[key1];

                          for(var key2 in obj){
                            if (!obj.hasOwnProperty(key2)) continue;
                            $('.mailingAddressDiv').eq(Number(key1)).find('.'+key2).closest('div.is_error').addClass('is-invalid');

                        }
                    }
                }
                /*---- Signing address validation message ----*/
                if (data.SigningAddressValidation) {

                    var SigningAddressValidation = data.SigningAddressValidation;
                    for(var key1 in SigningAddressValidation){
                      if (!SigningAddressValidation.hasOwnProperty(key1)) continue;
                      var obj = SigningAddressValidation[key1];

                      for(var key2 in obj){
                        if (!obj.hasOwnProperty(key2)) continue;
                        $('.signingAddressDiv').eq(Number(key1)).find('.'+key2).closest('div.is_error').addClass('is-invalid');

                    }
                }
            }

        }
        else if(data['validation_error'] == 2)
        {
            $('.spinnerclass').removeClass("be-loading-active");
          $('#duplicate-modal').modal('show');
          $('#Skip_duplicate').val(1);
          $('#button_value').val(button_val);
          $('#insert_html').html(data['html']); 
          $('#insert_order').removeAttr('disabled');
          $('#Customeronly').prop('checked', true).trigger('change');

          /*---- mailing address validation message ----*/

          if (data.MailingAddressValidation) {

            var MailingAddressValidation = data.MailingAddressValidation;
            for(var key1 in MailingAddressValidation){
              if (!MailingAddressValidation.hasOwnProperty(key1)) continue;
              var obj = MailingAddressValidation[key1];

              for(var key2 in obj){
                if (!obj.hasOwnProperty(key2)) continue;
                $('.mailingAddressDiv').eq(Number(key1)).find('.'+key2).closest('div.is_error').addClass('is-invalid');

            }
        }
        }

        /*---- Signing address validation message ----*/

        if (data.SigningAddressValidation)
        {
            var SigningAddressValidation = data.SigningAddressValidation;
            for(var key1 in SigningAddressValidation){
              if (!SigningAddressValidation.hasOwnProperty(key1)) continue;
              var obj = SigningAddressValidation[key1];

              for(var key2 in obj){
                if (!obj.hasOwnProperty(key2)) continue;
                $('.signingAddressDiv').eq(Number(key1)).find('.'+key2).closest('div.is_error').addClass('is-invalid');

            }
        }
        }
        }
        },
        error: function (jqXHR, textStatus, errorThrown) {
                  // console.log(errorThrown);
              },
              failure: function (jqXHR, textStatus, errorThrown) {
                  // console.log(errorThrown);
              },
          });
        } 
        else
        {
          event.preventDefault();
        }         

    });

    /** END */

     /**
        * @author Sathis Kannan P
        * @purpose copy order number function
        * @param nothing
        * click event for .redirectBtn
        * @date 15-09-2020
        * @version New Assignment
        */
        
        $(document).on('click', '.redirectBtn', function(event) {

            event.preventDefault();
            /* Act on the event */
            var BtnValue = $(this).val();
            var orderuid = $(this).attr('data-orderuid');

            console.log('MULTIPLE ORDERUID:-'+orderuid);

            if (BtnValue == 'gotoHome')
            {
                window.location.replace("<?php echo base_url();?>Dashboard");
            }
            if (BtnValue == 'gotoOrderSummary')
            {
                if(orderuid == '')
                {
                    window.location.replace("<?php echo base_url();?>Dashboard");
                } 
                else 
                {
                    /**
                        * @author Sathis Kannan P
                        * @purpose Multiple Order Entry Redirect to Order Summary With New Tab
                        * @throws nothing
                        * @date 15-09-2020
                        * @version New Assignment
                        */
                        var str = orderuid;
                        var str_array = str.split(',');

                        for(var i = 0; i < str_array.length; i++) {

                         str_array[i] = str_array[i].replace(/^\s*/, "").replace(/\s*$/, "");

                         if (str_array.length <= 1) {
                            window.location.replace("<?php echo base_url();?>Order_summary?OrderUID="+str_array[i]);
                        }
                        else
                        {
                            window.open("<?php echo base_url();?>Order_summary?OrderUID="+str_array[i], "_blank");
                            setTimeout(function () {
                                window.location.replace("<?php echo base_url();?>Dashboard");
                            }, 1000);                            
                        }                             

                    }   

                    /** END */                
                }
            }
            if (BtnValue == 'gotoNewOrder')
            {
                window.location.replace("<?php echo base_url();?>Order_entry");
            }
        });

    /**    
         * @author Sathis Kannan
         * @purpose Copy Clipboard OrderNumber
         * @throws nothing
         * @date 15-09-2020
         * @version New Assignment
     */

        $(document).on('click', '.btncopyOrder', function(event) {
          event.preventDefault();
          /* Act on the event */
            var copyText = document.getElementById("OrderNumber");
            copyText.select();
            document.execCommand("copy");
            $("#OrderNumber").attr('title', 'Copied!');
        });

        /** END */


</script>