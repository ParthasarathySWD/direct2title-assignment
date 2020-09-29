
<link href="<?php echo base_url('/assets/lib/jquery-ui-1.12.1.custom/jquery-ui.min.css'); ?>" rel="stylesheet" />

<style type="text/css">

.modal70 > .modal-dialog {
  width:70% !important;
  }
  .underline{
    text-decoration: underline;
  }

</style>

<h5 id="review_msg" class="text-center" style="font-size: 16px"><?php echo $Title; ?></h5>
<div class="row" id="PopUPLoador">
  <div class="col-lg-12">
    <div class="col-sm-12">

      <div class="col-sm-12" style="font-size: 12px;">

        <form id="frmgenerateprint">
          <?php

          foreach ($OrderUIDs as $key => $order) { ?>
            <input type="hidden" class="PrintOrderUID" name="OrderUID[]" value="<?php echo $order; ?>">
            
            <?php
          }

          ?>
          <div class="col-sm-6">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error" >

              <select name="Signor" id="Signor" class="mdl-select2 select2 mdl-textfield__input input-xs" >
                <option></option>
                <?php
                foreach($Sigors as $signor)
                {
                  echo '<option value="'.$signor->SignorUID.'">'.$signor->SignorName.'</option>';

                } ?> 
              </select>                                      
              <label class="mdl-textfield__label" for="Signor">Signor </label>
              <span class="mdl-textfield__error form_error"></span>
            </div>

          </div>
          <div class="col-sm-6">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error" >

              <select name="Notary" id="Notary" class="mdl-select2 select2 mdl-textfield__input input-xs" >
                <!-- <option></option> -->
                <?php
                foreach($Notarys as $notary)
                {
                  echo '<option value="'.$notary->NotaryUID.'">'.$notary->NotaryName.'</option>';

                } ?> 
              </select>                                      
              <label class="mdl-textfield__label" for="Notary">Notary </label>
              <span class="mdl-textfield__error form_error"></span>
            </div>

          </div>
          <div class="col-sm-6">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
              <input class="mdl-textfield__input  input-xs" type="text" id="Witness1" name="Witness1" >
              <label class="mdl-textfield__label" for="Witness1">Witness 1</label>
              <span class="mdl-textfield__error form_error"></span>
            </div> 
          </div>
          <div class="col-sm-6">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error">
              <input class="mdl-textfield__input  input-xs" type="text" id="Witness2" name="Witness2">
              <label class="mdl-textfield__label" for="Witness2">Witness 2</label>
              <span class="mdl-textfield__error form_error"></span>
            </div> 
          </div>

          <?php if (!empty($OrderDocuments)) { ?>
            
          <div class="col-sm-12">
            <table id="documents-table" style="border:1px solid #ddd" class="table table-striped table-inverse table-borded">
              <thead class="thead-inverse">
                <tr>
                  <th class="text-center">#</th>
                  <th>Doc File Name</th>
                  <th style="text-align: center;">Type of Doc</th>
                  <th style="text-align: center;">Doc Created Date</th>
                  <th style="text-align: center;">Uploaded User</th>
                  <th style="text-align: center;">Action</th>
                </tr>
              </thead>

              <tbody class="ui-sortable" style="">

                <?php foreach ($OrderDocuments as $key => $value) { ?>
                  
                <tr data-orderuid="<?php echo $value->OrderUID; ?>" data-position="<?php echo $key + 1; ?>" data-documentname="<?php echo $value->DocumentFileName; ?>">

                  <td style="text-align: center;">
                    <div class="be-checkbox be-checkbox-color inline">
                      <input type="checkbox" class="mergedocs" id="mergedocs<?php echo $key + $i; ?>"  data-position="<?php echo $key + 1; ?>" data-documentname="<?php echo $value->DocumentFileName; ?>">
                      <label for="mergedocs<?php echo $key + $i; ?>"></label>

                    </div>

                  </td>

                  <td style="text-align: left;"><?php echo $value->DisplayFileName; ?></td>

                  <td style="text-align: center;"><?php echo $value->TypeOfDocument; ?></td>

                  <td style="text-align: center;"><?php echo date('m/d/Y H:i:s', strtotime($value->DocumentCreatedDate)); ?></td>

                  <td style="text-align: center;"><?php echo $value->UserName; ?></td>
                  
                  <td style="text-align: center;">
                    <span style="text-align: center;width:100%;display:inline;">
                      <a target="_blank" href="<?php echo base_url() . $value->OrderDocsPath . $value->DocumentFileName; ?>" class="btn">
                        <span class="mdi mdi-eye"> PDF</span>
                      </a>
                    </span>
                  </td>

                </tr>
                <?php } ?>


              </tbody>
            </table>
          </div>
          <?php } ?>
          <div class="col-sm-12 text-right">
            <button type="submit" class="btn btn-success" id="generatedocument">Print</button>
            <button type="button" data-dismiss="modal" class="btn btn-default modal-close ">Cancel</button>
          </div>
        </form>


        <div class="panel panel-default panel-table" id="divPrint">
          <div class="col-sm-12 text-right">
            <button type="button" class="btn btn-success" id="btnPrintAll" onClick="PrintFunc()">Print</button>
            <button type="button" data-dismiss="modal" class="btn btn-default modal-close ">Cancel</button>
          </div>
        </div> <!-- divPrint ends -->

      </div>
    </div>
  </div>
</div>
</div>

<div class="row" id="divPrintComplete"></div>

<div>


  <iframe src="" id="myFrame" title="Test Title" frameborder="0" style="border:0; display:none;" width="300 " height="300" >
  </iframe>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/lib/jquery-ui-1.12.1.custom/jquery-ui.min.js'); ?>"></script>
<script type="text/javascript">
  $(function () {
    select_mdl();

    $('.select2').select2({
      theme: "bootstrap",
    });

    componentHandler.upgradeDom();
    
  });

  $('#documents-table tbody').sortable({
    axis: 'y',
    stop: function(e, ui) {
      // updatePosition();
    },
    placeholder: "ui-state-highlight",
  });

  $('#divPrint').hide();
  $('#divDownload').hide();

  function SingleOption(key){
  //alert(key);
  if(key == 'P'){
    $('#divPrint').show();
    $('#divDownload').hide();
    $('.btnPrintOption').css('background-color','#84a9e6');
    $('.btnDownloadOption').css('background-color','#4285f4');
  }else{
    $('#divPrint').hide();
    $('#divDownload').show();
    $('.btnPrintOption').css('background-color','#4285f4');
    $('.btnDownloadOption').css('background-color','#84a9e6');
  }
}

function MergeDownload()
{
  var documents=[];
  $('#documents-table tbody tr').each(function(key, value){
    var obj=new Object();
    obj.documentname=$(this).attr('data-documentname');
    obj.position=$(this).attr('data-position');
    obj.orderid=$(this).attr('data-orderuid');
    documents.push(obj);
  });

  $.ajax({
    url: "<?php echo base_url('Printing_Orders/merge_download');?>",
    data: {"documentfilenames":documents},
    xhrFields: {
      responseType: 'blob',
    },
    type:"POST",
    cache: false,
    beforeSend: function(){
      $('#btnDownload').attr('disabled',true); 
      $('#btnDownload').html("<span class='fa fa-spinner fa-spin'></span> Downloading..."); 
    },
    success: function(data)
    {

      var filename = "MergedPDF.pdf";
      if (typeof window.chrome !== 'undefined') {
            // Chrome version
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = "MergedPDF.pdf";
            link.click();
          } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
            // IE version
            var blob = new Blob([data], { type: 'application/pdf' });
            window.navigator.msSaveBlob(blob, filename);
          } else {
            // Firefox version
            var file = new File([data], filename, { type: 'application/force-download' });
            window.open(URL.createObjectURL(file));
          }
          setTimeout(function(){
            $('#PopUPLoador').hide();
            $('#divPrintComplete').html('<div class="col-sm-12 text-center"><p id="review_msg" class="text-center" style="padding: 5px 0px; color:#000;font-weight: 600; font-size: 16px">Are the orders Downloaded successfully?</p><p class="text-center"><button type="button" class="btn btn-success" id="btnPrintComplete" onClick="PrintCompleteFunc()" style="margin-right: 10px;">Yes, Printing Complete</button><button type="button" data-dismiss="modal" class="btn btn-default modal-close " >No</button></p></div>');
          },3000);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {

        },
      });

}

function SingleDownload(){
  //var documents=[];
  $('#documents-table tbody tr').each(function(key, value){
    var obj=new Object();
    obj.documentname=$(this).attr('data-documentname');
    obj.position=$(this).attr('data-position');
    obj.orderid=$(this).attr('data-orderuid');
    //documents.push(obj);

    $.ajax({
      url: "<?php echo base_url('Printing_Orders/SingleDownload');?>",
      data: obj,
      xhrFields: {
        responseType: 'blob',
      },
      type:"POST",
      cache: false,
      beforeSend: function(){
        $('#btnDownload').attr('disabled',true); 
        $('#btnDownload').html("<span class='fa fa-spinner fa-spin'></span> Downloading...");
      },
      success: function(data)
      {

        var filename = obj.documentname;
        if (typeof window.chrome !== 'undefined') {
            // Chrome version
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(data);
            link.download = filename;
            link.click();
          } else if (typeof window.navigator.msSaveBlob !== 'undefined') {
            // IE version
            var blob = new Blob([data], { type: 'application/pdf' });
            window.navigator.msSaveBlob(blob, filename);
          } else {
            // Firefox version
            var file = new File([data], filename, { type: 'application/force-download' });
            window.open(URL.createObjectURL(file));
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR.responseText);
        },
        complete: function () {
          setTimeout(function(){
            $('#PopUPLoador').hide();
            $('#divPrintComplete').html('<div class="col-sm-12 text-center"><p id="review_msg" class="text-center" style="padding: 5px 0px; color:#000;font-weight: 600; font-size: 16px">Are the orders Downloaded successfully?</p><p class="text-center"><button type="button" class="btn btn-success" id="btnPrintComplete" onClick="PrintCompleteFunc()" style="margin-right: 10px;">Yes, Printing Complete</button><button type="button" data-dismiss="modal" class="btn btn-default modal-close " >No</button></p></div>');
          },3000);   
        }
      }); 

  });
}

function DownloadFunc(){
  var key = [];
  $(".DownloadTypeCheck:checked").each(function() {
   key = $(this).val();
 });

  if(key == 'Single'){
    SingleDownload();
  }else{
    MergeDownload();
  }
}

function PrintFunc(){
  var documents=[];
  $('#documents-table tbody tr').each(function(key, value){
    var obj=new Object();
    obj.documentname=$(this).attr('data-documentname');
    obj.position=$(this).attr('data-position');
    obj.orderid=$(this).attr('data-orderuid');
    documents.push(obj);
  });

  $.ajax({
    url: "<?php echo base_url('Printing_Orders/merge_print');?>",
    data: {"documentfilenames":documents},
    type:"POST",
    cache: false,
    beforeSend: function(){
      $('#btnPrintAll').attr('disabled',true); 
      $('#btnPrintAll').html("<span class='fa fa-spinner fa-spin'></span> Loading...");        
        //$('#PopUPLoador').addClass('be-loading-active');
      },
      success: function(data)
      {
        $('#myFrame').attr('src',data);

        setTimeout(function(){ 
          var objFra = document.getElementById('myFrame'); 
          $.when(objFra.contentWindow.focus()).done(function () {
            $.when(objFra.contentWindow.print()).done(function () {
              setTimeout(function(){
                $('#PopUPLoador').hide();
                $('#divPrintComplete').html('<div class="col-sm-12 text-center"><p id="review_msg" class="text-center" style="padding: 5px 0px; color:#000;font-weight: 600; font-size: 16px">Are the orders printed successfully?</p><p class="text-center"><button type="button" class="btn btn-success" id="btnPrintComplete" onClick="PrintCompleteFunc()" style="margin-right: 10px;">Yes, Printing Complete</button><button type="button" data-dismiss="modal" class="btn btn-default modal-close " >No</button></p></div>'); 
              }, 1000);
            })
          })
        }, 1000);

        // $('#PopUPLoador').removeClass('be-loading-active');
      },
      error: function(jqXHR, textStatus, errorThrown)
      {

      },
    });  
}

function PrintCompleteFunc(){
  // alert("DDd");
  var documents=[];
  $('.PrintOrderUID').each(function(key, value){
    var obj=new Object();
    obj.orderid= $(value).val();
    documents.push(obj);
      // alert(documents);
    });
     // console.log(documents);
     $.ajax({
      url: "<?php echo base_url('Printing_Orders/print_complete');?>",
      data: {"documentfilenames":documents},
      type:"POST",
      cache: false,
      beforeSend: function(){
        $('#btnPrintComplete').attr('disabled',true); 
        $('#btnPrintComplete').html("<span class='fa fa-spinner fa-spin'></span> Loading...");        
        //$('#PopUPLoador').addClass('be-loading-active');
      },
      success: function(data)
      {
        setTimeout(function(){
          $('#divPrintComplete').html('<div class="col-sm-12 text-center"><p id="review_msg" class="text-center" style="padding: 5px 0px; color:#000;font-weight: 600; font-size: 16px">Order Completed</p><p class="text-center"><button type="button" data-dismiss="modal" class="btn btn-default modal-close " >Okay</button></p></div>');
        },1000);
        setTimeout(function(){
          window.location.reload();
        },2000);

        // $('#PopUPLoador').removeClass('be-loading-active');
      },
      error: function(jqXHR, textStatus, errorThrown)
      {

      },
    });   
   }

   $(document).off('submit', '#frmgenerateprint').on('submit', '#frmgenerateprint', function (e) {
    
    var me=$(this);
    e.preventDefault();
    e.stopPropagation();
    var formdata=new FormData($(this)[0]);
    var button_text=$('#generatedocument').html();

    $('.mergedocs:checked').each(function (key, element) {
      console.log($(element).attr('data-documentname'));
      formdata.append('mergedocs[]', $(element).attr('data-documentname'));
    });

    $.ajax({
      url: "<?php echo base_url('Printing_Orders/GenerateDocument');?>",
      data: formdata,
      dataType: 'json',
      type:"POST",
      cache: false,
      processData: false,
      contentType: false,
      beforeSend: function(){
        $('#generatedocument').prop('disabled',true); 
        $('#generatedocument').html("<span class='fa fa-spinner fa-spin'></span> Loading...");        
        //$('#PopUPLoador').addClass('be-loading-active');
      },
      success: function(data)
      {

        if (data.validation_error==1) {
          $.each(data, function(k, v) {
            $('#'+ k +'.select2').next().find('span.select2-selection').addClass('errordisplay');
            $('#'+k).closest('div.is_error').addClass('is-invalid');
          });
          
        }
        else{

          $('#myFrame').attr('src',data.source);

          setTimeout(function(){ 
            var objFra = document.getElementById('myFrame'); 
            // debugger;
            objFra.contentWindow.title = data.title;
            console.log('Title', objFra.title);
            $.when(objFra.contentWindow.focus()).done(function () {
              $.when(objFra.contentWindow.print()).done(function () {
                setTimeout(function(){
                  $('#PopUPLoador').hide();
                  $('#divPrintComplete').html('<div class="col-sm-12 text-center"><p id="review_msg" class="text-center" style="padding: 5px 0px; color:#000;font-weight: 600; font-size: 16px">Are the orders printed successfully?</p><p class="text-center"><button type="button" class="btn btn-success" id="btnPrintComplete" onClick="PrintCompleteFunc()" style="margin-right: 10px;">Yes, Printing Complete</button><button type="button" data-dismiss="modal" class="btn btn-default modal-close " >No</button></p></div>'); 
                }, 1000);
              })
            })
          }, 1000);

        }
        $('#generatedocument').prop('disabled',false); 
        $('#generatedocument').html(button_text);
        console.log(data);
        // $('#PopUPLoador').removeClass('be-loading-active');
      },
      error: function(jqXHR, textStatus, errorThrown)
      {

      },
    });   

  })




   function updatePosition()
   {
    $.map($('#documents-table tbody').find('tr'), function(el) {
      $(el).attr('data-position', $(el).index()+1);
      $(el).find('td:first').text($(el).index() + 1);

    });  
  }


</script>
