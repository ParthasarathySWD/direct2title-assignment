
<!-- start body header -->
<div id="page_top" class="section-body bg-new-header-top">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title"> Products</h1> 
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
                        <div class="col-md-12 col-lg-12">
                            <a href="Products/add_products" type="button" class="btn btn-primary btn-sm mt-2"><i class="fe fe-plus"></i> Add New Product
                            </a>
                            <a class="float-right mr-4 pr-2 mt-2" href=""><button type="button" class="btn btn-red btn-sm" ><i class="fe fe-download"></i> Export as Excel</button></a>
                        </div>
                        <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>
                    <div class="card-body pb-4 pt-3">

                        <div class="col-md-12 col-lg-12">
                            <table class="table table-vcenter table-new custom-datatable text-nowrap" cellspacing="0" id="" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th> Product Code</th>
                                        <th> Product Name</th>
                                        
                                        <th>Status</th>
                                        <th class="text-center" style="width:100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1; foreach($ProductsDetails as $row): ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td onBlur="saveToDatabase(this,'ProductCode','<?php echo $row->ProductUID; ?>')" onClick="showEdit(this);"><?php echo $row->ProductCode; ?></td>
                                        <td onBlur="saveToDatabase(this,'ProductName','<?php echo $row->ProductUID; ?>')" onClick="showEdit(this);"><?php echo $row->ProductName; ?></td>
                                        
                                       <!--  <td>
                                            <label class="custom-switch">
                                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description"></span>
                                            </label>
                                        </td> -->
                                        <?php if($row->Active==0): ?>
                                        <td  contenteditable="true" onClick="saveToDatabaseStatus(1,'Active','<?php echo $row->ProductUID; ?>')"><span style="text-align: center;width:100%;" class="btn btn-rounded btn-xs"><div class="switch-button switch-button-xs">
                                            <?php if($row->Active==0): ?>
                                            <input type="checkbox" name="Active<?php echo $row->ProductUID; ?>" id="Active<?php echo $row->ProductUID; ?>"  value="<?php echo $row->ProductUID; ?>" >
                                            <?php elseif($row->Active==1): ?>
                                            <input type="checkbox" name="Active<?php echo $row->ProductUID; ?>" id="Active<?php echo $row->ProductUID; ?>" value="<?php echo $row->ProductUID; ?>"  checked="strue" >
                                            <?php endif; ?>
                                            <span><label for="Active<?php echo $row->ProductUID; ?>"></label></span>
                                        </div> </span></td>
                                    <?php elseif($row->Active==1): ?>
                                        <td  contenteditable="true"  onClick="saveToDatabaseStatus(0,'Active','<?php echo $row->ProductUID; ?>')"><span style="text-align: center;width:100%;" class="btn btn-rounded btn-xs"><div class="switch-button switch-button-xs">
                                            <?php if($row->Active==0): ?>
                                            <input type="checkbox" name="Active<?php echo $row->ProductUID; ?>" id="Active<?php echo $row->ProductUID; ?>"  value="<?php echo $row->ProductUID; ?>" >
                                            <?php elseif($row->Active==1): ?>
                                            <input type="checkbox" name="Active<?php echo $row->ProductUID; ?>" id="Active<?php echo $row->ProductUID; ?>" value="<?php echo $row->ProductUID; ?>"  checked="strue" >
                                            <?php endif; ?>
                                            <span><label for="Active<?php echo $row->ProductUID; ?>"></label></span>
                                        </div> </span></td>
                                    <?php endif; ?>
                                        <td class="actions text-center">
                                            <a href="<?php echo base_url()."products/edit_products/".$row->ProductUID; ?>" class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                           <a href="" class="btn btn-sm btn-icon button-edit text-primary" title="Delete" data-value="<?php echo $row->ProductUID;?>" class="btn remove_btn btnDelete"><i class="icon-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php $i++; endforeach; ?>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
