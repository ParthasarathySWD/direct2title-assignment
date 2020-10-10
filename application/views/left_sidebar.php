    <!-- start User detail -->
    <div class="user_div">
        <h5 class="brand-name mb-4">User Crush<a href="javascript:void(0)" class="user_btn"><i class="icon-close"></i></a></h5>
        <div class="card">
            <img class="card-img-top" src="assets/images/gallery/6.jpg" alt="Card image cap">
            <div class="card-body">
                <h5 class="card-title">Daniel Kristeen</h5>
                <p class="card-text">795 Folsom Ave, Suite 600 San Francisco, 94107</p>
                <div class="row">
                    <div class="col-4">
                        <h6><strong>3265</strong></h6>
                        <small>Post</small>
                    </div>
                    <div class="col-4">
                        <h6><strong>1358</strong></h6>
                        <small>Followers</small>
                    </div>
                    <div class="col-4">
                        <h6><strong>10K</strong></h6>
                        <small>Likes</small>
                    </div>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">michael@example.com</li>
                <li class="list-group-item">+ 202-555-2828</li>
                <li class="list-group-item">October 22th, 1990</li>
            </ul>
            <div class="card-body">
                <a href="javascript:void(0);" class="card-link">View More</a>
                <a href="javascript:void(0);" class="card-link">Another link</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label class="d-block">Total Income<span class="float-right">77%</span></label>
                    <div class="progress progress-xs">
                        <div class="progress-bar bg-blue" role="progressbar" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100" style="width: 77%;"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="d-block">Total Expenses <span class="float-right">50%</span></label>
                    <div class="progress progress-xs">
                        <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;"></div>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="d-block">Gross Profit <span class="float-right">23%</span></label>
                    <div class="progress progress-xs">
                        <div class="progress-bar bg-green" role="progressbar" aria-valuenow="23" aria-valuemin="0" aria-valuemax="100" style="width: 23%;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="d-block">Storage <span class="float-right">77%</span></label>
            <div class="progress progress-sm">
                <div class="progress-bar" role="progressbar" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100" style="width: 77%;"></div>
            </div>
            <button type="button" class="btn btn-primary btn-block mt-3">Upgrade Storage</button>
        </div>
    </div>

    <!-- start Main menu -->
    <div id="left-sidebar" class="sidebar" >
        <div class="d-flex justify-content-between brand_name">
            <h5 class="brand-name"><img src="assets/images/logo.png"/></h5>
            <div class="theme_btn">
                <a class="theme1" data-toggle="tooltip" title="Theme Radical" href="#" onclick="setStyleSheet('assets/css/theme1.css', 0);"></a>
                <a class="theme2" data-toggle="tooltip" title="Theme Turmeric" href="#" onclick="setStyleSheet('assets/css/theme2.css', 0);"></a>
                <a class="theme3" data-toggle="tooltip" title="Theme Caribbean" href="#" onclick="setStyleSheet('assets/css/theme3.css', 0);"></a>
                <a class="theme4" data-toggle="tooltip" title="Theme Cascade" href="#" onclick="setStyleSheet('assets/css/theme4.css', 0);"></a>
            </div>
        </div>
        <div class="input-icon">
            <span class="input-icon-addon">
                <i class="fe fe-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Search...">
        </div>
        <ul class="nav nav-tabs b-none">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#all-tab"><i class="fa fa-list-ul"></i> All</a></li>
            <!-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#app-tab">Elements</a></li> -->
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#setting-tab">Settings</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active show" id="all-tab">
                <nav class="sidebar-nav">
                    <ul class="metismenu ci-effect-1">
                        <li class="g_heading">Directories</li>
                        <li class="active"><a href="Dashboard"><i class="icon-home"></i><span data-hover="Dashboard">Dashboard</span></a></li>
                        <li><a href="Order_entry"><i class="icon-calendar"></i><span data-hover="Place&nbsp;Order">Place Order</span></a></li>
                        <li><a href="Myorders"><i class="icon-speech"></i><span data-hover="My&nbsp;Orders">My Orders</span></a></li>
                        <li><a href="Review_orders"><i class="icon-notebook"></i><span data-hover="Review&nbsp;Orders">Review Orders</span></a></li>
                        <li><a href="Printing_Orders"><i class="icon-globe"></i><span data-hover="Printing&nbsp;Orders">Printing Orders</span></a></li>
                        <li><a href="Completed_Orders"><i class="fa fa-list" data-toggle="tooltip" title="" data-original-title="fa fa-list"></i><span data-hover="Completed&nbsp;Orders">Completed Orders</span></a></li>
                        <li class="g_heading">Product Setup</li>
                        <li><a href="Clients"><i class="icon-users"></i><span data-hover="Clients">Clients</span></a></li>
                        <li><a href="Products"><i class="icon-tag"></i><span data-hover="Products">Products</span></a></li>
                        <li><a href="Subproducts"><i class="icon-docs"></i><span data-hover="Sub&nbsp;Products">Sub Products</span></a></li>
                        <li><a href="Template"><i class="icon-layers"></i><span data-hover="Templates">Templates</span></a></li>
                        <li><a href="Projects"><i class="icon-folder-alt"></i><span data-hover="Projects">Projects</span></a></li>
                        <li>
                            <a href="javascript:void(0)" class="has-arrow arrow-b"><i class="icon-map"></i><span data-hover="Location">Location</span></a>
                            <ul>
                                <li><a href="City"><span data-hover="Cities">Cities</span></a></li>
                                <li><a href="County"><span data-hover="Counties">Counties</span></a></li>
                                <li><a href="State"><span data-hover="States">States</span></a></li>
                            </ul>
                        </li>   
                        <li><a href="Organization"><i class="icon-folder-alt"></i><span data-hover="Organization">Organization</span></a></li>             
                    </ul>
                </nav>
            </div>
            <div class="tab-pane fade" id="setting-tab">
                <div class="mb-4 mt-3">
                    <h6 class="font-14 font-weight-bold text-muted">Font Style</h6>
                    <div class="custom-controls-stacked font_setting">
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="font" value="font-opensans">
                            <span class="custom-control-label">Open Sans Font</span>
                        </label>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="font" value="font-montserrat">
                            <span class="custom-control-label">Montserrat Google Font</span>
                        </label>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="font" value="font-poppins" checked="">
                            <span class="custom-control-label">Poppins Google Font</span>
                        </label>
                    </div>
                </div>
                <div class="mb-4">
                    <h6 class="font-14 font-weight-bold text-muted">Dropdown Menu Icon</h6>
                    <div class="custom-controls-stacked arrow_option">
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="marrow" value="arrow-a" checked="">
                            <span class="custom-control-label">A</span>
                        </label>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="marrow" value="arrow-b">
                            <span class="custom-control-label">B</span>
                        </label>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="marrow" value="arrow-c">
                            <span class="custom-control-label">C</span>
                        </label>
                    </div>
                    <h6 class="font-14 font-weight-bold mt-4 text-muted">SubMenu List Icon</h6>
                    <div class="custom-controls-stacked list_option">
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="listicon" value="list-a" checked="">
                            <span class="custom-control-label">A</span>
                        </label>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="listicon" value="list-b">
                            <span class="custom-control-label">B</span>
                        </label>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="listicon" value="list-c">
                            <span class="custom-control-label">C</span>
                        </label>
                    </div>
                </div>
                <div>
                    <h6 class="font-14 font-weight-bold mt-4 text-muted">General Settings</h6>
                    <ul class="setting-list list-unstyled mt-1 setting_switch">
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">Night Mode</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-darkmode">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">Fix Navbar top</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-fixnavbar">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">Header Dark</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-pageheader">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">Min Sidebar Dark</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-min_sidebar" checked="">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">Sidebar Dark</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-sidebar">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">Icon Color</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-iconcolor">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">Gradient Color</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-gradient">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">Box Shadow</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-boxshadow">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">RTL Support</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-rtl">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                        <li>
                            <label class="custom-switch">
                                <span class="custom-switch-description">Box Layout</span>
                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input btn-boxlayout">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- start main body part-->
    <div class="page">

