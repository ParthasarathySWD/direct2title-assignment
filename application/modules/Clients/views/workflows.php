<!--BEGIN CONTENT-->
<!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/module/clients/style.css"> -->
<!-- start body header -->
<!-- <div id="page_top" class="section-body">
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
            <div class="col-md-12 col-lg-12"> -->
                <div class="card">

                    <div class="card">

                        <div class="card-header pt-3">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h3 class="card-title"> Workflows & Templates</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 col-lg-12">
                                <div class="row">

                                    <div class="col-sm-12 form-group"> 
                                        <div class="table-responsive"> 
                                            <table class="table table-vcenter table-new" cellspacing="0" id="" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space: nowrap;">Product / Subproduct</th>
                                                        <th style="width:25%;">Workflows</th>
                                                        <th style="width:25%;">Optional Workflows</th>
                                                        <th style="width:25%;">Templates</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-primary">A-Assignment Report</span>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2" style="width:100%;" multiple="">
                                                                <option></option>
                                                                <option value="1">Assignments Report</option>
                                                                <option value="1">Assignments Report</option>
                                                            </select>
                                                        </td>
                                                        <td>

                                                            <select name="beast" class="form-control select2" style="width:100%;" multiple="">
                                                                <option></option>
                                                                <option value="1">Assignments Report</option>
                                                                <option value="1">Assignments Report</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">ISGN Property Report</option>
                                                                <option value="1">EZ Close Deed Report</option>
                                                                <option value="1">EZ Close Property Report </option>
                                                            </select>
                                                        </td>
                                                        <td style="white-space: nowrap;">
                                                            <i class="fe fe-clipboard font-20 text-primary mr-2"></i>
                                                            <i style="cursor:pointer;" title="Expand" data-toggle="collapse" data-target="#customer_open1" class="fe fe-minimize-2 font-20 text-success row_plus_collapse"></i>
                                                        </td>
                                                    </tr>
                                                    <tr class="tr1 expand-div accordian-body collapse hiddenRow" id="customer_open1">
                                                        <td colspan="5" style="border-top:0;">

                                                            <div class="row">
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">Service Type </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option></option>
                                                                            <option value="1">UPS Standard</option>
                                                                            <option value="1">UPS Worldwide Expedited</option>
                                                                            <option value="1">UPS Worldwide Express</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">Cost Center </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option></option>
                                                                            <option value="1">Cost Center</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">API Platform </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option></option>
                                                                            <option value="1">RealEC</option>
                                                                            <option value="1">Stewart</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">Case Sensitive </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option value="1">Upper Case</option>
                                                                            <option value="1">Title Case</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">Order Policy Type </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option></option>
                                                                            <option value="1">Short</option>
                                                                            <option value="1">Long</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-9 col-lg-9 mt-4 pt-1">
                                                                    <label class="custom-control custom-checkbox custom-control-inline mr-4">
                                                                        <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                                        <span class="custom-control-label">Auto Billing</span>
                                                                    </label>

                                                                    <label class="custom-control custom-checkbox custom-control-inline mr-4">
                                                                        <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                                        <span class="custom-control-label">Vendor Individual Contact</span>
                                                                    </label>
                                                                    
                                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                                        <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                                        <span class="custom-control-label">Lender Messaging</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-primary">A-Assignment Report</span>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2" style="width:100%;" multiple="">
                                                                <option></option>
                                                                <option value="1">Assignments Report</option>
                                                                <option value="1">Assignments Report</option>
                                                            </select>
                                                        </td>
                                                        <td>

                                                            <select name="beast" class="form-control select2" style="width:100%;" multiple="">
                                                                <option></option>
                                                                <option value="1">Assignments Report</option>
                                                                <option value="1">Assignments Report</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">ISGN Property Report</option>
                                                                <option value="1">EZ Close Deed Report</option>
                                                                <option value="1">EZ Close Property Report </option>
                                                            </select>
                                                        </td>
                                                        <td style="white-space: nowrap;">
                                                            <i class="fe fe-clipboard font-20 text-primary mr-2"></i>
                                                            <i style="cursor:pointer;" title="Expand" data-toggle="collapse" data-target="#customer_open2" class="fe fe-minimize-2 font-20 text-success row_plus_collapse"></i>
                                                        </td>
                                                    </tr>
                                                    <tr class=" tr2 expand-div accordian-body collapse" id="customer_open2">
                                                        <td colspan="5" style="border-top:0;">

                                                            <div class="row">
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">Service Type </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option></option>
                                                                            <option value="1">UPS Standard</option>
                                                                            <option value="1">UPS Worldwide Expedited</option>
                                                                            <option value="1">UPS Worldwide Express</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">Cost Center </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option></option>
                                                                            <option value="1">Cost Center</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">API Platform </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option></option>
                                                                            <option value="1">RealEC</option>
                                                                            <option value="1">Stewart</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">Case Sensitive </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option value="1">Upper Case</option>
                                                                            <option value="1">Title Case</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-lg-3">
                                                                    <div class="form-group">
                                                                        <label class="form-label">Order Policy Type </label>
                                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                                            <option></option>
                                                                            <option value="1">Short</option>
                                                                            <option value="1">Long</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-9 col-lg-9 mt-4 pt-1">
                                                                    <label class="custom-control custom-checkbox custom-control-inline mr-4">
                                                                        <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                                        <span class="custom-control-label">Auto Billing</span>
                                                                    </label>

                                                                    <label class="custom-control custom-checkbox custom-control-inline mr-4">
                                                                        <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                                        <span class="custom-control-label">Vendor Individual Contact</span>
                                                                    </label>
                                                                    
                                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                                        <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                                        <span class="custom-control-label">Lender Messaging</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card-header">
                            <div class="col-md-12 col-lg-12 mb-3 mt-2">
                                <div class="btn-list  text-right">
                                    <!-- <a href="#" class="btn btn-outline-secondary">Cancel</a>
                                    <a href="#" class="btn btn-primary">Save</a> -->
                                     <button type="button" data-moveon="clientproducts" class="btn btn-outline-secondary wizard-btn btn-space"><i class="fa fa-angle-double-left"></i> Previous</button>
                                    <button data-moveon="clientprioritytat" class="btn btn-primary wizard-btn wizard-next">Next <i class="fa fa-angle-double-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
           <!--  </div>
        </div>
    </div> -->
    <!-- Modal -->
    <script type="text/javascript">
        $('.expand-collapse-btn').off('click').on('click', function (e) {
          $(this).closest("tr").siblings("tr.expand-div").slideToggle('slow');
      });
        $('.pricing-edit-btn').off('click').on('click', function (e) {
          $('.pricing-edit-div').slideToggle('slow');
      });
        $('.btn_upload_toggle').off('click').on('click', function (e) {
          $('.multiplefile_upload').slideToggle('slow');
      });
        $(document).on('click', '.row_plus_collapse', function(e){
          $(this).closest('tr').next('tr').find('.hiddenRow').show();
          $(this).toggleClass("add_toogle_cust_icon");
          $(this).attr('title','Collapse');
      });
        $(document).on('click', '.add_toogle_cust_icon', function(e){
          $(this).attr('title','Expand');
      });
  </script>
