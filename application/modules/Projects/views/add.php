<link href="assets/css/module/project/project.css" rel="stylesheet">
<style type="text/css">
    .card-title {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.2;
    }
</style>
<!-- start body header -->
<div id="page_top" class="section-body bg-new-header-top">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">My Projects</h1> 
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

                    <div class="card-header">
                        <div class="col-md-12 col-lg-12 p-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-template" aria-expanded="true">Edit Project</a></li>
                                <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="#audit-log" aria-expanded="false">Audit Logs</a></li>
                            </ul>
                        </div>
                        <div class="card-options" style="position: absolute;right: 20px;top: 20px;z-index:1;">
                          <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><span class="font-14 mr-2" style="position:relative;top:-2px;">View Fullscreen</span><i class="fe fe-maximize"></i></a>
                      </div>
                  </div>
                  <form action="#" name="update_Template" id="update_Template" class="frmTemplate">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="add-template">
                            <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pl-0">
                                            <h3 class="card-title pt-2">Project Details</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">

                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Project Code <sup style="color:red;">*</sup></label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Project Name <sup style="color:red;">*</sup></label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label">Project Description <sup style="color:red;">*</sup></label>
                                                <textarea class="form-control input-xs" type="text" id="" name="Code" value="" rows="1"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Client <sup style="color:red;">*</sup></label>
                                                <select name="beast" class="form-control select2">
                                                    <option></option>
                                                    <option value="1">Zions Bancorporation, N.A.</option>
                                                    <option value="1">Builder Finance Inc.</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Product <sup style="color:red;">*</sup></label>
                                                <select name="beast" class="form-control select2">
                                                    <option></option>
                                                    <option value="1">Assignments</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3 col-lg-3 mt-4 pl-4">
                                            <label class="custom-switch">
                                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description">Is Active</span>
                                            </label>
                                        </div>


                                    </div>
                                </div>
                            </div>

                            <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pl-0">
                                            <h3 class="card-title pt-2">Return Address Details</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">

                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Return Address 1</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Return Address 2</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Return ZipCode</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Return City</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Return County</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Return State</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Return To Org</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pl-0">
                                            <h3 class="card-title pt-2">Prepared Address Details</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">

                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Prepared Address 1</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Prepared Address 2</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Prepared ZipCode</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Prepared City</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Prepared County</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Prepared State</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Prepared By Org</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Prepared By Phone No</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Assignment Code</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Barcode Type</label>
                                                <input class="form-control input-xs" type="text" id="" name="Code" value="">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pl-0">
                                            <h3 class="card-title pt-2">Extra Add Ons</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-inline-checkbox1" value="option1" checked="">
                                                <span class="custom-control-label">Is Default Project</span>
                                            </label>
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-inline-checkbox2" value="option2">
                                                <span class="custom-control-label">Is Upper Case</span>
                                            </label>
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-inline-checkbox3" value="option3">
                                                <span class="custom-control-label">Has Bar Code</span>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">

                                        <div class="col-md-12 col-lg-12 mb-3 mt-3">
                                            <div class="btn-list  text-right">
                                                <a href="Projects" class="btn btn-secondary">Cancel</a>
                                                <button  type="submit" class="btn btn-success" id="BtnUpdate" value="1">Update</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="tab-pane fade" id="audit-log">

                            <div class="card-body pb-4 pt-3">

                                <div class="col-md-12 col-lg-12">
                                    <table class="table table-vcenter table-new new-datatable1 text-nowrap" cellspacing="0" id="" style="width:100%;">
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
                                                <td>Edit Project</td>
                                                <td>Project Edit Active to In Active</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>James</td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
 $(document).ready(function(e) {
    $('.new-datatable1').DataTable({

    });
});
</script>

