<style type="text/css">
    .nav-tabs.upload-tabs .nav-link {
        padding: 10px 0;
    }
</style>

<?php

$Orderdetailsss = $this->Common_model->get_orderdetails($OrderUID);

$OrderrUID = $Orderdetailsss->OrderUID;
$UserUID = $this->session->userdata('UserUID');

$UserRole = $this->Common_model->GetUserRoleTypeDetails($UserUID);
$UserRoleType = $UserRole->RoleType;

$SubProductUID = $Orderdetailsss->SubProductUID;
$ProductUID = $Orderdetailsss->ProductUID;
$Product = $ProductUID;

$Customer_Check = $this->Common_model->GetCustomerByUserUID($UserUID);

$Customer_Check = $this->parameters['CustomerUID'];

if($Customer_Check == 0){
    $fornotes = 1;
}
else{
    $fornotes = 2;
}

$forcompleted = 0;
$OrderSourceUID = $Orderdetailsss->OrderSourceUID;
$OrderSourceName = '';
if($OrderSourceUID){
    $OrderSourceName = $this->Attachments_model->GetOrderSourceName($OrderSourceUID);
}

$CustomerUID = $Orderdetailsss->CustomerUID;
$CheckPrintingWorkFlow = $this->Common_model->CheckPrintingWorkFlow($CustomerUID,$SubProductUID,$OrderrUID);

$AbsButton = 0;
$Details = $this->Attachments_model->CheckOrderAssignedtoAbstractor($OrderrUID);
$ApiOutBoundOrderUID = $Details['ApiOutBoundOrderUID'];
if($ApiOutBoundOrderUID){
    $AbsButton = 1;
}
$CheckFinalReports = $this->Attachments_model->CheckValidationForAPI($OrderrUID);

?>
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
                <input type="hidden" id="orderuid" value="<?php echo $OrderUID; ?>">
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
                                            <!-- <tbody>
                                                 <?php $i=1; foreach($SearchDocumentDetails as $row): ?> 
                                                <tr data-orderuid="<?php echo $row->OrderUID; ?>" data-documentfilename = "<?php echo $row->DocumentFileName; ?>" data-TypeOfDocument = "<?php echo $row->TypeOfDocument; ?>"> 
                                                    <td>
                                                        <label class="custom-control custom-checkbox" for="selectdoc<?php echo $i; ?>">
                                                            <input type="checkbox" class="custom-control-input" id="selectdoc<?php echo $i; ?>" data-filesize="<?php echo filesize($row->OrderDocsPath . $row->DocumentFileName); ?>">
                                                            <span class="custom-control-label"></span>
                                                        </label>
                                                    </td>
                                                    <td><?php echo $i; ?></td>
                                                    <td <?php if($this->session->userdata('RoleType')!=8){ echo 'contenteditable="true"';}?> onBlur="saveToDatabase(this,'DisplayFileName','<?php echo $row->DocumentFileName; ?>','<?php echo $row->DocumentFileName; ?>','<?php echo $row->DocumentCreatedDate; ?>','<?php echo $row->OrderUID; ?>')" id="<?php echo $row->DocumentFileName;?>">
                                                        <?php echo $row->DisplayFileName; ?>
                                                        <?php
                                                        $sendmail = date('m/d/Y H:i A', strtotime($row->IsMailSendDateTime ));
                                                        if($row->IsMailSend == 1){ ?> <span class="mdi mdi-check-all" style="color: green;" title="Mail Send <?php echo $sendmail; ?>"></span> <?php } ?>
                                                        <?php
                                                        $sendmailapi = date('m/d/Y H:i A', strtotime($row->IsAPISendDateTime ));
                                                        if($row->IsAPISend == 1){ ?> <span class="mdi mdi-check-all" style="color: green;" title="API Send <?php echo $sendmailapi; ?>"></span> <?php } ?>
                                                    </td>
                                                    <td>
                                                     <?php echo $this->common_model->filesize_formatted($row->OrderDocsPath . $row->DocumentFileName); ?>
                                                 </td>
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
                                                <?php $i++; endforeach; ?>
                                            </tbody> -->
                                              <tbody>

                                <?php $i=1; foreach($SearchDocumentDetails as $row):
                                preg_match("/\d+\/\S+/", $row->OrderDocsPath, $OrderPath);
                                $row->OrderDocsPath = SEARCHDOCSPATH . $OrderPath[0];
                                $file_path = $row->DocURLPath = DOCSURL . $OrderPath[0];


                                ?>

                                    <tr>
                                        <td>
                                            <label class="custom-control custom-checkbox" for="selectdoc<?php echo $i; ?>">
                                                <input type="checkbox" class="custom-control-input" id="selectdoc<?php echo $i; ?>" data-filesize="<?php echo filesize($row->OrderDocsPath . $row->DocumentFileName); ?>">
                                                <span class="custom-control-label"></span>
                                            </label>
                                        </td>
                                        <td><?php echo $i; ?></td>
                                        <td id="<?php echo $row->DocumentFileName;?>">
                                            <?php echo $row->DisplayFileName; ?>
                                            <?php
                                            $sendmail = date('m/d/Y H:i A', strtotime($row->IsMailSendDateTime ));
                                            if($row->IsMailSend == 1){ ?> <span class="mdi mdi-check-all" style="color: green;" title="Mail Send <?php echo $sendmail; ?>"></span> <?php } ?>
                                            <?php
                                            $sendmailapi = date('m/d/Y H:i A', strtotime($row->IsAPISendDateTime ));
                                            if($row->IsAPISend == 1){ ?> <span class="mdi mdi-check-all" style="color: green;" title="API Send <?php echo $sendmailapi; ?>"></span> <?php } ?>
                                        </td>
                                        <td>
                                            <?php  echo $this->Common_model->filesize_formatted($row->OrderDocsPath . $row->DocumentFileName); ?>
                                        </td>

                                        <?php 
                                            if (is_numeric($row->TypeOfDocument)) 
                                            {
                                            $mdocumenttype = $this->Common_model->get_row('mdocumenttypes', ['DocumentTypeUID'=>$row->TypeOfDocument]);
                                        ?>
                                            <td ><?php echo isset($mdocumenttype->DocumentTypeName) && !empty($mdocumenttype->DocumentTypeName) ? $mdocumenttype->DocumentTypeName : ""; ?></td>
                                        <?php
                                            }
                                            else
                                            {
                                        ?>

                                        <td class="<?php echo $type;?>"><?php echo ($row->TypeOfDocument == 'Search')?'Search Package':$row->TypeOfDocument; ?>
                                        <?php
                                            }
                                        ?>
                                            
                                        </td>

                                        <td><?php echo $row->Comments; ?></td>
                                        <td><?php echo date('m/d/Y H:i:s', strtotime($row->DocumentCreatedDate )); ?></td>
                                        <td class="actions text-center">
                                            <button class="btn btn-sm btn-icon text-primary" title="View" ><i class="fe fe-eye"></i></button>
                                            <button class="btn btn-sm btn-icon text-danger" title="Download" data-OrderUID="<?php echo $OrderUID;?>" data-type="<?php echo $row->TypeOfDocument; ?>" document-id="<?php echo $row->DocumentTypeUID; ?>" data-filename="<?php echo $row->DocumentFileName; ?>" data-createdon="<?php echo $row->DocumentCreatedDate; ?>" data-uploadedUser="<?php echo $row->UploadedUserUID; ?>"><i class="fe fe-download"></i></button>
                                            <button class="btn btn-sm btn-icon text-success" title="Print"><i class="fe fe-printer"></i></button>
                                        </td>

                                    </tr>

                                    <?php $i++; endforeach; ?>

                                </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="setting-tab1" id="Uploads">
                                <div class="col-md-12 col-lg-12 mt-3 mb-4">
                                     <form id="document-form">  
                                    <!-- <p style="font-weight: bold;  text-align: left;margin-bottom: 0px;">ORDER NO: <?php echo $order_details->OrderNumber?></p> -->
                                     <input type="hidden" name="orderuid" id = "orderuid" value="<?php echo $order_details->OrderUID;?>">
                                    <input type="file" id="upload_file" name="upload_file[]" class="dropify" multiple>
                                     <div class="col-md-12 col-lg-12 mb-2 mt-2">
                                        <div class="btn-list text-right">
                                            <button id="btn-uploadfile" class="btn btn-primary btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Uploading Files">Upload</button> 
                                        </div>
                                    </div>
                                    <div id="upload_preview" class="table-responsive pt-3">
                                        <table class="table table-hover table-vcenter table-new" cellspacing="0" id="upload-preview-table">
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
                                             <!--    <tr>
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
                                                </tr> -->
                                            
                                            </tbody>
                                        </table>
                                    </div>
                                   </form>
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



    var OrderUID = $('#orderuid').val();
    var filetoupload=[];
    var selectoption = [];
    var orderuid= $('#OrderUID').val();
    var uploaded={};
    var progresswidget = $('.progress-widget'); var progressvalue = $('.progress-value');
    var bar = $('#bar'); var progressdata = $('.progress-data');
    var o = [];

    $( document ).ready(function() {
        $('.attachment_table').DataTable({
        });
        tinymce.init({
          selector: "textarea#EmailMessage",
          theme: "modern",
          height: 300,       
          theme_advanced_toolbar_location : "bottom",
          menubar: false,
          branding: false,
          statusbar: false,
          relative_urls: false,
          remove_script_host: false,
          fontsize_formats: "8px 10px 12px 14px 18px 24px 36px",
          plugins: [
          "advlist autolink lists link charmap preview anchor",
          "searchreplace visualblocks code fullscreen fullpage",
          "insertdatetime table contextmenu paste template jbimages",
          "pagebreak print"

          ],
          toolbar: "fontsizeselect bold | italic | underline | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | ink | print preview media | forecolor backcolor emoticons  jbimages | pagebreak | paste", 
          pagebreak_separator: '<span style = "page-break-after: always;"></span>',

      });
    });

    $(document).ready(function(){
        $('.removeFile').on('click', function(){

            $('.removeclass'+rid).remove();
           $('tr data-postion="'+id+'"').remove()
            var r_ids = $(this).attr('data-fileid');
            colsole.log(r_ids)
            return
            // to make sure at least one row remains[
            if($('.removeRowBtn').length > 0){

            }
        });
    });

    function removeFunction(id)
    {

        $('#upload-preview-table tr[data-postion="'+id+'"]').remove();
        var groups = filetoupload;
        var lim = filetoupload.length;
        for (var i = 0; i < lim; i++){
            console.log(groups)
            if (groups[i].id == id){
               console.log(groups[i].id)
                groups.splice(groups[i],groups[i].id);
                break;
            }
        }
        console.log(filetoupload);
        console.log(filetoupload[id]);
    }

    $('#upload_file').change(function(event){
        
        fieldcounter = $('#upload-preview-table tbody').find('tr:last').attr('data-postion');
        var documenttype = $('#documenttype').find(":Selected").text();
        //added comments
        var comments  = $('#comments').find(":Selected").text();
        var documenttype = "Others";
        var searchmodes = $('#choosePortal').find(":Selected").text();
        var searchfromdate = $('#searchfromdate').val();
        var searchasofdate = $('#searchasofdate').val();
        var documenttypeuid = $('#documenttype').find(":Selected").val();



        var d = new Date,
            dformat = [(d.getMonth()+1),
                    d.getDate(),
                    d.getFullYear()].join('-') +' ' +
                [d.getHours(),
                    d.getMinutes(),
                    d.getSeconds()].join(':');


        var data = {};
        data.UserName = "Client";
        data.UserUID = 66
        data.datetime = dformat;

        $('#upload-preview-table').show();
        if(isNaN(fieldcounter))
        {
            fieldcounter =0;
        }

        for(var i = 0; i < event.target.files.length; i++)
        {
            fieldcounter++;
            var output = [];
            var file = event.target.files[i];

            var fieldid = fieldcounter;

            filetoupload.push({ id: fieldid, file: file, filename: file.name });

            var searchmodescombo='<select id="SearchModeUID'+i+'" name="SearchModeUID[]" class="searchmodescombo mdl-textfield__input input-xs select2 mdl-select2"><?php  foreach($site as $sites){if($sites->SearchModeUID==6){echo '<option Selected value="'.$sites->SearchModeUID.'">'.$sites->SearchSiteName.'</option>';} else { echo '<option value="'.$sites->SearchModeUID.'">'.$sites->SearchSiteName.'</option>';}} ?></select>';

            var documenttypecombo='<select id="DocumentTypeUID'+i+'" name="TypeOfDocument[]" class="documenttypecombo form-control mdl-textfield__input input-xs select2 mdl-select2"><?php   foreach($TypeOfDocument as $key => $type){ echo '<option value="'.$key.'">'.$type.'</option>';} ?></select>';

            <?php if ($this->session->userdata('RoleType')==13 || $this->session->userdata('RoleType')==14): ?>
            var documentpermissionscombo='<select id="TypeOfPermissions'+i+'" name="TypeOfPermissions[]" class="documentpermissionscombo form-control mdl-textfield__input input-xs select2 mdl-select2" style="width: 100%;"><?php foreach ($DocumentPermissions as $key => $docperm) {if ($docperm->PermissionTypeUID==3) {echo '<option selected value="'.$docperm->PermissionTypeUID.'">'.$docperm->PermissionTypeName.'</option>';}}?></select>';
            <?php else: ?>
            var documentpermissionscombo='<select id="TypeOfPermissions'+i+'" name="TypeOfPermissions[]" class="documentpermissionscombo form-control mdl-textfield__input input-xs select2 mdl-select2" style="width: 100%;"><?php foreach ($DocumentPermissions as $key => $docperm) {if ($docperm->PermissionTypeUID==3) {echo '<option selected value="'.$docperm->PermissionTypeUID.'">'.$docperm->PermissionTypeName.'</option>';} else { echo '<option value="'.$docperm->PermissionTypeUID.'">'.$docperm->PermissionTypeName.'</option>';}}?></select>';
            <?php
            endif;
            ?>
            var select = selectoption;
            uploaded.username=data.UserName;
            uploaded.userid=data.UserUID;
            uploaded.datetime=data.datetime;


            var removelink = " <button  type=\"button\"  onclick=\"removeFunction(" + fieldid + ")\"  class=\"removeFile btn btn-sm btn-icon text-danger\"   data-fileid=\"" + fieldid + "\"><i class=\"icon-trash\"></i></button>";
            

            var viewlink = " <a class=\" viewFile btn btn-sm btn-xs btn-success\" href=\"#\" data-fileid=\"" + fieldid + "\"><span class=\"glyphicon glyphicon-search\"></span></div></a>";
            //Add comments to the field @auth shruti v s, shruti.vs@avanzegroup.com
            //Permission combo filter based on roletype
            

             output.push("<tr data-postion =\""+ fieldcounter +"\" data-filename =\""+ file.name +"\" data-documenttypeuid = \"" + documenttypeuid + "\"><td class = \"text-center\"><input type=\"text\" name=\"DisplayFileName[]\" value=\"", file.name,"\" class=\"form-control\" style=\"height: 35px;\" ></td><td class = \"text-center\">", searchmodescombo, "</td><td class = \"text-center\">", documenttypecombo, "</td><td class= \"text-center\"><input type=\"text\" name=\"Comments[]\" id=\"comments\" value=\"", comments,"\" class=\"form-control\" style=\"height: 35px;\" ></td><td class = \"text-center\">", uploaded.datetime, "</td><td class = \"text-center\">", removelink,"</td></tr>");

            $('#upload-preview-table').DataTable().destroy();
            $('#upload-preview-table').find('tbody').append(output.join(""));

            upload_preview_table=$('#upload-preview-table').DataTable({
                processing: true, //Feature control the processing indicator.
                scrollCollapse: true,
                paging:false,
                columnDefs: [
                    {
                        orderable: false, targets:  "no-sort"}
                ],
                responsive:true,
                lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "All"]] ,

                iDisplayLength: 25,

                language: {
                    sLengthMenu: "Show _MENU_ File(s)",
                    emptyTable:     "No File(s) Found",
                    info:           "Showing _START_ to _END_ of _TOTAL_ File(s)",
                    infoEmpty:      "Showing 0 to 0 of 0 File(s)",
                    infoFiltered:   "(filtered from _MAX_ total File(s))",
                    zeroRecords:    "No matching File(s) found",
                    processing: '<span class="progrss"><i class="fa fa-spinner wobble-fix fa-spin fa-1x fa-fw"></i> Processing...</span>'
                },
                fnDrawCallback: function( oSettings ) {

                },
            });

            $('.select2').select2();
            $('#upload').show();
            $('#mergePDF').show();
            $('#upload_preview').show();
            $('.spinnerclass').removeClass('be-loading-active');


        }
    });


    $('#document-form').submit(function(e){

        e.preventDefault();
        var numfiles = filetoupload.length;
        var button = $('#btn-uploadfile');
        var button_text = button.html();

        if(numfiles>0)
        {
            var newposition = $('#upload-preview-table tbody').find('tr:last').attr('data-postion');
            if(isNaN(newposition))
            {
                newposition = 0;
            }
            else
            {
                newposition -= numfiles;
            }

            $(".upload-notify").css("display","block");
            var fileposition = newposition;
            var searchmodes=$('.searchmodescombo');
            var doctype=$('.documenttypecombo');

            $('#upload_file').val('');
            var formdatavalues = new FormData($(this)[0]);
            var $inputs = $('#document-form :input');
            var formData = new FormData($(this)[0]);

            formData.append("UploadedUserUID", uploaded.userid);
            formData.append("OrderUID", OrderUID);
            for (var i = 0; i <= filetoupload.length-1; i++)
            {
                fileposition++;
                formData.append("image[]", filetoupload[i].file);
                formData.append("position"+i, fileposition);
            }
            formData.append($('.txt_csrfname').attr('name'), $('.txt_csrfname').val());
            //added comments @auth shruti.vs@avanzegroup.com
            /*formData.append('Comments', $('#comments').val());*/

            $.ajax({
                url: '<?php echo base_url('Order_attachment/Document_upload')?>',
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                dataType: 'json',
                // this part is progress bar
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {

                            $('.progress-bar-show').show();
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            $('.progress-bar').text(percentComplete + '%');
                            $('.progress-bar').css('width', percentComplete + '%');
                            console.log(percentComplete + '% Completed');
                            bar.width(percentComplete + '%');
                            $('#upload').val('Please Wait ...');
                        }
                    }, false);
                    return xhr;
                },
                beforeSend: function(){
                    $inputs.prop('disabled', false);
                    progresswidget.show();
                    var percentval = '0%';
                    bar.width(percentval);
                    $('.progress-bar').text(percentval);
                    $('#upload').html($('#upload').data('loading-text'));
                    $(button).prop('disabled', true).html('<i class "fa fa-spin fa-spinner"></i> Uploading');
                     $('.spinnerclass').addClass('be-loading-active');

                },
                success: function(data){
                    console.log(data);
                    filetoupload = [];
                    delete formData;
                    $('.spinnerclass').removeClass('be-loading-active');
                    if(data.Status=='Success')
                    {
                        var doctypevalue = '';
                        $('.documenttypecombo').each( function( i, select ) {
                            var $options = $(select).find('option:selected');
                            $options.each( function( i, option ) {
                                if(option.value == 'Closing Package')
                                {
                                    doctypevalue = option.value;
                                }
                            });
                        });
                        var closingOrder ='<?php echo "$order_details->IsClosingProduct";?>';
                        var apiorder ='<?php echo "$order_details->APIOrder";?>';
                        if(closingOrder == 1 && apiorder == 1 && doctypevalue == 'Closing Package')
                        {
                            closing_761('<?php echo $order_details->OrderUID;?>','761','','');
                        }


                        $message =  data.success_msg;
                        toastr.options = {
                            "timeOut": "1000",
                            "closeButton": true,
                            "onHidden": function() {
                                window.location.reload();
                            }
                        };
                        toastr['info']($message);
                     }
                    $(".upload-notify").css("display","none");
                    $(button).prop('disabled', false).html(button_text);


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR.responseText);
                    $(button).prop('disabled', false).html(button_text);

                },
                complete: function () {
                    $('#upload').val('Upload Files');
                    var percentval = '0%';
                    bar.width(percentval);
                    progresswidget.hide();
                    $('#upload').html('<span class="mdi mdi-upload"></span> Upload Files ');
                    $(button).prop('disabled', false).html(button_text);
                }
            });

        }
        else
        {
           $message =  'here is no files for upload.';
            toastr.options = {
                "timeOut": "1000",
                "closeButton": true,

            };
            toastr['info']($message);

        }

    });


</script>
