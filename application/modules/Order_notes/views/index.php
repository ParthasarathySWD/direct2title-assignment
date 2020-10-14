<style type="text/css">
    .nav-tabs.upload-tabs .nav-link {
        padding: 10px 0;
    }
    .notes-timeline .timeline_item .msg p{
        font-size:14px;
        margin-bottom:10px;
    }
    .notes-timeline .timeline_item .msg{
        margin-top:5px;
    }
    .notes-timeline .timeline_item .dated{
        font-size:12px;
    }
    .notes-timeline .timeline_item .msg .comment-link-action{
        font-size:13px;
    }
    .notes-timeline .timeline_item {
        padding: 20px 30px 1px 30px;
    }
    .notes_scroll{
        max-height:300px;
        overflow:auto;
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
                <h3 class="card-title pt-2">Notes Activity</h3>

                <div class="card-options" style="position: absolute;right: 20px;top: 60px;z-index:1;">
                  <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><span class="font-14 mr-2" style="position:relative;top:-2px;">View Fullscreen</span><i class="fe fe-maximize"></i></a>
              </div>
          </div>
      
          <div class="card-body pb-4">
              <div class="notes-timeline notes_scroll">

            <?php $count=0; foreach ($NotesInfo as $key => $value) { $count++; ?>
                
                <div class="timeline_item ">
                    <img class="tl_avatar" src="assets/images/icon.png" alt="" />
                    <span><a href="javascript:void(0);" title=""><?php echo $value->UserName; ?></a> <?php echo $value->SectionName; ?><small class="float-right text-right dated"><?php echo $value->CreatedOn; ?></small></span>
                    <div class="msg">   
                        <p><?php echo $value->Note; ?></p>
                        <a class="text-muted comment-link-action Cmtnote" role="button" data-toggle="collapse" href="#collapseExample_<?php echo $count; ?>" aria-expanded="false" aria-controls="  collapseExample" data-noteid="<?php echo $value->NoteUID; ?>" ><i class="fa fa-comments"></i> 1 Comment</a>
                        <div class="collapse p-4 section-gray mt-2" id="collapseExample_<?php echo $count; ?>">
                            <form class="well" id="NoteComment">
                                 <input type="hidden" id="OrderUID" name="OrderUID" value="<?php echo $OrderUID; ?>">
                                <input type="hidden" id="NoteUID" name="NoteUID" value="<?php echo $value->NoteUID; ?>" >

                                <div class="form-group">
                                    <textarea rows="2" class="form-control no-resize Noteinfo" name="Noteinfo" id="Noteinfo" placeholder="Enter comments..."></textarea>
                                </div>
                                <button class="btn btn-primary btn-sm BtnComment" id="BtnComment">Submit</button>
                            </form>
                            <ul class="recent_comments list-unstyled mt-4 mb-0" >
                                
                            </ul>
                        </div>
                    </div>                                
                </div>
          <?php } ?>

            </div>

            <div class="col-md-12 mt-3 mb-5">
                <div class="p-4 mt-2" style="background: #f4f5f8;">
                    <form action="" id="NoteSave">
                        <input type="hidden" id="OrderUID" name="OrderUID" value="<?php echo $OrderUID; ?>">
                        <div class="form-group">
                            <label class="form-label" for="NoteCmt">Notes</label> 
                            <textarea rows="3" class="form-control no-resize" id="NoteCmt" name="NoteCmt" placeholder="Enter Notes..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-5 col-lg-5">
                                <div class="form-group">
                                    <label class="form-label" for="NoteType">Note Type</label>
                                    <select name="NoteType" id="NoteType" class="form-control select2">
                                        <option></option> 
                                        <?php foreach ($sections as $key => $value) { ?>
                                            <option value="<?php echo $value->SectionUID; ?>"><?php echo $value->SectionName; ?></option>  
                                        <?php } ?>   
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4">
                            </div>
                            <div class="col-md-3 col-lg-3 mt-4 text-right">
                                <button class="btn btn-primary btn-sm" id="SaveNote">Send</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
</div>

<script type="text/javascript">

$('#SaveNote').click(function(event)
 {

  event.preventDefault();

  if($('#note-details').val()!=0){

    var form=$('#NoteSave');

    var formData=$('#NoteSave').serialize();
    $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>Order_notes/SaveNote',
     data: formData, 
     dataType:'json',
     cache: false,
     success: function(data)
     {
        toastr["success"]("", data.msg);
        window.location.replace('<?php echo base_url(); ?>Order_notes?OrderUID=<?php echo $OrderUID ?>');
    }
});
}
else
{
  toastr["error"]("", 'Required Fields are Empty !! Please Check !!');
}
});

$('.BtnComment').click(function(event)
 {
 
  event.preventDefault();

  var Org = $('.Noteinfo').val();

  alert(Org);
  if($('.Noteinfo').val()!=0){

    var form=$('#NoteComment');

    var formData=$('#NoteComment').serialize();
    $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>Order_notes/SaveNoteComment',
     data: formData, 
     dataType:'json',
     cache: false,
     success: function(data)
     {
        toastr["success"]("", data.msg);
        window.location.replace('<?php echo base_url(); ?>Order_notes?OrderUID=<?php echo $OrderUID ?>');
    }
});
}
else
{
  toastr["error"]("", 'Required Comments Field are Empty !! Please Check !!');
}
});


$('.Cmtnote').click(function(event)
 {
  event.preventDefault();

 var NoteAttr = $(this).attr('data-noteid');

   $.ajax({
     type: "POST",
     url: '<?php echo base_url();?>Order_notes/CommentsAdd',
     data: {'NoteAttr':NoteAttr}, 
     dataType:'json',
     cache: false,
     success: function(data)
     {  
        $('.recent_comments').html(data);
        // toastr["success"]("", data.msg);
        // window.location.replace('<?php echo base_url(); ?>Order_notes?OrderUID=<?php echo $OrderUID ?>');
    }
});

});

</script>
