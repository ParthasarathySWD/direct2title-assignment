
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
<div class="section-body">
    <div class="container-fluid">
        <!-- <h5 class="pb-1 inner-page-head">Single Order</h5> -->
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card">


                    <div class="card-header">
                        <a href="Template/add_template" type="button" class="btn btn-red btn-sm mt-2"><i class="fe fe-plus"></i> Add New Template
                                    </a>
                        <div class="card-options">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>
                    <div class="card-body pb-4 pt-3">

                        <div class="col-md-12 col-lg-12">
                            <table class="table table-vcenter table-new custom-datatable text-nowrap" cellspacing="0" id="" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Template Code</th>
                                        <th>Template Name</th>
                                        <!-- <th>Status</th> -->
                                        <th class="text-center" style="width:100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($TemplateDetails as $row): ?>
                                        <tr>
                                           <td style="text-align: left;"><?php echo $row->TemplateCode; ?></td> 
                                           <td style="text-align: left;"><?php echo $row->TemplateName; ?></td>
                                           <!-- <td>
                                            <label class="custom-switch"> -->
                                               <!--  <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input"> -->
                                               <!-- <?php if ($row->IsDynamicTemplate==0): ?>
                                                <input type="checkbox" class="custom-switch-input" name="IsDynamicTemplate<?php echo $row->TemplateUID; ?>" id="IsDynamicTemplate<?php echo $row->TemplateUID; ?>"  value="<?php echo $row->TemplateUID; ?>" onClick="saveToDatabaseStatus(1,'IsDynamicTemplate','<?php echo $row->TemplateUID; ?>')">
                                                <?php elseif ($row->IsDynamicTemplate==1): ?>
                                                    <input type="checkbox" name="IsDynamicTemplate<?php echo $row->TemplateUID; ?>" class="custom-switch-input" id="IsDynamicTemplate<?php echo $row->TemplateUID; ?>" value="<?php echo $row->TemplateUID; ?>"  checked="strue" onClick="saveToDatabaseStatus(0,'IsDynamicTemplate','<?php echo $row->TemplateUID; ?>')">
                                                <?php endif; ?>
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description"></span>
                                            </label>
                                        </td> -->
                                        <td class="actions text-center">
                                            <a href="<?php echo base_url()."template/edit/".$row->TemplateUID; ?>" class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
