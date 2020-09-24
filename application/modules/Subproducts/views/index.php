
<!-- start body header -->
<div id="page_top" class="section-body">
    <div class="container-fluid">
        <div class="page-header">
            <div class="left">
                <h1 class="page-title">Sub Products</h1> 
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
                           <!--  <div class="form-group">
                              <select name="productfilter" id="productfilter" class="form-control select2">
                               <option value="0">--Select Product--</option>
                               <?php foreach ($ProductDetails as $key => $value) {?>
                                <option value="<?php echo $value->ProductUID;?>"><?php echo $value->ProductName;?></option>
                            <?php  } ?>
                        </select>
                          </div> -->
                            <a href="Subproducts/add" type="button" class="btn btn-primary btn-sm mt-2"><i class="fe fe-plus"></i> Add New Sub Product
                            </a>
                            <a class="float-right mr-4 pr-2 mt-2" href=""><button type="button" class="btn btn-red btn-sm" ><i class="fe fe-download"></i> Export as Excel</button></a>
                        </div>
                        <div class="card-options" style="position: absolute;right: 25px;">
                            <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                        </div>
                    </div>
                    <div class="card-body pb-4 pt-3">

                        <div class="col-md-12 col-lg-12">
                            <table class="table table-vcenter table-new custom-datatable text-nowrap" cellspacing="0" id="" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Sub Product Code</th>
                                        <th>Sub Product Name</th>
                                        <th>Products Name</th>
                                        <th>Report Heading</th>
                                        <th>Status</th>
                                        <th class="text-center" style="width:100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i=1;  foreach($SubproductDetails as $row): ?>

                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $row->SubProductCode; ?></td>
                                        <td><?php echo $row->SubProductName; ?></td>
                                        <td><?php echo $row->ProductName; ?></td>
                                        <td><?php echo $row->ReportHeading; ?></td>
                                        <td>
                                            <label class="custom-switch">
                                                <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input">
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description"></span>
                                            </label>
                                        </td>
                                        <td class="actions text-center">
                                            <a href="<?php echo base_url()."subproducts/edit_subproducts/".$row->SubProductUID; ?>" class="btn btn-sm btn-icon button-edit text-primary" title="Edit"><i class="icon-pencil"></i></a>
                                        </td>
                                    </tr>
                                 <?php $i++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<script type="text/javascript">
$(document).ready(function(){
// $("#productfilter").select2();

    $('.status').change(function(){ 
      var SubProductUID = $(this).attr('id');
      var status = $(this).val();
      if(status==1)
      {
        $('#'+SubProductUID).val('0');
        var status = 0;
      } else {
        $('#'+SubProductUID).val('1');
        var status = 1;
      } 
       $.ajax({
        type: "POST",
        url: "<?php echo base_url()?>subproducts/ajax_changestatus",
        dataType: "JSON",
        data: {'SubProductUID':SubProductUID,'status':status}, 
        cache: false,
        success: function(data)
        {
         if(data['validation_error']==0)
         {
          $.gritter.add({
           title: data['message'],
           class_name: 'color success',
           fade: true,
           time: 1000,
           speed:'slow',
          });
        } else {
         $.gritter.add({
          title: data['message'],
          class_name: 'color danger',
          fade: true,
          time: 1000,
          speed:'slow',
         });
        }
      }
    });
  });


    $('#example tfoot th').each( function () {
        var title = $('#example thead th').eq( $(this).index() ).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );

     $("#example").dataTable({
          processing: true,
          paging:true,
    // scrollY:"300px",
    scrollCollapse: true,
    // paging:false,
    responsive:true,
    scrollX:  true,
    pageLength: 15,
    lengthMenu: [[10, 15, 20, 25, 50, -1], [10, 15, 20, 25, 50, "All"]]
    // scrollY:  "300px",

        });


    $('#edit').click(function(){
      $('#edit').hide();
      $('td').each(function(){
        var content = $(this).html();
        $(this).html('<textarea>' + content + '</textarea>');
      });  
      
      $('#save').show();
    });

    $('#save').click(function(){
      $('#save').hide();
      $('textarea').each(function(){
        var content = $(this).val();//.replace(/\n/g,"<br>");
        $(this).html(content);
        $(this).contents().unwrap();    
      }); 

      $('#edit').show(); 
    });
 
     
    // Apply the filter
    $("#example tfoot input").on( 'keyup change', function () {
        var table = $('#example').DataTable();
        table
            .column( $(this).parent().index()+':visible' )
            .search( this.value )
            .draw();
    } );

    $("#example").on("click",".btnDelete", function(){
        var ID = $(this).attr('data-value');
    $.ajax({
            url: '<?php echo base_url();?>subproducts/check_subproduct_mapped_order',
            type: "POST",
            data: {SubProductUID:ID}, 
            dataType:'json',
            cache: false,
            success: function(data)
            {
              console.log(data);
              if(data.success == 1){
                $("#SubProductMapped").modal({
                  backdrop: 'static',
                  keyboard: false
                });

              } else {
                $("#alert-model").modal({
                  backdrop: 'static',
                  keyboard: false
                });
                $('.Yes').attr('data-ID',ID);
              }
            },
            error:function(jqXHR, textStatus, errorThrown)
            {
              console.log(jqXHR.responseText);
            }
          });

    return false;
    });

    $('.No').click(function(){
    setTimeout(function() {$('#alert-model').modal('hide');});
        setTimeout(function() {$('#SubProductMapped').modal('hide');});
    });

    $('.Yes').click(function(){
        var Id = $(this).attr('data-ID');
               $.ajax({
                 url: '<?php echo base_url();?>subproducts/delete_subproducts',
                 type: "POST",
                 data: {Id:Id}, 
                 dataType:'json',
                 cache: false,
                 success: function(data)
                 {
                  console.log(data);
                  if(data.validation_error == 1)
                  {
                                $.gritter.add({
                                title: data['message'],
                                class_name: 'color success',
                                fade: true,
                                time: 3000,
                                speed:'slow',
                                });
                                setTimeout(function() {$('#alert-model').modal('hide');});
                                setTimeout(function(){window.location.reload("<?php echo base_url();?>subproducts");}, 4000);   
                  }
                  else{
                      $.gritter.add({
                                title: data['message'],
                                class_name: 'color danger',
                                fade: true,
                                time: 3000,
                                speed:'slow',
                                });
                      setTimeout(function() {$('#alert-model').modal('hide');});
                      setTimeout(function(){window.location.reload("<?php echo base_url();?>subproducts");}, 4000);  
                  }

                 },
                error:function(jqXHR, textStatus, errorThrown)
                {
                  console.log(jqXHR.responseText);
                }
               });
    })


});
        

          $('#productfilter').change(function()
          {
                var productfilter =  $("#productfilter option:selected").val();
                $.ajax({
                type: "POST",
                data:{'ProductUID':productfilter},
                url: '<?php echo base_url();?>subproducts/get_SelectedSubProduct',
                success: function(data)
                {

                  console.log(data);
                  $('.filtered-data').html(data);
          

                },
                error:function(jqXHR, textStatus, errorThrown)
                {
                  console.log(jqXHR.responseText);
                }
              });
            
          });
    


    
</script>
