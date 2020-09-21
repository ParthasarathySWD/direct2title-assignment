
<!-- start body header -->
<div id="page_top" class="section-body">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Templates</h1> 
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
                                <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-template" aria-expanded="true">Add Template</a></li>
                                <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="#audit-log" aria-expanded="true">Audit Log</a></li>
                            </ul>
                        </div>
                        <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="add-template">

                            <div class="card-header pb-0">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12 pl-0">
                                            <h3 class="card-title pt-2">Template Details</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <div class="row">

                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Template Code <sup style="color:red;">*</sup></label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Template Name <sup style="color:red;">*</sup></label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">

                                            <div class="form-group">
                                                <label class="form-label">Document Type</label>
                                                <select name="beast" class="form-control select2">
                                                    <option></option>
                                                    <option value="1">Affidavit of heirship</option>
                                                    <option value="4">Bargain and Sale Deed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Projects</label>
                                                <select name="beast" class="form-control select2">
                                                    <option></option>
                                                    <option value="1">Projects 1</option>
                                                    <option value="4">Projects 2</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label class="form-label">Product Name</label>
                                                <select name="beast" class="form-control select2" multiple="">
                                                    <option value="1">Property Report</option>
                                                    <option value="4">Flood Cert</option>
                                                    <option value="3">Recording</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="card-header">
                                <h3 class="card-title pt-2">Page Setup</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Top (mm) <sup style="color:red;">*</sup></label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Bottom (mm) <sup style="color:red;">*</sup></label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Left (mm) <sup style="color:red;">*</sup></label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Right (mm) <sup style="color:red;">*</sup></label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">First Margin Top (mm) <sup style="color:red;">*</sup></label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">First Margin Bottom (mm) <sup style="color:red;">*</sup></label>
                                            <input type="text" class="form-control" name="" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Page Size</label>
                                            <select name="beast" class="form-control select2">
                                                <option></option>
                                                <option value="1">A4</option>
                                                <option value="4">A3</option>
                                                <option value="3">Legal</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>


                                <div class="row">
                                    <div class="col-md-6 col-lg-6">
                                        <h3 class="card-title mb-3 pt-2 display-inline-block">Header</h3>
                                        <select class="form-control display-inline-block width-auto pt-1 pl-1 pb-1" style="font-size:12px;">
                                            <option>Select Page Number</option>
                                            <option>Top Of Page Left</option>
                                            <option>Top Of Page Right</option>
                                            <option>Top Of Page Center</option>
                                        </select>
                                        <textarea id="mymce1" name="content"></textarea>
                                        <h3 class="card-title mb-3 pt-4">Body</h3>
                                        <textarea id="mymce2" name="content"></textarea>
                                        <h3 class="card-title mb-3 pt-4 display-inline-block">Footer</h3>
                                        <select class="form-control display-inline-block width-auto pt-1 pl-1 pb-1" style="font-size:12px;">
                                            <option>Select Page Number</option>
                                            <option>Bottom Of Page Left</option>
                                            <option>Bottom Of Page Right</option>
                                            <option>Bottom Of Page Center</option>
                                        </select>
                                        <textarea id="mymce3" name="content"></textarea>
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <h3 class="card-title pt-2 mb-3">Sample Output</h3>

                                        <div class="form-group">
                                            <label class="form-label">Multiple Document Cateogry</label>
                                            <select name="beast" class="form-control select2">
                                                <option></option>
                                                <option value="1">1</option>
                                                <option value="4">2</option>
                                                <option value="3">3</option>
                                                <option value="3">4</option>
                                                <option value="3">5</option>
                                            </select>
                                        </div>
                                        
                                        <iframe style="border:1px solid #eee;" id="iFramePDF" src="assets/pdf/preview.pdf" height="800" width="100%"></iframe>
                                    </div>

                                    <div class="col-md-12 col-lg-12 mb-3">
                                        <div class="btn-list  text-right">
                                            <a href="#" class="btn btn-secondary">Cancel</a>
                                            <a href="#" class="btn btn-primary">Save</a>
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
        height: 100,
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
    //Init tinymce 
    if($("#mymce2").length > 0){
      tinymce.init({
        selector: "textarea#mymce2",
        theme: "modern",
        height: 200,
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
    //Init tinymce 
    if($("#mymce3").length > 0){
      tinymce.init({
        selector: "textarea#mymce3",
        theme: "modern",
        height: 100,
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
