
<?php $OrderUID = $_GET['OrderUID']; ?>


<!-- <li class="nav-item ">
	<a class="nav-link <?php echo $this->uri->segment(1) == "Order_summary" ? 'active' : ''; ?> " href="Order_summary?OrderUID=<?php echo $OrderUID; ?>" role="tab" >Summary</a>
</li> -->

<div class="card-header pb-0 pt-0 pl-3 pr-3 order-sum-tab" >

  <div class="col-md-12 p-0">
    <ul class="nav nav-tabs" role="tablist" style="border-bottom:1px solid #fff;">
      <li class="nav-item">
        <a class="nav-link <?php echo $this->uri->segment(1) == "Order_summary" ? 'active' : ''; ?> " href="Order_summary?OrderUID=<?php echo $OrderUID; ?>"><i class="fe fe-bar-chart-2 menu-icon"></i> Summary</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo $this->uri->segment(1) == "Order_info" ? 'active' : ''; ?> " href="Order_info?OrderUID=<?php echo $OrderUID; ?>"><i class="fe fe-file-text menu-icon"></i> Order Info</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo $this->uri->segment(1) == "Order_reports" ? 'active' : ''; ?> " href="Order_reports?OrderUID=<?php echo $OrderUID; ?>"><i class="fe fe-paperclip menu-icon"></i> Reports</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#News" aria-expanded="false"><i class="fe fe-send menu-icon"></i> Delivery</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#News" aria-expanded="false"><i class="fe fe-message-square menu-icon"></i> Notes</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#News" aria-expanded="false"><i class="fe fe-layers menu-icon"></i> History</a>
      </li>
    </ul>
  </div>
</div>
