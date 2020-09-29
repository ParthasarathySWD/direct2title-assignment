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

                    <?php $this->load->view('Clients/client-top-header-menu'); ?>

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
                                                <input type="text" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Client Name </label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Account Number</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">#</span>
                                                </div>
                                                <input type="text" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-lg-3 mt-4 pt-1">
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                            <span class="custom-control-label">Parent Company</span>
                                        </label>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Parent Company Name</label>
                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                <option></option>
                                                <option value="1">Parent Company Name</option>
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
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Address Line 2</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Zipcode</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">City</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">County</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">State</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Contact Name</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Contact Mobile Number </label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Client Office Number </label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Client Fax Number </label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Contact Email Id </label>
                                            <input type="email" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Acknowledgement Email Id </label>
                                            <input type="email" class="form-control" name="" placeholder="">
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
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Templates</label>
                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                <option></option>
                                                <option value="1">Template</option>
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
                                                <input type="text" class="form-control money-dollar" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Priority </label>
                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                <option></option>
                                                <option value="1">Rush</option>
                                                <option value="1">ASAP</option>
                                                <option value="1">Normal</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Group  </label>
                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                <option></option>
                                                <option value="1">Group </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-lg-3">
                                        <label class="form-label">Document upload (pdf) </label>
                                        <div class="custom-file form-group">
                                            <input type="file" class="custom-file-input form-control" id="customFile">
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
                                    <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                    <span class="custom-control-label">Bundle Pricing</span>
                                </label>
                            </div>
                            <div class="col-md-3 col-lg-3 mt-2 pt-1">
                                <label class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" >
                                    <span class="custom-control-label">Adverse Conditions Enabled</span>
                                </label>
                            </div>
                            <div class="col-md-3 col-lg-3 mt-2 pt-1">
                                <label class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                    <span class="custom-control-label">Auto Billing</span>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="card-header">
                    <div class="col-md-12 col-lg-12 mb-3 mt-2">
                        <div class="btn-list  text-right">
                            <a href="#" class="btn btn-outline-secondary">Cancel</a>
                            <a href="#" class="btn btn-primary">Save</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Modal -->


<script type="text/javascript">

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
    

    //Init tinymce 
    if($("#mymce1").length > 0){
      tinymce.init({
        selector: "textarea#mymce1",
        theme: "modern",
        height: 400,
        statusbar: true,
        branding: false,
        content_style: ".mce-content-body {font-size:11px;font-family:Arial;}",
        fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 24px 36px",
        lineheight_formats: " 3pt 4pt 5pt 6pt 7pt 8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
        plugins: [
        "advlist autolink lists link charmap preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime table contextmenu paste template jbimages",
        "pagebreak","textcolor","colorpicker", "textpattern","lineheight"
        ],
        toolbar: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | table | jbimages',
        pagebreak_separator: '<span style="page-break-after: always;"></span>',
        setup: function (editor) {        
        }
    });
  }
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
</script>
