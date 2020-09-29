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
                                        <h3 class="card-title"> Priorities & TAT</h3>
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
                                                        <th>Product / Subproduct</th>
                                                        <th colspan="7">Priorities & TAT</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-primary">A-Assignment Report</span>
                                                        </td>
                                                        <td style="width:12.5%;min-width:80px;">
                                                            <div class="form-group">
                                                                <label class="form-label">Rush <sup class="text-danger">*</sup></label>
                                                                <input type="text" class="form-control" name="" placeholder="">
                                                            </div>
                                                        </td>
                                                        <td style="width:12.5%;">
                                                            <div class="form-group">
                                                                <label class="form-label">TAT</label>
                                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                                    <option></option>
                                                                    <option value="1">Standard SLA</option>
                                                                    <option value="1">SLA pre/post 3 PM</option>
                                                                    <option value="1">Skip Order Entry date</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td style="width:12.5%;min-width:80px;">
                                                            <div class="form-group">
                                                                <label class="form-label">ASAP <sup class="text-danger">*</sup></label>
                                                                <input type="text" class="form-control" name="" placeholder="">
                                                            </div>
                                                        </td>
                                                        <td style="width:12.5%;">
                                                            <div class="form-group">
                                                                <label class="form-label">TAT</label>
                                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                                    <option></option>
                                                                    <option value="1">Standard SLA</option>
                                                                    <option value="1">SLA pre/post 3 PM</option>
                                                                    <option value="1">Skip Order Entry date</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td style="width:12.5%;min-width:80px;">
                                                            <div class="form-group">
                                                                <label class="form-label">Normal <sup class="text-danger">*</sup></label>
                                                                <input type="text" class="form-control" name="" placeholder="">
                                                            </div>
                                                        </td>
                                                        <td style="width:12.5%;">
                                                            <div class="form-group">
                                                                <label class="form-label">TAT</label>
                                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                                    <option></option>
                                                                    <option value="1">Standard SLA</option>
                                                                    <option value="1">SLA pre/post 3 PM</option>
                                                                    <option value="1">Skip Order Entry date</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td style="white-space: nowrap; width:12.5%;">
                                                            <i class="fe fe-refresh-ccw font-20 text-primary mr-2"></i>
                                                            <div class="form-group" style="display: inline-block;width: 90px;">
                                                                <label class="form-label">Priority </label>
                                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                                    <option></option>
                                                                    <option value="1">Rush</option>
                                                                    <option value="1">ASAP</option>
                                                                    <option value="1">Normal</option>
                                                                </select>
                                                            </div>
                                                            <i  class="fe fe-clipboard font-20 text-success"></i>
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
