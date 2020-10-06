<style type="text/css">
    .order_due{
        position: absolute;
        right: 170px;
        width: auto;
        font-size: 13px;
        text-align: right;
    }
</style>
<?php $this->load->view('workflowview/workflow_topheader'); ?>

<div class="section-body">
    <div class="container-fluid">
      <div class="row" style="margin:0 -15px;">
        <div class="col-md-12 col-lg-12 p-0">
          <div class="card m-0" style="border-radius:0;">
           <?php $this->load->view('workflowview/workflow_menu'); ?>


           <div class="card-header">
            <h3 class="card-title pt-2 text-primary">WorkFlow Activity</h3>

            <h3 class="card-title pt-2 text-danger order_due">Order Due by 08/28/2020 03:20 PM</h3>

            <div class="card-options" style="position: absolute;right: 20px;top: 60px;z-index:1;">
              <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><span class="font-14 mr-2" style="position:relative;top:-2px;">View Fullscreen</span><i class="fe fe-maximize"></i></a>
          </div>
      </div>
      <div class="card-body pb-1">
        <div class="col-md-12">

            <table class="table table-vcenter table-new" style="width:100%;">
                <thead>
                    <tr>
                        <th>WorkFlowModule</th>
                        <th>Assigned User</th>
                        <th>AssignedDateTime</th>
                        <th>Status</th>
                        <th>OnholdDateTime</th>
                        <th>CompletedDateTime</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Search</td>
                        <td>Rachel Brock</td>
                        <td>08/25/2020 03:24 PM</td>
                        <td>
                            <label class="badge badge-primary">Completed</label>
                        </td>
                        <td>08/25/2020 03:24 PM</td>
                        <td>08/25/2020 03:24 PM</td>
                    </tr>
                    <tr>
                        <td>Typing</td>
                        <td>Rachel Brock</td>
                        <td>08/25/2020 03:24 PM</td>
                        <td>
                            <label class="badge badge-info">Progress</label>
                        </td>
                        <td>08/25/2020 03:24 PM</td>
                        <td>08/25/2020 03:24 PM</td>
                    </tr>
                    <tr>
                        <td>TaxCert</td>
                        <td>Rachel Brock</td>
                        <td>08/25/2020 03:24 PM</td>
                        <td>
                            <label class="badge badge-danger">Pending</label>
                        </td>
                        <td>08/25/2020 03:24 PM</td>
                        <td>08/25/2020 03:24 PM</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-header">
        <h3 class="card-title pt-2 text-primary">Loan Activity</h3>
    </div>
    <div class="card-body pb-4">
        <div class="col-md-12">

            <table class="table table-vcenter table-new history-datatable" cellspacing="0" style="width:100%;">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Module</th>
                        <th>Operations</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>08/26/2020 12:52 PM</td>
                        <td>EditOrder</td>
                        <td>ANOTHER RUSH AT ORDER PLACEMENT: IsSigningAddress Changed from property to</td>
                        <td>
                            <div>James</div>
                            <div class="small text-muted">Administrator</div>
                        </td>
                    </tr>
                    <tr>
                        <td>08/26/2020 12:52 PM</td>
                        <td>EditOrder</td>
                        <td>Order Edit Disabled For order_summary</td>
                        <td>
                            <div>James</div>
                            <div class="small text-muted">Administrator</div>
                        </td>
                    </tr>
                    <tr>
                        <td>08/26/2020 12:52 PM</td>
                        <td>EditOrder</td>
                        <td>ANOTHER RUSH AT ORDER PLACEMENT: PRBirthDate Changed from 0000-00-00 to</td>
                        <td>
                            <div>James</div>
                            <div class="small text-muted">Administrator</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

</div>
</div>
</div>

<script type="text/javascript">

   $( document ).ready(function() {
     $('.history-datatable').DataTable({

        pageLength: 5,
        lengthMenu: [[5, 10, 15, 25, 50, -1], [5, 10, 15, 20, 25, 50, "All"]],
    });
 });
</script>
