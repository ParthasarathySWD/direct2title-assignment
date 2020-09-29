
<!-- start body header -->
<!-- <div id="page_top" class="section-body">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Prinding Orders</h1> 
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
</div> -->
<!-- <div class="section-body">
    <div class="container-fluid">
         <h5 class="pb-1 inner-page-head">Single Order</h5> -->
        <!-- <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">

                    <div class="card-header"> -->
                        <!-- <div class="col-md-12 col-lg-12">
                            <a href="Subproducts/add" type="button" class="btn btn-primary btn-sm mt-2"><i class="fe fe-plus"></i> Add New Sub Product
                            </a>
                            <a class="float-right mr-4 pr-2 mt-2" href=""><button type="button" class="btn btn-red btn-sm" ><i class="fe fe-download"></i> Export as Excel</button></a>
                        </div> -->
                     <!--    <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>
                    <div class="card-body pb-4 pt-3"> -->

                        <div class="col-md-12 col-lg-12">
                            <table class="table table-vcenter table-new text-nowrap" cellspacing="0" id="review_table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th><div class="be-checkbox be-checkbox-color inline">
                                          <input type="checkbox" id="all_select_docs">
                                          <label for="all_select_docs"></label>
                                      </div></th>
                                        <th>loan No</th>
                                        <th>Order No</th>
                                        <th>Customer Name</th>
                                        <th>Project</th>
                                        <th>State</th>
                                        <th>Status</th>
                                        <th>Review Completed Date</th>
                                        <th>Order Entry Date</th>
                                        <th class="text-center" style="width:100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                   <?php 
                                   $TATOrdersUIDs = '1';
                                   $i=1; 
                                   foreach ($printing_orders as $key => $orders) { 
                                    $completed_workflowstatus = $this->common_model->completed_status_order($orders->OrderUID);

                                    $orderdetails = $this->common_model->get_orderdetails($orders->OrderUID);
                                    $CustomerUID = $orderdetails->CustomerUID;
                                    $SubProductUID = $orderdetails->SubProductUID;

                                    $ProductUID = $this->common_model->GetProductUIDBySubProductUID($SubProductUID);
                                    $Product = $ProductUID->ProductUID;

                                    $DynamicProduct = $this->common_model->CheckIsDynamicProduct($Product);
                                    $IsDynamicProduct = $DynamicProduct->IsDynamicProduct;
                                    $CheckFinalReport = $this->common_model->CheckFinalReportExist($orders->OrderUID);
                                    if(!$CheckFinalReport){
                                      $CColor = 'rgba(255, 0, 0, 0.32)';
                                      $TTitle = 'Please Generate Final Report';
                                      $DocumentFileName = '';
                                  }else{
                                      $CColor = '';
                                      $DocumentFileName = $CheckFinalReport->DocumentFileName;
                                  }
                                  ?>

                                  <tr style="" title="<?php echo $TTitle; ?>" data-orderuid="<?php echo $orders->OrderUID; ?>"
                                      data-documentfilename = "<?php echo $DocumentFileName; ?>">
                                      <td>
                                        <div class="be-checkbox be-checkbox-color inline">
                                          <?php 
                                          if(!$CheckFinalReport){ ?>
                                            <input type="checkbox"  class="select_doc" id="selectdoc<?php echo $i; ?>" <?php echo $orders->OrderUID==$OrderUID ? 'checked' : ''; ?> >
                                            <label for="selectdoc<?php echo $i; ?>"></label>
                                        <?php }else{ ?>
                                            <input type="checkbox"  class="select_doc" id="selectdoc<?php echo $i; ?>"  <?php echo $orders->OrderUID==$OrderUID ? 'checked' : ''; ?>>
                                            <label for="selectdoc<?php echo $i; ?>"></label>
                                        <?php } ?>

                                    </div>
                                </td>
                                <td><?php echo $orders->LoanNumber; ?></td>
                                <?php 
                                if($orders->PriorityUID == '3')
                                   {?>
                                    <td class="nowrap"><?php if(date('Y-m-d H:i:s', strtotime($orders->Ymd_OrderDueDatetime)) <= date('Y-m-d H:i:s')) : echo "<a href='".base_url('users/order_session/'.$orders->OrderUID)."' class='text-danger'>".$orders->OrderNumber."</a>"; else: echo '<a href="'.base_url('users/order_session/'.$orders->OrderUID).'" class="text-primary">'.$orders->OrderNumber.'</a>'; endif;  ?> <img src="<?php echo base_url(); ?>assets/img/asap.png" title="<?php echo $orders->PriorityName; ?>" height="20px" width="20px"></td>
                                <?php } else if($orders->PriorityUID == '1'){
                                    ?>
                                    <td class="nowrap"><?php if(date('Y-m-d H:i:s', strtotime($orders->Ymd_OrderDueDatetime)) <= date('Y-m-d H:i:s')) : echo "<a href='".base_url('users/order_session/'.$orders->OrderUID)."' class='text-danger'>".$orders->OrderNumber."</a>"; else: echo '<a href="'.base_url('users/order_session/'.$orders->OrderUID).'" class="text-primary">'.$orders->OrderNumber.'</a>'; endif;  ?> <img src="<?php echo base_url(); ?>assets/img/rush.png"  title="<?php echo $orders->PriorityName; ?>" height="20px" width="20px"></td>
                                <?php } else{?>
                                    <td><?php echo '<a href="'.base_url('users/order_session/'.$orders->OrderUID).'" class="text-primary">'.$orders->OrderNumber.'</a>';?></td>
                                <?php }?>
                                <td><?php echo $orders->CustomerNumber.' / '.$orders->CustomerName; ?></td>
                                <td><?php echo ($orders->ProjectName != '' ? $orders->ProjectName : '-'); ?></td>
                                <td><?php echo $orders->PropertyStateCode; ?></td>
                                <td><span class="btn btn-rounded btn-xs statusbutton" style="color: #fff;border-radius:25px;background: #696664"> Waiting For Print
                                </span></td>
                                <td><?php
                                $assignment=$this->db->get_where('torderassignment', array('OrderUID'=>$orders->OrderUID, 'WorkflowModuleUID'=>4, 'WorkflowStatus'=>5))->row();

                                echo $assignment->CompleteDateTime!='' ? date('Y-m-d H:i:s', strtotime($assignment->CompleteDateTime)) : ''; ?></td>
                                <td><?php echo $orders->OrderEntryDatetime; ?></td>
                                <td>
                                    <a class="btn edit_btn" title="Edit Order" href="<?php echo base_url();?>attachments?OrderUID=<?php echo $orders->OrderUID; ?>"><i class="icon-pencil"></i></i></a>                            
                                </td>

                                  </tr>
                                   <?php $i++; } ?> 

                                    <!-- <tr>
                                        <td><div class="be-checkbox be-checkbox-color inline">
                                          <input type="checkbox" id="all_select_docs">
                                          <label for="all_select_docs"></label></div></td>
                                        <td>56789</td>
                                        <td>12345</td>
                                        <td>Property Report</td>
                                        <td>Property Report</td>
                                        <td>Property Report</td>
                                        
                                        <td>
                                            <label class="custom-switch">
                                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description"></span>
                                            </label>
                                        </td>
                                        <td>00-00-0000</td>
                                        <td>00-00-0000</td>
                                        <td class="actions text-center">
                                            <a href="Order_summary" class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                        </td>
                                    </tr> -->
                                  
                                </tbody>
                            </table>
                        </div>
                           <div class="card-header">
                            <div class="col-md-12 col-lg-12 mb-3 mt-2">
                                <div class="btn-list  text-right">

                                    <!-- <a href="#" class="btn btn-primary" >Print</a> -->
                                     <button class ="btn btn-primary" data-id="<?php echo $OrderrUID;?>" data-title="Sort & Merge Documents" data-requireemail="no" onClick="print_popup(this)" >&nbsp;&nbsp; <i class="fa fa-print" aria-hidden="true"></i>  Print &nbsp;&nbsp;&nbsp;</button>
                                </div>
                            </div>
                    </div>
                    <!-- </div>
                </div>
            </div>
        </div> -->
<div class="modal fade bd-example-modal-lg" id="test" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Printing </h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="testprint">
        
      </div>
        <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cancel</button>
      <button type="button" data-dismiss="modal" class="btn btn-success modal-close">Proceed</button>
    </div>
    </div>
  </div>
</div>

<!-- <div id="PrintingModal" class="modal-container colored-header colored-header-success custom-width modal-effect-9" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
      <h3 class="modal-title">Printing </h3>
    </div>
    <div class="modal-body form">

    </div>
    <div class="modal-footer">
      <button type="button" data-dismiss="modal" class="btn btn-default modal-close">Cancel</button>
      <button type="button" data-dismiss="modal" class="btn btn-success modal-close">Proceed</button>
    </div>
  </div>
</div> -->

    <div id="md-fullWidth" tabindex="-1" role="dialog" class="modal colored-header colored-header-primary  fade">
      <div class="modal-dialog full-width  modal-spinnerclass be-loading">
        <div class="be-spinner">
          <svg width="40px" height="40px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle fill="none" stroke-width="4" stroke-linecap="round" cx="33" cy="33" r="30" class="circle"></circle>
          </svg>
        </div>

        <div class="modal-content">
          <div class="modal-header" style="height: 20px; padding-top:10px;">
            <button type="button" data-dismiss="modal" aria-hidden="true" class="close"><span class="mdi mdi-close"></span></button>
            <h3 class="modal-title text-center" style="font-size: 15px;">Bulk Printing </h3>
          </div>
          <div class="modal-body">

          </div>
          <div class="modal-footer">
            <div class="col-sm-12">
            <button type="button" class="btn btn-success btn-sm" style="margin-right: 28px;" id="btn-bulkprint">Print Orders</button>
          </div>
        </div>
      </div>
    </div>  
    </div>  

    <div id="md-printcomplete" tabindex="-1" role="dialog" class="modal colored-header colored-header-primary  fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header" style="height: 20px; padding-top:10px;">
            <button type="button" data-dismiss="modal" aria-hidden="true" class="close"><span class="mdi mdi-close"></span></button>
            <h3 class="modal-title text-center" style="font-size: 15px;">Print Confirmation </h3>
          </div>
          <div class="modal-body">
            <input type="hidden" value="" id="hid-bulkprint-orders">
            <div class="col-sm-12 text-center">
              <p id="review_msg" class="text-center" style="padding: 5px 0px; color:#000;font-weight: 600; font-size: 16px">Are the orders printed successfully?</p>
              <p class="text-center">
                <button type="button" class="btn btn-success" id="btnBulkPrintComplete" onClick="BulkPrintCompleteFunc()" style="margin-right: 10px;">Yes, Printing Complete</button>
                <button type="button" data-dismiss="modal" class="btn btn-default modal-close " >No</button>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>  


<script src="<?php echo base_url();?>assets/lib/jquery.niftymodals/dist/jquery.niftymodals.js" type="text/javascript"></script>



        <script type="text/javascript">
            

  $(document).ready(function(){
        $('#review_table').dataTable({
      // responsive:true, 
      // scrollcollapse:true,
      paging:true,
      scrollX:true,
      pageLength: 15,
      lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 20, 25, 50, "All"]],
      fixedColumns:   {
        leftColumns: 0,
        rightColumns: 1
      },

    });
    });


  //Select All Documents
      $("#all_select_docs").click(function(){

        if (this.checked)
        {
          $(".select_doc:enabled").prop('checked', this.checked);
          //CalculateFileSize();
        }
        else
        { 
          $(".select_doc:enabled").prop('checked', this.checked);
          //$(".select_doc").prop("disabled",false);
          //CalculateFileSize();
        }
      });


      $(document).off('change', '.select_doc').on('change', '.select_doc', function(e){
          // CalculateFileSize();

          var all_rows=$('.select_doc').length;
          var checked_rows=$('.select_doc:checked').length;

          if (all_rows == checked_rows) {
            $("#all_select_docs").prop('checked', true);
          }

          if (!this.checked) {
            $("#all_select_docs").prop('checked', this.checked);            
          }

        });   

      $(document).off('change', '.select_doc').on('change', '.select_doc', function(e){
          // CalculateFileSize();

          var all_rows=$('.select_doc').length;
          var checked_rows=$('.select_doc:checked').length;
          var filter_value = $('.dataTables_filter input').val();


          if ($(this).prop('checked')==true && $('.select_doc:checked').length==1 && !filter_value) {

            var OrderUID=$(this).closest('tr').attr('data-orderuid');

            // console.log(data);
            $.ajax({ 
              url: '<?php echo base_url("Printing_Orders/GetPrintingOrdersByOrderUID"); ?>',
              data: {"OrderUID":OrderUID},
              dataType:'json',
              type:"POST",
              cache: false,
              beforeSend: function(){
                $('.spinnerclass').addClass('be-loading-active');
              },
              success: function(data){
                if (data.validation_error==0) {

                  $('#DivLoadTable').html(data.html);

                  $('#customer option[value='+data.CustomerUID+']').prop('selected', true).trigger('change');
                  $('#mers option[value='+data.MERS+']').prop('selected', true);
                  
                  if (data.MERS==1) {

                    $('#vp').empty()


                    $('#vp').append('<option value="1">VP</option><option value="2">Non VP</option>');
                    $('#vp option[value='+data.VP+']').prop('selected', true);

                    $('#vp').select2({
                      theme: "bootstrap",
                    }); 
                    $('#vp').trigger('change');
                    $('#vp').parent().addClass('is-dirty');
                    $('#vp_div').show();
                  }
                  else{

                    $('#vp_div').hide();
                    $('#vp option[value=2]').prop('selected', true);
                  }

                  $('#stateuid').val(data.StateUID);
                }

                $('.spinnerclass').removeClass('be-loading-active');

                $('.select2').select2({
                  theme: "bootstrap",
                });
                $('.select2').parent().addClass('is-dirty');

              }
            })

          }
          else if($(this).prop('checked')==false && $('.select_doc:checked').length==0 && !filter_value){
            $('.select2').not('#project').each(function (k, element) {
              $(element).find(':selected').prop('selected', false);
            })
            $('#vp').empty()

            $('#vp_div').hide();

            $('#project').empty();
            $('.select2').select2({
              theme: "bootstrap",
            });

            $.ajax({
              type: "POST",
              url: "<?php echo base_url()?>Printing_Orders/GetPrintingOrders",
              dataType: "html",
              data: {}, 
              cache: false,
              beforeSend: function(){
                $('.spinnerclass').addClass("be-loading-active");
              },         
              success: function(data)
              {
                $('.spinnerclass').removeClass("be-loading-active");
                $('#DivLoadTable').html(data);
              },
              error:function(jqXHR, textStatus, errorThrown)
              {
                console.log(jqXHR.responseText);
              }
            });
            $('.select2').select2({
              theme: "bootstrap",
            });

          }
          if (all_rows == checked_rows) {
            $("#all_select_docs").prop('checked', true);
          }

          if (!this.checked) {
            $("#all_select_docs").prop('checked', this.checked);            
          }

        });      
     function print_popup(me){

          documents=[];
          OrderUID = [];
          OrderDocuments=[];
          var check = $('.select_doc:checked').length;
          var title = $(me).attr('data-title');
          var data = new Array();
          $('.select_doc:checked').each(function(){
            obj = new Object();
            obj['FileName']= $(this).closest('tr').attr('data-documentfilename');
            obj['OrderUID']= $(this).closest('tr').attr('data-orderuid');
            data.push(obj);

          });

          if(check == 1)
          {

            $.ajax({ 
              url: '<?php echo base_url("Printing_Orders/ShowPrintModal"); ?>',
              data: {"OrderDocuments":data},
              type:"POST",
              cache: false,
              beforeSend: function(){
                $('.spinnerclass').addClass('be-loading-active');
              },
              success: function(data){

                $('#testprint').html(data);
                $('#test').modal('show');


                // bootbox.modal({
                //   message: data,
                //   className: "modal70"
                // });

                $('.modal70').modal('show');

                $('.spinnerclass').removeClass('be-loading-active');

              }
            })

          }
          else if(check > 1){

            $.ajax({ 
              url: '<?php echo base_url("Printing_Orders/ShowBulkPrintModal"); ?>',
              data: {"OrderDocuments":data},
              type:"POST",
              dataType: 'json',
              cache: false,
              beforeSend: function(){
                $('.spinnerclass').addClass('be-loading-active');
              },
              success: function(data){
                console.log(data);

                if (data.validation_error == 1) {

                  $.gritter.add({
                    title: 'Error',
                    text: data.messge,
                    class_name: 'color danger',
                    fade: true,
                    speed:'slow',
                    time : 25,

                  });

                }
                else if(data.validation_error == 0){
                  $('#md-fullWidth .modal-body').html(data.html);
                  $('#md-fullWidth').modal('show');
                }

                $('.spinnerclass').removeClass('be-loading-active');

              },
              error: function (jqXHR) {
                console.log(jqXHR);

              }
            })

          }
          else
          {
            $('.spinnerclass').removeClass('be-loading-active');
            $.gritter.add({
              title: 'Error',
              text: 'No Document Choosen',
              class_name: 'color danger',
              fade: true,
              speed:'slow',
              time : 25,

            });

          }
        } 
      
        </script>
