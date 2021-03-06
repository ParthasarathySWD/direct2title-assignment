                  <div class="row top-box pl-1 pr-1 m-0" style="">
                    <div class="col-md-5 hh-grayBox text-left box-left mt-2 user_name_sec">

                        <p class="page-title mb-2 name" style="color:#fff!important;font-size: 17px;
                        "><i class="fe fe-user"></i> New Client</p> 
                        <p class="name-p"><i class="fe fe-bookmark"></i> Status : <span class="badge badge-primary" style="color:#fff;">Active</span></p>
                    </div>
                    <div class="col-md-2 hh-grayBox text-center box-center">
                        <ul class="list-group mt-2">
                            <li class="list-group-item p-0">
                                <div class="clearfix">
                                    <div class="float-left"><b class=""><i class="fe fe-check-circle"></i> Complete</b></div>
                                    <div class="float-right"><strong>35%</strong></div>
                                </div>
                                <div class="progress progress-xs mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 35%;height:5px;" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-5 hh-grayBox text-right box-right mt-1">
                        <p class="name-p"><i class="fe fe-mail"></i> Email Id : <span>mail@email.com</span></p>
                        <p class="name-p"><i class="fe fe-phone"></i> Contact No : <span>(987) 654-3210</span></p>
                    </div>
                </div>

              <div class="card-header pb-0 pt-0 pl-3 pr-3 order-sum-tab">
                    <div class="col-md-12">

                        <ul class="nav nav-tabs pr-3 clients-intab" role="tablist">
                      <?php if($Action == 'AddDetails'){ ?> 
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#clientinfo">Client Info</a></li>
                             <!-- <li class="nav-item"><a class="nav-link" href="Clients/contacts" aria-expanded="true">Contacts</a></li>  -->
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#clientpricing">Pricing</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#clientproducts">Products</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#clientworkflows">Workflows  & Templates</a></li>
                         <!--  <li class="nav-item"><a class="nav-link" href="Clients/task_management" aria-expanded="true">Task Management</a></li>  -->
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#clientprioritytat">Priority  & Tat</a></li>
                          <!--   <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#clientdefaultproduct">Others</a></li> -->
                            <!--<li class="nav-item"><a class="nav-link" href="Clients/billing" aria-expanded="true">Billing</a></li>
                            <li class="nav-item"><a class="nav-link" href="<CENTER></CENTER>lients/audit_log" aria-expanded="true">Audit Logs</a></li> -->
                        <?php } elseif($Action == 'EditDetails'){ ?>
                            <li class="nav-item"><a class="nav-link"  href= "<?php echo base_url().'Clients/Edit/'.$Customers->CustomerUID?>" aria-expanded="true">Client Info</a></li>
                            <li class="nav-item"><a class="nav-link" href= "<?php echo base_url().'Clients/contacts/'.$Customers->CustomerUID?>"   aria-expanded="true">Contacts</a></li>
                            <li class="nav-item"><a class="nav-link" href= "<?php echo base_url().'Clients/pricing/'.$Customers->CustomerUID?>" aria-expanded="true">Pricing</a></li>
                            <li class="nav-item"><a class="nav-link" href= "<?php echo base_url().'Clients/products/'.$Customers->CustomerUID?>"aria-expanded="true">Products</a></li>
                            <li class="nav-item"><a class="nav-link" href= "<?php echo base_url().'Clients/workflows/'.$Customers->CustomerUID?>"aria-expanded="true">Workflows &amp; Templates</a></li>
                            <li class="nav-item"><a class="nav-link"  href= "<?php echo base_url().'Clients/task_management/'.$Customers->CustomerUID?>"aria-expanded="true">Task Management</a></li>
                            <li class="nav-item"><a class="nav-link"  href= "<?php echo base_url().'Clients/priority_tat/'.$Customers->CustomerUID?>"aria-expanded="true">Priority &amp; TAT</a></li>
                            <li class="nav-item"><a class="nav-link"  href= "<?php echo base_url().'Clients/pass_through_cost/'.$Customers->CustomerUID?>"aria-expanded="true">Pass Through cost</a></li>   
                            <li class="nav-item"><a class="nav-link"  href= "<?php echo base_url().'Clients/billing/'.$Customers->CustomerUID?>"aria-expanded="true">Billing</a></li>
                            <li class="nav-item"><a class="nav-link"  href= "<?php echo base_url().'Clients/audit_log/'.$Customers->CustomerUID?>"aria-expanded="true">Audit Logs</a></li>

                        <?php } ?>

                        </ul>

                    </div>
                    <div class="card-options" style="position: absolute;right: 20px;">
                        <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                    </div>
                </div> 

                           

                <script type="text/javascript">
                    $(function(){
                        var url = window.location.pathname, 
                        urlRegExp = new RegExp(url.replace(/\/$/,''));
                        $('.clients-intab li a').each(function(){
                            if(urlRegExp.test(this.href)){
                                $(this).addClass('active');
                            }
                        });
                    });
                </script>