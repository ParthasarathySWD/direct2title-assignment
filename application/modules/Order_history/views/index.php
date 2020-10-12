<style type="text/css">
    .order_due{
        position: absolute;
        right: 170px;
        width: auto;
        font-size: 13px;
        text-align: right;
    }
</style>
<?php $this->load->view('workflowview/workflow_topheader'); ?>

<div class="section-body">
    <div class="container-fluid">
      <div class="row" style="margin:0 -15px;">
        <div class="col-md-12 col-lg-12 p-0">
          <div class="card m-0" style="border-radius:0;">
           <?php $this->load->view('workflowview/workflow_menu'); ?>


           <div class="card-header">
            <h3 class="card-title pt-2 text-primary">WorkFlow Activity</h3>


            <!-- <h3 class="card-title pt-2 text-danger order_due">Order Due by 08/28/2020 03:20 PM</h3> -->

            <?php 
            $date = d2t_format_date(date($this->config->item('date_format'),strtotime('now'))); 
            $DueDate=$assignedorder[0]->OrderDueDatetime; 
            $DueDate = ($DueDate == NULL) ? $DueDate : d2t_format_date($DueDate);

            if($DueDate < $date){
                $value='<h3 class="card-title pt-2 text-danger order_due">Order Due by'.' '.$DueDate.'</h3>';
            }

            if($DueDate > $date){
                $value='<h3 class="card-title pt-2 text-success order_due">Order Due by'.' '.$DueDate.'</h3>';
            }

            if($DueDate == $date){
                $value='<h3 class="card-title pt-2 text-info order_due">Order Due by'.' '.$DueDate.'</h3>';
            }
            ?>
              <?php echo $value;?>
            <div class="card-options" style="position: absolute;right: 20px;top: 60px;z-index:1;">
              <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><span class="font-14 mr-2" style="position:relative;top:-2px;">View Fullscreen</span><i class="fe fe-maximize"></i></a>
          </div>
      </div>
      <div class="card-body pb-1">
        <div class="col-md-12">

             <input type="hidden" class="hiddenclass" id="RelationalOrders" value="<?php echo implode(',',array_column($RelationalOrders,'OrderUID'));?>" >

            <table class="table table-vcenter table-new" id="test" style="width:100%;">
                <thead>
                    <tr>
                        <th>WorkFlowModule</th>
                        <th>Assigned User</th>
                        <th>AssignedDateTime</th>
                        <th>Status</th>
                        <th>OnholdDateTime</th>
                        <th>CompletedDateTime</th>
                    </tr>
                </thead>
               <?php foreach($assignedorder as $d){ ?>

            <?php if($d->AssignedToUserUID == NULL || $d->SendToVendor == '1'){
              $Workflow_User = strtok($d->VendorName, ' ');
            } else{
              $Workflow_User = $d->UserName;
            } ?>

            <tr>
              <td>
                <?php echo  $d->WorkflowModuleName; ?>                  
              </td>

              <td>
                <?php echo  $Workflow_User;  ?>                  
              </td>

              <td>  
                <?php echo d2t_format_date($d->AssignedDatetime); ?>
              </td>

              <td>
                <?php if(($d->AssignedToUserUID == NULL || $d->SendToVendor == '1') && ($d->WorkflowStatus=='5')){
                  if($d->QCCompletedDateTime!=''){
                    echo  "Completed"; 
                  }else{
                    echo  "QC In Progress"; 
                  }
                } else { ?>
                  <?php if($d->WorkflowStatus=='3'){ 
                    echo  "Work In Progress"; 
                  } ?>

                  <?php if($d->WorkflowStatus=='5'){  
                    echo  "Completed"; 
                  } ?> 

                  <?php if($d->WorkflowStatus=='4'){
                    echo 'OnHold';
                  }?>

                  <?php if($d->WorkflowStatus=='0'){
                    echo 'Assigned';
                  }?>
                <?php } ?>
              </td>

              <td>
                <?php if($d->OnholdDateTime!='00-00-0000 00:00:00' && $d->WorkflowStatus=='5'){  echo  d2t_format_date($d->OnholdDateTime);  } ?> 
              </td>

              <td>
                <?php if($d->AssignedToUserUID == NULL ||$d->SendToVendor == '1'){ 
                  if($d->QCCompletedDateTime!=''){  
                    echo  d2t_format_date($d->CompleteDateTime);  
                  }
                }else{
                  if($d->WorkflowStatus=='5' && $d->CompleteDateTime!='00-00-0000 00:00:00'){
                    echo  d2t_format_date($d->CompleteDateTime);
                  }
                } ?>
              </td>                  
            </tr> 
          <?php } ?>  

          <?php foreach ($revisionorders as $key => $rev) { ?>
            <tr>
              <td><?php echo  'Revision(' . $rev->WorkflowModuleName . ')'; ?></td>
              <td><?php echo  $rev->UserName; ?></td>
              <td><?php echo   d2t_format_date($rev->AssignedDateTime); ?></td>
              <td><?php echo   d2t_format_date($rev->CompleteDatetime) == NULL ? 'Work in Progress' : 'Completed'; ?></td>
              <td><?php echo  ' '; ?></td>
              <td><?php echo   d2t_format_date($rev->CompleteDatetime); ?></td>
            </tr>
          <?php } ?>

        </tbody>
            </table>
        </div>
    </div>

    <div class="card-header">
        <h3 class="card-title pt-2 text-primary">Loan Activity</h3>
    </div>
    <div class="card-body pb-4">
        <div class="col-md-12">

            <table class="table table-vcenter table-new" id="example_test" cellspacing="0" style="width:100%;">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Module</th>
                        <th>Operations</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>


      <tr>
        <?php   
        foreach($all_details as $d){ 
                  ?>  
                  <td class="cell-detail" data-order="<?php echo $d->AuditUID; ?>"> 
                    <?php echo  d2t_format_date($d->DateTime);?>
                    <button class="btn btn-space btn-secondary btn-xs OrderNumber_show" style="<?php if($MultiOrders==1){echo 'display:block;';}else{echo 'display:none;';}?>"><?php echo $d->OrderNumber; ?></button>
                  </td>
                  <td class="milestone">
                    <?php echo $d->ModuleName;?>
                  </td>
                  <td class="cell-detail">
                  <?php
                  if($d->ModuleName == 'Followup'){
                    $arr = json_decode($d->Content);
                    echo $arr->body;
                  }else{
                    echo htmlspecialchars_decode($d->Content);
                  }
                   ?>
                  </td>
                    <!-- Desc: D2TINT-273: Integration User Name Changes; Author: Shivaraja N; Since: Sept 4th 2020 -->
                    <?php 
                    $IntegrationUser = $this->common_model->CheckIntegratedOrder($OrderUID);
                     $SystemUserUID = $this->common_model->GetLoginIdByUserUID($d->UserUID);
                    $UserName = $d->UserName;
                    if (!empty($IntegrationUser) && $SystemUserUID == 'isgn')
                    {
                        $UserName = $IntegrationUser;
                    }
                   ?>
                  <td class="user-avatar cell-detail user-info"><!-- <img src="assets/img/avatar7.png" alt="Avatar"> --><span><?php echo $UserName; ?></span>
                    </span>
                    <span class="cell-detail-description"><?php echo $d->RoleName;?></span>
                    </td>                       


                </tr>
              <?php  }?>             


            </tbody> 
            </table>
        </div>

    </div>
</div>

</div>
</div>
</div>

<script type="text/javascript">

  // $( document ).ready(function() {
    //$('.history-datatable').DataTable({

       // pageLength: 5,
     //   lengthMenu: [[5, 10, 15, 25, 50, -1], [5, 10, 15, 20, 25, 50, "All"]],
 //   });
 //});


   $(document).ready(function() {

      $('#FilterOrders').on('change',function(e){
        e.preventDefault();
        var OrderUIDs = $(this).val();
        if(OrderUIDs == "All"){
            OrderUIDs = $('#RelationalOrders').val();
        }
        $.ajax({
         type: "POST",
         dataType : 'json',
         url: '<?php echo base_url();?>Order_history/getAllAudits',
         data: {OrderUID : OrderUIDs}, 
         cache: false,
         beforeSend: function(){

            $('.spinnerclass').addClass('be-loading-active');
        },
        success: function(data)
        {
            $('#audit_block').html(data);
            if(OrderUIDs == "All"){
              $('.OrderNumber_show').show();
          }
          var table = $('#example_test').dataTable({
              aaSorting: [],
              paging:true,
              lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]] ,
              "order": [[ 0, "desc" ]]
          });
          $('.spinnerclass').removeClass('be-loading-active');

      }

  });
    });

      var table = $('#example_test').dataTable({
          aaSorting: [],
          paging:true,
          lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]] ,
          "order": [[ 0, "desc" ]]
      });
  });
</script>
