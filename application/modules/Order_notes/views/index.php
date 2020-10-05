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
                <div class="timeline_item ">
                    <img class="tl_avatar" src="assets/images/icon.png" alt="" />
                    <span><a href="javascript:void(0);" title="">Rose Luchette</a> Customer Note<small class="float-right text-right dated">19-April-2019 - Yesterday</small></span>
                    <div class="msg">
                        <p>I'm speaking with myself, number one, because I have a very good brain and I've said a lot of things. I write the best placeholder text, and I'm the biggest developer on the web card she has is the Lorem card.</p>
                        <a class="text-muted comment-link-action" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-comments"></i> 1 Comment</a>
                        <div class="collapse p-4 section-gray mt-2" id="collapseExample">
                            <form class="well">
                                <div class="form-group">
                                    <textarea rows="2" class="form-control no-resize" placeholder="Enter comments..."></textarea>
                                </div>
                                <button class="btn btn-primary btn-sm">Submit</button>
                            </form>
                            <ul class="recent_comments list-unstyled mt-4 mb-0">
                                <li>
                                    <div class="avatar_img">
                                        <img class="rounded img-fluid" src="assets/images/icon.png" alt="">
                                    </div>
                                    <div class="comment_body">
                                        <h6>Donald Gardner <small class="float-right font-14">Just now</small></h6>
                                        <p>Lorem ipsum Veniam aliquip culpa laboris minim tempor</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>                                
                </div>

                <div class="timeline_item ">
                    <img class="tl_avatar" src="assets/images/icon.png" alt="" />
                    <span><a href="javascript:void(0);" title="">Rose Luchette</a> Customer Note<small class="float-right text-right">19-April-2019 - Yesterday</small></span>
                    <div class="msg">
                        <p>I'm speaking with myself, number one, because I have a very good brain and I've said a lot of things. on the web by far... While that's mock-ups and this is politics, are they really so different? I think the only card she has is the Lorem card.</p>
                        <a class="text-muted comment-link-action" role="button" data-toggle="collapse" href="#collapseExample1" aria-expanded="false" aria-controls="collapseExample1"><i class="fa fa-comments"></i> 2 Comment</a>
                        <div class="collapse p-4 section-gray mt-2" id="collapseExample1">
                            <form class="well">
                                <div class="form-group">
                                    <textarea rows="2" class="form-control no-resize" placeholder="Enter comments..."></textarea>
                                </div>
                                <button class="btn btn-primary btn-sm">Submit</button>
                            </form>
                            <ul class="recent_comments list-unstyled mt-4 mb-0">
                                <li>
                                    <div class="avatar_img">
                                        <img class="rounded img-fluid" src="assets/images/icon.png" alt="">
                                    </div>
                                    <div class="comment_body">
                                        <h6>Donald Gardner <small class="float-right font-14">Just now</small></h6>
                                        <p>Lorem ipsum Veniam aliquip culpa laboris minim tempor</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="avatar_img">
                                        <img class="rounded img-fluid" src="assets/images/icon.png" alt="">
                                    </div>
                                    <div class="comment_body">
                                        <h6>Dessie Parks <small class="float-right font-14">1min ago</small></h6>
                                        <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking</p>
                                    </div>
                                </li>
                            </ul>
                        </div>                                    
                    </div>
                </div>

                <div class="timeline_item ">
                    <img class="tl_avatar" src="assets/images/icon.png" alt="" />
                    <span><a href="javascript:void(0);" title="" >Rochelle Barton</a> San Francisco, CA <small class="float-right text-right">12-April-2019</small></span>
                    <div class="msg">
                        <p>I'm speaking with myself, number one, because I have a very good brain and I've said a lot of things. I write the best placeholder text, and I'm the biggest developer on the web by far... While that's mock-ups and this is politics, is the Lorem card.</p>
                        <a class="text-muted comment-link-action" role="button" data-toggle="collapse" href="#collapseExample2" aria-expanded="false" aria-controls="collapseExample2"><i class="fa fa-comments"></i> 1 Comment</a>
                        <div class="collapse p-4 section-gray mt-2" id="collapseExample2">
                            <form class="well">
                                <div class="form-group">
                                    <textarea rows="2" class="form-control no-resize" placeholder="Enter comments..."></textarea>
                                </div>
                                <button class="btn btn-primary btn-sm">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mt-3 mb-5">
                <div class="p-4 mt-2" style="background: #f4f5f8;">
                    <div class="form-group">
                        <label class="form-label" for="note-type">Notes</label>
                        <textarea rows="3" class="form-control no-resize" placeholder="Enter Notes..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-5 col-lg-5">

                            <div class="form-group">
                                <label class="form-label" for="note-type">Note Type</label>
                                <select name="note-type" id="note-type" class="form-control select2">
                                    <option></option>  
                                    <option value="1">System Note</option>  
                                    <option value="1">EDI Trans Note</option>  
                                    <option value="1">E-Commerce Note</option>  
                                    <option value="1">Abst Notification</option>  
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                        </div>
                        <div class="col-md-3 col-lg-3 mt-4 text-right">
                            <button class="btn btn-primary btn-sm">Send</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
</div>

<script type="text/javascript">

</script>
