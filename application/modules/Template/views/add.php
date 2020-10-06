
<!-- start body header -->
<div id="page_top" class="section-body bg-new-header-top">
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
<?php if ($Action == "EDIT"){ ?>
<div class="section-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">

                    <div class="card-header">
                        <div class="col-md-12 col-lg-12 p-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-template" aria-expanded="true">Edit Template</a></li>
                                <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="#audit-log" aria-expanded="true">Audit Log</a></li>
                            </ul>
                        </div>
                        <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>
                    <form action="#" name="update_Template" id="update_Template" class="frmTemplate">                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="add-template">
                        <?php foreach ($TemplateDetails as $row) { ?>
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
                                            <input type="hidden" name="TemplateUID" id="TemplateUID" value="<?php echo $row->TemplateUID; ?>"/>
                                            <input type="hidden" name="TemplatePath" id="TemplatePath" value="<?php echo $row->TemplatePath; ?>"/>
                                            <div class="form-group">
                                                <label class="form-label">Template Code <sup style="color:red;">*</sup></label>
                                                <input class="form-control mdl-textfield__input input-xs" type="text" id="Code" name="Code" value="<?php echo $row->TemplateCode; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Template Name <sup style="color:red;">*</sup></label>
                                                <input class="form-control mdl-textfield__input input-xs" type="text" id="TemplateName" name="Name" value="<?php echo $row->TemplateName; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Document Type</label>
                                                <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="DocumentTypeUID" name="DocumentTypeUID">
                                                    <option></option>
                                                    <?php
                                                    foreach ($DocumentTypes as $value) {
                                                        if ($row->DocumentTypeUID == $value->DocumentTypeUID) {
                                                            echo "<option value='".$value->DocumentTypeUID."' selected>".$value->DocumentTypeName."</option>";
                                                        } else {
                                                            echo "<option value='".$value->DocumentTypeUID."'>".$value->DocumentTypeName."</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Product Name</label>
                                                <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="ProductUID" name="ProductUID[]" multiple="">
                                                    <option></option>
                                                    <?php
                                                    $MultipleProduct = explode(',', $row->ProductUID);
                                                    foreach ($MultipleProduct as $Product) {
                                                        foreach ($ProductDetails as $value) {
                                                            if ($Product == $value->ProductUID) {
                                                                echo "<option value='".$value->ProductUID."' selected>".$value->ProductName."</option>";
                                                            } else {
                                                                echo "<option value='".$value->ProductUID."'>".$value->ProductName."</option>";
                                                            }
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                         <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Projects</label>
                                                <select class="form-control mdl-textfield__input input-xs ProjectUID f_multiselect mdl-select2 select2" multiple="multiple" placeholder="Select Project(s)" id="ProjectUID" name="ProjectUID[]" >
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php 
                            $DefaultMarginTop = 10;
                            $DefaultMarginBottom = 10;
                            $DefaultMarginLeft = 10;
                            $DefaultMarginRight = 10;
                            $DefaultFirstMarginTop = 10;
                            $DefaultFirstMarginBottom = 10;
                            $DefaultPageSize = 'Legal';
                            ?>
                            <div class="card-header">
                                <h3 class="card-title pt-2">Page Setup</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Top (mm) <sup style="color:red;">*</sup></label>
                                           <input class="form-control mdl-textfield__input input-xs" type="text" id="MarginTop" name="MarginTop" value="<?php echo $row->MarginTop; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Bottom (mm) <sup style="color:red;">*</sup></label>
                                          <input class="form-control mdl-textfield__input input-xs" type="text" id="MarginBottom" name="MarginBottom" value="<?php echo $row->MarginBottom; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Left (mm) <sup style="color:red;">*</sup></label>
                                          <input class="form-control mdl-textfield__input input-xs" type="text" id="MarginLeft" name="MarginLeft" value="<?php echo $row->MarginLeft; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Right (mm) <sup style="color:red;">*</sup></label>
                                           <input class="form-control mdl-textfield__input input-xs" type="text" id="MarginRight" name="MarginRight" value="<?php echo $row->MarginRight; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">First Margin Top (mm) <sup style="color:red;">*</sup></label>
                                           <input class="form-control mdl-textfield__input input-xs" type="text" id="FirstMarginTop" name="FirstMarginTop" value="<?php echo $row->FirstMarginTop; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">First Margin Bottom (mm) <sup style="color:red;">*</sup></label>
                                            <input class="form-control mdl-textfield__input input-xs" type="text" id="FirstMarginBottom" name="FirstMarginBottom" value="<?php echo $row->FirstMarginBottom; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Page Size</label>
                                            <select class="form-control  mdl-select2 select2 mdl-textfield__input input-xs" id="PageSize" name="PageSize" >
                                                <option></option>
                                                <?php
                                                foreach ($PageSizeArray as $ele) {
                                                    if ($ele==$row->PageSize) {?>

                                                        <option value="<?php echo $ele;?>" selected><?php echo $ele;?></option>
                                                    <?php } else {?>

                                                        <option value="<?php echo $ele;?>"><?php echo $ele;?></option>
                                                    <?php }
                                                } ?>
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
                                        <textarea id="txt-header" name="content"></textarea>
                                        <h3 class="card-title mb-3 pt-4">Body</h3>
                                        <textarea id="txt-body" name="content"></textarea>
                                        <h3 class="card-title mb-3 pt-4 display-inline-block">Footer</h3>
                                        <select class="form-control display-inline-block width-auto pt-1 pl-1 pb-1" style="font-size:12px;">
                                            <option>Select Page Number</option>
                                            <option>Bottom Of Page Left</option>
                                            <option>Bottom Of Page Right</option>
                                            <option>Bottom Of Page Center</option>
                                        </select>
                                        <textarea id="txt-footer" name="content"></textarea>
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
                                        
                                        <iframe style="border:1px solid #eee;" id="iFramePDF" src="" height="800" width="100%"></iframe>
                                    </div>
 <?php } ?>
                                    <div class="col-md-12 col-lg-12 mb-3">
                                        <div class="btn-list  text-right">
                                            <a href="<?php echo base_url(); ?>template" class="btn btn-secondary">Cancel</a>
                                            <button  type="submit" class="btn btn-success" id="BtnUpdate" value="1">Update</button>
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
<?php } elseif ($Action == "ADD") {?>
<div class="section-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">

                    <div class="card-header">
                        <div class="col-md-12 col-lg-12 p-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-template" aria-expanded="true">Add Template</a></li>
                               <!--  <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="#audit-log" aria-expanded="true">Audit Log</a></li> -->
                            </ul>
                        </div>
                        <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>
                    <form action="#" name="frm_template" id="frm_template" class="frmTemplate">
                        <input type="hidden" name="TemplateUID" id="TemplateUID" value="0"/>
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
                                              <input class="form-control mdl-textfield__input input-xs" type="text" id="Code" name="Code">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Template Name <sup style="color:red;">*</sup></label>
                                                <input class="form-control mdl-textfield__input input-xs" type="text" id="TemplateName" name="Name">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">

                                            <div class="form-group">
                                                <label class="form-label">Document Type</label>
                                                <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="DocumentTypeUID" name="DocumentTypeUID">
                                                    <option></option>
                                                    <?php

                                                    foreach ($DocumentTypes as $value) {
                                                        echo "<option value='".$value->DocumentTypeUID."'>".$value->DocumentTypeName."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Product Name</label>
                                                <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="ProductUID" name="ProductUID[]">
                                                    <option></option>
                                                    <?php
                                                    foreach ($ProductDetails as $row) {
                                                        echo "<option value='".$row->ProductUID."'>".$row->ProductName."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label">Projects</label>
                                                <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs ProjectUID f_multiselect" multiple="multiple" placeholder="Select Project(s)" id="ProjectUID" name="ProjectUID[]" >
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php 
                            $DefaultMarginTop = 10;
                            $DefaultMarginBottom = 10;
                            $DefaultMarginLeft = 10;
                            $DefaultMarginRight = 10;
                            $DefaultFirstMarginTop = 10;
                            $DefaultFirstMarginBottom = 10;
                            $DefaultPageSize = 'Legal';
                            ?>
                            <div class="card-header">
                                <h3 class="card-title pt-2">Page Setup</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Top (mm) <sup style="color:red;">*</sup></label>
                                            <input class="form-control mdl-textfield__input input-xs" type="text" id="MarginTop" name="MarginTop" value="<?php echo $DefaultMarginTop; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Bottom (mm) <sup style="color:red;">*</sup></label>
                                            <input class="form-control mdl-textfield__input input-xs" type="text" id="MarginBottom" name="MarginBottom" value="<?php echo $DefaultMarginBottom; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Left (mm) <sup style="color:red;">*</sup></label>
                                            <input class="form-control mdl-textfield__input input-xs" type="text" id="MarginLeft" name="MarginLeft" value="<?php echo $DefaultMarginLeft; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Margin Right (mm) <sup style="color:red;">*</sup></label>
                                           <input class="form-control mdl-textfield__input input-xs" type="text" id="MarginRight" name="MarginRight" value="<?php echo $DefaultMarginRight; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">First Margin Top (mm) <sup style="color:red;">*</sup></label>
                                           <input class="form-control mdl-textfield__input input-xs" type="text" id="FirstMarginTop" name="FirstMarginTop" value="<?php echo $DefaultFirstMarginTop; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">First Margin Bottom (mm) <sup style="color:red;">*</sup></label>
                                           <input class="form-control mdl-textfield__input input-xs" type="text" id="FirstMarginBottom" name="FirstMarginBottom" value="<?php echo $DefaultFirstMarginBottom; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label">Page Size</label>
                                            <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="PageSize" name="PageSize" >
                                                <?php foreach ($PageSizeArray as $ele) { 
                                                    if ($ele==$DefaultPageSize) {?>
                                                        <option value="<?php echo $ele;?>" selected><?php echo $ele;?></option>
                                                    <?php } else {?>
                                                        <option value="<?php echo $ele;?>"><?php echo $ele;?></option>
                                                    <?php } ?>
                                                <?php } ?>
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
                                        <textarea id="txt-header" name="content"></textarea>
                                        <h3 class="card-title mb-3 pt-4">Body</h3>
                                        <textarea id="txt-body" name="content"></textarea>
                                        <h3 class="card-title mb-3 pt-4 display-inline-block">Footer</h3>
                                        <select class="form-control display-inline-block width-auto pt-1 pl-1 pb-1" style="font-size:12px;">
                                            <option>Select Page Number</option>
                                            <option>Bottom Of Page Left</option>
                                            <option>Bottom Of Page Right</option>
                                            <option>Bottom Of Page Center</option>
                                        </select>
                                        <textarea id="txt-footer" name="content"></textarea>
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
                                        
                                        <iframe style="border:1px solid #eee;" id="iFramePDF" src="" height="800" width="100%"></iframe>
                                    </div>

                                    <div class="col-md-12 col-lg-12 mb-3">
                                        <div class="btn-list  text-right">
                                            <a href="<?php echo base_url(); ?>template" class="btn btn-secondary">Cancel</a>
                                           <!--  <a href="#" class="btn btn-primary">Save</a> -->
                                           <button type="submit" class="btn btn-primary pull-right" id="BtnSave" value="1">Save</button>
     
                                    </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                       <!--  <div class="tab-pane fade" id="audit-log">
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
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php  } ?>
<script src="<?php echo base_url('assets/lib/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/clipboard.min.js'); ?>"></script>

<script type="text/javascript">
$(document).ready(function(){
    //Init tinymce 
    // if($("#txt-header").length > 0){
      tinymce.init({
        selector: "textarea#txt-header",
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
                editor.on('keyup', function(e) {
                    console.log('Editor Body contents was modified. Contents: ' + editor.getContent());
                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
                editor.on('SetContent', function(e) {
                    console.log('Editor Body contents was modified. Contents: ' + editor.getContent());
                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
            }
    });
  // }
    //Init tinymce 
    // if($("#txt-body").length > 0){
      tinymce.init({
        selector: "textarea#txt-body",
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
                editor.on('keyup', function(e) {
                    console.log('Editor Body contents was modified. Contents: ' + editor.getContent());
                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
                editor.on('SetContent', function(e) {
                    console.log('Editor Body contents was modified. Contents: ' + editor.getContent());
                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
            }
    });
  // }
    //Init tinymce 
    // if($("#txt-footer").length > 0){
      tinymce.init({
        selector: "textarea#txt-footer",
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
                editor.on('keyup', function(e) {
                    console.log('Editor Footer contents was modified. Contents: ' + editor.getContent());

                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });

                editor.on('SetContent', function(e) {
                    console.log('Editor Footer contents was modified. Contents: ' + editor.getContent());

                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
            }
    });
  // }
})
    /*$('body').on('click','#PDFPreviewContent', function(event) {

        $('#TinyMCEContent').hide();
        $('#PDFContent').show();
        show_pdf_content();
        return false;

    });

    $('body').on('click','.ClosePDFContent', function(event) {

        $('#TinyMCEContent').show();
        $('#PDFContent').hide();
        return false;

    });


    function show_pdf_content(){
        var editorContent=tinymce.get('txt-body').getContent();

     $.ajax({
                url: '<?php echo base_url("template/GetPDFContent"); ?>',
                type: 'POST',
                data: {editorContent: editorContent},
                cache: false,
                beforeSend: function(){
                $('.spinnerclass').addClass('be-loading-active');
                },
                success: function(data){
                    $('.spinnerclass').removeClass('be-loading-active');
                    $('#iFramePDF').attr('src', "<?php echo base_url()?>"+data);
                }
            });
        }*/

        // select_mdl();
        // $('.select2').select2({
        //     theme: "bootstrap",
        // });



        // $(".f_multiselect:not(.singlemdbfselect)").each(function() {
        //     var placeholder = $(this).attr('placeholder');
        //     $(this).fSelect({
        //         placeholder: placeholder,
        //         numDisplayed: 1,
        //         overflowText: '{n} selected',
        //         showSearch: true
        //     });
        // });

        var clipboard = new ClipboardJS('.btn-copy');


        // Event Handler
        clipboard.on('success', function(e) {
            console.info('Action:', e.action);
            console.info('Text:', e.text);
            console.info('Trigger:', e.trigger);

            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            console.error('Action:', e.action);
            console.error('Trigger:', e.trigger);
        });

        var button = $('#BtnUpdate');
        var button1 = $('#BtnSave');
        // $(document).on('click','#BtnSave',function(e){

        $("#frm_template").on('submit',(function(e){
            e.preventDefault();

            var BodyContent=tinymce.get('txt-body').getContent();
            var HeaderContent=tinymce.get('txt-header').getContent();
            var FooterContent=tinymce.get('txt-footer').getContent();

            var formData = new FormData(this);

            formData.append("editorContent", BodyContent);
            formData.append("HeaderContent", HeaderContent);
            formData.append("FooterContent", FooterContent);

            $.ajax({
                url: '<?php echo base_url();?>template/save_template',
                async:false,
                type: "POST",
                data:  formData,
                dataType:'json',
                mimeType:"multipart/form-data",
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function(){
                    button1.attr("disabled", true);
                    button1.html('Loading ...');
                },
                success: function(data)
                {
                    if(data['validation_error'] == 0)
                    {
                        toastr["success"]("", data['message']);
                    }else{
                        toastr["error"]("", data['message']);
                        button1.html('Save');
                        button1.removeAttr("disabled");
                    }
                }
            });
            return false;
        }));

        $('#update_Template').submit(function()
        {
            var button = $('#BtnUpdate');
             // var formData=$('#update_Template').serialize();

             var BodyContent=tinymce.get('txt-body').getContent();
             var HeaderContent=tinymce.get('txt-header').getContent();
             var FooterContent=tinymce.get('txt-footer').getContent();

             var formData = new FormData(this);

             formData.append("editorContent", BodyContent);
             formData.append("HeaderContent", HeaderContent);
             formData.append("FooterContent", FooterContent);

             $.ajax({
                url: '<?php echo base_url();?>template/update_template',
                async:false,
                type: "POST",
                data:  formData,
                dataType:'json',
                mimeType:"multipart/form-data",
                contentType: false,
                cache: false,
                processData:false,
                beforeSend: function(){
                    button.attr("disabled", true);
                    button.html('Loading ...');
                },
                success: function(data)
                {
                    if(data['validation_error'] == 0)
                    {
                        toastr["success"]("", data['message']);
                    }else{
                        toastr["error"]("", data['message']);
                        button.html('Update');
                        button.removeAttr("disabled");
                    }
                }
             });
             return false;
            });

// Tiny Mce Content



        //#Variable init Region starts.
        var selectedfieldkeyword="", selectedcursor="", removefield="",selectedfieldname="";
        var OrderUID='<?php echo $OrderUID; ?>';
        //#Variable init Region Ends.

        //init Select2
        // select_mdl();
        // $('.select2').select2({
        //     theme: "bootstrap",
        // });



        tinymce.init({
            selector: "textarea#txt-body",
            theme: "modern",
            menubar:false,
            height: 400,
            statusbar: true,
            branding: false,
            content_style: ".mce-content-body {font-size:11px;font-family:Arial;}",
            fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 24px 36px",
            lineheight_formats: "3pt 4pt 5pt 6pt 7pt 8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
            plugins: [
            "advlist autolink lists link charmap preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime table contextmenu paste template jbimages",
            "pagebreak","textcolor","colorpicker", "textpattern","lineheight"
            ],
            toolbar: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | jbimages | lineheightselect ',
            pagebreak_separator: '<span style="page-break-after: always;"></span>',
            paste_preprocess: function(plugin, args) {
                console.log(args.content);
                args.content = '<b>'+args.content+'</b>';
            },
            setup: function (editor) {
                editor.on('keyup', function(e) {
                    console.log('Editor Body contents was modified. Contents: ' + editor.getContent());
                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
                editor.on('SetContent', function(e) {
                    console.log('Editor Body contents was modified. Contents: ' + editor.getContent());
                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
            }
        });


        tinymce.init({
            selector: "textarea#txt-header",
            theme: "modern",
            menubar:false,
            height: 50,
            statusbar: true,
            branding: false,
            content_style: ".mce-content-body {font-size:11px;font-family:Arial;}",
            fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 24px 36px",
            lineheight_formats: "3pt 4pt 5pt 6pt 7pt 8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
            plugins: [
            "advlist autolink lists link charmap preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime table contextmenu paste template jbimages",
            "pagebreak","textcolor","colorpicker", "textpattern","lineheight"
            ],
            toolbar: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | table | jbimages ',
            pagebreak_separator: '<span style="page-break-after: always;"></span>',
            paste_preprocess: function(plugin, args) {
                console.log(args.content);
                args.content = '<b>'+args.content+'</b>';
            },
            setup: function (editor) {
                editor.on('keyup', function(e) {
                    console.log('Editor Header contents was modified. Contents: ' + editor.getContent());
                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
                editor.on('SetContent', function(e) {
                    console.log('Editor Header contents was modified. Contents: ' + editor.getContent());
                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
            }
        });

        tinymce.init({
            selector: "textarea#txt-footer",
            theme: "modern",
            menubar:false,
            height: 50,
            statusbar: true,
            branding: false,
            content_style: ".mce-content-body {font-size:11px;font-family:Arial;}",
            fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 24px 36px",
            lineheight_formats: "3pt 4pt 5pt 6pt 7pt 8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
            plugins: [
            "advlist autolink lists link charmap preview anchor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime table contextmenu paste template jbimages",
            "pagebreak","textcolor","colorpicker", "textpattern","lineheight"
            ],
            toolbar: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | table | jbimages',
            pagebreak_separator: '<span style="page-break-after: always;"></span>',
            setup: function (editor) {
                editor.on('keyup', function(e) {
                    console.log('Editor Footer contents was modified. Contents: ' + editor.getContent());

                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });

                editor.on('SetContent', function(e) {
                    console.log('Editor Footer contents was modified. Contents: ' + editor.getContent());

                    clearTimeout(ajaxGetContent);
                    ajaxGetContent = setTimeout(function () {
                        getPDFContent();
                    }, ajaxTimer);
                });
            }
        });

    //Event func to select keyword

    $('body').on('click','.choose', function(event) {
        selectedfieldkeyword=$(this).attr('data-keyword');

        $('#CopyText').val('');

        $('#CopyText').val(selectedfieldkeyword);

        var cTxt = document.getElementById("CopyText");

        cTxt.select();
        document.execCommand("Copy");

     // $('#CopyText').val('');


     /* if(typeof selectedfieldkeyword!='undefined')
        {
            $(this).removeClass('btn-twitter');
        }*/
    })

    $('body').on('click','#AddTemplate', function(event) {

        $('#DivAddTax').show();
        return false;

    })




    //Event func to Remove Keywords in editor

    $('.revert').click(function(e){
        removefield=$(this).attr('data-keyword');
        removefield='<span style="color: blue;" data-keyword="'+removefield+'">'+removefield+'</span>'
        var editorContent=tinymce.get('txt-body').getContent(); var replaceStr='<button class="field">&nbsp;</button>';
        editorContent=replaceAll(editorContent, removefield, replaceStr);
        tinymce.get('txt-body').setContent(editorContent);
        $(this).closest('tr').find('.choose').addClass('btn-twitter');

    });

    //Get HTML Content From Template

    $('#preview').click(function(e){

        var TemplateUID = $('#TemplateUID').val();

        $.ajax({
            url: '<?php echo base_url("subproducts/previewtemplate_Mapping"); ?>',
            type: 'POST',
            data: {TemplateUID: TemplateUID},
            cache: false,
            beforeSend: function(){
                $('.spinnerclass').addClass('be-loading-active');
            },
            success: function(data){
                tinymce.get('txt-body').setContent(data);
                $('.spinnerclass').removeClass('be-loading-active');
            }
        })
    })

    //Event func to Save Template

    $('#save').click(function(e){

        var TemplateUID = $('#TemplateUID').val();
        var DynamincFieldUID = $('#DynamincFieldUID').val();
                //var DynamincFieldUID = "#FieldUID_000";
                var FileName = TemplateUID + 'Testing';
                var editorContent=tinymce.get('txt-body').getContent();
                var Mapping =  "#LevelMapping ."+DynamincFieldUID;

                $(Mapping).val(editorContent);

            });

    function escapeRegExp(string){
        return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    }

    function replaceAll(str, term, replacement) {
        return str.replace(new RegExp(escapeRegExp(term), 'g'), replacement);
    }

    // select_mdl();

    // $('.select2').select2({
    //     theme: "bootstrap",
    // });


    function Check()
    {
        var isChecked = $("#IsDynamicTemplate").prop("checked");
        if(isChecked==true)
        {
            $('.TemplateTypeFilediv').show();
        }
        else
        {
            $('.TemplateTypeFilediv').hide();
        }
    }

    $(document).ready(function(){


        var IsDynamicTemplate = $("#IsDynamicTemplate").prop("checked");

        if(IsDynamicTemplate==true)
        {
            $('.TemplateTypeFilediv').show();
        }
        else
        {
            $('.TemplateTypeFilediv').hide();
        }

        var TemplateUID = $('#TemplateUID').val();

        if(TemplateUID){
            $.ajax({
                type: "POST",
                url: '<?php echo base_url();?>Template/GetTemplateFileName',
                data: {'TemplateUID':TemplateUID},
                dataType: 'json',
                cache: false,
                success: function(data)
                {
                    if(data.status == "ok")
                    {

                        setTimeout(function () {
                            
                            tinymce.get('txt-body').setContent(data.MainContent);
                            tinymce.get('txt-header').setContent(data.HeaderContent);
                            tinymce.get('txt-footer').setContent(data.FooterContent);

                            getPDFContent();
                        }, 1000);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                failure: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
            });
        }

    });

    ajax_load_project_data();


    $('#ProductUID').on('change',function() {
        ajax_load_project_data();
    });

    function ajax_load_project_data()
    {
        var ProductUID = $('#ProductUID').val();
        var TemplateUID = $('#TemplateUID').val();
        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>template/GetProjectDetails',
            data: {'ProductUID':ProductUID,'TemplateUID':TemplateUID},
            dataType:'json',
            beforeSend: function(){
                $('.spinnerclass').addClass("be-loading-active");
            },
            success: function(data)
            {
                if(data['success'] == 1)
                {
                    $('.ProjectUID').empty();

                    console.log(data['Projects']);

                    $('.ProjectUID').append(data['Projects']);

                    if(data['ProjectDetailsByTemplateUID']){
                        $(data['ProjectDetailsByTemplateUID']).each(function (k, v) {

                            $('.ProjectUID').find('option[value="'+v.ProjectUID+'"]').prop('selected', true);
                            // select_mdl();
                            // $('.select2').select2({
                            //     theme: "bootstrap",
                            // });
                        });

                        $('#ProjectUID').parent().addClass('is-dirty');
                    }

                    // select_mdl();
                    // $('.select2').select2({
                    //     theme: "bootstrap",
                    // });


                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            failure: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
        })
    }


    var ajaxTimer = 700;
    var ajaxGetContent;

    clearTimeout(ajaxGetContent);
    ajaxGetContent = setTimeout(function () {
        getPDFContent();
    }, ajaxTimer);



    function getPDFContent() 
    {
        tinyMCE.triggerSave();
        var BodyContent = $('#txt-body').val();
        var HeaderContent = $('#txt-header').val();
        var FooterContent = $('#txt-footer').val();
        
        // var BodyContent=tinymce.get('txt-body').getContent();
        // var HeaderContent=tinymce.get('txt-header').getContent();
        // var FooterContent=tinymce.get('txt-footer').getContent();

        var formData = new FormData($('.frmTemplate')[0]);

        formData.append("editorContent", BodyContent);
        formData.append("HeaderContent", HeaderContent);
        formData.append("FooterContent", FooterContent);

        formData.append('RandomString', RandomString);

        $.ajax({
            url: 'Template/getPDF',
            type: 'POST',
            dataType: 'json',
            data: formData,
            mimeType:"multipart/form-data",
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                
            },
        })
        .done(function(response) {
            console.log("success", response);
            if(response.status == 'ok')
            {
                $('#iFramePDF').attr('src', response.URL);
            }
        })
        .fail(function(jqXHR) {
            console.error("error", jqXHR);
        })
        .always(function() {
            console.log("complete");
        });
        
    }

    var RandomString = App_Inst.makeid(8);

    /*@author Parthasarathy @purpose */
    /*change event for #MarginTop,#MarginBottom,#MarginLeft,#MarginRight,#PageSize*/
    $(document).on('change', '#MarginTop,#MarginBottom,#MarginLeft,#MarginRight,#PageSize, #ddl-multiplier', function(e){
        getPDFContent();
    });



    /* **** Section Parthasarathy Code Starts **** */
    /*@author Parthasarathy @purpose ddl-field-section*/
    /*change event for .ddl-field-section*/
    $(document).off('change', '.ddl-field-section').on('change', '.ddl-field-section', function(e){
        e.preventDefault();
        var value = $(this).val();

        if(value && value != '')
        {
            $('.ddl-field-section').not(this).val('').prop('disabled', true);
        }
        else
        {
            $('.ddl-field-section').val('').prop('disabled', false);
        }

        App_Inst.callselect2byclass('ddl-field-section');

    });

    /*@author Parthasarathy @purpose addFields*/
    /*click event for .btn-addfield*/
    $(document).off('click', '.btn-addfield').on('click', '.btn-addfield', function(e){


        e.preventDefault();
        var columns = [];
        var actionbutton = {};
        var obj = {};
        $('.ddl-field-section').each(function (key, elem) {
            
            var value = $(elem).val();

            if(value && value != '' )
            {
                var text = $(elem).find('option:selected').text();
                obj.name = $(elem).attr('data-name');
                obj.value = $(elem).val();
                if ( obj.name != 'Field' )
                {
                    obj.keyword = '%%' + obj.name + '_' + text + '%%';
                }
                else
                {
                    obj.keyword = '%%' + text + '%%';   
                }
                obj.text = text;

                columns.push('<td>'+text+'</td>');
            }
            else
            {
                columns.push('<td></td>');
            }

            $(elem).val('').prop('disabled', false);
            App_Inst.callselect2byclass('ddl-field-section');

        })


        if(obj.value != undefined){
            console.log(obj.value);

            var input = '<input type="hidden" name="'+obj.name+'" value="'+obj.value+'" class="field-tracker" data-name="'+obj.name+'" >'
            var copybutton = '<a title="Edit" href="javascript:void(0);" class="btn btn-copy" data-keyword="'+obj.keyword+'" data-clipboard-text="'+obj.keyword+'"><span class="glyphicon glyphicon-copy"></span></a>';
            var deletebutton = '<a style="padding-left:0;" title="Delete" href="javascript:void(0);" class="btn btn-remove--fieldrow"><span class="glyphicon glyphicon-trash"></span></a>';
            var row = '<tr id= "'+obj.value+'">' + input + columns.join(' ') + '<td>' + copybutton + deletebutton + '</td></tr>';

            $('#tbl-fields tbody').append(row);
            reSortFieldNames();
        }


    });

    function reSortFieldNames() 
    {
        $('#tbl-fields tr').each(function (key, elem) 
        {
            var name = $(elem).find('.field-tracker').attr('data-name');
            $(elem).find('.field-tracker').attr('name', name+'['+key+']');

            var FieldSectionUID = $(elem).find('.FieldSectionUID');
            if(FieldSectionUID)
            {
                var name = FieldSectionUID.attr('data-name');
                FieldSectionUID.attr('name', name+'['+key+']');             
            }
        })
    }

    /*@author Parthasarathy @purpose copytext*/
    /*click event for .btn-copy*/
    $(document).off('click', '.btn-copy').on('click', '.btn-copy', function(e){
        e.preventDefault();

        var button = $(this);
        var button_text = $(this).html();
        $(this).html('<span class="glyphicon" style="background: #ef1414; color: #ececec; padding: 5px 10px;">Copied</span>');
        setTimeout(function () {
            button.html(button_text);
        }, 1500);
    });


    $(document).ready(function(){
        $('.dropdown-submenu a.subdropdown').on("click", function(e){
            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
    });

    /*@author Parthasarathy @purpose insertpagenumber*/
    /*click event for .pagenumber .link-action*/
    $(document).off('click', '.pagenumber .link-action').on('click', '.pagenumber .link-action', function(e){
        e.preventDefault();
        var position = $(this).attr('data-position');
        var content = $(this).attr('data-content');
        if(position == 'top')
        {
            tinymce.get('txt-header').setContent(tinymce.get('txt-header').getContent() + content);
        }
        else if(position == 'bottom')
        {
            tinymce.get('txt-footer').setContent(tinymce.get('txt-footer').getContent() + content); 
        }
    });


    /* **** Section Parthasarathy Code Ends **** */




    /* Audit Feature Included @Karthick kandasamy @On Aug 10 2020---> Start*****/
    var TemplateUID = $('.TemplateAuditLog').attr('data-TemplateUID');

     function TemplateAuditLog(){
        var formData = {
            'TemplateUID':TemplateUID
        }
        table = $('#audit_table').DataTable({
            "bDestroy": true, // Every time call ajax destroy re-install datatable
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "pageLength": 15, // Set Page Length
            "lengthMenu":[[10, 15, 20, 50, 100,-1], [10, 15, 20, 50, 100,'ALL']],
            autoWidth: true,
            responsive: true,
            scrollCollapse: true,
            "language": {
                sZeroRecords: "No Records found",
                processing: '<span class="progrss"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Processing...</span>'
            },
            columnDefs: [
                {
                    orderable: false, targets:  "no-sort"
                }],
            "ajax": {
                "url": "<?php echo base_url('template/ajax_auditlogs')?>",
                "type": "POST",
                "data" : {'formData':formData}
            }
        });
    }

    TemplateAuditLog();

    $('.TemplateEditView').on('click',function(e){
        e.preventDefault();
        $('.TemplateAuditLog').parent().removeClass('active');
        $('.TemplateVersions').parent().removeClass('active');
        $('.GeoProduct').parent().removeClass('active');
        $(this).parent().addClass('active');
        $('#VersionView').hide();
        $('#AuditView').hide();
        $('#TemplateView').show();
    });

    $('.TemplateAuditLog').on('click',function(e){
        e.preventDefault();
        if(!$(this).parent().hasClass('active')){
            $(this).parent().parent().find('li').removeClass('active');
            $(this).parent().addClass('active');
            TemplateAuditLog();
            $('#AuditView').show();
            $('#TemplateView').hide();
            $('#VersionView').hide();
        }else{
            $(this).parent().removeClass('active');
            $(this).parent().parent().find('li:first-child').addClass('active');
            $('#AuditView').hide();
            $('#VersionView').hide();
            $('#TemplateView').show();
        }
    });

    $('.GeoProduct').on('click',function(e){
        e.preventDefault();
        if(!$(this).parent().hasClass('active')){
            $(this).parent().parent().find('li').removeClass('active');
            $(this).parent().addClass('active');
            $('#GeoProduct').show();
            $('#AuditView').hide();
            $('#TemplateView').hide();
            $('#VersionView').hide();
        }else{
            $(this).parent().removeClass('active');
            $(this).parent().parent().find('li:first-child').addClass('active');
            $('#AuditView').hide();
            $('#VersionView').hide();
            $('#TemplateView').hide();
            $('#GeoProduct').show();
        }
    });

    function TemplateVersions(){
        var formData = {
            'TemplateUID':TemplateUID
        }
        table = $('#version_table').DataTable({
            "bDestroy": true, // Every time call ajax destroy re-install datatable
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "pageLength": 15, // Set Page Length
            "lengthMenu":[[10, 15, 20, 50, 100,-1], [10, 15, 20, 50, 100,'ALL']],
            autoWidth: true,
            responsive: true,
            scrollCollapse: true,
            "language": {
                sZeroRecords: "No Records found",
                processing: '<span class="progrss"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Processing...</span>'
            },
            columnDefs: [
                {
                    orderable: false, targets:  "no-sort"
                }],
            "ajax": {
                "url": "<?php echo base_url('template/ajax_versions')?>",
                "type": "POST",
                "data" : {'formData':formData}
            }
        });
    }

    $('.TemplateVersions').on('click',function(e){
        e.preventDefault();
        if(!$(this).parent().hasClass('active')){
            $(this).parent().parent().find('li').removeClass('active');
            $(this).parent().addClass('active');
            TemplateVersions();
            $('#AuditView').hide();
            $('#TemplateView').hide();
            $('#VersionView').show();
        }else{
            $(this).parent().removeClass('active');
            $(this).parent().parent().find('li:first-child').addClass('active');
            $('#AuditView').hide();
            $('#VersionView').hide();
            $('#TemplateView').show();
        }
    });


    /* Audit Feature Included @Karthick kandasamy @On Aug 10 2020---> End*****/

    $('body').on('click','.remove_btn', function(event) {
        var TemplateFieldSectionUID =$(this).attr('data-fieldsectionuid');

        swal({
            title: "Are you sure",
            text: "Once you deleted, It will not be able to recover!",
            type: "question",
            showCancelButton: true,
            buttonStyle: false,
            confirmButtonText: "Yes, Delete it!",
            cancelButtonColor: '#d33',
            cancelButtonText: "No, Keep it!",
        }).then(function(isConfirm){
            if(isConfirm.value == true) {

                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url();?>template/AjaxTemplateFieldSectionDelete',
                    data: {'TemplateFieldSectionUID': TemplateFieldSectionUID},
                    dataType: 'json',
                    success: function (data) {
                        if (data['validation_error'] == 0) {
                            $.gritter.add({
                                title: data['message'],
                                class_name: 'color success',
                                fade: true,
                                speed: 'slow',
                                time: 2000,
                                after_close: function () {
                                    location.reload();
                                }
                            });
                        } else {
                            $.gritter.add({
                                title: data['message'],
                                class_name: 'color danger',
                                fade: true,
                                time: 3000,
                                speed: 'slow',
                            });

                        }

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                    failure: function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    },
                })

            }
        });

    });

    /*Field Mapping remove table tr Included @Karthick kandasamy @On Aug 17 2020---> *****/

    $('body').on('click','.btn-remove--fieldrow', function(event) {
        var trid = $(this).closest('tr').attr('id');
        $('#tbl-fields tr#'+trid).remove();
    });

    function valueChanged()
    {
        if($('.apply-filter-Check').is(":checked"))
            $(".apply-filter-document-types").show();
        else
            $(".apply-filter-document-types").hide();
            $('#FilterDocumentTypes').val('').trigger('change');
          }

    /*Getting Field List based on document type.
  * * @author Karthick kandasamy <karthick.kandasamy@avanzegroup.com>
   * @since 17 Aug 2020
  * */

     $('#FilterDocumentTypes').change(function() {

         var DocumentTypeUID = $(this).val();

        $.ajax({
            type:'POST',
            dataType: 'json',
            url:'<?php echo base_url();?>template/GetFieldList/',
            data: {'DocumentTypeUID':DocumentTypeUID,},
            beforeSend: function() {
                $('#loader').addClass('be-loading be-loading-active');
            },
            success:function(data)
            {
                console.log(data);

                if(data.length!=0)
                {
                    $('#FieldList').empty();
                    $('#FieldList').append('<option value="">Select Field List</option>');
                    for(var i=0;i < data.FieldList.length; i++)
                    {
                        $('#FieldList').append('<option value="'+data.FieldList[i].FieldUID+'">'+data.FieldList[i].FieldName+'</option>');
                    }

                } else {
                    $('#FieldList').html('<option></option>');
                }


            },

        });

    });



    $(document).off('change', '.ddl-product').on('change','.ddl-product', function()
    {
        var tr = $(this).closest('tr');

        var product = $(this).val();
        var customer = $('.ddl-customer').val();

        $('.ddl-subproduct').empty().append('<option></option>');


        $.ajax({
            type: "POST",
            url: 'template/getCustomerAvailableSubProduct',
            data: {Customer:customer, Product: product},
            dataType:'json',
            cache: false,
            success: function(data)
            {
                if(data!='')
                {
                    var AvailableSubProducts = data['SubProducts'].join("/n");
                    $('.ddl-subproduct').html(AvailableSubProducts);
                    // select_mdl();
                }
            }
        });
    });



    /*change event for .county-state*/

    $(document).off('change', '.ddl-state').on('change', '.ddl-state',function(){

        var currentRow = $(this).closest('tr');
        var StateUID  =  $(this).val();

        $.ajax({
            type: "POST",
            url: 'Document_language/getAvailableStates',
            dataType:'json',
            data: {'StateUID':StateUID},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(data)
            {
                $('.ddl-county').html('');
                $('.ddl-city').html('');
                $('.ddl-zipcode').html('');
                if (data.success==1)
                {
                    if(data.CountyDetails.length > 0) {
                        $('.ddl-county').html(data.CountyDetails);
                    }
                    if(data.CityDetails.length > 0 ) {
                        $('.ddl-city').html(data.CityDetails);
                    }
                    if(data.ZipcodeDetails.length > 0 ) {
                        $('.ddl-zipcode').html(data.ZipcodeDetails);
                    }
                    // select_mdl();
                }
                else
                {
                    $.gritter.add({
                        title: 'Something Went Wrong!',
                        class_name: 'color danger',
                        fade: true,
                        time: 3000,
                        speed:'fast',
                    });

                }

                callselect2byclass('select2');
                $('.loading').hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            failure: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
        });

    });




    $(document).off('change','.ddl-county').on('change','.ddl-county',function(){

        var currentRow = $(this).closest('tr');
        var CountyUID  =  $(this).val();
        var StateUID  =  $('.ddl-state').val();
        $.ajax({
            type: "POST",
            url: 'template/getCityZipcodeByCounty',
            data: {'StateUID':StateUID, 'CountyUID': CountyUID},
            dataType:'json',
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(data)
            {

                $('.ddl-city').html('');
                $('.ddl-zipcode').html('');
                if (data.success==1)
                {

                    if(data.CityDetails.length > 0 ) {

                        $('.ddl-city').html(data.CityDetails);
                    }
                    if(data.ZipcodeDetails.length > 0 ) {

                        $('.ddl-zipcode').html(data.ZipcodeDetails);
                    }
                    // select_mdl();
                }
                else
                {
                    $.gritter.add({
                        title: 'Something Went Wrong!',
                        class_name: 'color danger',
                        fade: true,
                        time: 3000,
                        speed:'fast',
                    });

                }

                callselect2byclass('select2');
                $('.loading').hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            failure: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
        });

    });


    //fetch county based on state
    $(document).off('change','.ddl-zipcode').on('change','.ddl-zipcode',function(){

        var currentRow = $(this).closest('tr');
        var zipcode  =  $(this).val();
        var stateuid  =  $('.ddl-state').val();
        var countyuid  = $('.ddl-county').val();
        if(zipcode != '')
        {

            $.ajax({
                type: "POST",
                url: 'vendor/get_vendorcounties_byzipcode',
                data: {'zipcode':zipcode,'stateuid': stateuid},
                dataType:'json',
                beforeSend: function(){
                    $('.loading').show();
                },
                success: function(data)
                {

                    if (data.success==1)
                    {
                        console.log($('.CountyUID'))
                        if(data.CountyUID) {
                            $('.ddl-county').val(data.CountyUID);
                        }

                        if(data.AllCityUID) {
                            $('.ddl-city').val(data.AllCityUID);
                        }
                    }
                    else
                    {
                        $.gritter.add({
                            title: '<?php echo $this->lang->line('failed'); ?>',
                            class_name: 'color danger',
                            fade: true,
                            time: 1000,
                            speed:'fast',
                        });

                    }

                    callselect2byclass('select2');
                    $('.loading').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
                failure: function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                },
            });

        }

    });


    $(document).off('change','.ddl-city').on('change','.ddl-city',function(){

        var currentRow = $(this).closest('tr');
        var CityUID  =  $(this).val();
        var StateUID  =  $('.ddl-state').val();
        var CountyUID  =  $('.ddl-county').val();
        $.ajax({
            type: "POST",
            url: 'vendor/get_vendorzipcode_bycityuid',
            data: {'CityUID': CityUID, 'CountyUID': CountyUID, 'StateUID': StateUID},
            dataType:'json',
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(data)
            {

                $('.ZipCode').html('');
                if (data.success==1)
                {

                    if(data.ZipcodeDetails.length > 0 ) {

                        $('.ddl-zipcode').html(data.ZipcodeDetails);
                    }

                    if(data.AllCountyUID.length > 0) {
                        $('.ddl-county').val(data.AllCountyUID);
                    }


                }
                else
                {
                    $.gritter.add({
                        title: 'Something Went Wrong!',
                        class_name: 'color danger',
                        fade: true,
                        time: 3000,
                        speed:'fast',
                    });

                }

                callselect2byclass('select2');
                $('.loading').hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            failure: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
        });

    });



    $(document).on('click', '.btn-client', function (e)
    {
        /** Variable Declaration */
        var templateProductUID,customerUID,productUID,subProductUID,buttonName,templateUID,formData;

        buttonName = $(this).attr('id');

        if (buttonName == "btn-client-save")
        {
            templateUID = '<?php echo $TemplateUID; ?>';
            customerUID = $('.ddl-customer :selected').val();
            productUID = $('.ddl-product :selected').val();
            subProductUID = $('.ddl-subproduct :selected').val();

            formData = {
                'TemplateUID' : templateUID,
                'CustomerUID' : customerUID,
                'ProductUID' : productUID,
                'SubProductUID' : subProductUID,
            };

            $.ajax({
                type: "POST",
                url: '<?php echo base_url(); ?>template/saveTemplateClient',
                data: formData,
                cache: false,
                dataType: 'json',
                beforeSend: function() {
                    $('.be-loading').addClass('be-loading-active');
                },
                success: function(data)
                {
                    $(this).attr('disabled',false);

                    if(data['error'] == 0)
                    {
                        $.gritter.add({
                            title: data['msg'],
                            class_name: 'color '+data['type'],
                            fade: true,
                            time: 3000,
                            speed: 'slow',
                        });
                        $('#tbl-doc-client-setup tbody').html(data['record']);
                        $('.ddl-customer').val('').trigger('change');
                        // button.closest('div').find('.btn-close').trigger('click');
                    }
                    if(data['error'] == 1)
                    {
                        $.gritter.add({
                            title: data['msg'],
                            class_name: 'color '+data['type'],
                            fade: true,
                            time: 3000,
                            speed: 'slow',
                        });
                    }
                    $('.be-loading').removeClass("be-loading-active");
                },
            });
        }

        if (buttonName == "btn-client-update")
        {
            templateUID = '<?php echo $TemplateUID; ?>';
            customerUID = $('.ddl-customer :selected').val();
            productUID = $('.ddl-product :selected').val();
            subProductUID = $('.ddl-subproduct :selected').val();
            templateProductUID = $(this).attr('data-clientuid');


            formData = {
                'TemplateProductUID' : templateProductUID,
                'TemplateUID' : templateUID,
                'CustomerUID' : customerUID,
                'ProductUID' : productUID,
                'SubProductUID' : subProductUID,
            };

            $.ajax({
                type: "POST",
                url: '<?php echo base_url(); ?>template/updateTemplateClient',
                data: formData,
                cache: false,
                dataType: 'json',
                beforeSend: function() {
                    $('.be-loading').addClass('be-loading-active');
                },
                success: function(data)
                {
                    $(this).attr('disabled',false);

                    if(data['error'] == 0)
                    {
                        $.gritter.add({
                            title: data['msg'],
                            class_name: 'color '+data['type'],
                            fade: true,
                            time: 3000,
                            speed: 'slow',
                        });
                        $('#tbl-doc-client-setup tbody').html(data['record']);
                        $('.ddl-customer').val('').trigger('change');
                        // select_mdl();
                        $('#btn-client-update').attr('data-clientuid', '');
                        $('.btn-save-group').css('display', 'block');
                        $('.btnupdate').css('display', 'none');
                    }
                    if(data['error'] == 1)
                    {
                        $.gritter.add({
                            title: data['msg'],
                            class_name: 'color '+data['type'],
                            fade: true,
                            time: 3000,
                            speed: 'slow',
                        });
                    }
                    $('.be-loading').removeClass("be-loading-active");
                },
            });
        }

    });

    $(document).on('click', '.edit_btn', function (event)
    {
        /** Variable Declaration */
        var buttonID,tblRow,customerUID,productUID,subProductUID,customerName,productName,subProductName,templateProductUID;

        /** Hide and Show a Save and Update Buttons */
        $('.btn-save-group').css('display', 'none');
        $('.btnupdate').css('display', 'block');

        buttonID = $(this).attr('id');
        tblRow = $(this).closest('tr');

        customerUID = tblRow.find('.td-customer').attr('id');
        productUID = tblRow.find('.td-product').attr('id');
        subProductUID = tblRow.find('.td-subproduct').attr('id');
        customerName = tblRow.find('.td-customer').text();
        productName = tblRow.find('.td-product').text();
        subProductName = tblRow.find('.td-subproduct').text();

         $('#ddl-customer').val(customerUID);
            App_Inst.callselect2byclass('select2');

       if (productUID != '0')
        {
            $('.ddl-product').val(productUID);
            App_Inst.callselect2byclass('select2');

            $.ajax({
                type: "POST",
                url: 'template/getCustomerAvailableSubProduct',
                data: {Customer:customerUID, Product: productUID,TemplateProductUID:buttonID},
                dataType:'json',
                cache: false,
                success: function(data)
                {
                    if(data!='')
                    {
                        var AvailableSubProducts = data['SubProducts'].join("/n");
                        $('.ddl-subproduct').html(AvailableSubProducts);
                        // select_mdl();
                    }
                }
            });

            $('#btn-client-update').attr('data-clientuid', buttonID);
        }

        $.ajax({
            type: "POST",
            url: 'template/getCustomerAvailableProduct',
            data: {Customer:customerUID,TemplateProductUID:buttonID},
            dataType:'json',
            cache: false,
            success: function(data)
            {
                console.log(data);

                if(data!='')
                {
                    var AvailableProducts = data['Products'].join("/n"); // array to string convert
                    $('.ddl-product').html(AvailableProducts);
                    componentHandler.upgradeDom();
                    // select_mdl();
                }
            }
        });

    });

    $(document).on('click', '.delete_row', function(event)
    {
        event.preventDefault();
        /* Act on the event */

        /** Variable Declaration */
        var buttonID,tblRowRemove,templateUID;

        buttonID = $(this).attr('id');
        tblRowRemove = $(this).closest('tr');
        templateUID = '<?php echo $TemplateUID; ?>';

        Swal.fire({
            title: 'Are you sure?',
            text: "Once you deleted, It will not be able to recover!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            allowOutsideClick: false
        }).then((result) => {
            if (result.value == true) {
                $.ajax({
                    url: "<?php echo base_url('template/delectTemplateProduct')?>",
                    data: {'TemplateProductUID': buttonID, 'TemplateUID': templateUID},
                    type: "POST",
                    dataType: "JSON",
                    beforeSend: function(){
                        let timerInterval
                        Swal.fire({
                            title: 'Record has Deleting...',
                            html: 'Please wait it will be close in <b></b>.',
                            timer: 2000,
                            timerProgressBar: true,
                            onBeforeOpen: () => {
                                Swal.showLoading()
                                timerInterval = setInterval(() => {
                                    const content = Swal.getContent()
                                    if (content) {
                                        const b = content.querySelector('b')
                                        if (b) {
                                            b.textContent = Swal.getTimerLeft()
                                        }
                                    }
                                }, 100)
                            },
                            onClose: () => {
                                clearInterval(timerInterval)
                            }
                        });
                    },
                    success:function(data)
                    {

                        if(data['error'] == 0)
                        {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                onOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: 'success',
                                title: data['msg']
                            });

                            $('#tbl-doc-client-setup tbody').html(data['record']);
                            // location.reload(true);
                            // button.closest('div').find('.btn-close').trigger('click');
                        }
                        if(data['error'] == 1)
                        {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                onOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: 'error',
                                title: data['msg']
                            });
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            onOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        Toast.fire({
                            icon: 'error',
                            title: 'Please try again'
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-client-close', function(event)
    {
        event.preventDefault();
        /* Act on the event */

        /** Variable Declaration */
        var buttonID,tblRowRemove,documentLanguageUID;

        buttonID = $(this).attr('id');

        if(buttonID == 'btn-client-close-save')
        {
            $('.ddl-customer').val('').trigger('change');
            // select_mdl();
        }
        else
        {
            $('.ddl-customer').val('').trigger('change');
            // select_mdl();
            $('.btn-save-group').css('display', 'block');
            $('.btnupdate').css('display', 'none');
        }
    });

    $(document).on('click', '.btn-geographic', function (e)
    {
        /** Variable Declaration */
        var templateUID,buttonName,templateGeographicUID,formData,stateUID,countyUID,cityUID,zipcode;

        buttonName = $(this).attr('id');

        if (buttonName == "btn-geographic-save")
        {
            templateUID = '<?php echo $TemplateUID; ?>';
            stateUID  = $('.ddl-state :selected').val();
            countyUID = $('.ddl-county :selected').val();
            cityUID   = $('.ddl-city :selected').val();
            zipcode   = $('.ddl-zipcode :selected').val();

            formData = {
                'TemplateUID' : templateUID,
                'StateUID' : stateUID,
                'CountyUID' : countyUID,
                'CityUID' : cityUID,
                'ZipCode' : zipcode,
            };

            $.ajax({
                type: "POST",
                url: '<?php echo base_url(); ?>template/saveTemplateGeographic',
                data: formData,
                cache: false,
                dataType: 'json',
                beforeSend: function() {
                    $('.be-loading').addClass('be-loading-active');
                },
                success: function(data)
                {
                    $(this).attr('disabled',false);

                    if(data['error'] == 0)
                    {
                        $.gritter.add({
                            title: data['msg'],
                            class_name: 'color '+data['type'],
                            fade: true,
                            time: 3000,
                            speed: 'slow',
                        });
                        $('#tbl-doc-geographic-setup tbody').html(data['record']);
                        $('.ddl-state').val('').trigger('change');
                        // select_mdl();
                        // location.reload(true);
                        // button.closest('div').find('.btn-close').trigger('click');
                    }
                    if(data['error'] == 1)
                    {
                        $.gritter.add({
                            title: data['msg'],
                            class_name: 'color '+data['type'],
                            fade: true,
                            time: 3000,
                            speed: 'slow',
                        });
                    }
                    $('.be-loading').removeClass("be-loading-active");
                },
            });
        }

        if (buttonName == "btn-geographic-update")
        {
            templateUID = '<?php echo $TemplateUID; ?>';
            stateUID  = $('.ddl-state :selected').val();
            countyUID = $('.ddl-county :selected').val();
            cityUID   = $('.ddl-city :selected').val();
            zipcode   = $('.ddl-zipcode :selected').val();
            templateGeographicUID = $(this).attr('data-clientuid');

            formData = {
                'TemplateGeographicUID' : templateGeographicUID,
                'TemplateUID' : templateUID,
                'StateUID' : stateUID,
                'CountyUID' : countyUID,
                'CityUID' : cityUID,
                'ZipCode' : zipcode,
            };

            $.ajax({
                type: "POST",
                url: '<?php echo base_url(); ?>template/updateTemplateGeographic',
                data: formData,
                cache: false,
                dataType: 'json',
                beforeSend: function() {
                    $('.be-loading').addClass('be-loading-active');
                },
                success: function(data)
                {
                    $(this).attr('disabled',false);

                    if(data['error'] == 0)
                    {
                        $.gritter.add({
                            title: data['msg'],
                            class_name: 'color '+data['type'],
                            fade: true,
                            time: 3000,
                            speed: 'slow',
                        });
                        $('#tbl-doc-geographic-setup tbody').html(data['record']);

                        $('.ddl-state').val('').trigger('change');
                        // select_mdl();
                        $('#btn-geographic-update').attr('data-clientuid', '');

                        $('.btn-save-geographic').css('display', 'block');
                        $('.btn-update-geographic').css('display', 'none');
                    }
                    if(data['error'] == 1)
                    {
                        $.gritter.add({
                            title: data['msg'],
                            class_name: 'color '+data['type'],
                            fade: true,
                            time: 3000,
                            speed: 'slow',
                        });
                    }
                    $('.be-loading').removeClass("be-loading-active");
                },
            });
        }

    });

    $(document).on('click', '.btn-geographic-edit', function (event)
    {
        /** Variable Declaration */
        var buttonID,tblRow,stateUID,countyUID,cityUID,zipCode,stateName,countyName,cityName;

        /** Hide and Show a Save and Update Buttons */
        $('.btn-save-geographic').css('display', 'none');
        $('.btn-update-geographic').css('display', 'block');

        buttonID = $(this).attr('id');
        tblRow = $(this).closest('tr');

        stateUID   = tblRow.find('.td-state').attr('id');
        countyUID  = tblRow.find('.td-county').attr('id');
        cityUID    = tblRow.find('.td-city').attr('id');
        zipCode    = tblRow.find('.td-zipcode').attr('id');
        stateName  = tblRow.find('.td-state').text();
        countyName = tblRow.find('.td-county').text();
        cityName   = tblRow.find('.td-city').text();;

        $('.ddl-state').val(stateUID);
        App_Inst.callselect2byclass('select2');
        $.ajax({
            type: "POST",
            url: 'template/getAvailableStates',
            dataType:'json',
            data: {'StateUID':stateUID, 'TemplateGeographicUID':buttonID},
            beforeSend: function(){
                $('.loading').show();
            },
            success: function(data)
            {
                $('.ddl-county').html('');
                $('.ddl-city').html('');
                $('.ddl-zipcode').html('');
                if (data.success==1)
                {
                    if(data.CountyDetails.length > 0) {
                        $('.ddl-county').html(data.CountyDetails);
                    }
                    if(data.CityDetails.length > 0 ) {
                        $('.ddl-city').html(data.CityDetails);
                    }
                    if(data.ZipcodeDetails.length > 0 ) {
                        $('.ddl-zipcode').html(data.ZipcodeDetails);
                    }
                    // select_mdl();
                }
                else
                {
                    $.gritter.add({
                        title: 'Something Went Wrong!',
                        class_name: 'color danger',
                        fade: true,
                        time: 3000,
                        speed:'fast',
                    });

                }

                callselect2byclass('select2');
                $('.loading').hide();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
            failure: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
        });

        $('#btn-geographic-update').attr('data-clientuid', buttonID);

    });

    $(document).on('click', '.btn-geographic-delete', function(event)
    {
        event.preventDefault();
        /* Act on the event */

        /** Variable Declaration */
        var buttonID,tblRowRemove,templateUID;

        buttonID = $(this).attr('id');
        tblRowRemove = $(this).closest('tr');
        templateUID = '<?php echo $TemplateUID; ?>';

        Swal.fire({
            title: 'Are you sure?',
            text: "Once you deleted, It will not be able to recover!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            allowOutsideClick: false
        }).then((result) => {
            if (result.value == true) {
                $.ajax({
                    url: "<?php echo base_url('template/delectTemplateGeographic')?>",
                    data: {'TemplateGeographicUID': buttonID, 'TemplateUID': templateUID},
                    type: "POST",
                    dataType: "JSON",
                    beforeSend: function(){
                        let timerInterval
                        Swal.fire({
                            title: 'Record has Deleting...',
                            html: 'Please wait it will be close in <b></b>.',
                            timer: 2000,
                            timerProgressBar: true,
                            onBeforeOpen: () => {
                                Swal.showLoading()
                                timerInterval = setInterval(() => {
                                    const content = Swal.getContent()
                                    if (content) {
                                        const b = content.querySelector('b')
                                        if (b) {
                                            b.textContent = Swal.getTimerLeft()
                                        }
                                    }
                                }, 100)
                            },
                            onClose: () => {
                                clearInterval(timerInterval)
                            }
                        });
                    },
                    success:function(data)
                    {

                        if(data['error'] == 0)
                        {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                onOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: 'success',
                                title: data['msg']
                            });

                            $('#tbl-doc-geographic-setup tbody').html(data['record']);
                            // location.reload(true);
                            // button.closest('div').find('.btn-close').trigger('click');
                        }
                        if(data['error'] == 1)
                        {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                onOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });

                            Toast.fire({
                                icon: 'error',
                                title: data['msg']
                            });
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            onOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        Toast.fire({
                            icon: 'error',
                            title: 'Please try again'
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-geographic-close', function(event)
    {
        event.preventDefault();
        /* Act on the event */

        /** Variable Declaration */
        var buttonID;

        buttonID = $(this).attr('id');

        if(buttonID == 'btn-geographic-save-cancle')
        {
            $('.ddl-state').val('').trigger('change');
            // select_mdl();
        }
        else
        {
            $('.ddl-state').val('').trigger('change');
            // select_mdl();
            $('.btn-save-geographic').css('display', 'block');
            $('.btn-update-geographic').css('display', 'none');
        }
    });

    $(document).on('change','.ddl-customer', function(e)
    {
        e.preventDefault();
        var customer = $(this).val();
        var unique = $('.edit_row').attr('id');

        $('.ddl-product').empty().append('<option></option>');
        $('.ddl-subproduct').empty().append('<option></option>');


        $.ajax({
            type: "POST",
            url: 'template/getCustomerAvailableProduct',
            data: {Customer:customer},
            dataType:'json',
            cache: false,
            success: function(data)
            {
                console.log(data);

                if(data!='')
                {
                    var AvailableProducts = data['Products'].join("/n"); // array to string convert
                    $('.ddl-product').html(AvailableProducts);
                    componentHandler.upgradeDom();
                    // select_mdl();
                }
            }
        });
    });



</script>
