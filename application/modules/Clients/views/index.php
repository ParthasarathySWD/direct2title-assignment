
<!-- start body header -->
<div id="page_top" class="section-body bg-new-header-top">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Clients</h1> 
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
                            <a href="Clients/add" type="button" class="btn btn-primary btn-sm mt-2"><i class="fe fe-plus"></i> Add New Client
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
                                        
                                        <th>Client Name</th>
                                        <th>Client Number</th>
                                        <th>State</th>
                                        <th>Contact Name</th>
                                        <th>Contact Number</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width:100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($CustomerDetails as $row) { ?>
                                        <?php $test = $this->Customers_Model->GetParentCompanyNameDetails($row->ParentCompanyUID);?> 
                                        <?php if($test == NULL) : ?> 
                                    <tr class="odd gradeX">
                                        <td><a href="<?php echo base_url('Clients/EditCustomer/'.$row->CustomerUID);?>"><?php echo $row->CustomerName?></a></td>
                                    <td><?php echo $row->CustomerNumber?></td>
                                    <td><?php echo $row->StateName; ?></td>
                                    <td><?php echo $row->CustomerPContactName; ?></td>
                                    <td><?php echo $row->CustomerPContactMobileNo; ?></td>
                                       <?php 
                                      if($row->CustStatus == 1)
                                       { ?>
                                        <td>
                                            <label class="custom-switch">
                                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked="true">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description"></span>
                                            </label>
                                        </td>
                                        <?php } 
                                      else 
                                      { ?>
                                        <td>
                                            <label class="custom-switch">
                                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description"></span>
                                            </label>
                                        </td>
                                    <?php } ?>


                                        <td class="actions text-center">
                                            <a href="<?php echo base_url('Clients/Edit/'.$row->CustomerUID);?>" class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                            <a href="Order_summary" class="btn btn-sm btn-icon button-delete text-primary" title="Delete"><i class="icon-trash"></i></a>
                                        </td>
                                    </tr>
                               <?php else: ?> 
                                    <tr class="odd gradeX">
                                        <td><?php 
                                      if($row->ParentCompanyCheck == 1)
                                      {?>
                                      <a href="<?php echo base_url('customers/EditCustomer/'.$row->CustomerUID);?>"><?php echo ucfirst($test->CustomerName) ?></a>
                                      <?php   } 
                                      else 
                                      {?>
                                        <a href="<?php echo base_url('customers/EditCustomer/'.$row->CustomerUID);?>"><?php echo ucfirst($row->CustomerName) ?></a>
                                      <?php } ?></td>
                                    <td><?php 
                                      if($row->ParentCompanyCheck == 1)
                                      {?>
                                      <?php echo ucfirst($test->CustomerNumber) ?>
                                      <?php   } 
                                      else 
                                      {?>
                                        <?php echo ucfirst($row->CustomerNumber) ?>
                                      <?php } ?></td>
                                    <td><?php echo ucfirst($row->StateName); ?></td>
                                    <td><?php echo ucfirst($row->CustomerPContactName); ?></td>
                                    <td><?php echo ucfirst($row->CustomerPContactMobileNo); ?></td>
                                        
                                        <td>
                                            <label class="custom-switch">
                                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description"></span>
                                            </label>
                                        </td>
                                        <td class="actions text-center">
                                            <a href="<?php echo base_url('Clients/Edit/'.$row->CustomerUID);?>" class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                            <a href="Order_summary" class="btn btn-sm btn-icon button-delete text-primary" title="Delete"><i class="icon-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endif; ?> 
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
