 <?php
  $OrderrUID      = $OrderUID;
  $UserUID        = $this->session->userdata('UserUID');
  $UserRole       = $this->Common_model->GetUserRoleTypeDetails($UserUID);
  $UserRoleType   = $UserRole->RoleType;

  $Customer_Check = $this->Common_model->GetCustomerByUserUID($UserUID);

  $Customer_Check = $Customer_Check->CustomerUID;

  if($Customer_Check == 0){
      $fornotes = 1;
  }
  else{
      $fornotes = 2;
  } 
  $forcompleted   = 0;
  $Orderdetailsss = $this->Common_model->get_orderdetails($OrderrUID);

  $SubProductUID  = $Orderdetailsss->SubProductUID; 
  $ProductUID     = $this->Common_model->GetProductUIDBySubProductUID($SubProductUID);
  $Product        = $ProductUID->ProductUID;


?> 
<!-- start body header -->
<div id="page_top" class="section-body" style="">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Order Reports</h1> 
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
                                <div class="col-md-12 col-lg-12 pl-0">
                                   <input id="OrderUID" name="OrderUID" type="hidden" value="<?php echo $order_details->OrderUID ?>"/>
                                 <iframe id="iFramePDF" src="" height="450" width="100%"></iframe>
                                 <div class="col-sm-12 text-right p-0 mt-4 mb-3">
                                  <a href="" id="reportsDownload" class="btn btn-md btn-success" download>
                                    Download
                                  </a>
                                <!--   <button class="btn btn-info  active" id="reportsDownload">Download</button>  -->
                                        <!-- <button id="addReport" data-id="<?php echo $order_details->OrderUID ?>" class="btn btn-success">Add Report to Attachments</button>
                                     <?php if($order_details->TemplateUID == 1): ?>
                                        <button class="btn btn-info" type="button" id="AddAttachment" data-id="<?php echo $order_details->OrderUID ?>">Add Tax 
                                        Cert to Attachment </button>
                                        <?php endif; ?> -->
                                    </div>
                                </div>

                             
                            </div>
                        </div>
                    </div>
                </div>

              <!--   <div class="col-md-12 col-lg-12 mb-3 mt-4">
                    <div class="btn-list text-right">
                        <a href="#" class="btn btn-success single_submit" id="UpdateRoles" value="1">Update</a>
                    </div>
                </div> -->
            </div>
        </form>
        </div>
    </div>

 <script type="text/javascript">
$( document ).ready(function() {

  var OrderUID = $('#OrderUID').val();
  setTimeout(function(){
    $.ajax({
      type: 'GET',
      url: '<?php echo base_url();?>Order_reports/Preview',
      data: {"OrderUID": OrderUID},
      success: function (data) 
      {
        // console.log(data);
        $('#iFramePDF').attr('src', "<?php echo base_url()?>Templates/Pdf/<?php echo $order_details->OrderNumber?>.pdf?<?php echo microtime(1); ?>");

        $('#reportsDownload').attr('href', "<?php echo base_url()?>Templates/Pdf/<?php echo $order_details->OrderNumber?>.pdf?<?php echo microtime(1); ?>");
      },
      error: function (jqXHR, textStatus, errorThrown) {
      },
      complete: function () {
      }
    });
  }, 5000);

//Add Tax Cert to Attachemnt
$('#AddAttachment').click(function(e){
  e.preventDefault();
  e.stopPropagation();
  
  var add_data = {'OrderUID': $(this).attr('data-id')};
  add_data[$('.txt_csrfname').attr('name')] = $('.txt_csrfname').val();


  $.ajax({
    type: "POST",
    url: '<?php echo base_url("Order_reports/TaxCert_to_Attachment"); ?>',
    data: add_data, 
    dataType: 'json',
    success: function(data)
    {
      // console.log(data);
      
      if(data.status == 'Ok')
      {
                toastr['success']("", "Added Taxcert On Attachement Successfully");

      }
      else
      {
        toastr['error']("", "Failed to Added Taxcert On Attachement ");
      }          
    }
  });
});


//Add Report to Attachemnt
$('#addReport').click(function(e){
  e.preventDefault();

  var add_data = {'OrderUID': $(this).attr('data-id'), "documentfilename" : 'report.pdf'};
  add_data[$('.txt_csrfname').attr('name')] = $('.txt_csrfname').val();


  $.ajax({
    type: 'post',
    url: '<?php echo base_url();?>Order_reports/AddAttachment',
    data: add_data,
    dataType:'json',
    success: function (data) 
    {
      if(data.status == 'Ok')
      {
        toastr['success']("", "Added Taxcert On Attachement Successfully");
        
        setTimeout(function(){
          window.location.replace('<?php echo base_url().'attachments?OrderUID='.$OrderUID;?>');       
        }, 1000);
      }
      else
      {
                toastr['error']("", "Failed to Added Taxcert On Attachement ");

      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR.responseText);
      console.log(textStatus);
    },
  });
});

});
</script>