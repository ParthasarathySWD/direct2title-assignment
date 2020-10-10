<link href="assets/css/module/state/state.css" rel="stylesheet">
<!-- start body header -->
<div id="page_top" class="section-body bg-new-header-top">
    <div class="container-fluid">
        <div class="page-header" style="border:0;">
            <div class="left">
                <h1 class="page-title">My Cities</h1> 
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
    <div class="container-fluid-1">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card" style="border-radius: 0;">
                    <div class="card-header pb-3">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 pl-0">
                                    <h3 class="card-title pt-2">Cities</h3>
                                </div>
                                <div class="col-md-6 col-lg-6 text-right pr-0 pt-2">

                                  <!-- Split dropup button -->
                                  <div class="btn-group dropdown">
                                    <a href="City/add" type="button" class="btn btn-success pl-3 pr-3" style="position: relative;right: -4px;border-top-left-radius: 20px;border-bottom-left-radius: 20px;">
                                        <i class="fe fe-plus"></i>  Add City
                                    </a>
                                    <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split pr-3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-top-right-radius: 20px;border-bottom-right-radius: 20px;">
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="City/add" class="dropdown-item"><i class="dropdown-icon fa fa-plus"></i> New City </a>
                                        <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fe fe-upload"></i> Import Cities </a>
                                    </div>
                                </div>
                                    <div class="card-options" style="display: inline-block; z-index:1;">
                                      <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><span class="font-14 mr-2" style="position:relative;top:-2px;">View Fullscreen</span><i class="fe fe-maximize"></i></a>
                                  </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-4 pt-1 new-table-search">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="new-custom-main-tab-head">
                                <li style="width:50%;">Total</li>
                                <li style="width:50%;">Active & In Active</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <ul class="new-custom-main-tab nav-tabs nav" style="border:0;">
                                <li style="width:50%;"><a data-toggle="tab" href="#all-1"><span class="count"><i class="icon-doc"></i> 100</span>Total Cities</a></li>
                                <li style="width:25%;"><a data-toggle="tab" href="#all-3"><span class="count"><i class="fe fe-check-circle"></i> 100</span>Active Cities</a></li>
                                <li style="width:25%;"><a data-toggle="tab" href="#all-4"><span class="count"><i class="icon-close"></i> 100</span>In Active Cities</a></li>
                            </ul>
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
                                    <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View Cities </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9 text-right">
                            <div class="actions">
                                <button class="btn btn-sm btn-icon text-primary pl-0 mt-1" title="Import"><b><i class=" font-18 fe fe-upload"></i></b></button>
                                <button class="btn btn-sm btn-icon text-primary pl-0 mt-1" title="Export"><b><i class=" font-18 fa fa-cloud-download"></i></b></button>
                                <button class="btn btn-sm btn-icon text-primary pl-0 mt-1" title="Print"><b><i class=" font-18 fe fe-printer"></i></b></button>
                                <!-- <button class="btn btn-sm btn-icon text-primary pl-0 mt-1" title="Delete"><b><i class=" font-18 fe fe fe-settings"></i></b></button> -->
                            </div>
                        </div>
                    </div>
                    <table class="table table-vcenter text-nowrap resizable new-datatable1">
                        <thead>
                            <tr>
                                <th class="nosort" style="width:50px;">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
                                        <span class="custom-control-label"></span>
                                    </label>
                                </th>
                                <th>City Name</th>
                                <th>County Name</th>
                                <th>State Name</th>
                                <th>ZipCode</th>
                                <th>Status</th>
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
                                <td>PORTSMOUTH</td>
                                <td>ROCKINGHAM</td>
                                <td>New Hampshire</td>
                                <td>210</td>
                                <td>
                                    <label class="custom-switch">
                                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked="">
                                        <span class="custom-switch-indicator"></span>
                                    </label>
                                </td>
                                <td class="text-center">

                                    <div class="item-action dropdown ml-2">
                                        <a href="javascript:void(0)" data-toggle="dropdown" style="font-weight: bold; color: #464bac; ">View City<i class="fe fe-more-vertical" style="vertical-align: middle; font-size: 20px !important;"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View City </a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon icon-close"></i> In Active</a>
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
                                <td>PLEASANTVILLE</td>
                                <td>WESTCHESTER</td>
                                <td>New York</td>
                                <td>401</td>
                                <td>
                                    <label class="custom-switch">
                                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked="">
                                        <span class="custom-switch-indicator"></span>
                                    </label>
                                </td>
                                <td class="text-center">

                                    <div class="item-action dropdown ml-2">
                                        <a href="javascript:void(0)" data-toggle="dropdown" style="font-weight: bold; color: #464bac; ">View City<i class="fe fe-more-vertical" style="vertical-align: middle; font-size: 20px !important;"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View City </a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon icon-close"></i> In Active</a>
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
                                <td>HOLTSVILLE</td>
                                <td>SUFFOLK</td>
                                <td>New York</td>
                                <td>501</td>
                                <td>
                                    <label class="custom-switch">
                                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                    </label>
                                </td>
                                <td class="text-center">

                                    <div class="item-action dropdown ml-2">
                                        <a href="javascript:void(0)" data-toggle="dropdown" style="font-weight: bold; color: #464bac; ">View City<i class="fe fe-more-vertical" style="vertical-align: middle; font-size: 20px !important;"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View City </a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fe fe-check-circle"></i> Active</a>
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
                                <td>ADJUNTAS</td>
                                <td>ADJUNTAS</td>
                                <td>Puerto Rico</td>
                                <td>601</td>
                                <td>
                                    <label class="custom-switch">
                                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked="">
                                        <span class="custom-switch-indicator"></span>
                                    </label>
                                </td>
                                <td class="text-center">

                                    <div class="item-action dropdown ml-2">
                                        <a href="javascript:void(0)" data-toggle="dropdown" style="font-weight: bold; color: #464bac; ">View City<i class="fe fe-more-vertical" style="vertical-align: middle; font-size: 20px !important;"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View City </a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon icon-close"></i> In Active</a>
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
                                <td>AGUADILLA</td>
                                <td>AGUADILLA</td>
                                <td>Puerto Rico</td>
                                <td>603</td>
                                <td>
                                    <label class="custom-switch">
                                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                    </label>
                                </td>
                                <td class="text-center">

                                    <div class="item-action dropdown ml-2">
                                        <a href="javascript:void(0)" data-toggle="dropdown" style="font-weight: bold; color: #464bac; ">View City<i class="fe fe-more-vertical" style="vertical-align: middle; font-size: 20px !important;"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View City </a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                                            <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fe fe-check-circle"></i> Active</a>
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
      "ordering": false,
      "lengthChange": false ,
      "pageLength": 5,
      "language": {

        "paginate": {
          "previous": '<',
          "next":     '>'
      },
  },
});

})
$(document).ready(function(){ 
    $('.new-table-search div.dataTables_wrapper div.dataTables_filter input').each(function(ev)
    {
      if(!$(this).val()) { 
         $(this).attr("placeholder", "Search");
     }
 });
});
</script>
