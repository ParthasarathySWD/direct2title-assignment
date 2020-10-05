<style type="text/css">
    .card-title{
        font-size: 20px;
        font-weight: 500;
        font-style: normal;
        color: #393a3d;
        padding-top: 5px;
    }
    .new-custom-main-tab{
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
    }
    .new-custom-main-tab li{
        -ms-flex-preferred-size: 0;
        -webkit-box-flex: 1;
        -ms-flex-positive: 1;
        flex-grow: 1;
    }
    .new-datatable1 thead tr th{
        font-size: 14px;
        padding-top: 5px;
        padding-bottom: 5px;
        color: #393a3d;
        font-weight: bold;
        border-right: 1px solid #ddd;
    }
    .new-datatable1 thead tr th.sorting_asc{
        font-weight: 600;
        background-color: #F4F5F8;
    }
    .new-datatable1 thead tr th.sorting_desc{
        font-weight: 600;
        background-color: #F4F5F8;
    }
    .new-datatable1  thead tr:first-child th{
        border-bottom:2px solid #ddd!important;
    }
    .new-datatable1 tbody tr td{
        border-top:0;
        border-bottom:1px solid #ddd;
    }
    .new-datatable1 thead tr th:last-child{
        border-right:0;
    }
    .new-custom-main-tab{
        margin:0;
        padding:0;
    }
    .new-custom-main-tab li{
        list-style: none;
    }
    .new-custom-main-tab li a{
        color:#fff;
        padding:5px 10px;
        display:block;
        font-size:13px;
    }
    .new-custom-main-tab li a span.count{
        display:block;
        font-size:20px;
    }
    .new-custom-main-tab li a span.count i{
        font-size:16px;
    }
    .new-custom-main-tab li:nth-child(1) a{
        background: #ff8000;
    }
    .new-custom-main-tab li:nth-child(2) a{
        background: #babec5;
        margin-right:5px;
    }
    .new-custom-main-tab li:nth-child(3) a{
        background: #78c500;
    }
    .new-custom-main-tab li:nth-child(1) a:hover, .new-custom-main-tab li:nth-child(1) a.active{
        border-bottom: 6px solid #a95500;
        margin-top: -6px;
    }
    .new-custom-main-tab li:nth-child(2) a:hover, .new-custom-main-tab li:nth-child(2) a.active{
       border-bottom: 6px solid #84878c;
       margin-top: -6px;
   }
   .new-custom-main-tab li:nth-child(3) a:hover, .new-custom-main-tab li:nth-child(3) a.active{
    border-bottom: 6px solid #5a9400;
    margin-top: -6px;
}
.new-custom-main-tab-head{
    margin:0;
    padding:0;

    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
}
.new-custom-main-tab-head li{
    -ms-flex-preferred-size: 0;
    -webkit-box-flex: 1;
    -ms-flex-positive: 1;
    flex-grow: 1;
    list-style: none;
    font-size:13px;
    padding-bottom: 7px;
    color: #396598;
}


</style>
<!-- start body header -->
<div id="page_top" class="section-body">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">New page</h1> 
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
                <div class="card" style="border-radius: 0;">
                    <div class="card-header pb-3">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 pl-0">
                                    <h3 class="card-title pt-2">My Orders</h3>
                                </div>
                                <div class="col-md-6 col-lg-6 text-right pr-0 pt-2">
                                    <!-- Split dropup button -->
                                    <div class="btn-group dropdown">
                                        <button type="button" class="btn btn-success pl-3 pr-3" style="position: relative;right: -4px;border-top-left-radius: 20px;border-bottom-left-radius: 20px;">
                                            <i class="fe fe-plus"></i>  Add New
                                        </button>
                                        <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split pr-3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-top-right-radius: 20px;border-bottom-right-radius: 20px;">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-plus"></i> New Order </a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View Order </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-4 pt-1">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="new-custom-main-tab-head nav-tabs nav">
                                    <li style="width:50%;">Total Orders</li>
                                    <li style="width:50%;">Completed</li>
                                </ul>
                            </div>
                            <div class="col-md-12">
                                <ul class="new-custom-main-tab nav-tabs nav">
                                    <li style="width:25%;"><a data-toggle="tab" href="#all-1"><span class="count"><i class="icon-doc"></i> 100</span>Total Orders</a></li>
                                    <li style="width:25%;"><a data-toggle="tab" href="#all-2"><span class="count"><i class="icon-close"></i> 100</span>Cancelled Orders</a></li>
                                    <li style="width:50%;"><a data-toggle="tab" href="#all-3"><span class="count"><i class="fe fe-check-circle"></i> 100</span>Completed Orders</a></li>
                                </ul>
                            </div>
                        </div>

<!--                         <ul class="nav nav-tabs b-none new-custom-main-tab">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#all-1"><i class="icon-home"></i> <span>100</span> <br/><span>My Ordes</span></a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#all-2">Pending orders</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#all-3">Completed Orders</a></li>
                        </ul> -->
                        <div class="tab-content" style="display:none;">
                            <div class="tab-pane fade active show" id="all-1">1
                            </div>
                            <div class="tab-pane fade" id="all-2">2
                            </div>
                            <div class="tab-pane fade" id="all-3">3
                            </div>
                        </div>

                        <div class="row mt-4 pt-3">
                            <div class="col-md-3 pl-4">
                                <div class="dropdown">
                                    <i class="fe fe-corner-left-down font-22" style="    margin-right: 0px;
                                    position: relative;
                                    top: 12px;left:-3px;color: #6c757d;"></i>
                                    <button class=" pl-3 pr-3 btn btn btn-outline-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 36px;">
                                        Batch Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View Order </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-icon mb-1">
                                    <input type="text" class="form-control" placeholder="Search">
                                    <span class="input-icon-addon"><i class="fe fe-search"></i></span>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="actions">
                                    <button class="btn btn-sm btn-icon text-primary pl-0 mt-1" title="Delete"><b><i class=" font-18 fe fe-printer"></i></b></button>
                                    <button class="btn btn-sm btn-icon text-primary pl-0 mt-1" title="Delete"><b><i class=" font-18 fe fe-upload"></i></b></button>
                                    <button class="btn btn-sm btn-icon text-primary pl-0 mt-1" title="Delete"><b><i class=" font-18 fe fe fe-settings"></i></b></button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-vcenter text-nowrap resizable new-datatable1">
                            <thead>
                                <tr>
                                    <th class="nosort">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </th>
                                    <th>User</th>
                                    <th>Usage</th>
                                    <th>Activity</th>
                                    <th class="text-center">Satisfaction</th>
                                    <th class="text-center nosort">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div>Teresa Reyes</div>
                                        <div class="small text-muted">Registered: Mar 4, 2018</div>
                                    </td>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left"><strong>36%</strong></div>
                                            <div class="float-right"><small class="text-muted">Jun 11, 2019 - Jul 10, 2019</small></div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-red" role="progressbar" style="width: 36%" aria-valuenow="36" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Last login</div>
                                        <div>2 minutes ago</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="mx-auto chart-circle chart-circle-xs" data-value="0.36" data-thickness="3" data-color="blue"><canvas width="40" height="40"></canvas>
                                            <div class="chart-circle-value">36%</div>
                                        </div>
                                    </td>
                                    <td class="text-center">

                                        <div class="item-action dropdown ml-2">
                                            <a href="javascript:void(0)" data-toggle="dropdown">View <i class="fa fa-caret-down ml-1"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View Order </a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-trash"></i> Cancel Order</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div>Emma Wade</div>
                                        <div class="small text-muted">Registered: Mar 20, 2018</div>
                                    </td>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left"><strong>7%</strong></div>
                                            <div class="float-right"><small class="text-muted">Jun 11, 2019 - Jul 10, 2019</small></div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-red" role="progressbar" style="width: 7%" aria-valuenow="7" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Last login</div>
                                        <div>a minute ago</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="mx-auto chart-circle chart-circle-xs" data-value="0.07" data-thickness="3" data-color="blue"><canvas width="40" height="40"></canvas>
                                            <div class="chart-circle-value">7%</div>
                                        </div>
                                    </td>
                                    <td class="text-center">

                                        <div class="item-action dropdown ml-2">
                                            <a href="javascript:void(0)" data-toggle="dropdown">View <i class="fa fa-caret-down ml-1"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View Order </a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-trash"></i> Cancel Order</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div>Carol Henderson</div>
                                        <div class="small text-muted">Registered: Feb 22, 2018</div>
                                    </td>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left"><strong>80%</strong></div>
                                            <div class="float-right"><small class="text-muted">Jun 11, 2019 - Jul 10, 2019</small></div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-green" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Last login</div>
                                        <div>9 minutes ago</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="mx-auto chart-circle chart-circle-xs" data-value="0.8" data-thickness="3" data-color="blue"><canvas width="40" height="40"></canvas>
                                            <div class="chart-circle-value">80%</div>
                                        </div>
                                    </td>
                                    <td class="text-center">

                                        <div class="item-action dropdown ml-2">
                                            <a href="javascript:void(0)" data-toggle="dropdown">View <i class="fa fa-caret-down ml-1"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View Order </a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-trash"></i> Cancel Order</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                            <span class="custom-control-label"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div>Christopher Harvey</div>
                                        <div class="small text-muted">Registered: Jan 22, 2018</div>
                                    </td>
                                    <td>
                                        <div class="clearfix">
                                            <div class="float-left"><strong>65%</strong></div>
                                            <div class="float-right"><small class="text-muted">Jun 11, 2019 - Jul 10, 2019</small></div>
                                        </div>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-green" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Last login</div>
                                        <div>8 minutes ago</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="mx-auto chart-circle chart-circle-xs" data-value="0.83" data-thickness="3" data-color="blue"><canvas width="40" height="40"></canvas>
                                            <div class="chart-circle-value">83%</div>
                                        </div>
                                    </td>
                                    <td class="text-center">

                                        <div class="item-action dropdown ml-2">
                                            <a href="javascript:void(0)" data-toggle="dropdown">View <i class="fa fa-caret-down ml-1"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View Order </a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                                                <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-trash"></i> Cancel Order</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        (function () {
    // hold onto the drop down menu                                             
    var dropdownMenu;

    // and when you show it, move it to the body                                     
    $(window).on('show.bs.dropdown', function (e) {

        // grab the menu        
        dropdownMenu = $(e.target).find('.dropdown-new-menu');

        // detach it and append it to the body
        $('body').append(dropdownMenu.detach());

        // grab the new offset position
        var eOffset = $(e.target).offset();

        // make sure to place it where it would normally go (this could be improved)
        dropdownMenu.css({
            'display': 'block',
            'top': eOffset.top + $(e.target).outerHeight(),
            'left': eOffset.left
        });
    });

    // and when you hide it, reattach the drop down, and hide it normally                                                   
    $(window).on('hide.bs.dropdown', function (e) {
        $(e.target).append(dropdownMenu.detach());
        dropdownMenu.hide();
    });
})();




$(function(){
    (function($) {
        $.fn.resizableColumns = function() {
          var isColResizing = false;
          var resizingPosX = 0;
          var _table = $(this);
          var _thead = $(this).find('thead');

          _table.innerWidth(_table.innerWidth());
          _thead.find('th').each(function() {
            $(this).css('position', 'relative');
            $(this).innerWidth($(this).innerWidth());
            if ($(this).is(':not(:last-child)')) $(this).append("<div class='resizer' style='position:absolute;top:0px;right:-3px;bottom:0px;width:6px;z-index:999;background:transparent;cursor:col-resize'></div>");
        })

          $(document).mouseup(function(e) {
            _thead.find('th').removeClass('resizing');
            isColResizing = false;
            e.stopPropagation();
        })

          _table.find('.resizer').mousedown(function(e) {
            _thead.find('th').removeClass('resizing');
            $(this).closest('th').addClass('resizing');
            resizingPosX = e.pageX;
            isColResizing = true;
            e.stopPropagation();
        })

          _table.mousemove(function(e) {
            if (isColResizing) {

              var _resizing = _thead.find('th.resizing .resizer');

              if (_resizing.length == 1) {

                var _nextRow = _thead.find('th.resizing + th');
                var _pageX = e.pageX || 0;
                var _widthDiff = _pageX - resizingPosX;
                var _setWidth = _resizing.closest('th').innerWidth() + _widthDiff;
                var _nextRowWidth = _nextRow.innerWidth() - _widthDiff;
                if (resizingPosX != 0 && _widthDiff != 0 && _setWidth > 50 && _nextRowWidth > 50) {
                  _resizing.closest('th').innerWidth(_setWidth);
                  resizingPosX = e.pageX;
                  _nextRow.innerWidth(_nextRowWidth);
              }

          }
      }
  })
      };
  }
  (jQuery))
    $('table.resizable').resizableColumns();

    $('.new-datatable1').DataTable({
            // scrollX:true,
            paging:false,
            searching:false,
            info: false


        });



})
</script>
