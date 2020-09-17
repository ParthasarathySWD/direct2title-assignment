
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
    <form action="#" name="frm_OrderSummary" id="frm_OrderSummary" onsubmit="return false;">

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
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#All" aria-expanded="true">Summary</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Images" aria-expanded="true">Order Info</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Video" aria-expanded="false">Reports</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#News" aria-expanded="false">Attachments</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#News" aria-expanded="false">Notes</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#News" aria-expanded="false">History</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-sm-12 form-group">
                                    <p style="font-weight: bold;  text-align: left;margin-bottom: 0px;">ORDER NO: 11111</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="col-md-12 col-lg-12 add-product-div p-0">
                            <div class="row">
                                <div class="col-md-3 col-lg-3">

                                    <div class="form-group">
                                        <label class="form-label">Client</label>
                                      <!--  <select class="form-control mdl-textfield__input input-xs mdl-select2 select2" id="customer" name="customer">
                              <?php foreach ($Customers as $key => $customer) {
                                if($customer->CustomerUID == $order_details->CustomerUID) {?>
                                  <option value="<?php echo $customer->CustomerUID; ?>" selected><?php echo $customer->CustomerNumber.' / '.$customer->CustomerName; ?></option>';
                                <?php }
                              } ?>
                            </select> -->
                                    </div>
                                </div>
                                
                              
                            </div>
                            
                        </div>
                    </div>
                   
                   
               
                </div>

                <div class="col-md-12 col-lg-12 mb-3 mt-4">
                    <div class="btn-list text-right">
                        <a href="#" class="btn btn-success single_submit" id="UpdateRoles" value="1">Update</a>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>

