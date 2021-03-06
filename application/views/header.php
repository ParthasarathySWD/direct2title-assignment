<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta name="description" content="D2T">
    <meta name="author" content="D2T">
    <base href="<?php echo base_url(); ?>">
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <title>D2T</title>

    <!-- Bootstrap Core and vandor -->
    <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css" />

    <link rel="stylesheet" href="assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" />
    <link rel="stylesheet" href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
    <link rel="stylesheet" href="assets/plugins/multi-select/css/multi-select.css">
    <link rel="stylesheet" href="assets/plugins/dropify/css/dropify.min.css">
    <!-- datatable css -->
    <link rel="stylesheet" href="assets/plugins/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/plugins/datatable/fixedeader/dataTables.fixedcolumns.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/plugins/datatable/fixedeader/dataTables.fixedheader.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>

    <link rel="stylesheet" href="assets/plugins/jquery-steps/jquery.steps.css">
    <link rel="stylesheet" type="text/css" href="assets/lib/fSelect-Selectall/fSelect.css">
    <!-- Core css -->
    <link rel="stylesheet" href="assets/css/main.css"/>
    <link rel="stylesheet" href="assets/css/theme1.css" id="stylesheet"/>
    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/custom.css" rel="stylesheet" />

    <!-- jQuery and bootstrtap js -->
    <link rel="stylesheet" href="assets/vendor/toastr/toastr.css">
    <script src="assets/bundles/lib.vendor.bundle.js"></script>
    <script src="assets/js/core.js"></script>


</head>

<body class="font-poppins">

    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
        </div>
    </div>

    <!-- Start main html -->
    <div id="main_content">

        <!-- Small icon top menu -->
        <div id="header_top" class="header_top dark">
            <div class="container">
                <div class="hleft">
                    <div class="dropdown end-left-menu">
                        <a href="javascript:void(0)" class="nav-link user_btn"><img style="margin-top: 5px;" class="avatar" src="assets/images/user.png" alt=""/></a>
                        <a href="dashboard" class="with-icon"><i class="fe fe-pie-chart"></i><span>Dashboard</span></a>
                        
                        <a href="Order_entry" class="with-icon"><i class="icon-cloud-upload"></i><span>New Loan</span></a>
                        <a href="Myloans" class="with-icon active"><i class="fe fe-layers"></i><span>My Loans</span></a>
                        <a href="javascript:void(0)" class="with-icon"><i class="icon-bar-chart"></i><span>Report</span></a>
                        <!-- <a href="My_orders" class="with-icon"><i class="fe fe-layers"></i><span class="activity-badge">55</span><span>My Orders</span></a> -->
<!--                         <a href="page-search.html" class="nav-link icon"><i class="fa fa-search"></i></a>
                        <a href="index.html" class="nav-link icon"><i class="fa fa-home"></i></a>
                        <a href="app-email.html"  class="nav-link icon app_inbox"><i class="fa fa-envelope"></i></a>
                        <a href="app-chat.html"  class="nav-link icon xs-hide"><i class="fa fa-comments"></i></a>
                        <a href="app-filemanager.html"  class="nav-link icon app_file xs-hide"><i class="fa fa-folder"></i></a> -->
                    </div>
                </div>
                <div class="hright">
                    <div class="dropdown">
                        <a href="javascript:void(0)" class="nav-link icon settingbar"><i class="fa fa-bell"></i></a>
                        <a href="javascript:void(0)" class="nav-link icon menu_toggle"><i class="fa fa-navicon"></i></a>
                    </div>            
                </div>
            </div>
        </div>


