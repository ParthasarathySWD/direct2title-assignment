
<!-- start body header -->
<div id="page_top" class="section-body">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Completed Orders</h1> 
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
                            <!-- <a href="Subproducts/add" type="button" class="btn btn-primary btn-sm mt-2"><i class="fe fe-plus"></i> Add New Sub Product
                            </a> -->
                            <a class="float-right mr-4 pr-2 mt-2" href=""><button type="button" class="btn btn-red btn-sm" ><i class="fe fe-download"></i> Export as Excel</button></a>
                        </div>
                        <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div> 
                    <div class="card-body pb-4 pt-3">

                        <div class="col-md-12 col-lg-12">
                            <table class="table table-vcenter table-new text-nowrap" cellspacing="0" id="myordertable" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Product/SubProduct</th>
                                        <th>Customer Name</th>
                                        <th>Ordered Date</th>
                                        <th>Order CompleteDate</th>
                                        <th>Order Priority</th>
                                        <th>Loan Number</th>
                                        <th>Property Address</th>
                                        <th>City</th>
                                        <th>County</th>
                                        <th>State</th>
                                        <th>Zipcode</th>
                                        <th>Order Type</th>
                                        <th>Attention</th>
                                        <th class="text-center" style="width:100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <!--  <?php foreach ($Complete as $key => $value) { ?>
                                        <tr>
                                            <td><?php echo $value->OrderNumber; ?></td>
                                            <td><?php echo $value->ProductName.'/'.$value->SubProductName; ?></td>
                                            <td><?php echo $value->CustomerName; ?></td>
                                            <td><?php echo $value->OrderEntryDatetime; ?></td>
                                            <td><?php echo $value->OrderCompleteDateTime; ?></td>
                                            <td><?php echo $value->PriorityName; ?></td>
                                            <td><?php echo $value->LoanNumber; ?></td>
                                            <td><?php echo $value->PropertyAddress1; ?></td>
                                            <td><?php echo $value->PropertyCityName; ?></td>
                                            <td><?php echo $value->PropertyCountyName; ?></td>
                                            <td><?php echo $value->PropertyStateCode; ?></td>
                                            <td><?php echo $value->PropertyZipcode; ?></td>
                                            <td><?php echo $value->OrderTypeName; ?></td>
                                            <td><?php echo $value->AttentionName; ?></td>
                                        <td class="actions text-center">
                                            <a href="Order_summary" class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?> -->

                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<script type="text/javascript">
      
     $(document).ready(function(){
        documentChecListTable();
    });

 function documentChecListTable() {

     var data = {'table_type':'assignment'};
      MaritalTableList = $('#myordertable').DataTable({
        scrollX: true,
        scrollCollapse: true,
        fixedHeader: true,
        paging: true,
        "bDestroy": true,
        "autoWidth": true,
        "processing": true, //Feature control the processing indicator.
        "serverSide": false, //Feature control DataTables' server-side processing mode.
        "order": [], //Initial no order.
        "pageLength": 10, // Set Page Length
        "lengthMenu": [
          [10, 25, 50, 100],
          [10, 25, 50, 100]
        ],
        fixedColumns:   {
            leftColumns: 1,
            rightColumns: 1
        },

        language: {
          sLengthMenu: "Show _MENU_ Orders",
          emptyTable: "No Orders Found",
          info: "Showing _START_ to _END_ of _TOTAL_ Orders",
          infoEmpty: "Showing 0 to 0 of 0 Orders",
          infoFiltered: "(filtered from _MAX_ total Orders)",
          zeroRecords: "No matching Orders found",
          processing: '<span class="progrss"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Processing...</span>',
        },
        // Load data for the table's content from an Ajax source
        "ajax": {
          "url": "<?php echo base_url('Completed_Orders/GetCompletedOrders') ?>",
          "type": "POST",
          "data": {
            'data': data,
            // 'workflow':'<?php echo $workflow; ?>',
          }
        },
        "columnDefs": [{
          "targets": 'no-sort',
          "orderable": false,
        }],
      });
      $($.fn.dataTable.tables(true)).css('width', '100%');
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    }

  </script>