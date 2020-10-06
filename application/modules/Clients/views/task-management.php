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
                                        <h3 class="card-title"> Task Management</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 col-lg-12">
                                <div class="row">

<!--                                     <div class="col-md-12 col-lg-12">
                                        <div class="btn-list mb-2">
                                            <a href="#" class="btn btn-primary btn-sm">Assignment</a>
                                            <a href="#" class="btn btn-secondary btn-sm">Property Report</a>
                                        </div>
                                    </div> -->
                                    <div class="col-md-12 col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table table-vcenter table-new text-nowrap" cellspacing="0" id="" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </th>
                                                        <th>Task</th>
                                                        <th>Auto Creation</th>
                                                        <th style="width:30%;">Previous Task</th>
                                                        <th style="width:20%;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>OrderAssignment</td>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">  OrderAssignment</option>
                                                                <option value="1">OrderRe-assign</option>
                                                                <option value="1">AbstractorFollow-up</option>
                                                                <option value="1">SearchReceived</option>
                                                                <option value="1">Miscellaneous1</option>
                                                                <option value="1">TaxCertificationRequired</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">Order Open</option>
                                                                <option value="1">Task Open</option>
                                                                <option value="1">Task Complete</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>OrderRe-assign</td>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">  OrderAssignment</option>
                                                                <option value="1">OrderRe-assign</option>
                                                                <option value="1">AbstractorFollow-up</option>
                                                                <option value="1">SearchReceived</option>
                                                                <option value="1">Miscellaneous1</option>
                                                                <option value="1">TaxCertificationRequired</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">Order Open</option>
                                                                <option value="1">Task Open</option>
                                                                <option value="1">Task Complete</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>AbstractorFollow-up</td>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">  OrderAssignment</option>
                                                                <option value="1">OrderRe-assign</option>
                                                                <option value="1">AbstractorFollow-up</option>
                                                                <option value="1">SearchReceived</option>
                                                                <option value="1">Miscellaneous1</option>
                                                                <option value="1">TaxCertificationRequired</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">Order Open</option>
                                                                <option value="1">Task Open</option>
                                                                <option value="1">Task Complete</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>SearchReceived</td>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">  OrderAssignment</option>
                                                                <option value="1">OrderRe-assign</option>
                                                                <option value="1">AbstractorFollow-up</option>
                                                                <option value="1">SearchReceived</option>
                                                                <option value="1">Miscellaneous1</option>
                                                                <option value="1">TaxCertificationRequired</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">Order Open</option>
                                                                <option value="1">Task Open</option>
                                                                <option value="1">Task Complete</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>TaxCertificationRequired</td>
                                                        <td>
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option" checked>
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">  OrderAssignment</option>
                                                                <option value="1">OrderRe-assign</option>
                                                                <option value="1">AbstractorFollow-up</option>
                                                                <option value="1">SearchReceived</option>
                                                                <option value="1">Miscellaneous1</option>
                                                                <option value="1">TaxCertificationRequired</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="beast" class="form-control select2">
                                                                <option></option>
                                                                <option value="1">Order Open</option>
                                                                <option value="1">Task Open</option>
                                                                <option value="1">Task Complete</option>
                                                            </select>
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
        $(document).on('click', '.row_plus_collapse', function(e){
          $(this).closest('tr').next('tr').find('.hiddenRow').show();
          $(this).toggleClass("add_toogle_cust_icon");
          $(this).attr('title','Collapse');
      });
        $(document).on('click', '.add_toogle_cust_icon', function(e){
          $(this).attr('title','Expand');
      });
  </script>
