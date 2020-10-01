
<!-- start body header -->
<div id="page_top" class="section-body">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">My Orders</h1> 
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
                    <div class="card-header pb-0">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 pl-0">
                                    <h3 class="card-title pt-2">Assignment Orders</h3>
                                </div>
                                <div class="col-md-6 col-lg-6 text-right pr-0 pt-1">
                                    <a href="<?php echo base_url().'Myorders/excelExport' ?>"><button type="button" class="btn btn-red btn-sm" ><i class="fe fe-download"></i> Export as Excel
                                    </button></a>
                                </div>
                            </div>
                        </div>
                    </div>
             
                    <div class="card-body pb-4 pt-3">

                        <div class="col-md-12 col-lg-12">
                            <table class="table table-vcenter table-new text-nowrap" cellspacing="0" id="myordertable" style="width:100%; ">
                                <thead>
                                    <tr>
                                        <th>Order No</th>
                                        <th>Product/SubProduct</th>
                                        <th>Order Priority</th>
                                        <th>Current Status</th>
                                        <th>Current WorkFlow</th>
                                        <th>Property Address</th>
                                        <th>City</th>
                                        <th>County</th>
                                        <th>State</th>
                                        <th>Zip Code</th>
                                        <th>Order DateTime</th>
                                        <th>Due DateTime</th>
                                        <th class="text-center" style="width:120px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="assets/js/datatable.js"></script>
  
  <script type="text/javascript">
      
     $(document).ready(function(){
        // MyOrders_DataTable();
        documentChecListTable();
    });

// function MyOrders_DataTable() {

//     var table = $('#myordertable').DataTable();
//      table.destroy();


//      var data = {'table_type':'assignment'};
//      var table = $('#myordertable').DataTable( {
//       scrollX:     true,
//       "scrollY": "400px",
//       scrollCollapse: true,
//       // paging:  true,
//       paging: true,
     
//       fixedColumns:   {
//         leftColumns: 1,
//         rightColumns: 1
//     },
//       stateSave: true,
//       'bDestroy': true,
//       "autoWidth": true,
//           "processing": true, //Feature control the processing indicator.
//           "serverSide": true, //Feature control DataTables' server-side processing mode.
//           "order": [], //Initial no order.
//           "pageLength": 50, // Set Page Length
//           "lengthMenu":[[10, 25, 50, 100], [10, 25, 50, 100]],
//           language: {
//             sLengthMenu: "Show _MENU_ Orders",
//             emptyTable:     "No Orders Found",
//             info:           "Showing _START_ to _END_ of _TOTAL_ Orders",
//             infoEmpty:      "Showing 0 to 0 of 0 Orders",
//             infoFiltered:   "(filtered from _MAX_ total Orders)",
//             zeroRecords:    "No matching Orders found",
//             processing: '<span class="progrss"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Processing...</span>'
//           },
//           // Load data for the table's content from an Ajax source
//           "ajax": {
//             "url": "<?php echo base_url('Myorders/GetAssignmentOrders')?>",
//             "type": "POST",
//             "data": data, 
//           }, 

//           //Set column definition initialisation properties.
//           columnDefs: [
//           {
//             orderable: false, targets:  "no-sort"
//           }]
//         });


//   }

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
        fixedColumns: {
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
          "url": "<?php echo base_url('Myorders/GetAssignmentOrders') ?>",
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