<!--BEGIN CONTENT-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/module/clients/style.css">
<!-- start body header -->
<div id="page_top" class="section-body bg-new-header-top">
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

                        <div class="card-header">
                            <div class="col-md-6 col-lg-6">
                                <h3 class="card-title pt-1">Contacts</h3>
                            </div>
                            <div class="col-md-6 col-lg-6 text-right contact-add-btn-div">
                                <a href="javascript:void(0)" type="button" class="btn btn-primary btn-sm mt-2 addnew-cnt-btn"><i class="fe fe-plus"></i> Add Contact
                                </a>
                            </div>
                        </div>
                        <div class="card-body pb-4 pt-3">

                            <div class="col-md-12 col-lg-12 contact-grid-div">
                                <table class="table table-vcenter table-new custom-datatable text-nowrap" cellspacing="0" id="" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Contact Name</th>
                                            <th>Contact Position</th>
                                            <th>Contact Type</th>
                                            <th>Primary</th>
                                            <th>Status</th>
                                            <th>Has Login</th>
                                            <th>Login ID</th>
                                            <th class="text-center" style="width:100px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Contact Name</td>
                                            <td>Accounting</td>
                                            <td>Accounting Department</td>
                                            <td>Yes</td>
                                            <td>Active</td>
                                            <td>Yes</td>
                                            <td>Login1</td>
                                            <td class="actions text-center">
                                                <a href="" class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div id="wizard_horizontal" class="mt-3 contact-wizard" style="display:none;">
                                <h2>Contact Details</h2>
                                <section>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">ContactType </label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">Accounting Department</option>
                                                    <option value="1">Closing/Settlement Department</option>
                                                    <option value="1">Confirmation</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Contact Position </label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">Accounting</option>
                                                    <option value="1">Vendor</option>
                                                    <option value="1">Administration</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">First Name </label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Last Name </label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Job Titile </label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Company Name</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">NMLS ID </label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">State License ID</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12 mt-3">
                                            <div class="form-group">
                                                <label class="custom-switch pr-4">
                                                    <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                    <span class="custom-switch-description">Active</span>
                                                    <span class="custom-switch-indicator"></span>
                                                </label>
                                                <label class="custom-switch pr-4">
                                                    <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                    <span class="custom-switch-description">Primary</span>
                                                    <span class="custom-switch-indicator"></span>
                                                </label>
                                                <label class="custom-switch pr-4">
                                                    <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                    <span class="custom-switch-description">Delivery</span>
                                                    <span class="custom-switch-indicator"></span>
                                                </label>
                                                <label class="custom-switch pr-4">
                                                    <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                    <span class="custom-switch-description">Require Login?</span>
                                                    <span class="custom-switch-indicator"></span>
                                                </label>
                                                <label class="custom-switch">
                                                    <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked="">
                                                    <span class="custom-switch-description">Contact Address Different than Company?</span>
                                                    <span class="custom-switch-indicator"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-lg-12">
                                            <h3 class="card-title mt-4 mb-3">Address Details</h3>
                                        </div>


                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Address Line 1</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Address Line 2</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">TimeZone </label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">EST</option>
                                                    <option value="1">CST</option>
                                                    <option value="1">MST</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Zipcode</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">State</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">City</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>

                                    </div>

                                </section>
                                <h2>Contact Methods</h2>
                                <section>

                                    <div class="col-md-12 col-lg-12 p-0">
                                        <table class="table table-vcenter table-new text-nowrap mb-1 table-borderless" cellspacing="0" id="" style="width:100%;">
                                            <thead>
                                                <tr style="border-bottom:1px solid #E8E9E9;">
                                                    <th>Method Name</th>
                                                    <th>Method Type</th>
                                                    <th>Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" name="" placeholder="">
                                                    </td>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Cell</option>
                                                            <option value="1">EDI</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="" placeholder="">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" name="" placeholder="">
                                                    </td>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Cell</option>
                                                            <option value="1">EDI</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" name="" placeholder="">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-12 col-lg-12 text-right p-0">
                                        <a class="add-another-cnt"><i class="fe fe-plus"></i> Add Another Contact</a>
                                    </div>
                                </section>
                                <h2>Content Delivery</h2>
                                <section>

                                    <div class="col-md-12 col-lg-12 p-0">
                                        <table class="table table-vcenter table-new text-nowrap mb-1 table-borderless" cellspacing="0" id="" style="width:100%;">
                                            <thead>
                                                <tr style="border-bottom:1px solid #E8E9E9;">
                                                    <th>Contact Method</th>
                                                    <th>Document Type Preferred</th>
                                                    <th>Secondary Contact Method</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Contact Method</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Document Type</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Secondary Contact Method</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Contact Method</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Document Type</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Secondary Contact Method</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-12 col-lg-12 text-right p-0">
                                        <a class="add-another-cnt"><i class="fe fe-plus"></i> Add Another Delivery</a>
                                    </div>
                                </section>
                            </div>
                        </div>


                        <div class="card-header">
                            <div class="col-md-12 col-lg-12 mb-3 mt-2">
                                <div class="btn-list  text-right">
                                    <a href="#" class="btn btn-outline-secondary">Cancel</a>
                                    <!-- <a href="#" class="btn btn-primary">Save</a> -->
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
        $('.addnew-cnt-btn').on('click', function(){
            $('.contact-wizard').show();
            $('.contact-grid-div').hide();
            $('.contact-add-btn-div').hide();
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
