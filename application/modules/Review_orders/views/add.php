<!--BEGIN CONTENT-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/lib/fSelect-Selectall/fSelect.css">
<!-- start body header -->
<div id="page_top" class="section-body bg-new-header-top">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Sub Products</h1> 
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
                                <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-subproduct" aria-expanded="true">Add Sub Product</a></li>
                                <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="#audit-log" aria-expanded="true">Audit Log</a></li>
                            </ul>
                        </div>
                        <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="add-subproduct">
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Sub Product Code</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Sub Product Name <sup style="color:red;">*</sup></label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Product</label>
                                                <select name="beast" class="form-control select2">
                                                    <option></option>
                                                    <option value="1">Assignments</option>
                                                    <option value="1">Assignments</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Default Assignment Type</label>
                                                <select name="beast" class="form-control select2">
                                                    <option></option>
                                                    <option value="1">2 Owner search</option>
                                                    <option value="1">1 Owner with Full Value Deed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Default Priority</label>
                                                <select name="beast" class="form-control select2">
                                                    <option></option>
                                                    <option value="1">Rush</option>
                                                    <option value="1">ASAP</option>
                                                    <option value="1">Normal</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Report Heading</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Default Duration (Minutes)</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-8 mt-4 pt-1">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                <span class="custom-control-label">Task Management</span>
                                            </label>
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option2" >
                                                <span class="custom-control-label">Reverse Mortgage</span>
                                            </label>
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option3" >
                                                <span class="custom-control-label">Refinance</span>
                                            </label>
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option4" checked>
                                                <span class="custom-control-label">Active</span>
                                            </label>
                                        </div>

                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-12 col-lg-12">

                                        <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#task-mgnt" aria-expanded="true">Task Management</a></li>
                                            <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="#template-setup" aria-expanded="true">Template setup</a></li>
                                        </ul>

                                        <div class="tab-content mt-3">
                                            <div class="tab-pane fade active show" id="task-mgnt">

                                                <div class="col-md-12 col-lg-12">
                                                    <table class="table table-vcenter table-new custom-datatable text-nowrap" cellspacing="0" id="" style="width:100%;">
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
                                            <div class="tab-pane fade" id="template-setup">

                                                <div class="row m-0">

                                                    <div class="col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label class="form-label">Mapping Level</label>
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">SubProduct Level</option>
                                                                <option value="1">State Level</option>
                                                                <option value="1">County Level</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row m-0 edit-filed-level" style="display:none;">
                                                    <div class="col-md-3 col-lg-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Templates </label>
                                                            <select name="beast" class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                <option value="1">SubProduct Level</option>
                                                                <option value="1">State Level</option>
                                                                <option value="1">County Level</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 col-lg-3">
                                                        <div class="form-group">
                                                            <label class="form-label">Document Type</label>
                                                            <input type="text" class="form-control" name="" placeholder="">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <label class="form-label">Select Fields </label>
                                                                <select name="beast" class="form-control mdb-fselect" style="width:100%;" multiple="" placeholder="Select Fields">
                                                                    <option value="1">AssigneeName</option>
                                                                    <option value="1">AssignmentName</option>
                                                                    <option value="1">BorrowerName</option>
                                                                    <option value="1">LenderName</option>
                                                                    <option value="1">PropertyAddress1</option>
                                                                    <option value="1">Book</option>
                                                                    <option value="1">Page</option>
                                                                    <option value="1">RecordedDate</option>
                                                                    <option value="1">LoanAmount</option>
                                                                    <option value="1">ExecutionDate</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 col-lg-2 text-right">
                                                        <a style="cursor:pointer;" data-toggle="modal" data-target="#configure_field" class="btn btn-info btn-sm mt-4"> Configure Fields</a>
                                                    </div>
                                                    <div class="col-md-12 col-lg-12 text-right">
                                                        <a href="#." class="btn btn-success btn-sm mt-1 mb-4"> Update Template Mapping</a>
                                                    </div>
                                                </div>
                                                
                                                <div class="row m-0">
                                                    <div class="col-md-12 col-lg-12">
                                                        <table class="table table-vcenter table-new custom-datatable text-nowrap" cellspacing="0" id="" style="width:100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>SNo</th>
                                                                    <th>State</th>
                                                                    <th>County</th>
                                                                    <th>Template</th>
                                                                    <th>Document Type</th>
                                                                    <th style="width:50px" class="text-center">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>1</td>
                                                                    <td>MD</td>
                                                                    <td>BALTIMORE</td>
                                                                    <td>MD-121RE-BC</td>
                                                                    <td>Deed of Trust</td>
                                                                    <td class="actions text-center">
                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-icon edit-filed-level-btn button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                                        <button class="btn btn-sm btn-icon text-primary" title="Delete"><i class="fe fe-clipboard"></i></button>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>2</td>
                                                                    <td>MD</td>
                                                                    <td>BALTIMORE</td>
                                                                    <td>MD-121RE-BC</td>
                                                                    <td>Deed of Trust</td>
                                                                    <td class="actions text-center">
                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-icon edit-filed-level-btn button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                                        <button class="btn btn-sm btn-icon text-primary" title="Delete"><i class="fe fe-clipboard"></i></button>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>3</td>
                                                                    <td>MD</td>
                                                                    <td>BALTIMORE</td>
                                                                    <td>MD-121RE-BC</td>
                                                                    <td>Deed of Trust</td>
                                                                    <td class="actions text-center">
                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-icon edit-filed-level-btn button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                                        <button class="btn btn-sm btn-icon text-primary" title="Delete"><i class="fe fe-clipboard"></i></button>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>4</td>
                                                                    <td>MD</td>
                                                                    <td>BALTIMORE</td>
                                                                    <td>MD-121RE-BC</td>
                                                                    <td>Deed of Trust</td>
                                                                    <td class="actions text-center">
                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-icon edit-filed-level-btn button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                                        <button class="btn btn-sm btn-icon text-primary" title="Delete"><i class="fe fe-clipboard"></i></button>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>5</td>
                                                                    <td>MD</td>
                                                                    <td>BALTIMORE</td>
                                                                    <td>MD-121RE-BC</td>
                                                                    <td>Deed of Trust</td>
                                                                    <td class="actions text-center">
                                                                        <a href="javascript:void(0);" class="btn btn-sm btn-icon edit-filed-level-btn button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                                                        <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                                        <button class="btn btn-sm btn-icon text-primary" title="Delete"><i class="fe fe-clipboard"></i></button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>


                            </div>

                        </div>
                        <div class="tab-pane fade" id="audit-log">

                            <div class="card-body pb-4 pt-3">

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
                                                <td>EditTemplate</td>
                                                <td>Template Edit Active to In Active</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>James</td>
                                            </tr>
                                            <tr>
                                                <td>EditTemplate</td>
                                                <td>Template Edit Active to In Active</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>James</td>
                                            </tr>
                                            <tr>
                                                <td>EditTemplate</td>
                                                <td>Template Edit Active to In Active</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>James</td>
                                            </tr>
                                            <tr>
                                                <td>EditTemplate</td>
                                                <td>Template Edit Active to In Active</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>James</td>
                                            </tr>
                                            <tr>
                                                <td>EditTemplate</td>
                                                <td>Template Edit Active to In Active</td>
                                                <td>2020-06-13 07:34:10</td>
                                                <td>James</td>
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
                                <a href="#" class="btn btn-secondary">Cancel</a>
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

<style type="text/css">
    .icon-set .fa-forward{
        color: #2e9549;
        font-size: 16px;
        padding:6px;
        cursor:pointer;
        margin-left: 10px;
    }
    .icon-set .fa-trash{
        color: #FF4858;
        font-size: 16px;
        padding:6px;
        cursor:pointer;
    }
    .scroll-field{
        max-height:420px;
        overflow:auto;
    }
    .field-add-btn{
        float: left;
        margin-left: 10px;
        margin-top: 28px;
    }
    .field-add-select{
        width:calc(100% - 60px);
        float:left;
    }
</style>
<div class="modal fade" id="configure_field" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Configure Fields</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <h3 class="card-title pt-2 mb-3">Map Fields</h3>
                        <div class="col-md-12 col-lg-12 p-0 pr-1 scroll_field field-panel-div">

                            <div class="form-group field-add-select">
                                <label class="form-label">Select Fields </label>
                                <select name="beast" class="form-control mdb-fselect" style="width:100%;" multiple="" placeholder="Select Fields">
                                    <option value="1">AssigneeName</option>
                                    <option value="1">AssignmentName</option>
                                    <option value="1">BorrowerName</option>
                                    <option value="1">LenderName</option>
                                    <option value="1">PropertyAddress1</option>
                                    <option value="1">Book</option>
                                    <option value="1">Page</option>
                                    <option value="1">RecordedDate</option>
                                    <option value="1">LoanAmount</option>
                                    <option value="1">ExecutionDate</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm field-add-btn">Add</button>
                            <h6 style="float:left;width:100%;">Fields</h6>
                            <div class="scroll-field col-md-12 p-0">

                                <table class="table table-vcenter table-new text-nowrap mb-0" cellspacing="0" id="" style="width:100%;">

                                    <tbody class="" style="">
                                        <tr>
                                            <td>BorrowerName</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>PropertyCountyName</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Book</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Page</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>RecordedDate</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>LoanAmount</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ExecutionDate</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>PropertyStateName</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>DocumentNumber</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>DeedOfTrustDated</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Book</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Page</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>RecordedDate</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>LoanAmount</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>ExecutionDate</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>PropertyStateName</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>DocumentNumber</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>DeedOfTrustDated</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Account</td>
                                            <td class="text-center icon-set">
                                                <i class="fa fa-trash"></i>
                                                <i class="fa fa-forward"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-8">

                        <h3 class="card-title pt-2 mb-3">Preview Panel</h3>
                        <textarea id="mymce1" name="content"></textarea>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info">Reset</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/lib/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?php echo base_url(); ?>assets/lib/fSelect-Selectall/fSelect.js"></script>
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
</script>
