
<link rel="stylesheet" href="assets/css/module/orderinfo/orderinfo.css" rel="stylesheet" />
<!-- start body header -->
<div id="page_top" class="section-body" style="">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Order Summary</h1> 
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
                    <div class="card-header pb-0 pt-0 pl-3 pr-3 order-sum-header">
                        <table class="table table-vcenter text-nowrap table_custom mb-0 order-summary-head">
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="small text-muted">Order Number</div>
                                        <div>F19088674</div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Loan Number</div>
                                        <div>123456789</div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Product/SubProduct</div>
                                        <div>F-Life Of Loan</div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Property Address</div>
                                        <div>3133 LONGWOOD DRIVE, JACKSON, MS, 39212</div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Borrower Name</div>
                                        <div>WILLIAM</div>
                                    </td>
                                    <td>
                                        <span class="tag tag-cyan">New</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-header pb-0 pt-0 pl-3 pr-3 order-sum-tab">

                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#All" aria-expanded="true">Summary</a></li>
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#Images" aria-expanded="true">Order Info</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Video" aria-expanded="false">Reports</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#News" aria-expanded="false">Attachments</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#News" aria-expanded="false">Notes</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#News" aria-expanded="false">History</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card">
<!--                     <div class="card-header pb-0">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 col-lg-12 pl-0">
                                    <h3 class="card-title pt-2">Order Info</h3>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="card-body pt-2">
                        <div class="col-md-12 col-lg-12 add-product-div p-0">
                            <div class="row">
                                <div class="col-md-6 col-lg-6">

                                    <h3 class="card-title pt-2 mb-3">Template List</h3>

                                    <div class="form-group">
                                        <select name="beast" class="form-control select2">
                                            <option>Select Template</option>
                                            <option value="1">Template List 1</option>
                                            <option value="1">Template List 2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-lg-3">
                                    <h3 class="card-title pt-2 mb-3">Fields Panel</h3>
                                    <div class="col-md-12 col-lg-12 p-0 pr-1 scroll_field field-panel-div">

                                        <div class="form-group">
                                            <label class="form-label">Book</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Page</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">DocumentNumber</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">RecordedDate</label>
                                            <div class="input-group">
                                                <input data-provide="datepicker" data-date-autoclose="true" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">DeedOfTrustDated</label>
                                            <div class="input-group">
                                                <input data-provide="datepicker" data-date-autoclose="true" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">TaxID</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">OriginalTrustee</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Loan Amount</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-dollar"></i></span>
                                                </div>
                                                <input type="text" class="form-control money-dollar" placeholder="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Comments</label>
                                            <textarea type="text" class="form-control" name="" placeholder="" rows="2"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">EndorserName</label>
                                            <select name="beast" class="form-control select2">
                                                <option></option>
                                                <option value="1">EndorserName</option>
                                                <option value="1">EndorserName</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">AssignorName</label>
                                            <select name="beast" class="form-control select2">
                                                <option></option>
                                                <option value="1">AssignorName</option>
                                                <option value="1">AssignorName</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">Municipality</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">AssigneeName</label>
                                            <select name="beast" class="form-control select2">
                                                <option></option>
                                                <option value="1">AssigneeName</option>
                                                <option value="1">AssigneeName</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">BorrowerName</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">LenderName</label>
                                            <select name="beast" class="form-control select2">
                                                <option></option>
                                                <option value="1">LenderName</option>
                                                <option value="1">LenderName</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">PropertyAddress1</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">PropertyAddress2</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">PropertyCountyName</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">PropertyCityName</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">PropertyStateCode</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">PropertyZipCode</label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-9 col-lg-9">

                                    <h3 class="card-title pt-2 mb-3">Preview Panel</h3>
                                    <textarea id="mymce1" name="content"></textarea>
                                    <div class="btn-list text-left mt-3">
                                        <a href="#" class="btn btn-info">Save</a>
                                        <a href="#" class="btn btn-secondary">Reset</a>
                                        <a href="#" class="btn btn-primary">Edit</a>
                                    </div>

                                    <div class="col-md-12 col-lg-12 mb-3 mt-0 p-0">
                                        <div class="btn-list text-right">
                                            <a href="#" class="btn btn-success">Complete</a>
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


    <script src="<?php echo base_url('assets/lib/tinymce/tinymce.min.js'); ?>"></script>
    <script type="text/javascript">

    //Init tinymce 
    if($("#mymce1").length > 0){
      tinymce.init({
        selector: "textarea#mymce1",
        theme: "modern",
        height: 600,
        statusbar: true,
        branding: false,
        content_style: ".mce-content-body {font-size:11px;font-family:Arial;}",
        fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 24px 36px",
        lineheight_formats: " 3pt 4pt 5pt 6pt 7pt 8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
        plugins: [
        "advlist autolink lists link charmap preview anchor",
        "searchreplace visualblocks code fullscreen fullpage",
        "insertdatetime table contextmenu paste template jbimages",
        "pagebreak","textcolor","colorpicker", "textpattern","lineheight"
        ],
        toolbar: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | jbimages | lineheightselect ',
        pagebreak_separator: '<p style="page-break-after: always;"></p>',
        setup: function (editor) {        
        }
    });
  }
</script>
