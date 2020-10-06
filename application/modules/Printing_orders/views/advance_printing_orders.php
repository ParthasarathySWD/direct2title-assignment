<!--BEGIN CONTENT-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/lib/fSelect-Selectall/fSelect.css">
<!-- start body header -->
<div id="page_top" class="section-body  bg-new-header-top">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Printing Orders</h1> 
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
                                <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-subproduct" aria-expanded="true">Printing Orders</a></li>
                               
                            </ul>
                        </div>
                        <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="add-subproduct">
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">

                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Client</label>
                                                <select name="customer" id="customer" class="form-control mdl-textfield__input input-xs select2 mdl-select2" >
                                                    <option></option>
                                                    <?php
                                                    foreach($customers as $cust)
                                                    {
                                                      if(count($customers) == 1)
                                                      {
                                                        echo '<option value="'.$cust->CustomerUID.'" selected="">'.$cust->CustomerNumber.' / '.$cust->CustomerName.'</option>'; 
                                                    }
                                                    else
                                                    {
                                                        echo '<option value="'.$cust->CustomerUID.'">'.$cust->CustomerNumber.' / '.$cust->CustomerName.'</option>';                                   
                                                    }
                                                } ?> 
                                            </select>      
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Project</label>
                                                <select name="project" id="project" class="form-control mdl-textfield__input input-xs select2 mdl-select2" >
                                                    <option></option>
                                                    <?php
                                                    foreach($Projects as $project)
                                                    {
                                                      echo '<option value="'.$project->ProjectUID.'">'.$project->ProjectName.'</option>';                                   
                                                  } ?> 
                                              </select>  
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">MERS</label>
                                                <select name="mers" id="mers" class="form-control mdl-textfield__input input-xs select2 mdl-select2">
                                                    <option></option>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">State</label>
                                                <select name="stateuid" id="stateuid" class="form-control mdl-textfield__input input-xs select2 mdl-select2">
                                                    <option value="all">ALL STATES</option>
                                                    <?php foreach ($States as $key => $state) { ?>
                                                        <option value="<?php echo $state->StateUID; ?>"><?php echo $state->StateCode; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                         <div class="col-md-3 col-lg-3" style="display: none;">
                                            <div class="form-group">
                                                <label class="form-label">VP/Non VP</label>
                                                <select name="vp" id="vp" class="form-control mdl-textfield__input input-xs select2 mdl-select2" >
                                                    <option></option>
                                                    <option value="1" >VP</option>
                                                    <option value="2">Non VP</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        

                                    </div>
                                </div>

                               


                            </div>

                        </div>
                       
                    </div>

                    <div class="card-header">
                        <div class="col-md-12 col-lg-12 mb-3 mt-2">
                            <div class="btn-list  text-right">
                                <a href="#" class="btn btn-secondary">Reset</a>
                                <a href="#" class="btn btn-primary">Search</a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
          <div class="col-sm-12" id="DivLoadTable">

          </div>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/lib/fSelect-Selectall/fSelect.js"></script>
<script type="text/javascript">

    $(document).ready(function(){
  $.ajax({
    type: "POST",
    url: "<?php echo base_url()?>printing_orders/GetPrintingOrders",
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
});

    $(".mdb-fselect:not(.singlemdbfselect)").each(function() {
      var placeholder = $(this).attr('placeholder');
      $(this).fSelect({
        placeholder: placeholder,
        numDisplayed: 1,
        overflowText: '{n} selected',
        showSearch: true
    });
  });
    $('.edit-filed-level-btn').on('click', function(){
        $('.edit-filed-level').show();
    })

     $(document).on('change', '#customer', function (e) {
    var CustomerUID=$(this).val();
    $.ajax({
      type:'POST',
      url:'<?php echo base_url("Printing_Orders/GetProjectByCustomer"); ?>',
      data:{'CustomerUID':CustomerUID},
      dataType:'json',
      success: function(data){

        if (data.validation_error==1) {
          $.gritter.add({
            title: data['message'],
            class_name: 'color danger',
            fade: true,
            time: 3000,
            speed:'slow',
          });
          return false;
        }
        $('#project').empty();
        if(data['Projects'].length > 0){
          $.each(data['Projects'], function(k, v) {
            if(data['Projects'].length == 1){
              $('#project').append('<option value="' + v['ProjectUID'] + '" selected>' + v['ProjectName'] + '</option>');              
            }
            else{
              $('#project').append('<option value="' + v['ProjectUID'] + '">' + v['ProjectName'] + '</option>');              
            }

            $('#project').parent().addClass('is-dirty');
          });
          $('.select2').select2({
            theme: "bootstrap",
          });
        }
      }
    })

  })

    
</script>
