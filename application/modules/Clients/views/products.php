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


                        <div class="card-header pt-3">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <h3 class="card-title">Client Products</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 col-lg-12">
                                <div class="row">

                                    <div class="col-sm-12 form-group"> 
                                        <table class="table table-vcenter table-new text-nowrap" cellspacing="0" id="" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th style="width:25%;">Product Name</th>
                                                    <th style="width:60%;">SubProduct Name</th>
                                                    <th class="text-center">Is Api</th>
                                                    <th class="text-center action-icon"><i class="fe fe-plus-circle text-primary"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Assignments</option>
                                                        </select>
                                                    </td>
                                                    <td>

                                                        <select name="beast" class="form-control select2" style="width:100%;" multiple="">
                                                            <option></option>
                                                            <option value="1">Assignments Report</option>
                                                            <option value="1">Assignments Report</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <label class="custom-control custom-checkbox custom-control-inline mr-0">
                                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                            <span class="custom-control-label"></span>
                                                        </label>
                                                    </td>
                                                    <td class="text-center action-icon">
                                                        <i class="fe fe-minus-circle text-danger"></i>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select name="beast" class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Assignments</option>
                                                        </select>
                                                    </td>
                                                    <td>

                                                        <select name="beast" class="form-control select2" style="width:100%;" multiple="">
                                                            <option></option>
                                                            <option value="1">Assignments Report</option>
                                                            <option value="1">Assignments Report</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <label class="custom-control custom-checkbox custom-control-inline mr-0">
                                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                            <span class="custom-control-label"></span>
                                                        </label>
                                                    </td>
                                                    <td class="text-center action-icon">
                                                        <i class="fe fe-minus-circle text-danger"></i>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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
        $('.pricing-edit-btn').off('click').on('click', function (e) {
          $('.pricing-edit-div').slideToggle('slow');
      });
        $('.btn_upload_toggle').off('click').on('click', function (e) {
          $('.multiplefile_upload').slideToggle('slow');
      });
  </script>
