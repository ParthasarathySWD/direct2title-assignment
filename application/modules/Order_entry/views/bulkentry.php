
<!-- start body header -->
<div id="page_top" class="section-body bg-new-header-top">
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
                                    <h3 class="card-title pt-2">Product Info</h3>
                                </div>

                                <div class="col-md-6 col-lg-6 text-right pr-0 pt-2">

                                    <a href="Order_entry" class="btn btn-info btn-sm">Single Order</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-4 pt-2">
                        <div class="row">

                            <div class="col-md-4 col-lg-4">

                                <div class="form-group">
                                    <label class="form-label">Client</label>
                                    <select id="bulk_Customers" name="bulk_Customers" class="form-control select2">
                                        <?php foreach ($bulk_Customers as $key => $customer) { ?>
                                            <option value="<?php echo $customer->CustomerUID; ?>" selected><?php echo $customer->CustomerNumber.' / '.$customer->CustomerName; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label">Product</label>
                                        <select id="bulk_products" name="bulk_products" class="form-control select2 ">
                                            
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label class="form-label">SubProduct</label>
                                    <select id="bulk_subproducts" name="bulk_subproducts" class="form-control select2 ">                                       
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
                               <!--  <input type="file" class="dropify"> -->
                                <form  name="formfile"  class="formfile">
                                                            <input type="file" name="excelfile" id="filebulk_entry" class="dropify">
                                                        </form>
                                                        <p></p>

                                <div class="btn-list text-right pt-2">
                                    <a href="javascript:void(0);" class="btn btn-primary"  id="bulk_save">Save</a>
                                    <a href="javascript:void(0);" id="preview" class="btn btn-success  btn-preview-success" >Preview</a>
                                </div>
                            </div>
                            <div class="col-md-6 bulk-right">
                                <p>Please follow the below steps to upload Order.</p>
                                <p>Download the available Excel Format XLSX sheet.</p>
                                <p><a href="javascript:void(0);" href="<?php echo base_url('Order_entry/bulkentrypreviewfile/D2T-Std-BulkFormat.xlsx'); ?>" type="button" class="btn btn-sm btn-info changeentryfilename disabled" disabled="true">Excel Format</a></p>
                                <p><span style="color:red;">*</span> Fill in your Order details into the downloaded XLSX.</p>
                                <p><span style="color:red;">*</span> Upload file size max 5MB</p>
                                <p><span style="color:red;">*</span> Upload back the XLSX.</p>
                            </div>
                            <div class="col-md-12 upload-preview" >

                               <!--  <ul class="nav nav-tabs b-none">
                                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#date-preview"> Data Preview</a></li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#file-preview">File Preview</a></li>
                                </ul> -->
                                <!-- <div class="tab-content">
                                    <div class="tab-pane fade " id="preview-table"> -->

                                        <!-- <div class="table-responsive pt-3">
                                            <table class="table table-hover table-vcenter table-new" cellspacing="0" id="date-preview">
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
                                        </div> -->
                                   <!--  </div>
                                    <div class="tab-pane fade" id="imported-table"> -->

                                        <!-- <div class="table-responsive pt-3">
                                            <table class="table table-hover table-vcenter table-new" cellspacing="0" id="file-preview">
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
                                        </div> -->
                                    <!-- </div>
                                </div> -->

                                <div class="col-md-12 form-group" id="preview-table">

                                </div>

                                <div class="col-md-12 form-group" id="imported-table">

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">


/*For Bulk Entry*/
$('#bulk_Customers').change(function(){
    var CustomerUID = $(this).val();
    $('#bulk_products').empty();
    $('#bulk_subproducts').empty();
    if(CustomerUID == '') {
        return false;
    }
    $('.spinnerclass').addClass("be-loading-active");
    $('.changeentryfilename').attr('disabled',true).addClass('disabled');

    $.ajax({
        type:'POST',
        url:'<?php echo base_url("Order_entry/subproduct_by_customer_product"); ?>',
        data:{'CustomerUID':CustomerUID},
        dataType:'json',
        success: function(data){
            $('#bulk_products').empty();
            $('#bulk_subproducts').empty();
            $('#bulk_projects').empty();
            if(data['Products'].length > 0){
                $.each(data['Products'], function(k, v) {
                    $('#bulk_products').append('<option value="' + v['ProductUID'] + '" data-type="' + v['BulkImportFormat'] + '" data-typename="' + v['BulkImportTemplateName'] + '">' + v['ProductName'] + '</option>');
                });
                $('#bulk_products').trigger('change');
            }else{
                $('#bulk_products').parent().removeClass('is-focused');
            }
            
            if(data['SubProducts'].length > 0){
                $('#bulk_subproducts').append('<option value=""></option>');
                $.each(data['SubProducts'], function(k, v) {
                    $('#bulk_subproducts').append('<option value="' + v['SubProductUID'] + '" data-type="' + v['BulkImportFormat'] + '" data-typename="' + v['BulkImportTemplateName'] + '">' + v['SubProductName'] + '</option>');
                });
                $('#bulk_subproducts').trigger('change');
            }else{
                $('#bulk_subproducts').parent().removeClass('is-focused');
            }

            if(data['Projects'].length > 0){
                $('#bulk_projects').append('<option value=""></option>');
                $.each(data['Projects'], function(k, v) {
                    $('#bulk_projects').append('<option value="' + v['ProjectUID'] + '" data-type="' + v['BulkImportFormat'] + '" data-typename="' + v['BulkImportTemplateName'] + '">' + v['ProjectName'] + '</option>');
                });
            }else{
                $('#bulk_projects').parent().removeClass('is-focused');
            }

            
            // select_mdl();
                // componentHandler.upgradeDom();
            $('.spinnerclass').removeClass("be-loading-active");
        }
    })
});

  $('#bulk_products').change(function(){
    $('#bulk_subproducts').empty();
    var ProductUID = $(this).val();
    var Entrytype = $(this).find(':selected').attr('data-type');
    var Entrytypefile = $(this).find(':selected').attr('data-typename');

    if(Entrytype == 'D2T-Assignment'){
        $('.entry_standardtype').hide()             
        $('.entry_assignmenttype').show();
        $('.entry_reversetype').hide()              
        $('.changeentryfilename').removeAttr('disabled').removeClass('disabled');               
        $('.changeentryfilename').attr('href','<?php echo base_url("Order_entry/bulkentrypreviewfile");?>/'+Entrytypefile);
    }else if(Entrytype == 'D2T-ReverseMortgage'){
        $('.entry_assignmenttype').hide()               
        $('.entry_standardtype').hide()             
        $('.entry_reversetype').show()              
        $('.changeentryfilename').removeAttr('disabled').removeClass('disabled');               
        $('.changeentryfilename').attr('href','<?php echo base_url("Order_entry/bulkentrypreviewfile"); ?>/'+Entrytypefile);         
    }else{
        $('.entry_assignmenttype').hide()               
        $('.entry_reversetype').hide()              
        $('.entry_standardtype').show()             
        $('.changeentryfilename').removeAttr('disabled').removeClass('disabled');               
        $('.changeentryfilename').attr('href','<?php echo base_url("Order_entry/bulkentrypreviewfile"); ?>/'+Entrytypefile);         
    }
    var CustomerUID = $('#bulk_Customers').val();
    $.ajax({
        type:'POST',
        url:'<?php echo base_url("Order_entry/get_subproduct_by_product_customer"); ?>',
        data:{'CustomerUID':CustomerUID,'ProductUID':ProductUID},
        dataType:'json',
        success: function(data){
            console.log(data);
            $('#bulk_projects').empty();

            if(data['SubProducts'].length > 0){
                $.each(data['SubProducts'], function(k, v) {
                    $('#bulk_subproducts').append('<option value="' + v['SubProductUID'] + '">' + v['SubProductName'] + '</option>');
                });
                $('#bulk_subproducts').trigger('change');

            }else{
                $('#bulk_subproducts').parent().removeClass('is-focused');
            }

            if(data['Projects'].length > 0){
                $.each(data['Projects'], function(k, v) {
                    $('#bulk_projects').append('<option value="' + v['ProjectUID'] + '">' + v['ProjectName'] + '</option>');
                });
            }else{
                $('#bulk_projects').parent().removeClass('is-focused');
            }

            // componentHandler.upgradeDom();
            // select_mdl();
            $('.spinnerclass').removeClass("be-loading-active");
        }
    })

});

  //preview bulk entry
  $('#preview').click(function(event) {
    event.preventDefault();
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();

    var file_data = $('#filebulk_entry').prop('files')[0];
    var form_data = new FormData();
    form_data.append('file', file_data);
    form_data.append('CustomerUID', $('#bulk_Customers').val());
    form_data.append('ProductUID', $('#bulk_products').val());
    form_data.append('SubProductUID', $('#bulk_subproducts').val());
    form_data.append('BorrowerType', $('#bulk_BorrowerType').val());
    form_data.append('PropertyType', $('#bulk_PropertyType').val());
    form_data.append('TransactionType', $('#bulk_TransactionType').val());
    form_data.append('bundledsubproductenabled', $('#bundledsubproductenabled').is( ':checked' ) ? 1: 0);;


    // $.each(excelmultiplefileupload_Obj, function (key, value) {
    //     form_data.append('MIME_FILES[]', value.filename);
    // });

    $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>Order_entry/preview_bulkentry',
        data: form_data,
        processData: false,
        contentType: false,
        cache:false,

        beforeSend: function(){
            $('.spinnerclass').addClass("be-loading-active");  
            button.attr("disabled", true);
            button.html('Loading ...'); 
        },
        success: function(data)
        { 
            $('.spinnerclass').removeClass("be-loading-active");
            $('#preview-table').html('');
            try {
                obj = JSON.parse(data);
                // $.gritter.add({
                //     title: obj['message'],
                //     class_name: 'color danger',
                //     fade: true,
                //     time: 1000,
                //     speed:'fast',

                // });
                toastr['error']("", obj['message']);
            } catch (e) {
                $('#imported-table').html('');
                $('#preview-table').html(data);
            }

            button.html('Preview');
            button.removeAttr("disabled");

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);

        },
        failure: function (jqXHR, textStatus, errorThrown) {

            console.log(errorThrown);

        },
    });
});

//save bulk entry
$(document).off('click', '#bulk_save').on('click', '#bulk_save', function(e){
    e.preventDefault();
    button = $(this);
    button_val = $(this).val();
    button_text = $(this).html();
    var file_data = $('#filebulk_entry').prop('files')[0];
    var form_data = new FormData();
    form_data.append('file', file_data);
    form_data.append('CustomerUID', $('#bulk_Customers').val());
    form_data.append('ProductUID', $('#bulk_products').val());
    form_data.append('SubProductUID', $('#bulk_subproducts').val());
    form_data.append('ProjectUID', $('#bulk_projects').val());
    form_data.append('BorrowerType', $('#bulk_BorrowerType').val());
    form_data.append('PropertyType', $('#bulk_PropertyType').val());
    form_data.append('TransactionType', $('#bulk_TransactionType').val());
    form_data.append('bundledsubproductenabled', $('#bundledsubproductenabled').is( ':checked' ) ? 1: 0);
    form_data.append('chk_mailing','property');

    // $.each(excelmultiplefileupload_Obj, function (key, value) {
    //     form_data.append('MIME_FILES[]', value.file);
    // });

    $.ajax({
        type: "POST",
        url: '<?php echo base_url();?>Order_entry/save_bulkentry',
        data: form_data,
        processData: false,
        contentType: false,
        cache:false,
        beforeSend: function(){
            $('.spinnerclass').addClass("be-loading-active");
            button.attr("disabled", true);
            button.html('Loading ...'); 
        },
        success: function(data)
        {
            button.html('save'); 
            button.removeAttr('disabled');
            $('.spinnerclass').removeClass("be-loading-active");
            try {
                obj = JSON.parse(data);
                // $.gritter.add({
                //     title: obj['message'],
                //                             // text: data['message'],
                //                             class_name: 'color danger',
                //                             fade: true,
                //                             time: 1000,
                //                             speed:'fast',

                //                         });
                 toastr['error']("", obj['message']);
            } catch (e) {
                $('#preview-table').html('');
                $('#imported-table').html(data);
                                        //  $('#modal-data').html(data); 
                                        //  $('#md-modal').modal('show');
                                        //  $("#md-modal").on("hidden.bs.modal", function () {
                                        //  window.location.replace("<?php echo base_url();?>orderentry");
                                        // });
                                    }
                                    $('.dropify-clear').click();



                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    console.log(errorThrown);

                                },
                                failure: function (jqXHR, textStatus, errorThrown) {

                                    console.log(errorThrown);

                                },
                            });
});



</script>