<!--BEGIN CONTENT-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/module/clients/style.css">
<!-- start body header -->
<div id="page_top" class="section-body">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Clients</h1> 
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
            <form action="#" name="frm_customer" id="frm_customer">
            <input type="hidden" name="CustomerUID" id="CustomerUID" value="0" />
                    <?php $this->load->view('Clients/client-top-header-menu'); ?>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="clientinfo">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12 col-lg-12 pl-0">
                                                <h3 class="card-title pt-3">  Client Info</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12 col-lg-12 add-product-div p-0">
                                        <div class="row">

                                            <div class="col-md-3 col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-label">Client Number</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">#</span>
                                                        </div>
                                                        <input type="text" class="form-control " id="CustomerNumber" name="CustomerNumber">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-label">Client Name </label>
                                                    <input type="text" class="form-control required" id="CustomerName" name="CustomerName">
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-lg-3">
                                                <div class="form-group">
                                                    <label class="form-label">Account Number</label>
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">#</span>
                                                        </div>
                                                        <input type="text" class="form-control" id="SAPReportName" name="SAPReportName">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 col-lg-3 mt-4 pt-1">
                                                <label class="custom-control custom-checkbox custom-control-inline">
                                                    <input type="checkbox" class="custom-control-input" id="ParentCompany" name="ParentCompany"  checked="checked" onclick = "Check()">
                                                    <span class="custom-control-label">Parent Company</span>
                                                </label>
                                            </div>
                                            <div class="col-md-3 col-lg-3" id="ShowParentCompany">
                                                <div class="form-group">
                                                    <label class="form-label">Parent Company Name</label>
                                                    <select name="ParentCompanyUID" id="ParentCompanyUID" class="form-control select2" style="width:100%;">
                                                        <option></option>
                                                        <?php 
                                                        foreach ($ParentCompanyDetails as $row) { 
                                                          echo "<option value='".$row->CustomerUID."'>".$row->CustomerName."</option>";
                                                      }
                                                      ?>
                                                  </select>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pl-0">
                                            <h3 class="card-title pt-1">  Address Info</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">


                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Address Line 1</label>
                                                <input type="text" class="form-control" id="CustomerAddress1" name="CustomerAddress1">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Address Line 2</label>
                                                <input type="text" class="form-control" id="CustomerAddress2" name="CustomerAddress2">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Zipcode</label>
                                                <input type="text" class="form-control" id="CustomerZipCode" name="CustomerZipCode">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">City</label>
                                                <input type="text" class="form-control" id="CustomerCityUID" name="CustomerCityUID" >
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">County</label>
                                                <input type="text" class="form-control" id="CustomerCountyUID" name="CustomerCountyUID">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">State</label>
                                                <input type="text" class="form-control" id="CustomerStateUID" name="CustomerStateUID">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Contact Name</label>
                                                <input type="text" class="form-control" id="CustomerPContactName" name="CustomerPContactName">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Contact Mobile Number </label>
                                                <input type="text" class="form-control" id="CustomerPContactMobileNo" name="CustomerPContactMobileNo">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Client Office Number </label>
                                                <input type="text" class="form-control" id="CustomerOfficeNo" name="CustomerOfficeNo">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Client Fax Number </label>
                                                <input type="text" class="form-control" id="CustomerFaxNo" name="CustomerFaxNo">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Contact Email Id </label>
                                                <input type="email" class="form-control" id="CustomerPContactEmailID" name="CustomerPContactEmailID">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Acknowledgement Email Id </label>
                                                <input type="email" class="form-control" id="CustomerOrderAckEmailID" name="CustomerOrderAckEmailID">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pl-0">
                                            <h3 class="card-title pt-1">  Other Info</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Website</label>
                                                <input type="text" class="form-control" id="CustomerWebsite" name="CustomerWebsite">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Templates</label>
                                                <select id="DefaultTemplateUID" name="DefaultTemplateUID" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <?php 
                                                    foreach ($TemplateDetails as $row) { 
                                                      echo "<option value='".$row->TemplateUID."'>".$row->TemplateName."</option>";
                                                  }
                                                  ?>
                                              </select>
                                          </div>
                                      </div>

                                      <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">App Usage Cost</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                                </div>
                                                <input type="text" class="form-control money-dollar" id="AppUsageCost" name="AppUsageCost" placeholder="0.00">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Priority </label>
                                            <select id="PriorityUID" name="PriorityUID" class="form-control select2" style="width:100%;">
                                                <option></option>
                                                <?php 
                                                foreach ($Prioritys as $row) { 
                                                  echo "<option value='".$row->PriorityUID."'>".$row->PriorityName."</option>";
                                              }
                                              ?>
                                          </select>
                                      </div>
                                  </div>
                                  <div class="col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label">Group  </label>
                                        <select id="GroupUID" name="GroupUID" class="form-control select2 required" style="width:100%;">
                                            <option></option>
                                            <?php 
                                            foreach ($Groups as $row) { 
                    // echo "<option value='".$row->GroupUID."'>".$row->GroupName."</option>";

                                              if($row->DefaultGroup==1){

                                                echo "<option value='".$row->GroupUID."' selected>".$row->GroupName."</option>";
                                            }else{
                                                echo "<option value='".$row->GroupUID."'>".$row->GroupName."</option>";

                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-lg-3">
                                <label class="form-label">Document upload (pdf) </label>
                                <div class="custom-file form-group">
                                    <input type="file" class="custom-file-input form-control" id="customFile" >
                                    <label class="custom-file-label text-truncate form-control" for="customFile">Choose file</label>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3 mt-4 pt-1">
                                <a href="#" class="btn btn-outline-info btn-sm"><i class="fe fe-eye"></i> View Document</a>
                                <a href="#" class="btn btn-outline-danger btn-sm"><i class="fe fe-trash-2"></i></a>
                            </div>
                            <div class="col-md-3 col-lg-3 mt-4 pt-1">
                               <!-- Default dropup button -->
                               <div class="btn-group dropdown">
                                  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Tax Certificate
                                    <input type="hidden" id="TaxCertificateRequired" value="" name="TaxCertificateRequired">

                                </button>
                                <ul role="menu" class="dropdown-menu">

                                    <li><a class="btn display-block" value="Seperate Report" id="BtnYesVariance">Separate Page</a></li>
                                    <li><a class="btn display-block BtnNovariance" value="Part of Property Report" id="BtnNovariance">Part of Report</a></li>
                                    <li><a class="btn display-block" value="No Tax Certificate" id="BtnVariance" style="background-color:#edebb8">No Tax Info</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3 mt-2 pt-1">
                            <label class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" name="BundlePricing" id="BundlePricing" value="1" checked="true">
                                <span class="custom-control-label">Bundle Pricing</span>
                            </label>
                        </div>
                        <div class="col-md-3 col-lg-3 mt-2 pt-1">
                            <label class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" name="AdverseConditionsEnabled" id="AdverseConditionsEnabled" >
                                <span class="custom-control-label">Adverse Conditions Enabled</span>
                            </label>
                        </div>
                        <div class="col-md-3 col-lg-3 mt-2 pt-1">
                            <label class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" class="custom-control-input" name="AutoBilling" id="AutoBilling">
                                <span class="custom-control-label">Auto Billing</span>
                            </label>
                        </div>

                    </div>
                </div>
                <div class="card-header">
    <div class="col-md-12 col-lg-12 mb-3 mt-2">
        <div class="btn-list  text-right">
         <!--  <a href="#" class="btn btn-outline-secondary">Cancel</a> -->
         <!-- <button data-moveon="clientpricing" class="btn btn-primary btn-space wizard-btn wizard-next">Next <i class="fa fa-angle-double-right"></i></button> -->
         <button  data-moveon="clientpricing" class="btn btn-primary wizard-next">Next</button>
     </div>
 </div>
</div>
            </div>
        </div>

    </div>
    <div class="tab-pane fade" id="clientpricing">
        <?php $this->load->view('Clients/pricing'); ?>
    </div>
    <div class="tab-pane fade" id="clientproducts">
       <?php $this->load->view('Clients/products'); ?>
   </div>
   <div class="tab-pane fade" id="clientworkflows">
       <?php $this->load->view('Clients/workflows'); ?>
   </div>
   <div class="tab-pane fade" id="clientprioritytat">
       <?php $this->load->view('Clients/priority-tat'); ?>
   </div>
  
</div>





<!-- <div class="card-header">
    <div class="col-md-12 col-lg-12 mb-3 mt-2">
        <div class="btn-list  text-right">
          <a href="#" class="btn btn-outline-secondary">Cancel</a>
         <button data-moveon="clientpricing" class="btn btn-primary btn-space wizard-btn wizard-next">Next <i class="fa fa-angle-double-right"></i></button>
         <button  data-moveon="clientpricing" class="btn btn-primary wizard-next">Next</button>
     </div>
 </div>
</div> -->
</form>
</div>
</div>
</div>
</div>
</div>



<script type="text/javascript">

    $(document).on('keyup', '#CustomerName', function(e) {
      e.preventDefault();
      if($(this).val() != ""){
          $('.user_name_sec .name').text($(this).val());
          $('.user_icon .textavatar').attr('data-name', $(this).val());
          $('.user_icon .textavatar abbr').attr('title', $(this).val());
          var matches = $(this).val().match(/\b(\w)/g);
          if(matches.length == 2){
            $('.user_icon .textavatar abbr').text(matches[0]+''+matches[1]);
        }else if(matches.length == 1){
            $('.user_icon .textavatar abbr').text(matches[0]);
        }
    }
});

    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
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
    
  $(function () {
    "use strict";
    
    //Horizontal form basic
    $('#wizard_horizontal').steps({
        headerTag: 'h2',
        bodyTag: 'section',
        transitionEffect: 'slideLeft',
        onInit: function (event, currentIndex) {
            setButtonWavesEffect(event);
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
            setButtonWavesEffect(event);
        }
    });

});

  function setButtonWavesEffect(event) {
    $(event.currentTarget).find('[role="menu"] li a').removeClass('');
    $(event.currentTarget).find('[role="menu"] li:not(.disabled) a').addClass('');
}


$('#CustomerZipCode').change(function(event) {

  CustomerZipCode = $(this).val();

  if (CustomerZipCode) {
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>Clients/getzip',
      data: {
        'CustomerZipCode': CustomerZipCode
    },
    dataType: 'json',
    cache: false,
    success: function(data) {
        console.log(data);
        $('#CustomerCityUID').empty();
        $('#CustomerStateUID').empty();
        $('#CustomerCountyUID').empty();

        if (data != '') {

          $('#CustomerCityUID').val(data['CityName']);
          $('#CustomerStateUID').val(data['StateName']);
          $('#CustomerCountyUID').val(data['CountyName']);
          $('#CustomerStateUID').parent().addClass('is-dirty');
          $('#CustomerCityUID').parent().addClass('is-dirty');
          $('#CustomerCountyUID').parent().addClass('is-dirty');
      }

  },
  error: function(jqXHR, textStatus, errorThrown) {

    console.log(errorThrown);

},
failure: function(jqXHR, textStatus, errorThrown) {

    console.log(errorThrown);

},
});
} else {
    $('#CustomerCityUID').val('');
    $('#CustomerStateUID').val('');
    $('#CustomerCountyUID').val('');
    $('#CustomerStateUID').parent().removeClass('is-dirty');
    $('#CustomerCityUID').parent().removeClass('is-dirty');
    $('#CustomerCountyUID').parent().removeClass('is-dirty');
}

});


function validate_customerinfo(next_form, event) {
    var data = new FormData($('#cust_info')[0]);
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>Clients/customer_info_validate',
      data: data,
      dataType: 'json',
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function() {
        $('.wizard-previous').attr("disabled", true);
        $('.wizard-next').attr("disabled", true);
        $('.wizard-next').html('Please Wait <i class="fa fa-spinner fa-spin"></i>');
    },
    success: function(data) {
        $('.wizard-previous').attr("disabled", false);
        $('.wizard-next').attr("disabled", false);
        $('.wizard-next').html('Next Step <i class="fa fa-angle-double-right"></i>');
        if (data['validation_error'] == 0) {
          $(next_form).wizard("next");
      } else {
          $.gritter.add({
            title: data['message'],
            class_name: 'color danger',
            fade: true,
            time: 3000,
            speed: 'slow',
        });
          $.each(data, function(k, v) {
            $('#' + k).closest('div.is_error').addClass('is-invalid');
        });
      }
  }
});
}



function validate_customerproduct(next_form, event) {
    var data = new FormData($('#cust_info')[0]);
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>Clients/customer_product_validate',
      data: data,
      dataType: 'json',
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function() {
        $('.wizard-previous').attr("disabled", true);
        $('.wizard-next').attr("disabled", true);
        $('.wizard-next').html('Please Wait <i class="fa fa-spinner fa-spin"></i>');
    },
    success: function(data) {
        $('.wizard-previous').attr("disabled", false);
        $('.wizard-next').attr("disabled", false);
        $('.wizard-next').html('Next Step <i class="fa fa-angle-double-right"></i>');
        if (data['validation_error'] == 0) {
          $(next_form).wizard("next");
      } else {
          $.gritter.add({
            title: data['message'],
            class_name: 'color danger',
            fade: true,
            time: 3000,
            speed: 'slow',
        });
          $.each(data, function(k, v) {
            $('#' + k).closest('div.is_error').addClass('is-invalid');
        });
      }
  }
});
}
function validate_custtemplate(next_form, event) {
    var data = new FormData($('#cust_info')[0]);
    $.ajax({
      type: "POST",
      url: '<?php echo base_url(); ?>Clients/customer_template_validate',
      data: data,
      dataType: 'json',
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function() {
        $('.wizard-previous').attr("disabled", true);
        $('.wizard-next').attr("disabled", true);
        $('.wizard-next').html('Please Wait <i class="fa fa-spinner fa-spin"></i>');
    },
    success: function(data) {
        $('.wizard-previous').attr("disabled", false);
        $('.wizard-next').attr("disabled", false);
        $('.wizard-next').html('Next Step <i class="fa fa-angle-double-right"></i>');
        $('#finish').html('<i class="fa fa-check"></i> Complete');
        if (data['validation_error'] == 0) {
          $(next_form).wizard("next");
      } else {
          $.gritter.add({
            title: data['message'],
            class_name: 'color danger',
            fade: true,
            time: 3000,
            speed: 'slow',
        });
          $.each(data, function(k, v) {
            $('#' + k).closest('div.is_error').addClass('is-invalid');
        });
      }
  }
});
}

$(".wizard-ux").wizard(), $(".wizard-ux").on("changed.fu.wizard", function() {
  $(".bslider").slider();
}),

$(document).on('click', '.wizard-btn', function(a) {
  a.preventDefault();


  /* Act on the event */
  var tabID = $(this).attr('data-moveon');
  var required = false;
  var ClosestTab = $(this).closest('.tab-pane').attr('id');
  console.log(ClosestTab);
  $('#' + ClosestTab + ' .required').each(function(index, element) {
    if ($(element).is(':visible') && ($(element).val() == '' || $(element).val() == ' ' || $(element).val() == 'null')) {
      $(element).parents('.mdl-textfield').addClass('is-invalid');
      required = true;
  } else {
      $(element).parents('.mdl-textfield').removeClass('is-invalid').addClass('is-dirty');
  }
});
  if (required == true) {
    $.gritter.add({
      title: "Fill the required fields",
      class_name: 'color danger',
      fade: true,
      time: 1000,
      speed: 'fast',

  });
    return false;
} else if (required == false) {
    clearNavigationTabs();
    $('.tab-content .tab-pane#' + tabID + '').addClass('active');
    $('#frm_customer ul.nav-tabs-success li.' + tabID + '').addClass('active');
    $("html, body").animate({
      scrollTop: 0
  }, "slow");
}

});

$(".wizard-previous").click(function(a) {
    var b = $(this).data("wizard");
    $(b).wizard("previous"), a.preventDefault()
})

    

</script>
