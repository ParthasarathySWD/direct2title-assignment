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


                        <div class="card-header pt-3">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h3 class="card-title"> Billing</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 col-lg-12">
                                <div class="row">
                                    <div class="col-sm-12 form-group"> 
                                        <div class="table-responsive"> 
                                            <table class="table table-vcenter table-new custom-datatable" cellspacing="0" id="" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Sub Product</th>
                                                        <th>State</th>
                                                        <th>County</th>
                                                        <th>City</th>
                                                        <th>Zip Code</th>
                                                        <th>Workflow</th>
                                                        <th>Billing Period</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Assignments</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Assignment Report</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Alaska</option>
                                                                <option value="1">Alabama</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">ALEUTIAN ISLANDS</option>
                                                                <option value="1">ALEUTIANS EAST</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">ATKA</option>
                                                                <option value="1">NIKOLSKI</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">99547</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Search</option>
                                                                <option value="1">Typing</option>
                                                                <option value="1">TaxCert</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Immediate</option>
                                                                <option value="1">1st of every month</option>
                                                                <option value="1">last day of the month</option>
                                                            </select>
                                                        </td>
                                                        <td style="width:50px;">
                                                            <i class="fe fe-trash-2 text-danger font-20"></i>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Assignments</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Assignment Report</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Alaska</option>
                                                                <option value="1">Alabama</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">ALEUTIAN ISLANDS</option>
                                                                <option value="1">ALEUTIANS EAST</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">ATKA</option>
                                                                <option value="1">NIKOLSKI</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">99547</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Search</option>
                                                                <option value="1">Typing</option>
                                                                <option value="1">TaxCert</option>
                                                            </select>
                                                        </td>
                                                        <td style="min-width:150px;">
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">Immediate</option>
                                                                <option value="1">1st of every month</option>
                                                                <option value="1">last day of the month</option>
                                                            </select>
                                                        </td>
                                                        <td style="width:50px;">
                                                            <i class="fe fe-plus-circle text-primary font-20"></i>
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
        $('.expand-collapse-btn').off('click').on('click', function (e) {
          $(this).closest("tr").siblings("tr.expand-div").slideToggle('slow');
      });
        $('.pricing-edit-btn').off('click').on('click', function (e) {
          $('.pricing-edit-div').slideToggle('slow');
      });
        $('.btn_upload_toggle').off('click').on('click', function (e) {
          $('.multiplefile_upload').slideToggle('slow');
      });
  </script>
