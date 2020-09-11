
<!-- start body header -->
<div id="page_top" class="section-body">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Place Order</h1> 
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
        <h5 class="pb-1 inner-page-head">Bulk Order</h5>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 pl-0">
                                    <h3 class="card-title pt-3">Product Info</h3>
                                </div>

                                <div class="col-md-6 col-lg-6 text-right pr-0 pt-2">

                                    <a href="Order_entry" class="btn btn-info btn-sm">Single Order</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-4 col-lg-4">

                                <div class="form-group">
                                    <label class="form-label">Client</label>
                                    <select name="beast" class="form-control select2">
                                        <option></option>
                                        <option value="1">60245101 / Zions Bancorporation, N.A.</option>
                                        <option value="4">12345 / Test Customer</option>
                                        <option value="3">99903780 / Builder Finance Inc.</option>
                                        <option value="3">99903808 / Community Financial Credit Union</option>
                                        <option value="3">6025381 / First Atlantic Federal CU</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label">Product</label>
                                    <select name="beast" class="form-control select2">
                                        <option></option>
                                        <option value="1">Property Report</option>
                                        <option value="4">Flood Cert</option>
                                        <option value="3">Recording</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label">SubProduct</label>
                                    <select name="beast" class="form-control select2">
                                        <option></option>
                                        <option value="1">Property Report</option>
                                        <option value="4">Copies</option>
                                        <option value="3">EZ Prop</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="btn-list text-left pt-2">
                                    <a href="javascript:void(0);" class="btn btn-primary upload-image-btn">Upload Image(s)</a>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 upload-image-div" style="display:none;">
                                <input type="file" class="dropify">
                                <div class="table-responsive pt-3">
                                    <table class="table table-hover table-vcenter table-new" cellspacing="0" id="">
                                        <thead>
                                            <tr>
                                                <th>Document Name</th>
                                                <th>Uploaded DateTime</th>
                                                <th class="text-center" style="width:120px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>document.pdf</td>
                                                <td>09-10-2020 19:57:27</td>
                                                <td class="actions text-center">
                                                    <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>mortgage.pdf</td>
                                                <td>09-10-2020 19:57:27</td>
                                                <td class="actions text-center">
                                                    <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>info.pdf</td>
                                                <td>09-10-2020 19:57:27</td>
                                                <td class="actions text-center">
                                                    <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="file" class="dropify">

                                <div class="btn-list text-right pt-2">
                                    <a href="javascript:void(0);" class="btn btn-primary">Save</a>
                                    <a href="javascript:void(0);" class="btn btn-success btn-preview-success">Preview</a>
                                </div>
                            </div>
                            <div class="col-md-6 bulk-right">
                                <p>Please follow the below steps to upload Order.</p>
                                <p>Download the available Excel Format XLSX sheet.</p>
                                <p><a href="javascript:void(0);" type="button" class="btn btn-sm btn-info">Excel Format</a></p>
                                <p><span style="color:red;">*</span> Fill in your Order details into the downloaded XLSX.</p>
                                <p><span style="color:red;">*</span> Upload file size max 5MB</p>
                                <p><span style="color:red;">*</span> Upload back the XLSX.</p>
                            </div>
                            <div class="col-md-12 upload-preview" style="display:none;">

                                <ul class="nav nav-tabs b-none">
                                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#date-preview"> Data Preview</a></li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#file-preview">File Preview</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="date-preview">

                                        <div class="table-responsive pt-3">
                                            <table class="table table-hover table-vcenter table-new" cellspacing="0" id="">
                                                <thead>
                                                    <tr>
                                                        <th>Document Name</th>
                                                        <th>Uploaded DateTime</th>
                                                        <th class="text-center" style="width:120px">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>document.pdf</td>
                                                        <td>09-10-2020 19:57:27</td>
                                                        <td class="actions text-center">
                                                            <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>mortgage.pdf</td>
                                                        <td>09-10-2020 19:57:27</td>
                                                        <td class="actions text-center">
                                                            <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>info.pdf</td>
                                                        <td>09-10-2020 19:57:27</td>
                                                        <td class="actions text-center">
                                                            <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="file-preview">

                                        <div class="table-responsive pt-3">
                                            <table class="table table-hover table-vcenter table-new" cellspacing="0" id="">
                                                <thead>
                                                    <tr>
                                                        <th>Document Name</th>
                                                        <th>Uploaded DateTime</th>
                                                        <th class="text-center" style="width:120px">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>document.pdf</td>
                                                        <td>09-10-2020 19:57:27</td>
                                                        <td class="actions text-center">
                                                            <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>mortgage.pdf</td>
                                                        <td>09-10-2020 19:57:27</td>
                                                        <td class="actions text-center">
                                                            <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>info.pdf</td>
                                                        <td>09-10-2020 19:57:27</td>
                                                        <td class="actions text-center">
                                                            <button class="btn btn-sm btn-icon text-danger" title="Delete"><i class="icon-trash"></i></button>
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
        </div>
    </div>
