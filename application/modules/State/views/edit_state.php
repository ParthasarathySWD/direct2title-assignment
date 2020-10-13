<link href="assets/css/module/state/state.css" rel="stylesheet">
<style type="text/css">
    .card-title {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.2;
    }
</style>
<!-- start body header -->
<div id="page_top" class="section-body bg-new-header-top">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">My States</h1> 
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
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">

                    <div class="card-header">
                        <div class="col-md-12 col-lg-12 p-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-template" aria-expanded="true">Edit State</a></li>
                                <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="#audit-log" aria-expanded="false">Audit Logs</a></li>
                            </ul>
                        </div>
                        <div class="card-options" style="position: absolute;right: 20px;top: 20px;z-index:1;">
                          <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><span class="font-14 mr-2" style="position:relative;top:-2px;">View Fullscreen</span><i class="fe fe-maximize"></i></a>
                      </div>
                  </div>
                 <form action="#" name="frm_state" id="frm_state">
                  <input type="hidden" name="StateUID" id="StateUID" value="<?php echo $StateDetails->StateUID?>" />
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="add-template">
                            <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pl-0">
                                            <h3 class="card-title pt-2">State Details</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">

                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">State Name <sup style="color:red;">*</sup></label>
                                                <input class="form-control input-xs" type="text" id="StateName" name="StateName" value="<?php echo $StateDetails->StateName?>" maxlength="50" >
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">State Code <sup style="color:red;">*</sup></label>
                                                <input class="form-control input-xs" type="text" id="StateCode" name="StateCode" value="<?php echo $StateDetails->StateCode?>" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">FIPS Code <sup style="color:red;">*</sup></label>
                                                <input class="form-control input-xs" type="text" id="FIPSCode" name="FIPSCode" value="<?php echo $StateDetails->FIPSCode?>" maxlength="50">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3 mt-4 pl-4">
                                            <label class="custom-switch">
                                                <?php  $Checklist = ($StateDetails->Active == 1) ? 'checked' : ''; ?>
                                                <input type="checkbox" name="Active" id="Active" class="custom-switch-input" value="<?php echo $StateDetails->Active; ?>" checked="<?php echo $Checklist;  ?>">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">Is Active</span>
                                            </label>
                                        </div>

                                        <div class="col-md-12 col-lg-12 mb-3 mt-3">
                                            <div class="btn-list  text-right">
                                                <a href="State" class="btn btn-secondary">Cancel</a>
                                                <button  type="submit" class="btn btn-primary" id="BtnUpdate" >Update State</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="tab-pane fade" id="audit-log">
                            <div class="card-body pb-4 pt-3">
                                <div class="col-md-12 col-lg-12">
                                    <table class="table table-vcenter table-new new-datatable1 text-nowrap" cellspacing="0" id="" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Module Name</th>
                                                <th>Activity</th>
                                                <th>DateTime</th>
                                                <th style="width:50px">User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($Auditlog as $key => $value) { ?>
                                             <tr>
                                                <td><?php echo $value->ModuleName; ?></td>
                                                <td><?php echo $value->Content; ?></td>
                                                <td><?php echo $value->DateTime; ?></td>
                                                <td><?php echo $value->UserUID; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
   $(document).ready(function(e) {
    $('.new-datatable1').DataTable({

    });
});

   $('#BtnUpdate').click(function(event)
 {

  event.preventDefault();

  if($('#StateName').val()!=0){

    var form=$('#frm_state');

    var formData=$('#frm_state').serialize();
    $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>State/update_state',
     data: formData, 
     dataType:'json',
     cache: false,
     success: function(data)
     {
        toastr["success"]("", data.msg);
        window.location.replace('<?php echo base_url(); ?>state');
    }
});
}
else
{
  toastr["error"]("", 'Required Fields are Empty !! Please Check !!');
}
});
</script>

