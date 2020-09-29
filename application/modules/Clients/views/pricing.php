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

                        <div class="card-header">
                            <div class="col-md-12 col-lg-12">
                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <h3 class="card-title pt-2">Client Pricing</h3>
                                    </div>
                                    <div class="col-md-6 col-lg-6 text-right">
                                        <a href="javascript:void(0)" type="button" class="btn btn-primary btn-sm mt-1"><i class="fe fe-plus"></i>  View Pricing
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pb-0 pt-3">

                            <div class="col-md-12 col-lg-12">
                                <div class="row">

                                    <div class="col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label">Client Pricing</label>
                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                <option></option>
                                                <option value="1">AXIS100</option>
                                                <option value="1">Bank of Africa</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-lg-2">
                                        <div class="form-group mt-3">

                                            <a href="javascript:void(0)" type="button" class="btn btn-info btn-sm mt-2 pricing-edit-btn"> Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 p-0 pricing-edit-div" style="display:none;">
                            <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <h3 class="card-title pt-0">  Edit Pricing</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12">
                                    <div class="row">

                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Pricing Name </label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Pricing Type</label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">Customer</option>
                                                    <option value="1">Vendor</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2">
                                            <div class="form-group mt-3">

                                                <a href="javascript:void(0)" type="button" class="btn btn-success btn-sm mt-2 addnew-cnt-btn"> Update
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <h3 class="card-title pt-0"> Customer Pricing Product</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12">
                                    <div class="row">
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Copy From</label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">AXIS100</option>
                                                    <option value="1">Bank of Africa</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-lg-2">
                                            <div class="form-group mt-3">

                                                <a href="javascript:void(0)" type="button" class="btn btn-success btn-sm mt-2 addnew-cnt-btn"> Copy
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">State</label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">Alaska</option>
                                                    <option value="1">Alabama</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">County</label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">ALEUTIAN ISLANDS</option>
                                                    <option value="1">ANCHORAGE</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Product</label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">Assignments</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Sub Product</label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">Migration Assignment</option>
                                                    <option value="1">Assignment Report</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Vendor Type</label>
                                                <select name="beast" class="form-control select2" style="width:100%;">
                                                    <option></option>
                                                    <option value="1">Abstractor</option>
                                                    <option value="1">Attorney</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-5 col-lg-5">
                                            <div class="row">
                                                <div class="col-md-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Pricing </label>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Dual Closing </label>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control" placeholder="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">CancellationFee </label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-lg-1 mt-4 pt-1">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                <span class="custom-control-label">Quote</span>
                                            </label>
                                        </div>

                                        <div class="col-sm-12">
                                          <div class="row">
                                            <div class="col-sm-6 form-group">
                                             <a href="javascript:void()" class="btn btn-info btn-sm btn_upload_toggle" style="margin-top: 10px"><i class="fa fa-download"></i> Pricing Import</a>
                                             <a href="javascript:void()" class="btn btn-secondary btn-sm" style="margin-top: 10px"><i class="fa fa-upload"></i> Pricing Export</a>
                                         </div>

                                         <div class="col-sm-6 form-group text-right" style="min-height: 40px;">

                                             <a href="javascript:void()" class="btn btn-success btn-sm " style="margin-top: 10px"><i class="fa fa-save"></i> Add Pricing</a>

                                         </div>
                                     </div>
                                 </div>

                                 <div class="col-sm-12 multiplefile_upload" style="display:none;">
                                     <div class="row">
                                       <div class="col-sm-12 form-group">
                                        <input type="file" id="multiplefileupload"  class="dropify" name="multiplefileupload">
                                    </div>
                                    <div class="col-md-12 text-right mb-2" style="margin-top:0px;">                 
                                        <button type="button" class="btn btn-info btn-sm" id="">Preview</button>
                                        <button type="button" class="btn btn-success btn-sm" id="">Upload</button>
                                    </div>

                                    <div class="col-md-12 col-lg-12 mt-2 mb-3">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-6">
                                                <h3 class="card-title">Import Data Preview</h3>
                                            </div>
                                            <div class="col-md-6 col-lg-6 text-right"> 
                                                <span class="badge badge-primary">Pricing</span>
                                                <span class="badge badge-secondary">SubProduct</span>
                                                <span class="badge badge-info">County</span>
                                                <span class="badge badge-secondary">State</span>
                                                <span class="badge badge-danger"><i class="fe fe-x"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 form-group"> 
                                        <table class="table table-vcenter table-new text-nowrap" cellspacing="0" id="" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th>Pricing ID</th>
                                                    <th>State</th>
                                                    <th>County</th>
                                                    <th>Sub Product</th>
                                                    <th>Pricing</th>
                                                    <th>Cancellation Fee</th>
                                                    <th>Vendor Type</th>
                                                    <th>Dual Closing</th>
                                                    <th>Quote</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Test pricing</td>
                                                    <td>Alaska</td>
                                                    <td>ALEUTIAN ISLANDS</td>
                                                    <td>Property Report</td>
                                                    <td>$105.00</td>
                                                    <td>$0.00</td>
                                                    <td>Attorney</td>
                                                    <td>$0.00</td>
                                                    <td>No</td>
                                                </tr>
                                                <tr>
                                                    <td>Test pricing</td>
                                                    <td>Arkansas</td>
                                                    <td>ALEUTIAN ISLANDS</td>
                                                    <td>Property Report</td>
                                                    <td>$105.00</td>
                                                    <td>$0.00</td>
                                                    <td>Notary</td>
                                                    <td>$0.00</td>
                                                    <td>No</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-header pb-0 pt-0">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <h3 class="card-title pt-0">Customer Pricing List</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12 col-lg-12">
                        <div class="row">

                            <div class="col-sm-12 form-group"> 
                                <table class="table table-vcenter table-new text-nowrap custom-datatable" cellspacing="0" id="" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Pricing ID</th>
                                            <th>State</th>
                                            <th>County</th>
                                            <th>Sub Product</th>
                                            <th>Pricing</th>
                                            <th>Cancellation Fee</th>
                                            <th>Vendor Type</th>
                                            <th>Dual Closing</th>
                                            <th>Quote</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Test pricing</td>
                                            <td>Alaska</td>
                                            <td>ALEUTIAN ISLANDS</td>
                                            <td>Property Report</td>
                                            <td>$105.00</td>
                                            <td>$0.00</td>
                                            <td>Attorney</td>
                                            <td>$0.00</td>
                                            <td>No</td>
                                            <td class="actions text-center">
                                                <a href="" class="btn btn-sm p-1 font-17 btn-icon button-edit text-primary" title="Edit"><i class="fe fe-edit"></i>
                                                </a>
                                                <a href="" class="btn btn-sm p-1 font-17 btn-icon button-edit text-danger" title="Delete"><i class="fe fe-trash-2"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Test pricing</td>
                                            <td>Arkansas</td>
                                            <td>ALEUTIAN ISLANDS</td>
                                            <td>Property Report</td>
                                            <td>$105.00</td>
                                            <td>$0.00</td>
                                            <td>Notary</td>
                                            <td>$0.00</td>
                                            <td>No</td>
                                            <td class="actions text-center">
                                                <a href="" class="btn btn-sm p-1 font-17 btn-icon button-edit text-primary" title="Edit"><i class="fe fe-edit"></i>
                                                </a>
                                                <a href="" class="btn btn-sm p-1 font-17 btn-icon button-edit text-danger" title="Delete"><i class="fe fe-trash-2"></i>
                                                </a>
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
    $('.pricing-edit-btn').off('click').on('click', function (e) {
      $('.pricing-edit-div').slideToggle('slow');
  });
    $('.btn_upload_toggle').off('click').on('click', function (e) {
      $('.multiplefile_upload').slideToggle('slow');
  });
</script>
