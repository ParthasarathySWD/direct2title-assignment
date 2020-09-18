
<?php $OrderUID = $_GET['OrderUID']; ?>


<!-- <li class="nav-item ">
	<a class="nav-link <?php echo $this->uri->segment(1) == "Order_summary" ? 'active' : ''; ?> " href="Order_summary?OrderUID=<?php echo $OrderUID; ?>" role="tab" >Summary</a>
</li> -->



                    <ul class="nav nav-tabs" role="tablist">
                        <!-- <li class="nav-item"><a class="nav-link active"href="Order_summary">Summary</a></li> -->
                        <li class="nav-item ">
                        	<a class="nav-link <?php echo $this->uri->segment(1) == "Order_summary" ? 'active' : ''; ?> " href="Order_summary?OrderUID=<?php echo $OrderUID; ?>" role="tab" >Summary</a>
                        </li>
                          <li class="nav-item ">
                            <a class="nav-link <?php echo $this->uri->segment(1) == "Order_info" ? 'active' : ''; ?> " href="Order_info?OrderUID=<?php echo $OrderUID; ?>" role="tab" >Order Info</a>
                        </li>
                      <!--   <li class="nav-item"><a class="nav-link" href="javascript:void(0);">Issues</a></li> -->
                        <li class="nav-item"><a class="nav-link" href="Order_attachments">Attachments</a></li>
                        <li class="nav-item"><a class="nav-link" href="Order_notes">Notes</a></li>
                      <!--   <li class="nav-item"><a class="nav-link" href="Order_preview">Preview</a></li> -->

                       <li class="nav-item ">
                            <a class="nav-link <?php echo $this->uri->segment(1) == "Order_reports" ? 'active' : ''; ?> " href="Order_reports?OrderUID=<?php echo $OrderUID; ?>" role="tab" >Reports</a>
                        </li>
                        <!-- <li class="nav-item"><a class="nav-link" href="Order_reports">Reports</a></li> -->
                    </ul>