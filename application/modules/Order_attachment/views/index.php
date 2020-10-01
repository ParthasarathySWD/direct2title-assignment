<style type="text/css">
    .nav-tabs.upload-tabs .nav-link {
        padding: 10px 0;
    }
</style>
<?php $this->load->view('workflowview/workflow_topheader'); ?>

<div class="section-body">
    <div class="container-fluid">
      <div class="row" style="margin:0 -15px;">
        <div class="col-md-12 col-lg-12 p-0">
          <div class="card m-0" style="border-radius:0;">
           <?php $this->load->view('workflowview/workflow_menu'); ?>

           <div class="card-options" style="position: absolute;right: 20px;top: 60px;z-index:1;">
              <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><span class="font-14 mr-2" style="position:relative;top:-2px;">View Fullscreen</span><i class="fe fe-maximize"></i></a>
          </div>
          <div class="card-body pt-2">
            <div class="col-md-12 col-lg-12">
                <div class="row">
                    <div class="col-md-12 col-lg-12">

                        <ul class="nav nav-tabs b-none upload-tabs">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#all-tab1">Documents </a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#setting-tab1">Uploads</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="all-tab1">

                                <div class="col-md-12 col-lg-12 mt-3 mb-4">

                                    <div class="table-responsive">
                                        <table class="table table-hover nowrap table-vcenter table-new" cellspacing="0" id="">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <label class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                                            <span class="custom-control-label"></span>
                                                        </label>
                                                    </th>
                                                    <th>#</th>
                                                    <th>Doc File Name</th>
                                                    <th>File Size</th>
                                                    <th>Type of Doc</th>
                                                    <th>Comments</th>
                                                    <th>Uploaded DateTime</th>
                                                    <th class="text-center" style="width:120px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <label class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                                            <span class="custom-control-label"></span>
                                                        </label>
                                                    </td>
                                                    <td>1</td>
                                                    <td>borrower.pdf</td>
                                                    <td>
                                                        150.00 kb
                                                    </td>
                                                    <td>
                                                        Reports
                                                    </td>
                                                    <td>
                                                        test
                                                    </td>
                                                    <td>09-10-2020 19:57:27</td>
                                                    <td class="actions text-center">
                                                        <button class="btn btn-sm btn-icon text-primary" title="Delete"><i class="fe fe-eye"></i></button>
                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="fe fe-download"></i></button>
                                                        <button class="btn btn-sm btn-icon text-success" title="Delete"><i class="fe fe-printer"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                                            <span class="custom-control-label"></span>
                                                        </label>
                                                    </td>
                                                    <td>2</td>
                                                    <td>borrower.pdf</td>
                                                    <td>
                                                        150.00 kb
                                                    </td>
                                                    <td>
                                                        Reports
                                                    </td>
                                                    <td>
                                                        test
                                                    </td>
                                                    <td>09-10-2020 19:57:27</td>
                                                    <td class="actions text-center">
                                                        <button class="btn btn-sm btn-icon text-primary" title="Delete"><i class="fe fe-eye"></i></button>
                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="fe fe-download"></i></button>
                                                        <button class="btn btn-sm btn-icon text-success" title="Delete"><i class="fe fe-printer"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                                            <span class="custom-control-label"></span>
                                                        </label>
                                                    </td>
                                                    <td>3</td>
                                                    <td>borrower.pdf</td>
                                                    <td>
                                                        150.00 kb
                                                    </td>
                                                    <td>
                                                        Reports
                                                    </td>
                                                    <td>
                                                        test
                                                    </td>
                                                    <td>09-10-2020 19:57:27</td>
                                                    <td class="actions text-center">
                                                        <button class="btn btn-sm btn-icon text-primary" title="Delete"><i class="fe fe-eye"></i></button>
                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="fe fe-download"></i></button>
                                                        <button class="btn btn-sm btn-icon text-success" title="Delete"><i class="fe fe-printer"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                                            <span class="custom-control-label"></span>
                                                        </label>
                                                    </td>
                                                    <td>4</td>
                                                    <td>borrower.pdf</td>
                                                    <td>
                                                        150.00 kb
                                                    </td>
                                                    <td>
                                                        Reports
                                                    </td>
                                                    <td>
                                                        test
                                                    </td>
                                                    <td>09-10-2020 19:57:27</td>
                                                    <td class="actions text-center">
                                                        <button class="btn btn-sm btn-icon text-primary" title="Delete"><i class="fe fe-eye"></i></button>
                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="fe fe-download"></i></button>
                                                        <button class="btn btn-sm btn-icon text-success" title="Delete"><i class="fe fe-printer"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="setting-tab1">
                                <div class="col-md-12 col-lg-12 mt-3 mb-4">
                                    <input type="file" class="dropify">
                                    <div class="table-responsive pt-3">
                                        <table class="table table-hover table-vcenter table-new" cellspacing="0" id="">
                                            <thead>
                                                <tr>
                                                    <th style="width:17%;">Doc File Name</th>
                                                    <th style="width:17%;">Doc Type</th>
                                                    <th style="width:17%;">Type of Doc</th>
                                                    <th style="width:17%;">Comments</th>
                                                    <th>Uploaded DateTime</th>
                                                    <th class="text-center" style="width:120px">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" id="" name="" placeholder="">
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Others</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Reports</option>
                                                            <option value="1">Search</option>
                                                            <option value="1">Final Reports</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="" name="" placeholder="">
                                                    </td>
                                                    <td>09-10-2020 19:57:27</td>
                                                    <td class="actions text-center">
                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" id="" name="" placeholder="">
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Others</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Reports</option>
                                                            <option value="1">Search</option>
                                                            <option value="1">Final Reports</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="" name="" placeholder="">
                                                    </td>
                                                    <td>09-10-2020 19:57:27</td>
                                                    <td class="actions text-center">
                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" id="" name="" placeholder="">
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Others</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-control select2" style="width:100%;">
                                                            <option></option>
                                                            <option value="1">Reports</option>
                                                            <option value="1">Search</option>
                                                            <option value="1">Final Reports</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" id="" name="" placeholder="">
                                                    </td>
                                                    <td>09-10-2020 19:57:27</td>
                                                    <td class="actions text-center">
                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12 col-lg-12 mb-2 mt-2">
                                        <div class="btn-list text-right">
                                            <a href="#" class="btn btn-primary btn-sm">Upload</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>
</div>
</div>

<script type="text/javascript">

</script>
