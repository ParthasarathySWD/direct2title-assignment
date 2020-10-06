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
                                        <h3 class="card-title"> Audit Logs</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 col-lg-12">
                                <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <table class="table table-vcenter table-new custom-datatable text-nowrap" cellspacing="0" id="" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Module Name</th>
                                                <th>Activity</th>
                                                <th>DateTime</th>
                                                <th style="width:50px">User</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Client</td>
                                                <td>Sub Product: Refinance is Removed</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>Technical User 1</td>
                                            </tr>
                                            <tr>
                                                <td>Client</td>
                                                <td>Sub Product: Refinance is Removed</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>Technical User 1</td>
                                            </tr>
                                            <tr>
                                                <td>Client</td>
                                                <td>Sub Product: Refinance is Removed</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>Technical User 1</td>
                                            </tr>
                                            <tr>
                                                <td>Client</td>
                                                <td>Sub Product: Refinance is Removed</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>Technical User 1</td>
                                            </tr>
                                            <tr>
                                                <td>Client</td>
                                                <td>Sub Product: Refinance is Removed</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>Technical User 1</td>
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
