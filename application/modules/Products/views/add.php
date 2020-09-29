<!--BEGIN CONTENT-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/lib/fSelect-Selectall/fSelect.css">
<!-- start body header -->
<div id="page_top" class="section-body">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Products</h1> 
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
<?php if($Action == "EDIT") { ?>
<div class="section-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">

                    <div class="card-header">
                        <div class="col-md-12 col-lg-12 p-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="<?php echo base_url(); ?>Products/edit_products/<?php echo $ProductsDetails->ProductUID?>" aria-expanded="true">Edit Product</a></li>
                                <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="<?php echo base_url(); ?>Products/auditlog/<?php echo $ProductsDetails->ProductUID?>" aria-expanded="true">Audit Log</a></li>
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
                                    <h5>Edit Product</h5>
                                <form action="#" name="frm_Products" id="frm_Products">
                                <input type="hidden" name="ProductUID" id="ProductUID" value="<?php echo $ProductsDetails->ProductUID?>" />

                                    <div class="row">

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Product Code</label>
                                                <input type="text" class="form-control" id="ProductCode" maxlength="4" name="ProductCode" value="<?php echo $ProductsDetails->ProductCode?>" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Product Name <sup style="color:red;">*</sup></label>
                                                <input type="text" class="form-control" id="ProductName" name="ProductName" value="<?php echo $ProductsDetails->ProductName?>" maxlength="50" placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Avalilable WorkFlow</label>
                                                <select name="beast" class="form-control select2">
                                                    <option></option>
                                                    <option value="1">Assignments</option>
                                                    <option value="1">Assignments</option>
                                                </select>
                                            </div>
                                        </div>
                                       <!--  <div class="col-md-8 col-lg-8 mt-4 pt-1"> -->
                                           <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="custom-control custom-checkbox custom-control-inline">
                                                   <?php if($ProductsDetails->IsDynamicProduct==0): ?>
                                                    <input type="checkbox" class="custom-control-input"  name="IsDynamicProduct" id="IsDynamicProduct">
                                                    <?php elseif($ProductsDetails->IsDynamicProduct==1): ?>
                                                      <input type="checkbox" class="custom-control-input"  name="IsDynamicProduct" id="IsDynamicProduct" checked="true"> 
                                                  <?php endif; ?> 
                                                  <span class="custom-control-label">Is Dynamic</span>
                                              </label>
                                          </div>
                                      </div>
                                      <div class="col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <?php if($ProductsDetails->IsFloodProduct==0): ?>
                                                    <input type="checkbox" class="custom-control-input" name="IsFloodProduct" id="IsFloodProduct">
                                                    <?php elseif($ProductsDetails->IsFloodProduct==1): ?>
                                                        <input type="checkbox" class="custom-control-input" name="IsFloodProduct" id="IsFloodProduct" checked="true">
                                                    <?php endif; ?>
                                                    <span class="custom-control-label">Is Flood Product</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="custom-control custom-checkbox custom-control-inline">
                                                    <?php if($ProductsDetails->AdverseConditionEnable==0): ?>
                                                        <input type="checkbox" class="custom-control-input" name="AdverseConditionEnable" id="AdverseConditionEnable">
                                                        <?php elseif($ProductsDetails->AdverseConditionEnable==1): ?>
                                                            <input type="checkbox" class="custom-control-input" name="AdverseConditionEnable" id="AdverseConditionEnable" checked="true">
                                                        <?php endif; ?>
                                                        <span class="custom-control-label">Adverse Conditions</span>
                                                    </label>
                                                </div>
                                            </div>
                                            
                                        

                                    </div>
                                </div>
                            </br>
                                    <h5>Multiple Pricing</h5>
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="custom-control custom-checkbox custom-control-inline">
                                                    <?php if($ProductsDetails->Active==0): ?>
                                                        <input type="checkbox" class="custom-control-input" name="Active" id="Active" >
                                                        <?php elseif($ProductsDetails->Active==1): ?>
                                                            <input type="checkbox" class="custom-control-input" name="Active" id="Active"  checked="true">
                                                        <?php endif; ?>
                                                        <span class="custom-control-label">Active</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-4">
                                                <div class="form-group">
                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                        <?php if($ProductsDetails->InsuranceType==0): ?>
                                                            <input type="checkbox" class="custom-control-input" name="InsuranceType" id="InsuranceType" >
                                                            <?php elseif($ProductsDetails->InsuranceType==1): ?>
                                                                <input type="checkbox" class="custom-control-input" name="InsuranceType" id="InsuranceType"  checked="true">
                                                            <?php endif; ?>
                                                            <span class="custom-control-label">Insurance Type</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label class="custom-control custom-checkbox custom-control-inline">
                                                           <?php if($ProductsDetails->AgentPricing==0): ?>
                                                            <input type="checkbox" class="custom-control-input" name="AgentPricing" id="AgentPricing" >
                                                            <?php elseif($ProductsDetails->AgentPricing==1): ?>
                                                                <input type="checkbox" class="custom-control-input" name="AgentPricing" id="AgentPricing" checked="true">
                                                            <?php endif; ?>
                                                            <span class="custom-control-label">Agent Pricing</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label class="custom-control custom-checkbox custom-control-inline">
                                                            <?php if($ProductsDetails->IsSelfAssign==0): ?>
                                                                <input type="checkbox" class="custom-control-input" name="IsSelfAssign" id="IsSelfAssign"  >
                                                                <?php elseif($ProductsDetails->IsSelfAssign==1): ?>
                                                                    <input type="checkbox" class="custom-control-input" name="IsSelfAssign" id="IsSelfAssign" checked="true"  >
                                                                <?php endif; ?>
                                                                <span class="custom-control-label">Is Self Assign</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-lg-4">
                                                        <div class="form-group">
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <?php if($ProductsDetails->UnderWritingPricing==0): ?>
                                                                    <input type="checkbox" class="custom-control-input"  name="UnderWritingPricing" id="UnderWritingPricing">
                                                                    <?php elseif($ProductsDetails->UnderWritingPricing==1): ?>
                                                                        <input type="checkbox" class="custom-control-input"  name="UnderWritingPricing" id="UnderWritingPricing" checked="true" >
                                                                    <?php endif; ?>
                                                                    <span class="custom-control-label">Under Writing Pricing</span>
                                                                </label>
                                                            </div>
                                                        </div>
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
                        </div> -->
                    </div>

                    <div class="card-header">
                        <div class="col-md-12 col-lg-12 mb-3 mt-2">
                            <div class="btn-list right">
                               <!--  <a href="#" class="btn btn-secondary">Cancel</a> -->
                                <button href="#" class="btn btn-primary" id="BtnSaveProducts">Update Product</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             </form>
        </div>
    </div>
</div>
<?php }elseif ($Action == "ADD") { ?>
  <div class="section-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">

                    <div class="card-header">
                        <div class="col-md-12 col-lg-12 p-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-subproduct" aria-expanded="true">Add Product</a></li>
                               <!--  <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="#audit-log" aria-expanded="true">Audit Log</a></li> -->
                            </ul>
                        </div>
                        <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>

                    <div class="tab-content">
                <form action="#" name="frm_Products" id="frm_Products">
                                <input type="hidden" name="ProductUID" id="ProductUID" value="<?php echo $ProductsDetails->ProductUID?>" />
                        <div class="tab-pane fade active show" id="add-subproduct">
                            <div class="card-body">
                                <div class="col-md-12 col-lg-12 add-product-div p-0">
                                    <h5>Product</h5>

                                    <div class="row">

                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Product Code</label>
                                               <input class="form-control" type="text" id="ProductCode" maxlength="4" name="ProductCode" value="" >
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Product Name <sup style="color:red;">*</sup></label>
                                                 <input class="form-control" type="text" id="ProductName" name="ProductName" value="" maxlength="50" >
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Avalilable WorkFlow</label>
                                                <select name="beast" class="form-control select2">
                                                    <option></option>
                                                    <option value="1">Assignments</option>
                                                    <option value="1">Assignments</option>
                                                </select>
                                            </div>
                                        </div>
                                       <!--  <div class="col-md-8 col-lg-8 mt-4 pt-1"> -->
                                             <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" id="IsDynamicProduct" name="IsDynamicProduct" value="" >
                                                <span class="custom-control-label">Is Dynamic</span>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" id="IsFloodProduct" name="IsFloodProduct" value="" >
                                                <span class="custom-control-label">Is Flood Product</span>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" id="AdverseConditionEnable" name="AdverseConditionEnable" value="" >
                                                <span class="custom-control-label">Adverse Conditions</span>
                                            </label>
                                        </div>
                                    </div>
                                            <!-- <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option2" >
                                                <span class="custom-control-label">Reverse Mortgage</span>
                                            </label>
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option3" >
                                                <span class="custom-control-label">Refinance</span>
                                            </label> -->
                                           
                                       <!--  </div> -->
                                       
                                        

                                    </div>
                                </div>
                            </br>
                                    <h5>Multiple Pricing</h5>
                                    <div class="row">
                                         <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" id="InsuranceType" name="InsuranceType" value="">
                                                <span class="custom-control-label">Insurance Type</span>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" id="AgentPricing" name="AgentPricing" value="">
                                                <span class="custom-control-label">Agent Pricing</span>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" id="IsSelfAssign" name="IsSelfAssign" value="">
                                                <span class="custom-control-label">Is Self Assign</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" id="UnderWritingPricing" name="UnderWritingPricing" value="">
                                                <span class="custom-control-label">Under Writing Pricing</span>
                                            </label>
                                        </div>
                                    </div>
                                    </div>
                                </div>

                            

                            </div>

                        </div>
                      
                    </div>

                    <div class="card-header">
                        <div class="col-md-12 col-lg-12 mb-3 mt-2">
                            <div class="btn-list  text-right">
                                <!-- <a href="#" class="btn btn-secondary">Cancel</a> -->
                                <button href="#" class="btn btn-primary" id="BtnSaveProducts">Save Products</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<?php } ?>
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
    
   $('#BtnSaveProducts').click(function(event)
        {
          event.preventDefault();
          if($('#ProductName').val()!=0){

              var form=$('#frm_Products');
                var formData=$('#frm_Products').serialize();

                $.ajax({
                     type: "POST",
                     url: '<?php echo base_url();?>Products/save_products',
                     data: formData, 
                     dataType:'json',
                     cache: false,
                     success: function(data)
                     {
                        console.log(data);
                        //$('#frm_Products')[0].reset();
                    toastr["success"]("", data['msg']);
                    window.location.replace('<?php echo base_url(); ?>Products');
                     }
                   });
          }
          else
            
          {
              toastr["error"]("", 'Please Fill the Manatory Fields');
           
              }
      });


    $('#ProductCode').change(function(event) {

    ProductCode = $(this).val();
    $('.loading').show()

    $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>Products/GetProductName',
     data: {'ProductCode':ProductCode}, 
     dataType:'json',
     cache: false,
     success: function(data)
     {

      $('.loading').hide();


      $('#ProductName').val('');
      $('#ProductName').parent().removeClass('is-dirty');

      if(data != ''){

        $('#ProductName').val(data['ProductName']);

        $('#ProductName').parent().addClass('is-dirty');
      }


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
