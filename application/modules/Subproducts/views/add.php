<!--BEGIN CONTENT-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/lib/fSelect-Selectall/fSelect.css">
<!-- start body header -->
<div id="page_top" class="section-body">
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
<?php if($Action == "EDIT"){ ?>
    <div class="section-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="col-md-12 col-lg-12 p-0">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-subproduct" aria-expanded="true">Edit Sub Product</a></li>
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
                                        <input type="hidden" name="SubProductUID" id="SubProductUID" value="<?php echo $SubproductDetails->SubProductUID?>" />
                                        <div class="row">

                                            <div class="col-md-4 col-lg-4">
                                                <div class="form-group">
                                                    <label class="form-label">Sub Product Code</label>
                                                    <input class="form-control mdl-textfield__input input-xs" type="text" id="SubProductCode" name="SubProductCode" value="<?php echo $SubproductDetails->SubProductCode?>" maxlength="4">
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-4">
                                                <div class="form-group">
                                                    <label class="form-label">Sub Product Name <sup style="color:red;">*</sup></label>
                                                    <input class="form-control mdl-textfield__input input-xs" type="text" id="SubProductName" name="SubProductName" value="<?php echo $SubproductDetails->SubProductName?>" maxlength="50" >
                                                </div>
                                            </div>
                                            <input type="hidden" name="Product" class="Product" id="Product" value="<?php echo $SubproductDetails->ProductUID;?>">
                                            <div class="col-md-4 col-lg-4">
                                                <div class="form-group">
                                                    <label class="form-label">Product</label>
                                                    <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="ProductUID" name="ProductUID" >
                                                        <option></option>
                                                        <?php 
                                                        foreach ($ProductDetails as $row) 
                                                        {
                                                            if($row->ProductUID==$SubproductDetails->ProductUID)
                                                                echo "<option value='".$row->ProductUID."' selected>".$row->ProductName."</option>";
                                                            else
                                                              echo "<option value='".$row->ProductUID."'>".$row->ProductName."</option>";
                                                      }
                                                      ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Default Assignment Type</label>
                                                <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="OrderTypeUID" name="OrderTypeUID" >
                                                    <option></option>
                                                    <?php 
                                                    foreach ($OrderTypeDetails as $row) 
                                                    {
                                                        if($row->OrderTypeUID==$SubproductDetails->OrderTypeUID)
                                                            { ?>
                                                              <option value="<?php echo $row->OrderTypeUID;?>" selected><?php echo $row->OrderTypeName;?></option>
                                                          <?php } else
                                                          {?>
                                                              <option value="<?php echo $row->OrderTypeUID;?>"><?php echo $row->OrderTypeName;?></option>
                                                          <?php }
                                                      }
                                                      ?>
                                                  </select>
                                              </div>
                                          </div>
                                          <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Default Priority</label>
                                                <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="PriorityUID" name="PriorityUID" >
                                                    <option></option>
                                                    <?php 
                                                    foreach ($PriorityDetails as $row) 
                                                    { 
                                                      if($row->PriorityUID==$SubproductDetails->PriorityUID)
                                                        {?>

                                                          <option value="<?php echo $row->PriorityUID;?>" selected><?php echo $row->PriorityName;?></option>
                                                      <?php }else{?>

                                                          <option value="<?php echo $row->PriorityUID;?>"><?php echo $row->PriorityName;?></option>
                                                      <?php }

                                                  }
                                                  ?>
                                              </select>
                                          </div>
                                      </div>
                                      <div class="col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label">Report Heading</label>
                                            <input class="form-control mdl-textfield__input input-xs" type="text" id="ReportHeading" name="ReportHeading" value="<?php echo $SubproductDetails->ReportHeading?>" maxlength="50" >
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label">Default Duration (Minutes)</label>
                                            <input class="form-control mdl-textfield__input input-xs" type="text" id="ScheduleDuration" name="ScheduleDuration" value="<?php echo $SubproductDetails->ScheduleDuration?>" maxlength="50" >
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-8 mt-4 pt-1">
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" id="task_management" checked>
                                            <span class="custom-control-label">Task Management</span>
                                        </label>
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" id="RMS" name="RMS" <?php echo ($SubproductDetails->RMS == 1) ? 'checked=""' : '' ; ?> >
                                            <span class="custom-control-label">Reverse Mortgage</span>
                                        </label>
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" id="IsRefinance" name="IsRefinance"  <?php echo ($SubproductDetails->IsRefinance == 1) ? 'checked=""' : '' ; ?>>
                                            <span class="custom-control-label">Refinance</span>
                                        </label>
                                        <label class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" class="custom-control-input" name="Active" id="active_sub_prod"  <?php echo ($SubproductDetails->Active == 1) ? 'checked=""' : '' ; ?>>
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
                                                        <?php foreach($SubProductTasks as $key => $SubProductTask):?>
                                                            <tr>
                                                                <td>
                                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                                        <input type="checkbox" class="custom-control-input" id="chkChoose<?php echo $key; ?>" <?php echo !empty($SubProductTask->SubProductTaskUID) ? "checked" : "" ?> class="chkChoose" name="choose[<?php echo $SubProductTask->TaskUID; ?>]">

                                                                        <span class="custom-control-label"></span>
                                                                    </label>
                                                                </td>
                                                                <td><?php echo $SubProductTask->ShortDescription; ?></td>
                                                                <td>
                                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                                        <input type="checkbox" class="custom-control-input" id="auto_creation<?php echo $key; ?>" name="auto_creation[<?php echo $SubProductTask->TaskUID; ?>]" <?php if($SubProductTask->AutoCreation == 1):echo 'checked';endif;?>>

                                                                        <span class="custom-control-label"></span>
                                                                    </label>
                                                                </td>
                                                                <td>
                                                                 <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs previous_task" id="previous_task<?php echo $key; ?>" name="previous_task[<?php echo $SubProductTask->TaskUID; ?>]" >
                                                                  <option></option>
                                                                  <?php foreach($Tasks as $Task):?>
                                                                      <option value="<?php echo $Task->TaskUID;?>" <?php if($SubProductTask->PreviousTaskUID == $Task->TaskUID):echo 'selected';endif;?>><?php echo $Task->ShortDescription;?></option>
                                                                  <?php endforeach;?>
                                                              </select>
                                                          </td>
                                                          <td>
                                                            <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs actiontype" id="actiontype" name="actiontype[<?php echo $SubProductTask->TaskUID; ?>]" >
                                                                <option></option>
                                                                <option value="Order Open" <?php if($SubProductTask->Action == 'Order Open'):echo 'selected';endif;?>>Order Open</option>
                                                                <option value="Task Open" <?php if($SubProductTask->Action == 'Task Open'):echo 'selected';endif;?>>Task Open</option>
                                                                <option value="Task Complete" <?php if($SubProductTask->Action == 'Task Complete'):echo 'selected';endif;?>>Task Complete</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="template-setup">
                                <div id="Div_TemplateMapping">
                                    <div class="row m-0">
                                        <input type="hidden" name="Inp_SubProductName"  id="Inp_SubProductName" class="Inp_SubProductName">
                                        <input type="hidden" name="Inp_ProductName"  id="Inp_ProductName" class="Inp_ProductName">
                                        <div class="col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Mapping Level</label>
                                                <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="MappingLevel" name="MappingLevel" >
                                                    <option></option>
                                                    <option value="1">SubProduct Level</option>
                                                    <option value="2">State Level</option>
                                                    <option value="3">County Level</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div  id="LevelMapping">
                                        <div class="row m-0 edit-filed-level" id="SubProductLevel" style="display:none;">
                                            <table id="search_table" class="table table-responsive search_table" style="width:100%;">
                                                <tbody class="Div_SubProductLevel">
                                                    <tr>
                                                        <td width="30%">
                                                            <div class="form-group">

                                                                <label class="form-label">Templates </label>
                                                                <select class="form-control select2 TemplateUID validate" id="TemplateUID" name="TemplateUID">
                                                                    <option></option>
                                                                    <option value='Template'>Add Template</option>
                                                                    <?php foreach($TemplateDetails as $row){ echo "<option value=".$row->TemplateUID.">".$row->TemplateName."</option>"; } ?>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td width="30%">
                                                            <div class="form-group">
                                                                <label class="form-label">Document Type</label>
                                                                <input class="form-control mdl-textfield__input input-xs DocumentType" type="text" id="DocumentType" name="DocumentType" value="">
                                                            </div>
                                                        </td>
                                                        <td width="30%">
                                                            <div class="form-group">
                                            <div class="form-group">
                                                <label class="form-label">Select Fields </label>
                                                <select class="form-control select2 FieldUID validate" id="FieldUID" name="FieldUID[]" multiple>
                                                  <option></option>
                                                  <option value='Field'>Add Field</option>
                                                  <?php foreach($mfields as $row){ echo "<option value=".$row->FieldUID.">".$row->FieldName."</option>"; } ?>
                                              </select>
                                          </div>
                                          
                                      </div>
                                                      </td>
                                                      <td>
                                                        <div class="col-md-2 col-lg-2 text-right">
                                                            <a style="cursor:pointer;" data-toggle="modal" data-target="#TemplateMapping" class="btn btn-info btn-sm mt-4 report_view" id="report_view"> Configure Fields</a>
                                                            <input type="hidden" data-File-Name="FieldUID" id="FieldRow" name="FieldRow" class="FieldRow">
                                                            <input type="hidden" id="TemplateFileName" name="TemplateFileName" class="FieldUID_0 TemplateFileName" data-id="FieldUID_0">

                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                
                                    <div class="row m-0 edit-filed-level" id="StateLevel" style="display:none;">
                                       <table id="search_table" class="table table-responsive search_table" style="width:100%;">
                                        <tbody class="Div_StateLevel">
                                            <tr>
                                              <td width="30%">
                                        
                                            <div class="form-group">
                                                <label class="form-label">State </label>
                                                <select class="form-control select2 StateUID validate" id="StateUID" name="StateUID[]" multiple>
                                              <option></option>
                                              <?php foreach($States as $row){ echo "<option value=".$row->StateUID.">".$row->StateCode."</option>"; } ?>
                                          </select>
                                            </div>
                                        
                                    </td>
                                    <td width="30%">
                                        
                                            <div class="form-group">
                                                <label class="form-label">Templates </label>
                                                <select class="form-control select2 TemplateUID validate" id="TemplateUID" name="TemplateUID">
                                                    <option></option>
                                                    <option value='Template'>Add Template</option>
                                                    <?php foreach($TemplateDetails as $row){ echo "<option value=".$row->TemplateUID.">".$row->TemplateName."</option>"; } ?>
                                                </select>
                                            </div>
                                        
                                    </td>
                                    <td width="30%">
                                        
                                            <div class="form-group">
                                                <label class="form-label">Document Type</label>
                                               <input class="form-control mdl-textfield__input input-xs DocumentType" type="text" id="DocumentType" name="DocumentType" value="">
                                            </div>
                                        
                                    </td>
                                    <td width="40%">
                                        
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <label class="form-label">Select Fields </label>
                                                     <select class="form-control select2 FieldUID validate" id="FieldUID" name="FieldUID[]" multiple>
                                              <option></option>
                                              <option value='Field'>Add Field</option>
                                              <?php foreach($mfields as $row){ echo "<option value=".$row->FieldUID.">".$row->FieldName."</option>"; } ?>
                                          </select>
                                                </div>
                                            </div>
                                       
                                    </td>
                                    <td>
                                        <div class="col-md-2 col-lg-2 text-right">
                                            <a style="cursor:pointer;" data-toggle="modal" data-target="#TemplateMapping" class="btn btn-info btn-sm mt-4 report_view" id="report_view"> Configure Fields</a>
                                            <input type="hidden" data-File-Name="FieldUID" id="FieldRow" name="FieldRow" class="FieldRow">
                                            <input type="hidden" id="TemplateFileName" name="TemplateFileName" class="FieldUID_0 TemplateFileName" data-id="FieldUID_0">
                                            
                                        </div>
                                    </td>
                                       <!--  <div class="col-md-12 col-lg-12 text-right" id="Buttons">
                                           <a href="#." class="btn btn-success btn-sm mt-1 mb-4"> Update Template Mapping</a> 
                                            <button type="submit" class="btn btn-success btn-space" id="Complete"><i class="fa fa-plus"></i> Map Template</button>
                                            <button type="submit" class="btn btn-success btn-space" id="Update_Complete"> Update Template Mapping</button>
                                        </div> -->
                                    </tr>
                                </tbody>
                            </table>
                                    </div>
                                     <div class="row m-0 edit-filed-level" id="CountyLevel" style="display:none;">
                                       <table id="search_table" class="table table-responsive search_table" style="width:100%;">
                                          <tbody class="Div_CountyLevel">
                                            <tr>
                                              <td width="30%">
                                        
                                            <div class="form-group">
                                                <label class="form-label">State </label>
                                                <select class="form-control select2 StateUID validate" id="StateUID" name="StateUID[]" multiple>
                                              <option></option>
                                              <?php foreach($States as $row){ echo "<option value=".$row->StateUID.">".$row->StateCode."</option>"; } ?>
                                          </select>
                                            </div>
                                      
                                    </td>
                                    <td width="30%">
                                        
                                            <div class="form-group">
                                                <label class="form-label">County </label>
                                                 <select class="mdl-select2 select2 mdl-textfield__input input-xs CountyUID validate" name="CountyUID[]" id="CountyUID" multiple></select>
                                            </div>
                                        
                                    </td>
                                    <td width="30%">
                                        
                                            <div class="form-group">
                                                <label class="form-label">Templates </label>
                                                <select class="form-control select2 TemplateUID validate" id="TemplateUID" name="TemplateUID">
                                                    <option></option>
                                                    <option value='Template'>Add Template</option>
                                                    <?php foreach($TemplateDetails as $row){ echo "<option value=".$row->TemplateUID.">".$row->TemplateName."</option>"; } ?>
                                                </select>
                                            </select>
                                            </div>
                                       
                                    </td>
                                    <td width="30%">

                                       
                                            <div class="form-group">
                                                <label class="form-label">Document Type</label>
                                                <input type="text" class="form-control" name="" placeholder="">
                                            </div>
                                       
                                    </td>
                                    <td width="30%">
                                        
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label class="form-label">Select Fields </label>
                                                <select class="form-control select2 FieldUID validate" id="FieldUID" name="FieldUID[]" multiple>
                                                  <option></option>
                                                  <option value='Field'>Add Field</option>
                                                  <?php foreach($mfields as $row){ echo "<option value=".$row->FieldUID.">".$row->FieldName."</option>"; } ?>
                                              </select>
                                          </div>
                                          
                                      </div>

                                    </td>
                                    <td>
                                        <div class="col-md-2 col-lg-2 text-right">
                                            <a style="cursor:pointer;" data-toggle="modal" data-target="#TemplateMapping" class="btn btn-info btn-sm mt-4 report_view" id="report_view"> Configure Fields</a>
                                            <input type="hidden" data-File-Name="FieldUID" id="FieldRow" name="FieldRow" class="FieldRow">
                                        <input type="hidden" id="TemplateFileName" name="TemplateFileName" class="FieldUID_0 TemplateFileName" data-id="FieldUID_0">
                                            
                                        </div>
                                    </td>
                                       </tr>
                                   </tbody>
                               </table>
                                    </div>
                                </div>
                                     <div class="col-md-12 col-lg-12 text-right" id="Buttons">
                                           <!--  <a href="#." class="btn btn-success btn-sm mt-1 mb-4"> Update Template Mapping</a> -->
                                            <button type="submit" class="btn btn-success btn-space" id="Complete"><i class="fa fa-plus"></i> Map Template</button>
                                            <button type="submit" class="btn btn-success btn-space" id="Update_Complete"> Update Template Mapping</button>
                                        </div>

                    <?php if(count($TemplateMappingDetails) > 0) { ?> 
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
                                                    <?php $i=1; foreach($TemplateMappingDetails as $row): ?>

                                              <?php 

                                              // Mapping Level 

                                                if($row['MappingLevel'] == 1){
                                                  $MappingLevel = "SubProduct Level";
                                                }
                                                else if($row['MappingLevel'] == 2){
                                                  $MappingLevel ="State Level";
                                                }
                                                else if($row['MappingLevel'] == 3){
                                                  $MappingLevel ="County Level";
                                                }

                                              //State

                                                $StateCode = [];

                                                $StateUID = $row['StateUID'];

                                                $StateUID_Explode = explode(',', $StateUID);

                                                foreach ($StateUID_Explode as $key => $value) {
                                                  $StateCode[] = $this->Subproducts_model->GetStatesBySubProduct($value); 
                                                }

                                                $StateCodeflatten = [];

                                                foreach ($StateCode as $childArray) {
                                                  foreach ($childArray as $value) {
                                                    $StateCodeflatten[] = $value;
                                                  }
                                                }

                                                $StateCode_Concat = implode(', ', $StateCodeflatten);

                                                if($StateCode_Concat == ''){
                                                  $StateCode_Final = "---";
                                                }
                                                else{
                                                  $StateCode_Final = implode(', ', $StateCodeflatten);
                                                }

                                              //County

                                                $CountyName = [];

                                                $CountyUID = $row['CountyUID'];

                                                $CountyUID_Explode = explode(',', $CountyUID);

                                                foreach ($CountyUID_Explode as $key => $value) {
                                                  $CountyName[] = $this->Subproducts_model->GetCountyBySubProduct($value); 
                                                }

                                                $CountyNameflatten = [];

                                                foreach ($CountyName as $childArray) {
                                                    foreach ($childArray as $value) {
                                                        $CountyNameflatten[] = $value;
                                                    }
                                                }

                                                $CountyName_Concat = implode(', ', $CountyNameflatten);

                                                if($CountyName_Concat == ''){
                                                  $CountyName_Final = "---";
                                                }
                                                else{
                                                  $CountyName_Final = implode(', ', $CountyNameflatten);
                                                }


                                                //Fields

                                                $FieldName = [];

                                                $FieldUID = $row['FieldUID'];

                                                $FieldUID_Explode = explode(',', $FieldUID);

                                                foreach ($FieldUID_Explode as $key => $value) {
                                                  $FieldName[] = $this->Subproducts_model->GetFieldBySubProduct($value); 
                                                }

                                                $FieldNameflatten = [];

                                                foreach ($FieldName as $childArray) {
                                                    foreach ($childArray as $value) {
                                                        $FieldNameflatten[] = $value;
                                                    }
                                                }

                                                $FieldName_Concat = implode(', ', $FieldNameflatten);


                                              ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $StateCode_Final; ?></td>
                                                        <td><?php echo $CountyName_Final; ?></td>
                                                        <td><?php echo $row['TemplateName']; ?></td>
                                                        <td><?php echo $row['DocumentType']; ?></td>
                                                        <td class="actions text-center">
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-icon edit-filed-level-btn button-edit text-primary edit_mapping"data-field-row="<?php echo $row['FieldRow']; ?>" data-subprod-uid="<?php echo $row['SubProductUID']; ?>" data-mapping-level="<?php echo $row['MappingLevel']; ?>" title="Edit"><i class="icon-pencil"></i></a>
                                                            <button class="btn btn-sm btn-icon text-danger delete" data-field-row="<?php echo $row['FieldRow']; ?>" data-subprod-uid="<?php echo $row['SubProductUID']; ?>" data-mapping-level="<?php echo $row['MappingLevel']; ?>" title="Delete"><i class="icon-trash"></i></button>
                                                            <button class="btn btn-sm btn-icon text-primary Copy_Fields" data-field-row="<?php echo $row['FieldRow']; ?>" data-subprod-uid="<?php echo $row['SubProductUID']; ?>" data-mapping-level="<?php echo $row['MappingLevel']; ?>" title="Delete"><i class="fe fe-clipboard"></i></button>
                                                        </td>
                                                    </tr>
                                                    <!-- <tr>
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
                                                    </tr> -->
                                                   <?php $i++; endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                    <?php } ?>
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
<?php } else { ?>
    <div class="section-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <form action="#" name="frm_SubProduct" id="frm_SubProduct">
                        <div class="card">

                            <div class="card-header">
                                <div class="col-md-12 col-lg-12 p-0">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item"><a class="nav-link active pt-2 pb-3" data-toggle="tab" href="#add-subproduct" aria-expanded="true">Add Sub Product</a></li>
                                        <!--  <li class="nav-item"><a class="nav-link pt-2 pb-3" data-toggle="tab" href="#audit-log" aria-expanded="true">Audit Log</a></li> -->
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
                                                        <input class="form-control mdl-textfield__input input-xs" type="text" id="SubProductCode" name="SubProductCode" maxlength="4" value="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Sub Product Name <sup style="color:red;">*</sup></label>
                                                        <input class="form-control mdl-textfield__input input-xs" type="text" id="SubProductName" name="SubProductName" value="" maxlength="50" >
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Product</label>
                                                        <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="ProductUID" name="ProductUID" >
                                                            <option></option>
                                                            <?php
                                                            foreach($ProductDetails as $row){
                                                                echo "<option value='".$row->ProductUID."'>".$row->ProductName."</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Default Assignment Type</label>
                                                        <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs" id="OrderTypeUID" name="OrderTypeUID" >
                                                            <option></option>
                                                            <?php 
                                                            foreach ($OrderTypeDetails as $row) 
                                                                {?>
                                                                  <option value="<?php echo $row->OrderTypeUID;?>"><?php echo $row->OrderTypeName;?></option>
                                                              <?php }
                                                              ?>
                                                          </select>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Default Priority</label>
                                                        <select class="form-control mdl-select2 select2 mdl-textfield__input input-xs " id="PriorityUID" name="PriorityUID" >
                                                            <option></option>
                                                            <?php 
                                                            foreach ($PriorityDetails as $row) 
                                                                {?>
                                                                  <option value="<?php echo $row->PriorityUID;?>"><?php echo $row->PriorityName;?></option>
                                                              <?php }
                                                              ?>
                                                          </select>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Report Heading</label>
                                                        <input class="form-control mdl-textfield__input input-xs" type="text" id="ReportHeading" name="ReportHeading" value="<?php echo $SubproductDetails->SubProductName?>" maxlength="50" >
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label">Default Duration (Minutes)</label>
                                                        <input class="form-control mdl-textfield__input input-xs" type="text" id="ScheduleDuration" name="ScheduleDuration" value="" maxlength="50" >
                                                    </div>
                                                </div>
                                                <div class="col-md-8 col-lg-8 mt-4 pt-1">
                                                   <!--  <label class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1" checked>
                                                        <span class="custom-control-label">Task Management</span>
                                                    </label> -->
                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input"  id="RMS" name="RMS" value="option2" >
                                                        <span class="custom-control-label">Reverse Mortgage</span>
                                                    </label>
                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input" id="IsRefinance" name="IsRefinance" value="" >

                                                        <span class="custom-control-label">Refinance</span>
                                                    </label>
                                                   <!--  <label class="custom-control custom-checkbox custom-control-inline">
                                                        <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option4" checked>
                                                        <span class="custom-control-label">Active</span>
                                                    </label> -->
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="card-header">
                                <div class="col-md-12 col-lg-12 mb-3 mt-2">
                                    <div class="btn-list  text-right">
                                        <a href="<?php echo base_url(); ?>Subproducts" class="btn btn-secondary">Cancel</a>
                                        <!-- <a href="#" class="btn btn-primary">Save</a> -->
                                        <button type="submit" class="btn btn-primary" id="BtnSaveProducts" >Save</button>
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
<div id="TemplateMapping" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
<!--                         <h3 class="card-title pt-2 mb-3">Map Fields</h3>
 -->                        <div class="col-md-12 col-lg-12 p-0 pr-1 scroll_field field-panel-div">

                            <!-- <div class="form-group field-add-select">
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
                            </div> -->
                           <!--  <button type="button" class="btn btn-primary btn-sm field-add-btn">Add</button> -->
                            <h6 style="float:left;width:100%;">Fields
                                <button class="btn btn-space btn-social btn-color btn-twitter EditFields pull-right" id="EditFields" style="margin-top: -10px;"> <i class="fa fa-pencil" aria-hidden="true"></i></button>
                                <button class="btn btn-space btn-social btn-color btn-success SaveFields pull-right" id="SaveFields" style="margin-top: -10px;"> <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                            </h6>
                            <input class="TemplateUID TempUID" type="hidden" name="TemplateUID" id="TemplateUID" >
                            <input class="DynamincFieldUID DynamincUID" type="hidden" name="DynamincFieldUID" id="DynamincFieldUID">
                            <input class="CopyText" id="CopyText" name="CopyText" type="text" value="" style="border:none;color:#fff;font-size: 1px;" >
                            <div class="scroll-field col-md-12 p-0">

                                <table class="table table-vcenter table-new text-nowrap mb-0" cellspacing="0" id="" style="width:100%;">
                                  <thead>
                                      <tr>
                                        <th>Field Name</th>
                                        <th class="text-center" style="width: 35%;">Action</th>
                                    </tr>
                                </thead>
                                    <tbody class="row_position" style="">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-8">

                        <h3 class="card-title pt-2 mb-3">Preview Panel</h3>
                        <button type="button" class="btn btn-info" id="ResetSubProductTemplate">Reset</button>
                        <button type="button" class="btn btn-primary" id="save">Save</button>
                        <textarea id="mymce1" name="content"></textarea>
                    </div>

                </div>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info">Reset</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div> -->
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
  //   if($("#mymce1").length > 0){
  //     tinymce.init({
  //       selector: "textarea#mymce1",
  //       theme: "modern",
  //       height: 400,
  //       statusbar: true,
  //       branding: false,
  //       content_style: ".mce-content-body {font-size:11px;font-family:Arial;}",
  //       fontsize_formats: "8px 9px 10px 11px 12px 14px 16px 18px 24px 36px",
  //       lineheight_formats: " 3pt 4pt 5pt 6pt 7pt 8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt",
  //       plugins: [
  //       "advlist autolink lists link charmap preview anchor",
  //       "searchreplace visualblocks code fullscreen",
  //       "insertdatetime table contextmenu paste template jbimages",
  //       "pagebreak","textcolor","colorpicker", "textpattern","lineheight"
  //       ],
  //       toolbar: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | table | jbimages',
  //       pagebreak_separator: '<span style="page-break-after: always;"></span>',
  //       setup: function (editor) {        
  //       }
  //   });
  // }

  $(document).ready(function(){
   // if($("#mymce1").length > 0){

     tinymce.init({
        selector: "textarea#mymce1",
        theme: "modern",
        height: 800,
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
        pagebreak_separator: '<span style="page-break-after: always;"></span>',
        setup: function (editor) {        
        }
    });

    $(document).on('focusin', function(e) {
      if ($(e.target).closest(".mce-window").length) {
        e.stopImmediatePropagation();
      }
    });
  // } 
})

  $('#BtnSaveProducts').click(function(event)
  {

      event.preventDefault();
      button = $(this);
      button_text = $(this).html();


      var form=$('#frm_SubProduct');

      // form.validate();

      var formData=$('#frm_SubProduct').serialize();

      $.ajax({
         type: "POST",
         url: '<?php echo base_url();?>subproducts/save_subproducts',
         data: formData, 
         dataType:'json',
         cache: false,
         beforeSend: function()
         { 
            button.attr("disabled", true);
            button.html('Processing <i class="fa fa-spinner fa-spin"></i>');
        },
        success: function(data)
        {
                        //$('#frm_SubProduct')[0].reset();
                        if(data.validation_error == 1)
                        {
                          toastr["error"]("", data['message']);
                          $.each(data, function(k, v) {
                          // $('#'+k).nextAll('span:first').html(v);
                          $('#'+ k +'.select2').next().find('span.select2-selection').addClass('errordisplay'); 
                          $('#'+k).closest('div.is_error').addClass('is-invalid');

                      });
                          button.html(button_text);
                          button.removeAttr("disabled");
                      }else{
                        toastr["success"]("", data['message']);
                        window.location.replace('<?php echo base_url(); ?>subproducts');

                      //     $.gritter.add({
                      //       title: data.msg,
                      //       class_name: 'color success',
                      //       fade: true,
                      //       speed:'fast',
                      //       time : 25,
                      //       after_close: function()
                      //       {
                      //         window.location.replace('<?php echo base_url(); ?>subproducts');
                      //     }

                      // });
                  }
              }
          });


  });

  $('#SubProductCode').change(function(event) {

    SubProductCode = $(this).val();
    $('.loading').show()

    $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>subproducts/GetSubProductName',
     data: {'SubProductCode':SubProductCode}, 
     dataType:'json',
     cache: false,
     success: function(data)
     {

      $('.loading').hide();     

      $('#SubProductName').val('');
      $('#ProductUID').empty();

      $('#SubProductName').parent().removeClass('is-dirty');
      $('#ProductUID').parent().removeClass('is-dirty');

      if(data != '' && data['validation_error'] == 0 ){    

        $('#SubProductName').val(data['details']['SubProductName']);

        $('#ProductUID').append('<option value="' + data['details']['ProductUID'] + '" selected>' + data['details']['ProductName'] + '</option>').trigger('change');


        $('#SubProductName').parent().addClass('is-dirty');
        $('#ProductUID').parent().addClass('is-dirty');
    }

    if(data != '' && data['validation_error'] == 1 ){
      $('#SubProductName').val('');
      $.each(data['details'], function(k, v) {

        $('#ProductUID').append('<option value="' + v['ProductUID'] +'">' + v['ProductName'] + '</option>');
        $('#ProductUID').parent().addClass('is-dirty');

    });

      $('#SubProductName').parent().removeClass('is-dirty');

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
    $('#TemplateMapping').hide();
    $('#DivAddTax').hide();
    $('#DivAddField').hide(); 
    $('#DivAddTaxAfter').hide();
    $('#StateLevel').hide();
    $('#CountyLevel').hide();
    $('#Update_Complete').hide();
    $('#Buttons').hide();
    //$('#StateLevel_table').hide();
    $('.BtnCompleteMapping').hide();
    //$('#Complete').hide();

 $('body').on('change','#MappingLevel', function(event) {
      var Level = $(this).val();
      if(Level == '1'){
        $('#SubProductLevel').show();
        $('#Buttons').show();
        $('#StateLevel').hide();
        $('#CountyLevel').hide();
      }

      if(Level == '2'){
        $('#SubProductLevel').hide();
        $('#Buttons').show();
        $('#StateLevel').show();
        $('#CountyLevel').hide();
      } 

      if(Level == '3'){
        $('#SubProductLevel').hide();
        $('#Buttons').show();
        $('#StateLevel').hide();
        $('#CountyLevel').show();
      } 

      if(Level == ''){
        $('#SubProductLevel').hide();
        $('#Buttons').hide();
        $('#StateLevel').hide();
        $('#CountyLevel').hide();

        $('#MappingLevel').empty();
        $('#MappingLevel').append('<option></option>'); 
        $('#MappingLevel').append('<option value="1">SubProduct Level</option>'); 
        $('#MappingLevel').append('<option value="2">State Level</option>'); 
        $('#MappingLevel').append('<option value="3">County Level</option>'); 

      }
    }); 


    // Save Template Mapping

    $('#Complete').click(function(e){

        e.preventDefault();

        var SubProductUID = $('#SubProductUID').val();
        var MappingLevel = $('#MappingLevel').val();  
        var SubProductCode = $('#SubProductCode').val();  
        var SubProductName = $('#SubProductName').val();  
        var ProductUID = $('#ProductUID').val();  
        var ReportHeading = $('#ReportHeading').val();  

        var SubProductDetails = []; 
        var SubProductLevel_Array = []; 
        var StateLevel_Array = []; 
        var CountyLevel_Array = []; 

        SubProductDetails.push({
          SubProductUID :  $('#SubProductUID').val(),
          MappingLevel :  $('#MappingLevel').val()
        });

        var FieldPosition = []; 

        $('.row_position tr').each(function(key,value){
          var Position = $(value).attr('data-postion');
          var FieldUID = $(value).attr('data-fielduid');

          FieldPosition.push({
            Position : $(value).attr('data-postion'),
            FieldUID : $(value).attr('data-fielduid'),
          });
        });

        if(MappingLevel == 1){
          $('.Div_SubProductLevel tr').each(function(key,value){

            var TemplateUID = $(value).find('#TemplateUID').val();
            var FieldUID = $(value).find('#FieldUID').val();
            var DocumentType = $(value).find('#DocumentType').val();

            if((TemplateUID == '' || TemplateUID == 'Template' ) || (FieldUID =='' || FieldUID == 'Field' )){
                $.gritter.add({
                title:'Please Fill the Mandatory Fields',
                class_name: 'color danger ',
                fade: true,
                time: 3000,
                speed:'slow',
                });
            } else {
              SubProductLevel_Array.push({
                TemplateUID : $(value).find('#TemplateUID').val(),
                FieldUID : $(value).find('#FieldUID').val(),
                DocumentType : $(value).find('#DocumentType').val(),
                TemplateFileName : $(value).find('#TemplateFileName').val(),
              });
            }
          });
        } 
        
        if(MappingLevel == 2){

          $('.Div_StateLevel tr').each(function(key,value){

            var TemplateUID = $(value).find('#TemplateUID').val();
            var FieldUID = $(value).find('#FieldUID').val();
            var DocumentType = $(value).find('#DocumentType').val();
            var StateUID = $(value).find('#StateUID').val();

            if((TemplateUID == '' || TemplateUID == 'Template' ) || (FieldUID =='' || FieldUID == 'Field' ) || (StateUID =='')){
                $.gritter.add({
                title:'Please Fill the Mandatory Fields',
                class_name: 'color danger ',
                fade: true,
                time: 3000,
                speed:'slow',
                });
            } else {
              StateLevel_Array.push({
                StateUID : $(value).find('#StateUID').val(),
                TemplateUID : $(value).find('#TemplateUID').val(),
                FieldUID : $(value).find('#FieldUID').val(),
                DocumentType : $(value).find('#DocumentType').val(),
                TemplateFileName : $(value).find('#TemplateFileName').val(),
              });
            }

          });
        }

        if(MappingLevel == 3){

          $('.Div_CountyLevel tr').each(function(key,value){

            var TemplateUID = $(value).find('#TemplateUID').val();
            var FieldUID = $(value).find('#FieldUID').val();
            var DocumentType = $(value).find('#DocumentType').val();
            var StateUID = $(value).find('#StateUID').val();
            var CountyUID = $(value).find('#CountyUID').val();

            if((TemplateUID == '' || TemplateUID == 'Template' ) || (FieldUID =='' || FieldUID == 'Field' ) || (StateUID =='') || (CountyUID =='')){
                $.gritter.add({
                title:'Please Fill the Mandatory Fields',
                class_name: 'color danger ',
                fade: true,
                time: 3000,
                speed:'slow',
                });
            } else {
              CountyLevel_Array.push({
                StateUID : $(value).find('#StateUID').val(),
                CountyUID : $(value).find('#CountyUID').val(),
                TemplateUID : $(value).find('#TemplateUID').val(),
                FieldUID : $(value).find('#FieldUID').val(),
                DocumentType : $(value).find('#DocumentType').val(),
                TemplateFileName : $(value).find('#TemplateFileName').val(),
              });
            }
          });
        }

        $.ajax({
              type: "POST",
              url: '<?php echo base_url();?>subproducts/Update_SubProduct_test',
              dataType:'JSON',
              data: {'SubProductDetails': SubProductDetails,'SubProductLevel_Array': SubProductLevel_Array,'StateLevel_Array': StateLevel_Array,'CountyLevel_Array': CountyLevel_Array,'SubProductUID': SubProductUID,'FieldPosition': FieldPosition}, 
              cache: false,
              beforeSend: function(){ 
                  $('#Complete').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...'); 
                  $('#Complete').attr("disabled", true);
              },
              success: function(data)
              {  
                  if(data.validation_error==1)
                  {
                      $('.wizard-previous').attr("disabled", false);
                      $('#Complete').attr("disabled", false);
                      $('#Complete').html('<i class="fa fa-check"></i> Complete');
                      $.gritter.add({
                      title: data['message'],
                      class_name: 'color '+data['color'],
                      fade: true,
                      time: 3000,
                      speed:'slow',
                      });
                  } 
                  else 
                  {
                      console.log(data.SubProductUID);
                      var Subprod = data.SubProductUID;

                      var MenuLink = "<?php echo base_url();?>subproducts/edit_subproducts/"+Subprod;
                      window.location.replace(MenuLink);
                      $('#Div_TemplateMapping').show();
                      $('#tab_template_setup').show();
                  }
              } 
          }); 

        return false;
    });

    // Again Update Template Mpping

    $('#Update_Complete').click(function(e){

        e.preventDefault();

        var SubProductUID = $('#SubProductUID').val();
        var MappingLevel = $('#MappingLevel').val();  
        var SubProductCode = $('#SubProductCode').val();  
        var SubProductName = $('#SubProductName').val();  
        var ProductUID = $('#ProductUID').val();  
        var ReportHeading = $('#ReportHeading').val();  

        var SubProductDetails = []; 
        var SubProductLevel_Array = []; 
        var StateLevel_Array = []; 
        var CountyLevel_Array = []; 

        SubProductDetails.push({
            SubProductUID :  $('#SubProductUID').val(),
            MappingLevel :  $('#MappingLevel').val()           
        });

        var FieldPosition = []; 

        $('.row_position tr').each(function(key,value){

          var Position = $(value).attr('data-postion');
          var FieldUID = $(value).attr('data-fielduid');

          FieldPosition.push({
            Position : $(value).attr('data-postion'),
            FieldUID : $(value).attr('data-fielduid'),
          });
        });

        if(MappingLevel == 1){
          $('.Div_SubProductLevel tr').each(function(key,value){

            var TemplateUID = $(value).find('#TemplateUID').val();
            var FieldUID = $(value).find('#FieldUID').val();
            var DocumentType = $(value).find('#DocumentType').val();

            if((TemplateUID == '' || TemplateUID == 'Template' ) || (FieldUID =='' || FieldUID == 'Field' )){
                $.gritter.add({
                title:'Please Fill the Mandatory Fields',
                class_name: 'color danger ',
                fade: true,
                time: 3000,
                speed:'slow',
                });
            } else {
              SubProductLevel_Array.push({
                TemplateUID : $(value).find('#TemplateUID').val(),
                FieldUID : $(value).find('#FieldUID').val(),
                DocumentType : $(value).find('#DocumentType').val(),
                FieldRow : $(value).find('#FieldRow').val(),
                TemplateFileName : $(value).find('#TemplateFileName').val(),
              });
            }
          });
        } 

        if(MappingLevel == 2){
          $('.Div_StateLevel tr').each(function(key,value){

            var TemplateUID = $(value).find('#TemplateUID').val();
            var FieldUID = $(value).find('#FieldUID').val();
            var DocumentType = $(value).find('#DocumentType').val();
            var StateUID = $(value).find('#StateUID').val();

            if((TemplateUID == '' || TemplateUID == 'Template' ) || (FieldUID =='' || FieldUID == 'Field' ) || (StateUID =='')){
                $.gritter.add({
                title:'Please Fill the Mandatory Fields',
                class_name: 'color danger ',
                fade: true,
                time: 3000,
                speed:'slow',
                });
            } else {
              StateLevel_Array.push({
                StateUID : $(value).find('#StateUID').val(),
                TemplateUID : $(value).find('#TemplateUID').val(),
                FieldUID : $(value).find('#FieldUID').val(),
                DocumentType : $(value).find('#DocumentType').val(),
                FieldRow : $(value).find('#FieldRow').val(),
                TemplateFileName : $(value).find('#TemplateFileName').val(),
              });
            }
          });
        }

        if(MappingLevel == 3){
          $('.Div_CountyLevel tr').each(function(key,value){

            var TemplateUID = $(value).find('#TemplateUID').val();
            var FieldUID = $(value).find('#FieldUID').val();
            var DocumentType = $(value).find('#DocumentType').val();
            var StateUID = $(value).find('#StateUID').val();
            var CountyUID = $(value).find('#CountyUID').val();

            if((TemplateUID == '' || TemplateUID == 'Template' ) || (FieldUID =='' || FieldUID == 'Field' ) || (StateUID =='') || (CountyUID =='')){
                $.gritter.add({
                title:'Please Fill the Mandatory Fields',
                class_name: 'color danger ',
                fade: true,
                time: 3000,
                speed:'slow',
                });
            } else {
              CountyLevel_Array.push({
                StateUID : $(value).find('#StateUID').val(),
                CountyUID : $(value).find('#CountyUID').val(),
                TemplateUID : $(value).find('#TemplateUID').val(),
                FieldUID : $(value).find('#FieldUID').val(),
                DocumentType : $(value).find('#DocumentType').val(),
                FieldRow : $(value).find('#FieldRow').val(),
                TemplateFileName : $(value).find('#TemplateFileName').val(),
              });
            }
          });
        }

        $.ajax({
            type: "POST",
            url: '<?php echo base_url();?>subproducts/Again_Update_SubProduct',
            dataType:'JSON',
            data: {'SubProductDetails': SubProductDetails,'SubProductLevel_Array': SubProductLevel_Array,'StateLevel_Array': StateLevel_Array,'CountyLevel_Array': CountyLevel_Array,'SubProductUID': SubProductUID,'FieldPosition': FieldPosition}, 
            cache: false,
            beforeSend: function(){ 
                $('#Update_Complete').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...'); 
                $('#Update_Complete').attr("disabled", true);
            },
            success: function(data)
            {  
                if(data.validation_error==1)
                {
                    $('.wizard-previous').attr("disabled", false);
                    $('#Complete').attr("disabled", false);
                    $('#Complete').html('<i class="fa fa-check"></i> Complete');
                    $.gritter.add({
                    title: data['message'],
                    class_name: 'color '+data['color'],
                    fade: true,
                    time: 3000,
                    speed:'slow',
                    });
                }
                else
                {
                    console.log(data.SubProductUID);
                    var Subprod = data.SubProductUID;
                    var MenuLink = "<?php echo base_url();?>subproducts/edit_subproducts/"+Subprod;
                    window.location.replace(MenuLink);
                }
            } 
        }); 
        return false;
    });


  $('body').on('click','.Copy_Fields', function(event) {
      var FieldRow = $(this).attr('data-field-row');
      var MappingLevel = $('#MappingLevel').val();
      var SubProductUID = $(this).attr('data-subprod-uid');

      $.ajax({
          url: '<?php echo base_url();?>subproducts/CopyMappingFields',
          type: "POST",
          data: {FieldRow:FieldRow,SubProductUID:SubProductUID }, 
          dataType:'json',
          cache: false,
          success: function(data)
          {
            $(".FieldUID").val(' ');

            if(MappingLevel == 1){
                $.each(data, function(k, v) {
                  $('.Div_SubProductLevel .FieldUID option[value='+v.FieldUID+']').prop('selected', true);
                });
                select2_append();
            } else if(MappingLevel == 2){
                $.each(data, function(k, v) {
                  $('.Div_StateLevel .FieldUID option[value='+v.FieldUID+']').prop('selected', true);
                });
                select2_append();
            } else if(MappingLevel == 3){
                $.each(data, function(k, v) {
                  $('.Div_CountyLevel .FieldUID option[value='+v.FieldUID+']').prop('selected', true);
                });
                select2_append();
            } else {
              $.gritter.add({
                title: 'Unable to Copy',
                class_name: 'color danger',
                fade: true,
                time: 1000,
                speed:'slow',
              });
            }

          },
          error:function(jqXHR, textStatus, errorThrown)
          {
            console.log(jqXHR.responseText);
          }
      });
    });


   $('body').on('click','.edit_mapping', function(event) {
      var FieldRow = $(this).attr('data-field-row');
      var MappingLevel = $(this).attr('data-mapping-level');
      var SubProductUID = $(this).attr('data-subprod-uid');

      var objDiv = document.getElementById("LevelMapping");
      objDiv.scrollTop = objDiv.scrollHeight;

      $.ajax({
          url: '<?php echo base_url();?>subproducts/EditMappingFieldsTemplates',
          type: "POST",
          data: {FieldRow:FieldRow,SubProductUID:SubProductUID,MappingLevel:MappingLevel}, 
          dataType:'json',
          cache: false,
          success: function(data)
          {
            console.log(data.Field_Content);
            $('#Complete').hide();
            $('#Update_Complete').show();
            $('#Buttons').show();
            $('#MappingLevel').empty();
            $('#MappingLevel').append('<option value=""></option>'); 

            if(data.MappingLevel == 1)
            {
              $('#SubProductLevel').show();
              $('#StateLevel').hide();
              $('#CountyLevel').hide();

              /*$('.Div_SubProductLevel').find('.TemplateUID').empty();
              $('.Div_SubProductLevel').find('.FieldUID').empty();*/

              $('.Div_SubProductLevel').find('.report_view').addClass('report_view_template');
              $('.Div_SubProductLevel').find('.report_view').removeClass('report_view');

              //Field

              var FieldUID = data.Field_Content;

              var FieldIDs=[];

              var test = $('.Div_SubProductLevel').find('.FieldUID');
              $.each(FieldUID, function(k, v) {
                test.find('option[value='+v+']').prop('selected', true);
               FieldIDs.push(v);
              });



              // $('.Div_SubProductLevel').find('.FieldUID').val(FieldIDs).trigger('change');
              $('.Div_SubProductLevel').find('.FieldUID').parent().addClass('is-dirty');

              //Template

              /*$('.Div_SubProductLevel').find('.TemplateUID').append('<option value="' + data.details['TemplateUID'] + '" selected>' + data.details['TemplateName'] + '</option>').trigger('change');*/
              $('.Div_SubProductLevel').find('.TemplateUID').val(data.details['TemplateUID']).trigger('change'); 
              $('.Div_SubProductLevel').find('.TemplateUID').parent().addClass('is-dirty');

              $('.Div_SubProductLevel').find('.DocumentType').val(data.details['DocumentType']); 
              $('.Div_SubProductLevel').find('.DocumentType').parent().addClass('is-dirty');


              $('.Div_SubProductLevel').find('.FieldRow').val(data.details['FieldRow']);
              $('.Div_SubProductLevel').find('.TemplateFileName').val(data.Rep_msg);

              $('#MappingLevel').append('<option value="1" selected>SubProduct Level</option>'); 
              $('.select2').select2({
                theme: "bootstrap",
              }); 
              $('#MappingLevel').parent().addClass('is-dirty');

            }

            if(data.MappingLevel == 2)
            {
              $('#StateLevel').show();
              $('#SubProductLevel').hide();
              $('#CountyLevel').hide();

              $('.Div_StateLevel').find('.report_view').addClass('report_view_template');
              $('.Div_StateLevel').find('.report_view').removeClass('report_view');

              //Field
              $('.Div_StateLevel .FieldUID :selected').prop("selected", false)
              var FieldUID = data.Field_Content;

              var FieldIDs=[];

              var test = $('.Div_StateLevel').find('.FieldUID');
              $.each(FieldUID, function(k, v) {
                $('.Div_StateLevel .FieldUID option[value='+v+']').prop('selected', true);
               FieldIDs.push(v);
              });

              $('.Div_StateLevel .FieldUID').trigger('change');
              //$('.Div_StateLevel').find('.FieldUID').val(FieldIDs).trigger('change');
              $('.Div_StateLevel').find('.FieldUID').parent().addClass('is-dirty');

              //Template

              /*$('.Div_StateLevel').find('.TemplateUID').append('<option value="' + data.details['TemplateUID'] + '" selected>' + data.details['TemplateName'] + '</option>').trigger('change');*/
              $('.Div_StateLevel').find('.TemplateUID').val(data.details['TemplateUID']).trigger('change'); 
              $('.Div_StateLevel').find('.TemplateUID').parent().addClass('is-dirty');

              $('.Div_StateLevel').find('.DocumentType').val(data.details['DocumentType']); 
              $('.Div_StateLevel').find('.DocumentType').parent().addClass('is-dirty');

              //State

              var StateUID = data.State_Content;

              var StateUIDs=[];

              $.each(StateUID, function(k, v) {
                StateUIDs.push(v);
              });

              /*$('.Div_StateLevel').find('.StateUID').append('<option value="' + v + '" selected >' + k + '</option>').trigger('change');*/
              $('.Div_StateLevel').find('.StateUID').val(StateUIDs).trigger('change');
              $('.Div_StateLevel').find('.StateUID').parent().addClass('is-dirty');

              $('.Div_StateLevel').find('.FieldRow').val(data.details['FieldRow']);
              $('.Div_StateLevel').find('.TemplateFileName').val(data.Rep_msg);

              $('#MappingLevel').append('<option value="2" selected>State Level</option>'); 
              $('#MappingLevel').parent().addClass('is-dirty');

            }

            if(data.MappingLevel == 3)
            {
              $('#CountyLevel').show();
              $('#StateLevel').hide();
              $('#SubProductLevel').hide();

              $('.Div_CountyLevel').find('.CountyUID').empty();

              $('.Div_CountyLevel').find('.report_view').addClass('report_view_template');
              $('.Div_CountyLevel').find('.report_view').removeClass('report_view');
              
              //Field

              var FieldUID = data.Field_Content;

              var FieldIDs=[];

               var test = $('.Div_CountyLevel').find('.FieldUID');
              $.each(FieldUID, function(k, v) {
                test.find('option[value='+v+']').prop('selected', true);
               FieldIDs.push(v);
              });

              //$('.Div_CountyLevel').find('.FieldUID').val(FieldIDs).trigger('change');
              $('.Div_CountyLevel').find('.FieldUID').parent().addClass('is-dirty');

              //Template

              $('.Div_CountyLevel').find('.TemplateUID').val(data.details['TemplateUID']).trigger('change'); 
              $('.Div_CountyLevel').find('.TemplateUID').parent().addClass('is-dirty');

              $('.Div_CountyLevel').find('.DocumentType').val(data.details['DocumentType']); 
              $('.Div_CountyLevel').find('.DocumentType').parent().addClass('is-dirty');

              //State

              var StateUID = data.State_Content;

              var StateUIDs=[];

              $.each(StateUID, function(k, v) {
                StateUIDs.push(v);
              });

              $('.Div_CountyLevel').find('.StateUID').val(StateUIDs).trigger('change');
              $('.Div_CountyLevel').find('.StateUID').parent().addClass('is-dirty');

              //County

              var CountyUID = data.County_Content;

             /* var CountyUIDs=[];

              $.each(CountyUID, function(k, v) {
                CountyUIDs.push(v);
              });

              console.log(CountyUIDs);

              $('.Div_CountyLevel').find('.CountyUID').val(CountyUIDs).trigger('change');
              $('.Div_CountyLevel').find('.CountyUID').parent().addClass('is-dirty');*/

              $.each(CountyUID, function(k, v) {
                $('.Div_CountyLevel').find('.CountyUID').append('<option value="' + v + '" selected >' + k + '</option>').trigger('change');
                $('.Div_CountyLevel').find('.CountyUID').parent().addClass('is-dirty');
              });

              $('.Div_CountyLevel').find('.FieldRow').val(data.details['FieldRow']);
              $('.Div_CountyLevel').find('.TemplateFileName').val(data.Rep_msg);

              $('#MappingLevel').append('<option value="3" selected>County Level</option>'); 
              $('#MappingLevel').parent().addClass('is-dirty');

            }
          },
          error:function(jqXHR, textStatus, errorThrown)
          {
            console.log(jqXHR.responseText);
          }
      });
    });

 $('body').on('click','.EditFields', function(event) {

    var TemplateUID = $('.TempUID').val();
    var DynamincFieldUID = $('.DynamincUID').val();
    var FieldPosition =[];

    $('.row_position tr').each(function(key,value){
      FieldPosition.push({
        FieldUID : $(value).attr('data-fielduid'),
      });
    });

    $.ajax({
      url: '<?php echo base_url("subproducts/EditFieldsAdding"); ?>',
      type: 'POST',
      data: {TemplateUID: TemplateUID,FieldPosition: FieldPosition,DynamincFieldUID: DynamincFieldUID},
      cache: false,
      dataType:'json',
      beforeSend: function(){ 
        $('.EditFields').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...'); 
      },
      success: function(data){
        updatePosition();
        $('#TemplateMapping').find('.modal-content').find('tbody').html('');
        $('#TemplateMapping').find('.modal-content').find('tbody').append(data.query);
        $('#TemplateMapping').find('.modal-content').find('.Sort').addClass('SortTable');
        //$('.tinymce_content').hide();
        $('.EditFields').hide();
        $('.SaveFields').show();

        updatePosition();

        $('.SaveFields').html('<i class="fa fa-floppy-o" aria-hidden="true"></i>'); 
        $('.EditFields').html('<i class="fa fa-pencil" aria-hidden="true"></i>'); 
      }
    });
  });

  $('body').on('click','.SaveFields', function(event) {

    $('#SaveFields').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...'); 

    var TemplateUID = $('.TempUID').val();
    var DynamincFieldUID = $('.DynamincUID').val();
    var FieldPosition =[];
    var FieldUIDs =[];

    $('.CheckFields:checked').each(function(){
      FieldPosition.push({
        FieldUID : $(this).closest('tr').attr('data-fielduid'),
        FieldName : $(this).closest('tr').attr('data-fieldname'),
      });
    });

    $('.CheckFields:checked').each(function(){
      FieldUIDs.push($(this).closest('tr').attr('data-fielduid'));
    });

    $('.FieldUID').empty();
    $(FieldPosition).each(function(key,value) {
      $(value).each(function(keys,values) {
        $('.FieldUID').append('<option value="' + values['FieldUID'] + '" selected >' + values['FieldName'] + '</option>').trigger('change');
      })
    });

    $.ajax({
        url: '<?php echo base_url("subproducts/previewtemplate"); ?>',
        type: 'POST',
        data: {TemplateUID: TemplateUID,FieldUID: FieldUIDs,DynamincFieldUID: DynamincFieldUID},
        cache: false,
        dataType:'json',
        beforeSend: function(){ 
          $('#SaveFields').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...'); 
          $('.EditFields').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...'); 
        },
        success: function(data){
            $('#TemplateMapping').modal('show');
            $('#TemplateMapping').find('.modal-content').find('tbody').html('');
            $('#TemplateMapping').find('.modal-content').find('tbody').append(data.query);
            $('#TemplateMapping').find('.modal-content').find('.DynamincFieldUID').val(data.DynamincFieldUID);
            $('#TemplateMapping').find('.modal-content').find('.TemplateUID').val(data.TemplateUID);
            $('.SaveFields').hide();
            $('.EditFields').show();
            $('.tinymce_content').show();

            updatePosition();
            $('.SaveFields').html('<i class="fa fa-floppy-o" aria-hidden="true"></i>'); 
            $('.EditFields').html('<i class="fa fa-pencil" aria-hidden="true"></i>'); 
            //tinymce.get('mymce1').setContent(data.Rep_msg);

        }
    }) 

  });

   $('body').on('click','.report_view_template', function(event) { 

    var FieldRow = $(this).closest("tr").find('.FieldRow').val();
    var FieldUIDs = $(this).closest("tr").find('.FieldUID').val();
    var TemplateUID = $(this).closest("tr").find('.TemplateUID').val();
    var DynamincFieldUID = $(this).closest("tr").find('.TemplateFileName').attr('data-id');
    var SubProductUID = $('#SubProductUID').val();

     $.ajax({
        url: '<?php echo base_url("subproducts/Editpreviewtemplate"); ?>',
        type: 'POST',
        data: {TemplateUID: TemplateUID,FieldUID: FieldUIDs,DynamincFieldUID: DynamincFieldUID,FieldRow: FieldRow,SubProductUID: SubProductUID},
        cache: false,
        dataType:'json',
        success: function(data){

            $("#TemplateMapping").modal({
               backdrop: 'static',
               keyboard: false
            }); 

            $('#TemplateMapping').find('.modal-content').find('tbody').html('');
            $('#TemplateMapping').find('.modal-content').find('tbody').append(data.query);
            $('#TemplateMapping').find('.modal-content').find('.DynamincFieldUID').val(data.DynamincFieldUID);
            $('#TemplateMapping').find('.modal-content').find('.TemplateUID').val(data.TemplateUID);
            $('#TemplateMapping').modal('show');
            updatePosition();

            tinymce.get('mymce1').setContent(data.Rep_msg);

        }
      }) 

  });
       $('.SaveFields').hide();

    // $(document).on('click', '#ResetSubProductTemplate').on('click', '#ResetSubProductTemplate', function(){
     $('#ResetSubProductTemplate').click(function(){
        var TemplateUID = $('.TempUID').val();
        $.ajax({
          url: '<?php echo base_url("subproducts/ResetSubProductTemplate"); ?>',
          type: 'POST',
          data: {TemplateUID: TemplateUID},
          cache: false,
          dataType:'json',
          beforeSend: function(){
            $('#ResetSubProductTemplate').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...'); 
            $('#ResetSubProductTemplate').attr("disabled", true);
          },
          success: function(data){
              console.log(data.Rep_msg);
              $('#ResetSubProductTemplate').html('<i class="fa fa-mail-reply-all"></i> Reset');
              $('#ResetSubProductTemplate').attr("disabled", false);
              tinymce.get('mymce1').setContent(data.Rep_msg);
          }
      }) 

    });

      $('#save').click(function(e){

      var TemplateUID = $('#TemplateUID').val();
      var DynamincFieldUID = $('#DynamincFieldUID').val();
      var FileName = TemplateUID + 'Testing';
      var editorContent=tinymce.get('mymce1').getContent();
      var Mapping =  "#LevelMapping ."+DynamincFieldUID;
      $(Mapping).val(editorContent);

      $('#TemplateMapping').modal('hide');

    });

        //Event func to select keyword

  $('body').on('click','.choose', function(event) { 

    $('.FieldUID').trigger('change');
    var Frm_FieldUID = $('.FieldUID').val();

    selectedfieldkeyword=$(this).attr('data-keyword');
    var Modal_FieldUID=$(this).attr('data-value');
    var Modal_FieldName=$(this).attr('data-display-name');
    $(this).addClass('CurrentFields');
    
    /*console.log($('.FieldUID').val());

    if($.inArray(Modal_FieldUID, Frm_FieldUID) == -1) {
      $('.FieldUID').append('<option value="' + Modal_FieldUID + '" selected >' + Modal_FieldName + '</option>').trigger('change');
    }*/

    // $('#CopyText').val('');

    $('#CopyText').val(selectedfieldkeyword);

    var cTxt = document.getElementById("CopyText");

    cTxt.select();
    document.execCommand("copy");
    document.execCommand("BackColor", false, 'white');

   // $('#CopyText').val('');


   /* if(typeof selectedfieldkeyword!='undefined')
    {
      $(this).removeClass('btn-twitter');
    }*/
  })


   $( ".row_position" ).sortable({
    delay: 150,
    stop: function() {
      var selectedData = new Array();
      $('.row_position>tr').each(function() {
          selectedData.push($(this).attr("id"));
      });
      updatePosition();
    }
  });
    function updatePosition()
  {
    $.map($('.row_position').find('tr'), function(el) {
      $(el).attr('data-postion', $(el).index()+1);
    });  
  }


</script>
