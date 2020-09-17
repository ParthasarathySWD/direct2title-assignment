<style type="text/css">

  select option {
    text-transform: capitalize;
  }

  select {text-transform:capitalize}

  .modal70 > .modal-dialog {
    width:90% !important;
  }

  div.ex13 {
    height: 1050px;
    overflow: auto;
  }

  div.attach {
    width: 100%;
    height: 200px;
    overflow: auto;
  }

  th,tr{
    font-size: 11px;
  }

  div .md-editor-left1
  {
    border-left: 1px solid #dce5f7 !important;
  }

  div .md-editor-right1
  {
    border-right: 1px solid #dce5f7 !important;
  }

  p{
    margin: 0 0 -10px;
  }

  #spinner{
    position: absolute;
    right: 5px;
    top: 25px;
    display: none;
  }

  .gritter-title p 
  {
    font-size: 16px !important;
  }

  table tr td {
    padding: 3px 10px;
    border-right: 1px solid #ddd;
  }

  .material-icons  {
    cursor: pointer;
  }

  .select2-results__options{
    font-size: 13px !important;
  }

  table tr td {
    padding: 3px 10px;
    border-right: none;
  }

  .table > tbody > tr > td, .table > tfoot > tr > td {
    border-top: none;
  }

  .table > thead > tr > th {
    vertical-align: bottom;
    border-bottom: none; 
  }

  .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
    padding: 5px 10px !important;
  }

  .select2-selection__rendered{
    position: absolute;
    width: 100%;
  }

   .input_style{
    margin-top: 10px !important;
  }

  label{
    margin-bottom: 0px !important;
  }

  .form-control:focus {
      border-color: #59595973;
      outline: 0;
  }
  .form-control {
      border-color: #59595973;
  }

  .select2-container--bootstrap .select2-selection {
    border: 1px solid #59595973 !important; 
  }
  label {
    font-size: 10px !important;
  }
  .select2-container .select2-selection--single {
    height: 20px;
    font-size: 10px !important;
        padding: 3px 9px 6px 0;
  }
  .input-xs {
    font-size: 10px !important;
    height: 20px;
    padding: 3px 9px;
  }
.datepicker.btn {
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
    border: 1px solid transparent;
    padding: 6px 5px 2px 5px;
    font-size: 7px !important;
    line-height: 8px;
    border-radius: 2px;
}
.mdi-calendar
{
  font-size: 11px !important;
}
.input-group-addon {
    line-height: 18px;
}
.input-group-addon {
    line-height: 12px;
}
.select2-container--bootstrap .select2-results__option[aria-selected=true] {
    background-color: #f5f5f5;
    color: #262626;
}
li.ui-menu-item {
    font-size: 9px !important;
    line-height: 13px !important;
}
</style>
<?php 

global $is_workflowcomplete_enabled;
global $is_workflow_completed;
global $is_save_enabled;
global $is_review_enabled;
global $is_reverse_workflow;
global $is_exception_enabled;
global $checkholdstatus;
global $checkreviewholdstatus;
global $checkreviewassigned_user;
global $qc_enabled;
global $is_vendor_login;
global $workflowModuleUID;
global $canrevise;
global $is_revise_enabled;
global $IsUpper;

$OrderrUID = $OrderUID;
$UserUID = $this->session->userdata('UserUID');
$UserRole = $this->Common_model->GetUserRoleTypeDetails($UserUID);
$UserRoleType = $UserRole->RoleType;

$Orderdetailsss = $this->Common_model->get_orderdetails($OrderrUID);

$SubProductUID = $Orderdetailsss->SubProductUID; 
$ProductUID = $this->Common_model->GetProductUIDBySubProductUID($SubProductUID);
$Product = $ProductUID->ProductUID;

$is_vendor_login = $this->common_model->is_vendorlogin();
$arrVendorUID = $this->common_model->GetVendorUIDbyWorkflow($workflowModuleUID,$OrderUID);
$CrtVendorUID = $arrVendorUID->VendorUID;

$vendor_workflows = $this->common_model->get_vendor_assigned_workflow($CrtVendorUID,$OrderUID);

$ProjectUID = $Orderdetailsss->ProjectUID;
$IsUppercase = $this->Order_info_model->GetProjectDetailsForUpper($ProjectUID);
$StatusUID = $Orderdetailsss->StatusUID;

/*$torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
$Fields = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);
$Template=$this->Order_info_model->GetTemplateMappingByOrderUIDFieldRow($torders);*/

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
                               <div class="row MainContent">
                <div class="col-md-4 ex13">
                  <form method="POST" name="myForm1" id="myForm1" enctype="multipart/form-data">
                      <div class="col-sm-12 form-group " style="padding: 0px;">
                        <h6 class="panel-heading-divider" style="margin:0px; margin-top: 10px; margin-bottom: 10px; font-weight: 500;">Fields Panel</h6>
                        <input type="hidden" id="OrderUID" name="OrderUID" value="<?php echo $OrderUID; ?>">
                        <input type="hidden" id="OrderNumber" name="OrderNumber" value="<?php echo $order_details->OrderNumber?>">
                        <input type="hidden" id="IsTemplateSaved" name="IsTemplateSaved" value="<?php echo $order_details->IsTemplateSaved?>">

                        

                        <div id="AssignorAppend"></div>
                        <div id="AssigneeAppend"></div>
                        <div id="LenderAppend"></div>
                        <div id="EndorserAppend"></div>
                        <div id="FieldSection">

                        </div>
                      </div>

                      <div class="col-sm-12 form-group " style="padding: 0px;">
                        <?php if(count($AttachmentDetails) > 0 ){ ?>
                          <h6 class="panel-heading-divider" style="margin:0px; font-weight: 500;margin-bottom: 10px; "><i class="mdi mdi-attachment-alt"></i>  &nbsp; Attachments</h6>
                          <div class="col-sm-12 form-group attach">
                            <table id="Attachments" style="border:1px solid #ddd" class="table table-striped table-inverse ">
                              <thead class="thead-inverse">
                                  <tr>
                                    <th width="10%">SNo</th>
                                    <th>Attachment Files</th>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php $i=1; foreach($AttachmentDetails as $row): ?>
                                                        
                                  <tr data-id="<?php echo $OrderUID;?>" data-table-name="torderdocuments" data-file-name="<?php echo $row->DocumentFileName; ?>" data-file-path="<?php echo $row->OrderDocsPath; ?>">

                                    <td style="text-align: left;"><?php echo $i; ?></td>
 
                                    <td style="text-align: left;" data-filename="<?php echo $row->DocumentFileName; ?>"><?php echo $row->DocumentFileName; ?></td>

                                    <td style="text-align: left;"><button class="PDFFile" data-filename="<?php echo $row->DocumentFileName; ?>"><i class="mdi mdi-attachment-alt"></i></button></td>

                                  </tr>

                                <?php $i++; endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        <?php } ?>
                      </div>
                  </form> 
                </div>

                <div class="col-sm-8 md-editor-left1" id="ShowEditorContent">
                  <div class="col-md-12" >
                      <h6 class="panel-heading-divider" style="margin:0px; margin-top: 10px; margin-bottom: 10px; font-weight: 500;">Preview Panel</h6>
                      <div id="preview-container">

                          <!-- <button class="btn btn-space btn-default active" id="preview"> <i class="fa fa-eye"></i> Preview</button> -->
                          <button class="btn btn-space btn-success active save save_order_info"> <i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>

                          <button class="btn btn-space btn-default active Reset"> <i class="fa fa-mail-reply-all" aria-hidden="true"></i> Reset</button>

                          <button class="btn btn-space btn-social btn-color btn-twitter EditTemplate"> <i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>

                          <!-- <?php if($order_details->IsTemplateSaved == 1){ ?>
                            <button class="btn btn-space btn-danger Preview_Complete"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF View</button>
                          <?php } ?> -->

                        <textarea id="mymce" name="content"></textarea>

                          <button class="btn btn-space btn-success active save save_order_info" style="margin-top: 10px;"> <i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>

                          <button class="btn btn-space btn-default active Reset" style="margin-top: 10px;"> <i class="fa fa-mail-reply-all" aria-hidden="true"></i> Reset</button>

                          <button class="btn btn-space btn-social btn-color btn-twitter EditTemplate" style="margin-top: 10px;"> <i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>

                          <!-- <?php if($order_details->IsTemplateSaved == 1){ ?>
                            <button class="btn btn-space btn-danger Preview_Complete"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF View</button>
                          <?php } ?> -->
                      </div>
                  </div>
                </div>

                <div class="col-sm-8 md-editor-left1" id="ShowPDFContent">
                  <div class="col-md-12" >
                      <h6 class="panel-heading-divider" style="margin:0px; margin-top: 10px; margin-bottom: 10px; font-weight: 500;">PDF View <button class="btn btn-space btn-default pull-right CloseEditContentTemplate" style="line-height: 20px;"> <i class="fa fa-close" aria-hidden="true"></i> Close</button></h6>
                      <div id="preview-container">
                        <iframe id="iFramePDF" src=" "  height="950px" width="300"></iframe>
                      </div>
                  </div>
                </div>

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
                                      <!--  <select class="form-control mdl-textfield__input input-xs mdl-select2 select2" id="customer" name="customer">
                              <?php foreach ($Customers as $key => $customer) {
                                if($customer->CustomerUID == $order_details->CustomerUID) {?>
                                  <option value="<?php echo $customer->CustomerUID; ?>" selected><?php echo $customer->CustomerNumber.' / '.$customer->CustomerName; ?></option>';
                                <?php }
                              } ?>
                            </select> -->
                                    </div>
                                </div>
                                
                              
                            </div>
                            
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
  <script src="<?php echo base_url('assets/lib/tinymce/tinymce.min.js'); ?>"></script>
 <script type="text/javascript">

    $(document).ready(function(){

      $('.projectselect2').trigger('change');
      $( ".currency" ).trigger( "change" );
      $('#LegalDescription').trigger('change');
      $('.legal_change').trigger('change');
      //$('.Div_LegDes').hide();

      show_content_edit();
      
      $("body").on("input" , "textarea, date, select" , function(){
        call_event();
      });

      $("body").on("input" , "input[type=text],input[type=hidden]" , function(){
        call_event();
      });

      $("body").on("change" , ".select2" , function(){
        call_event();
      });

      /*$("body").on("change" , ".currency" , function(){
        call_event();
      });*/

      $("body").on("click change keyup" , ".datepicker" , function(){
        call_event();
      });

      $('body').on('change','.currency', function(event) { 
        var number = $(this).val();
        number = number.replace(/[,$]/g , ''); 
        var AmtInWord = $(this).attr('data-in-words');
        var data ='';
        if(number == '0' || number == '0.00'){
          data = 'Zero';
        } else {
          data = numToWords(number);
        }
        //console.log(number);
        console.log(data);
        $('#'+AmtInWord).val(data);
        call_event();
        
      });

      $("body").on("change" , "#MIN" , function(){
        var MIN = $('#MIN').val();
        var length = MIN.length;

        if(length < 18){
          $.gritter.add({
            title: 'MIN value should not be less than 18 Digit',
            class_name: 'color danger',
            fade: false,
            speed:'slow',
          });
        }
      });
    });

    function call_event(){
      show_content();   
      componentHandler.upgradeDom();
      select_mdl();
     /* $('.select2').select2({
        theme: "bootstrap",
      }); */
    }

    $.fn.niftyModal('setDefaults',{
      overlaySelector: '.modal-overlay',
      closeSelector: '.modal-close',
      classAddAfterOpen: 'modal-show',
    });

    $('.Preview_Complete').click(function(e){
      e.preventDefault();
      var OrderUID = $('#OrderUID').val();
      var OrderNumber = $('#OrderNumber').val();
      var DocumentFileName = OrderNumber+'.pdf';
      $('.TemplateMappingContent').hide();           
      $('#ShowEditorContent').hide();   
      $('#ShowPDFContent').show();   

      show_content_pdf(DocumentFileName);
    });


    $('.TemplateMappingContent').hide();
    $('#ShowPDFContent').hide();

    $('.PDFFile').click(function(e){
      e.preventDefault();
      var DocumentFileName = $(this).attr('data-filename');
      $('.TemplateMappingContent').hide();           
      $('#ShowEditorContent').hide();   
      $('#ShowPDFContent').show();   

      show_content_pdf(DocumentFileName);
    });

     $('.EditTemplate').click(function(e){
      $('.TemplateMappingContent').show();           
      $('.MainContent').hide();
      $('#ShowPDFContent').hide();      
      show_content_edit();
    });

    $('.CloseEditContentTemplate').click(function(e){
      $('.TemplateMappingContent').hide();           
      $('.MainContent').show();   
      $('#ShowEditorContent').show(); 
      $('#ShowPDFContent').hide();      
      show_content_edit();
    });

    function show_content_pdf(DocumentFileName) {

      var OrderUID = $('#OrderUID').val();

      $.ajax({
        type: 'POST',
        url: '<?php echo base_url();?>order_info/GetAttachments',
        data: {"OrderUID": OrderUID, "DocumentFileName": DocumentFileName},
        success: function (data) 
        {
          $('#iFramePDF').attr('src', "<?php echo base_url()?>"+data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
        },
        complete: function () {
        }
      });

    };

    //Init tinymce 
    if($("#mymce1").length > 0){
      tinymce.init({
        selector: "textarea#mymce1",
        theme: "modern",
        height: 800,
        statusbar: true,
        branding: false,
        content_style: ".mce-content-body {font-size:11px;font-family:Arial;}",
        fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 24px 36px",
        lineheight_formats: " 3pt 4pt 5pt 6pt 7pt 8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
        plugins: [
         "advlist autolink lists link charmap preview anchor",
         "searchreplace visualblocks code fullscreen fullpage",
         "insertdatetime table contextmenu paste template jbimages",
         "pagebreak","textcolor","colorpicker", "textpattern","lineheight"
        ],
        toolbar: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | jbimages | lineheightselect ',
        pagebreak_separator: '<p style="page-break-after: always;"></p>',
       setup: function (editor) {        
      }
    });
  }

    $('#EditContentTemplate').click(function(e){

      var editorContent=tinymce.get('mymce1').getContent();
      var formData = new FormData($('#myForm1')[0]);
      formData.append('content',editorContent);

      $.ajax({
          type: 'POST',
          url: '<?php echo base_url();?>order_info/EditMainTemplate',
          data: formData,
          cache: false,
          processData: false,
          contentType: false,
          beforeSend: function(){
            $('#EditContentTemplate').attr("disabled", true);
            $('#EditContentTemplate').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
          },
          success: function (response, textStatus, jqXHR) {
            show_content(); 
            call_event();
            $('#EditContentTemplate').attr("disabled", false);
            $('#EditContentTemplate').html('<i class="fa fa-floppy-o"></i> Save');
            $('.TemplateMappingContent').show();           
            $('.MainContent').hide();   
          },
          error: function (jqXHR, textStatus, errorThrown) {
          },
          complete: function () {
          }
      });
     });

  $('body').on('click','.choose', function(event) { 
    selectedfieldkeyword=$(this).attr('data-keyword');
    var Modal_FieldUID=$(this).attr('data-value');
    var Modal_FieldName=$(this).attr('data-display-name');

    var Frm_FieldUID = $('.FieldUID').val();

    if($.inArray(Modal_FieldUID, Frm_FieldUID) == -1) {
      $('.FieldUID').append('<option value="' + Modal_FieldUID + '" selected >' + Modal_FieldName + '</option>').trigger('change');
    }

    $('#CopyText').val('');

    $('#CopyText').val(selectedfieldkeyword);

    var cTxt = document.getElementById("CopyText");

    cTxt.select();
    document.execCommand("Copy");

  })

  var delay = (function(){
    var timer = 0;
    return function(callback, ms){
      clearTimeout (timer);
      timer = setTimeout(callback, ms);
    };
  })();

  
  function show_content(){
        var editorContent=tinymce.get('mymce').getContent();
        var formData = new FormData($('#myForm1')[0]);
        formData.append('content',editorContent);
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url();?>order_info/ReplaceContent',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response, textStatus, jqXHR) {
              //console.log(response);
              tinymce.get('mymce').setContent(response);          
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            complete: function () {
            }
        });
  }

  function show_content_edit(){
    delay(function(){
        var editorContent=tinymce.get('mymce1').getContent();
        var formData = new FormData($('#myForm1')[0]);
        formData.append('content',editorContent);
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url();?>order_info/EditContentMCE',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (response, textStatus, jqXHR) {
              //console.log(response);
              tinymce.get('mymce1').setContent(response);
              $('.IsCheckMers').trigger('change');
          
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            complete: function () {
            }
        });
      }, 300 );
  }


//  $( ".date" ).datepicker({ dateFormat: 'yy-mm-dd' });

    $(document).ready(function () {

      trigger_fun();


      //$('#TemplatesMappingUID').trigger('change');

          App.formElements();

          select_mdl();

                  $('.select2').select2({
                    theme: "bootstrap",
                  });

  
        //initialize the javascript
      $(".datepicker").datetimepicker({
        format: "mm/dd/yyyy",
        autoclose: true,
        minView : 2,
        componentIcon: '.mdi.mdi-calendar',
        navIcons:{
          rightIcon: 'mdi mdi-chevron-right',
          leftIcon: 'mdi mdi-chevron-left'
        }
      });

      /*$('.datepicker').datetimepicker({ 
        format: 'mm/dd/yyyy', 
        minView : 2,
        autoclose: true,
        pickTime: false,
        pickerPosition: "bottom-left",
      });
      */

      });
    //#Variable init Region starts.
    var selectedfieldkeyword="", selectedcursor="", removefield="",selectedfieldname="";
    var OrderUID='<?php echo $OrderUID; ?>';
    //#Variable init Region Ends.

    //init Select2 
    select_mdl();
    $('.select2').select2({
      theme: "bootstrap",
    }); 


    //Init tinymce 
    if($("#mymce").length > 0){
        tinymce.init({
          selector: "textarea#mymce",
          theme: "modern",
          height: 800,
          statusbar: true,
          branding: false,
          content_style: ".mce-content-body {font-size:11px;font-family:Arial;}",
          fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 24px 36px",
          lineheight_formats: " 3pt 4pt 5pt 6pt 7pt 8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
          plugins: [
           "advlist autolink lists link charmap preview anchor",
           "searchreplace visualblocks code fullscreen fullpage",
           "insertdatetime table contextmenu paste template jbimages",
           "pagebreak","textcolor","colorpicker", "textpattern","lineheight"
          ],
          toolbar: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | jbimages | lineheightselect ',
          pagebreak_separator: '<p style="page-break-after: always;"></p>',
          setup: function (editor) {        
          }
    });
  }

   //Event func to select keyword
   $('.choose').click(function(e){
      selectedfieldkeyword=$(this).attr('data-keyword');
      if(typeof selectedfieldkeyword!='undefined')
      {
        $(this).removeClass('btn-twitter');
      }
   })

   //Event func to Remove Keywords in editor 
   $('.revert').click(function(e){
    removefield=$(this).attr('data-keyword');
    removefield='<span style="color: blue;" data-keyword="'+removefield+'">'+removefield+'</span>'
    var editorContent=tinymce.get('mymce').getContent(); var replaceStr='<button class="field">&nbsp;</button>';
    editorContent=replaceAll(editorContent, removefield, replaceStr);
    tinymce.get('mymce').setContent(editorContent);
    $(this).closest('tr').find('.choose').addClass('btn-twitter');       

   });


  select_mdl();

  $('.select2').select2({
    theme: "bootstrap",
  }); 

    function attach(){

      var OrderUID = $('#OrderUID').val();

      $.ajax({
        type: 'POST',
        url: '<?php echo base_url();?>reports/Preview_info',
        data: {"OrderUID": OrderUID},
        success: function (data) 
        {
          $.ajax({
            type: 'POST',
            url: '<?php echo base_url();?>reports/AddAttachments',
            data: {"OrderUID": OrderUID},
            success: function (data) 
            {
              //window.location.reload();              
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            complete: function () {
            }
          }); 
        },
        error: function (jqXHR, textStatus, errorThrown) {
        },
        complete: function () {
        }
      }); 
    }

    //Event func to Save Template
    $('.save').click(function(e){
      var TemplatesMappingUID = $('#TemplatesMappingUID').val();
      var MIN = $('#MIN').val();
      var OrderUID = $('#OrderUID').val();

      var RecordedDate= $('#RecordedDate').val();
      RecordedDate = new Date(RecordedDate);
      var DeedOfTrustDated= $('#DeedOfTrustDated').val();
      DeedOfTrustDated =  $.trim(DeedOfTrustDated);
      DeedOfTrustDated = new Date(DeedOfTrustDated);
      var MortgageDated= $('#MortgageDated').val();
      MortgageDated =  $.trim(MortgageDated);
      MortgageDated = new Date(MortgageDated);
      if(DeedOfTrustDated >= RecordedDate && DeedOfTrustDated != '')
      {
           $.gritter.add({
            title: 'Recorded Date should be greater than Deed Date',
            class_name: 'color danger',
            fade: false,
            speed:'slow',
          });
      } else if(MortgageDated >= RecordedDate && MortgageDated != '')
      {
        $.gritter.add({
            title: 'Recorded Date should be greater than Mortgage Date',
            class_name: 'color danger',
            fade: false,
            speed:'slow',
          });
      }
      else
      {
        if (typeof MIN !== typeof undefined) {
          if(MIN.length < 18 ){
            $.gritter.add({
              title: 'MIN value should not be less than 18 Digit',
              class_name: 'color danger',
              fade: false,
              speed:'slow',
            });
          } else {
            var editorContent=tinymce.get('mymce').getContent();
            var formData = new FormData($('#myForm1')[0]);
            formData.append('content',editorContent);

            $.ajax({
              type: 'POST',
              url: '<?php echo base_url();?>order_info/savetemplate',
              data: formData,
              cache: false,
              processData: false,
              contentType: false,
              beforeSend: function(){
                $('.save').attr("disabled", true);
                $('.save').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
              },
              success: function (response, textStatus, jqXHR) {
                  //console.log(response);
                  show_content();
                  call_event(); 
                  $('.save').attr("disabled", false);
                  $('.save').html('<i class="fa fa-floppy-o"></i> Save');
                  $('#IsTemplateSaved').val('1');
                  attach();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                },
                complete: function () {
                }
              });
          }
        } else {
          var editorContent=tinymce.get('mymce').getContent();
          var formData = new FormData($('#myForm1')[0]);
          formData.append('content',editorContent);

          $.ajax({
            type: 'POST',
            url: '<?php echo base_url();?>order_info/savetemplate',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function(){
              $('.save').attr("disabled", true);
              $('.save').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
            },
            success: function (response, textStatus, jqXHR) {
                //console.log(response);
                show_content();
                call_event(); 
                //$( ".currency" ).trigger( "change" );
                $('.save').attr("disabled", false);
                $('.save').html('<i class="fa fa-floppy-o"></i> Save');
                $('#IsTemplateSaved').val('1');
                attach();            
              },
              error: function (jqXHR, textStatus, errorThrown) {
              },
              complete: function () {
              }
            });
        }
      }

       
    });

    $('.Reset').click(function(e){
      var editorContent=tinymce.get('mymce').getContent();
      var formData = new FormData();
      var formData = $('#myForm1').serialize()+'&'+$.param({ 'content': editorContent });

      $.ajax({
        url: '<?php echo base_url("order_info/reset_template"); ?>',
        type: 'POST',
        data: formData,
        cache: false,
        beforeSend: function(){
        $('.Reset').attr("disabled", true);
        $('.Reset').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
        },
      })
      .done(function(data){
        console.log(data);
        tinymce.get('mymce').setContent(data);
       
        //$('.save').trigger('click');
        //$('input').trigger("change");
        show_content();
        show_content_edit();
        call_event();   
        $('.Reset').attr("disabled", false);
        $('.Reset').html('<i class="fa fa-mail-reply-all"></i> Reset');
      })
      .fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
      })
    });

   /* Define function for escaping user input to be treated as 
   a literal string within a regular expression */
    function escapeRegExp(string){
        return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    }
     
    /* Define functin to find and replace specified term with replacement string */
    function replaceAll(str, term, replacement) {
      return str.replace(new RegExp(escapeRegExp(term), 'g'), replacement);
    }

    function numToWords(number) {

      //Validates the number input and makes it a string
      if (typeof number === 'string') {
          number = parseInt(number, 10);
      }
      if (typeof number === 'number' && isFinite(number)) {
          number = number.toString(10);
      } else {
          return '';
      }

      //Creates an array with the number's digits and
      //adds the necessary amount of 0 to make it fully 
      //divisible by 3
      var digits = number.split('');
      while (digits.length % 3 !== 0) {
          digits.unshift('0');
      }

      //Groups the digits in groups of three
      var digitsGroup = [];
      var numberOfGroups = digits.length / 3;
      for (var i = 0; i < numberOfGroups; i++) {
          digitsGroup[i] = digits.splice(0, 3);
      }

      //Change the group's numerical values to text
      var digitsGroupLen = digitsGroup.length;
      var numTxt = [
          [null, 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'], //hundreds
          [null, 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'], //tens
          [null, 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'] //ones
          ];
      var tenthsDifferent = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];

      // j maps the groups in the digitsGroup
      // k maps the element's position in the group to the numTxt equivalent
      // k values: 0 = hundreds, 1 = tens, 2 = ones
      for (var j = 0; j < digitsGroupLen; j++) {
          for (var k = 0; k < 3; k++) {
              var currentValue = digitsGroup[j][k];
              digitsGroup[j][k] = numTxt[k][currentValue];
              if (k === 0 && currentValue !== '0') { // !==0 avoids creating a string "null hundred"
                  digitsGroup[j][k] += ' hundred ';
              } else if (k === 1 && currentValue === '1') { //Changes the value in the tens place and erases the value in the ones place
                  digitsGroup[j][k] = tenthsDifferent[digitsGroup[j][2]];
                  digitsGroup[j][2] = 0; //Sets to null. Because it sets the next k to be evaluated, setting this to null doesn't work.
              }
          }
      }

      //console.log(digitsGroup); //debug

      //Adds '-' for gramar, cleans all null values, joins the group's elements into a string
      for (var l = 0; l < digitsGroupLen; l++) {
          if (digitsGroup[l][1] && digitsGroup[l][2]) {
              digitsGroup[l][1] += '-';
          }
          digitsGroup[l].filter(function (e) {return e !== null});
          digitsGroup[l] = digitsGroup[l].join('');
      }

      //console.log(digitsGroup); //debug

      //Adds thousand, millions, billion and etc to the respective string.
      var posfix = [null, 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion'];
      if (digitsGroupLen > 1) {
          var posfixRange = posfix.splice(0, digitsGroupLen).reverse();
          for (var m = 0; m < digitsGroupLen - 1; m++) { //'-1' prevents adding a null posfix to the last group
              if (digitsGroup[m]) {
                  digitsGroup[m] += ' ' + posfixRange[m];
              }
          }
      }

      //console.log(digitsGroup); //debug

      //Joins all the string into one and returns it
      return digitsGroup.join(' ');

    } //End of numToWords function

    $('body').on('change','.projectselect2', function(event) { 
        event.stopPropagation();
        event.stopImmediatePropagation();

        var OrderUID = $('#OrderUID').val();
        var Val = $(this).val();
        var MIN_Val = $('#MIN').val();
        var PrimaryColUID = $(this).find(":selected").data("id");
        var PrimaryColName = $(this).find(":selected").data("primary");
        var TableUsed = $(this).find(":selected").data("table");
        var IsMERS = $(this).find(":selected").data("mers");
        var Keyword = $(this).find(":selected").data("keyword");
        var PrintName = $(this).find(":selected").data("printname");
        var FieldName = $(this).find(":selected").data("filename");
        var OtherName = $(this).find(":selected").data("othername");

        var IsMERSAssignee = $('#AssigneeName').find(":selected").data("ismers");
        var IsMERSAssignor = $('#AssignorName').find(":selected").data("ismers");
        var IsMERSLender = $('#LenderName').find(":selected").data("ismers");
        var IsMERSEndorser = $('#EndorserName').find(":selected").data("ismers");

        if(Val){
          $.ajax({
              type: 'POST',
              url: '<?php echo base_url();?>order_info/GetMasterProjectDetailsForSelect2',
              data: {'PrimaryColUID':PrimaryColUID,'PrimaryColName':PrimaryColName,'PrintName':PrintName,'TableUsed':TableUsed,'IsMERS':IsMERS,'OrderUID':OrderUID,'Keyword':Keyword,'FieldName':FieldName,'OtherName':OtherName,'Val':Val},
              dataType:'JSON',
              cache: false,
              success: function (response, textStatus, jqXHR) {
                /*console.log(response.Keyword);
                console.log(response.Append);*/

                if(Val === 'OTHERS'){
                  $('#'+response.Keyword+'Append').html('');
                  $('#'+response.Keyword+'Append').hide();
                  $('#'+OtherName+'Append').show();
                  $('#'+OtherName+'Append').html('');
                  $('#'+OtherName+'Append').html(response.Append);
                } else {
                  $('#'+OtherName+'Append').html('');
                  $('#'+OtherName+'Append').hide();
                  $('#'+response.Keyword+'Append').show();
                  $('#'+response.Keyword+'Append').html('');
                  $('#'+response.Keyword+'Append').html(response.Append);
                }

                show_content();
                call_event();      
              },
              error: function (jqXHR, textStatus, errorThrown) {
              },
              complete: function () {
              }
          });
        } else {
          $('#'+Keyword+'Append').html('');
          $('#'+Keyword+'OtherNameAppend').html('');
          show_content();
        }

        var IsMERSVal = $('#'+FieldName).find(":selected").data("ismers");
        $('#'+IsMERS).val(IsMERSVal);

        update_mers();
    });

    $('body').on('change','.IsCheckMers', function(event) { 

      var IsMERSCol = $(this).attr("data-mers");

      if($('#'+IsMERSCol+'1').prop("checked") == true){
        var IsMERSVal = 1;
        $('#'+IsMERSCol).val(IsMERSVal);
      } 

      if($('#'+IsMERSCol+'1').prop("checked") == false){
        var IsMERSVal = 0;
        $('#'+IsMERSCol).val(IsMERSVal);
      }

      update_mers();
            
    });

    function update_mers(){

      var OrderUID = $('#OrderUID').val();
      var IsMERSAssignee = $('#IsMERSAssignee').val();
      var IsMERSAssignor = $('#IsMERSAssignor').val();
      var IsMERSLender = $('#IsMERSLender').val();
      var IsMERSEndorser = $('#IsMERSEndorser').val(); 

      console.log(IsMERSAssignee);
      console.log(IsMERSAssignor);
      console.log(IsMERSLender);
      console.log(IsMERSEndorser);

      $.ajax({
        type: 'POST',
        url: '<?php echo base_url();?>order_info/UpdateIsMERSOrderLevel',
        data: {'IsMERSAssignee':IsMERSAssignee,'IsMERSAssignor':IsMERSAssignor,'IsMERSLender':IsMERSLender,'IsMERSEndorser':IsMERSEndorser,'OrderUID':OrderUID},
        dataType:'JSON',
        cache: false,
        success: function (response, textStatus, jqXHR) {

          if(response.success == 1){
            $('#Div_MIN').html(response.MIN_str);
            $('#Div_MERS').html(response.MERSPhoneNo);
            show_content();
            call_event();  
          } else {
            $('#Div_MIN').html('');   
            $('#Div_MERS').html('');      
            //show_content_edit();
            show_content();
            call_event();       
          }

          if(MIN_Val){
            $('#MIN').val(MIN_Val);
          }

          show_content();
          call_event();      

        },
        error: function (jqXHR, textStatus, errorThrown) {
        },
        complete: function () {
        }
      });
    }

    $('body').on('change','.legal_change', function(event) { 
        event.stopPropagation();
        event.stopImmediatePropagation();

        var OrderUID = $('#OrderUID').val();
        var Val = $('#LegDesc').val();

        $('#LegalDescription').val(Val);

        if(Val == 'OTHERS'){
          var test = $('#LegalDescription').val();
          if(test == 'OTHERS')
          {
            $('#LegalDescription').val('');
          }
          $('.Div_legal').show();          
        } else {
          $('.Div_legal').hide();
        }

        call_event();

    });

      function trigger_fun(){

        $('.typing_complete').attr("disabled", true);
        $('.typing_complete').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');

        $('.review_complete_loading').attr("disabled", true);
        $('.review_complete_loading').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');

        $('.save').attr("disabled", true);
        $('.save').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');

        $('.Reset').attr("disabled", true);
        $('.Reset').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');

        delay(function(){

          var TemplateUID = $('#TemplatesMappingUID').val();
          var OrderUID = $('#OrderUID').val();
          var IsTemplateSaved = $('#IsTemplateSaved').val();
          $.ajax({
            type: 'POST',
            url: '<?php echo base_url();?>order_info/UpdateFieldsTemplateMapping',
            data: {"OrderUID": OrderUID, "TemplateUID": TemplateUID},
            dataType: 'JSON',
            beforeSend: function(){
              
            },
            success: function (data) 
            {
         
              if(data.success == 1){
                $('#FieldSection').html('');
                $('#FieldSectionTableView').html('');
                $('#FieldSection').html(data.DynamicFields);
                $('#FieldSectionTableView').html(data.FieldSectionTableView);
              }  

              $('.typing_complete').attr("disabled", false);
              $('.typing_complete').html('<i class="fa fa-save" style="color: #fff;"></i> &nbsp; Typing Complete');

              $('.review_complete_loading').attr("disabled", false);
              $('.review_complete_loading').html('<i class="fa fa-save" style="color: #fff;"></i> Review Complete <span class="caret"></span>');

              $('.save').attr("disabled", false);
              $('.save').html('<i class="fa fa-floppy-o"></i> Save');

              $('.Reset').attr("disabled", false);
              $('.Reset').html('<i class="fa fa-mail-reply-all"></i> Reset');

          
              componentHandler.upgradeDom();
              select_mdl();
              $('.select2').select2({
                theme: "bootstrap",
              }); 

              $(".datepicker").datetimepicker({
                format: "mm/dd/yyyy",
                autoclose: true,
                minView : 2,
                componentIcon: '.mdi.mdi-calendar',
                navIcons:{
                  rightIcon: 'mdi mdi-chevron-right',
                  leftIcon: 'mdi mdi-chevron-left'
                }
              });

              show_content_edit();
              show_content();
              call_event();   
              $('.projectselect2').trigger('change');
              $( ".currency" ).trigger( "change" );
              //$( ".legal_change" ).trigger( "change" );

              if(IsTemplateSaved == 0){
                $('.Reset').trigger( "click" );
              }

              enableorderedit();
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            complete: function () {

              show_content_edit();
              show_content();
              call_event();   

              if(IsTemplateSaved == 0){
                $('.Reset').trigger( "click" );
              }
             
            }
          });
        }, 2000 );
      }


    $('#TemplatesMappingUID').change(function(event) {
      event.preventDefault();
      var TemplateUID = $(this).val();
      var OrderUID = $('#OrderUID').val();
      var IsTemplateSaved = $('#IsTemplateSaved').val();
      $.ajax({
        type: 'POST',
        url: '<?php echo base_url();?>order_info/UpdateFieldsTemplateMapping',
        data: {"OrderUID": OrderUID, "TemplateUID": TemplateUID},
        dataType: 'JSON',
        beforeSend: function(){
        $('.save').attr("disabled", true);
        $('.save').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
        },
        success: function (data) 
        {
     
          if(data.success == 1){
            $('#FieldSection').html('');
            $('#FieldSectionTableView').html('');
            $('#FieldSection').html(data.DynamicFields);
            $('#FieldSectionTableView').html(data.FieldSectionTableView);
          }  
      
          componentHandler.upgradeDom();
          select_mdl();
          $('.select2').select2({
            theme: "bootstrap",
          }); 

          $(".datepicker").datetimepicker({
            format: "mm/dd/yyyy",
            autoclose: true,
            minView : 2,
            componentIcon: '.mdi.mdi-calendar',
            navIcons:{
              rightIcon: 'mdi mdi-chevron-right',
              leftIcon: 'mdi mdi-chevron-left'
            }
          });

          $('.save').attr("disabled", false);
          $('.save').html('<i class="fa fa-floppy-o"></i> Save');

          $('.projectselect2').trigger('change');
          $( ".currency" ).trigger( "change" );
          //$( ".legal_change" ).trigger( "change" );

          $('.Reset').trigger( "click" );

          

          /*show_content_edit();
          show_content();
          call_event();   
          $('.projectselect2').trigger('change');
          $( ".currency" ).trigger( "change" );*/
          enableorderedit();

        },
        error: function (jqXHR, textStatus, errorThrown) {
        },
        complete: function () {

          /*show_content_edit();
          show_content();
          call_event();   

            $('.Reset').trigger( "click" );*/

        }
      });
    });

  function auto_saving(buttonval){
    var TemplatesMappingUID = $('#TemplatesMappingUID').val();
    var MIN = $('#MIN').val();
    var OrderUID = $('#OrderUID').val();
    var StatusUID = $('#StatusUID').val();

    var is_review_enabled = '<?php echo $is_review_enabled?>';
    var checkreviewholdstatus = '<?php echo $checkreviewholdstatus?>';
    var RecordedDate= $('#RecordedDate').val();
    RecordedDate = new Date(RecordedDate);
    var DeedOfTrustDated= $('#DeedOfTrustDated').val();
    DeedOfTrustDated =  $.trim(DeedOfTrustDated);
    DeedOfTrustDated = new Date(DeedOfTrustDated);
    var MortgageDated= $('#MortgageDated').val();
    MortgageDated =  $.trim(MortgageDated);
    MortgageDated = new Date(MortgageDated);
    if(DeedOfTrustDated >= RecordedDate && DeedOfTrustDated != '')
    {
     $.gritter.add({
      title: 'Recorded Date should be greater than Deed Date',
      class_name: 'color danger',
      fade: false,
      speed:'slow',
    });

     
   } else if(MortgageDated >= RecordedDate && MortgageDated != '')
   {
    $.gritter.add({
      title: 'Recorded Date should be greater than Mortgage Date',
      class_name: 'color danger',
      fade: false,
      speed:'slow',
    });
    
  }
  else
  {
      if (typeof MIN !== typeof undefined) {
      if(MIN.length < 18 ){
        $.gritter.add({
          title: 'MIN value should not be less than 18 Digit',
          class_name: 'color danger',
          fade: false,
          speed:'slow',
        });
        $('.auto_save_info').html(buttonval);
      } else {
        var editorContent=tinymce.get('mymce').getContent();
        var formData = new FormData($('#myForm1')[0]);
        formData.append('content',editorContent);

        $.ajax({
              type: 'POST',
              url: '<?php echo base_url();?>order_info/savetemplate',
              data: formData,
              cache: false,
              processData: false,
              contentType: false,
              beforeSend: function(){
              $('.save').attr("disabled", true);
              $('.save').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
              },
              success: function (response, textStatus, jqXHR) {
                show_content();
                call_event(); 
                $('.save').attr("disabled", false);
                $('.save').html('<i class="fa fa-floppy-o"></i> Save');
                $('#IsTemplateSaved').val('1');
                attach();

                if(is_review_enabled == 1 && checkreviewholdstatus == 0){
                  $('.trigger_typing_review_complete').trigger('click');                  
                } else {
                  $('.trigger_typing_complete').trigger('click');
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
              },
              complete: function () {
              }
          });
      }
    } else {
      var editorContent=tinymce.get('mymce').getContent();
      var formData = new FormData($('#myForm1')[0]);
      formData.append('content',editorContent);

      $.ajax({
            type: 'POST',
            url: '<?php echo base_url();?>order_info/savetemplate',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function(){
            $('.save').attr("disabled", true);
            $('.save').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
            },
            success: function (response, textStatus, jqXHR) {
              show_content();
              call_event(); 
              $('.save').attr("disabled", false);
              $('.save').html('<i class="fa fa-floppy-o"></i> Save');
              $('#IsTemplateSaved').val('1');
              attach();

              if(is_review_enabled == 1 && checkreviewholdstatus == 0){
                $('.trigger_typing_review_complete').trigger('click'); 
              } else {
                $('.trigger_typing_complete').trigger('click');               
              }
            },
            error: function (jqXHR, textStatus, errorThrown) {
            },
            complete: function () {
            }
        });
    }
  }
    

  }

  $('.auto_save_info').click(function(){
    var buttonval = $(this).html();
    $(this).html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
    auto_saving(buttonval);
    setTimeout(function(){ $('.auto_save_info').html(buttonval); }, 2000);
    //$('.auto_save_info').html(buttonval);
  });
  $('.auto_save_info1').click(function(){
    //alert();
    var buttonval = $(this).html();
    $(this).html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
    auto_saving(buttonval);
    setTimeout(function(){ $('.review_complete').html(buttonval); }, 2000);
    //$('.auto_save_info').html(buttonval);
  });
  $('.auto_save_info2').click(function(){
    var buttonval = $(this).html();
    $(this).html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
    auto_saving(buttonval);
    setTimeout(function(){ $('.review_complete_bill').html(buttonval); }, 2000);
    //$('.auto_save_info').html(buttonval);
  });

  function close(){
    $('.typing_complete').html(' ');
    $('.typing_complete').html('<i class="fa fa-save" style="color: #fff;"></i> &nbsp; Typing Complete');
    $('.review_complete').html(' ');
    $('.review_complete').html('Review Complete');
    $('.review_complete_bill').html(' ');
    $('.review_complete_bill').html('Review Complete & Bill Order');
  }

  $('.modal-close').click(function(){
    close();
  })

  // Currency for Order INFO

  /*var keyDown = false, ctrl = 17, vKey = 86, Vkey = 118, Vdown=46; 

  $("body").on("blur" , ".currency" , function(){
    var ele=$(this);
    var val = $(this).val();
     
    if(val==='0.00' || val==='0' || val==='$0.00'){
      $(this).val('0.00');
      $(this).parent().removeClass('is-dirty');
    }

    if($(this).val()!= 0){
      $(this).val($(this).val().split(" ").join(""));
      ele.formatCurrency();
    } 

    call_event();
  });

  $("body").on("keypress" , ".currency" , function(){
      if (!e) var e = window.event;
      if (e.keyCode > 0 && e.which == 0) return true;
      if (e.keyCode)    code = e.keyCode;
      else if (e.which) code = e.which;
      var character = String.fromCharCode(code);

      if (character == '\b' || character == ' ' || character == '\t') return true;

      if ((code == vKey || code == Vkey || code == Vdown))  {                
         if (e.keyCode === 46 && this.value.split('.').length === 2) {
            return false   
           }else{
            return (character) 
           }
      }
      else return (/[0-9]$/.test(character));

      call_event();

  });

  $("body").on("focus" , ".currency" , function(){
    $(this).val(remove($(this).val()));
    $(this).select();
  });

  $("body").on("keydown" , ".currency" , function(){
    if (e.keyCode == ctrl) keyDown = true;
  });

  $("body").on("keyup" , ".currency" , function(){
    if (e.keyCode == ctrl) keyDown = false;
  });*/
  $("body").on("change" , "#PropertyCountyName,#PropertyCityName,#PropertyStateCode,#PropertyStateName" , function(event){
    show_content();
  });
   $("body").on("blur" , "#PropertyCountyName,#PropertyCityName,#PropertyStateCode,#PropertyStateName" , function(event){
    show_content();
  });
 $("body").on("change" , "#PropertyZipCode" , function(event){
      zip_val = $(this).val();
      if(zip_val!='')
      {
        $('.spinnerclass').addClass("be-loading-active");
        $.ajax({
          type: "POST",
          url: '<?php echo base_url();?>order_complete/GetZipCodeDetails',
          data: {'Zipcode':zip_val}, 
          dataType:'json',
          cache: false,
          success: function(data)
          {
            $('.PropertyCityName').empty();
            $('#PropertyStateCode').empty();
            $('.PropertyCountyName').empty();
            $('.MultiOrderedcity').html(' ');
            $('.MultiOrderedcounty').html(' ');
            $('.MultiOrderedstate').html(' ');

            componentHandler.upgradeDom();

            if(data != ''){

              if(data['success'] == 1)
              {
                $("#zipcodeadd").hide();

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
                  $('.PropertyCityName').append('<li><a href="javascript:(void);" data-value="' + v['CityName'] + '">' + v['CityName'] + '</a></li>');
                  $('#PropertyCityName').parent().parent().addClass('is-dirty');
                  zipcode_select();
                });

                $.each(data['County'], function(k, v) {
                  $('#PropertyCountyName').val(v['CountyName']);
                  $('.PropertyCountyName').append('<li><a href="javascript:(void);" data-value="' + v['CountyName'] + '">' + v['CountyName'] + '</a></li>');
                  $('#PropertyCountyName').parent().parent().addClass('is-dirty'); 
                  zipcode_select();
                });

                $.each(data['State'], function(k, v) {
                  $('#PropertyStateCode').val(v['StateCode']);
                  $('.PropertyStateCode').append('<li><a href="javascript:(void);" data-value="' + v['StateCode'] + '">' + v['StateCode'] + '</a></li>');
                  $('#PropertyStateCode').parent().parent().addClass('is-dirty');
                  zipcode_select();
                });
                $.each(data['State'], function(k, v) {
                  $('#PropertyStateName').val(v['StateName']);
                  $('.PropertyStateName').append('<li><a href="javascript:(void);" data-value="' + v['StateName'] + '">' + v['StateCode'] + '</a></li>');
                  $('#PropertyStateName').parent().parent().addClass('is-dirty');
                  zipcode_select();
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

                $("#zipcodeadd").show();
              }
              $('.spinnerclass').removeClass("be-loading-active");
              //$('#joyRideTipContent').joyride('destroy');
            }
            call_event(); 

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
        call_event(); 
      }     
    });
 function zipcode_select(){
                $('.dropdown-menu a').click(function() {
                  $(this).closest('.dropdown').find('input.select')
                  .val($(this).attr('data-value'));
                });
              } 
</script>


