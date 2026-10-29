<?php $get_animate = $this->Xin_model->get_content_animate();?>
<div class="row m-b-1 <?php echo $get_animate;?>">
    <style>
        p {
            padding-bottom: 10px;
        }
    </style>
    <!-- ================= EVENT Details ================= -->
    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"> Events Details </h3>
            </div>
            <div class="box-body">
                <div class="col-md-7">
                    <p>Title : <?php echo $event->event_title; ?></p>
                </div>
                <div class="col-md-5">
                    <p>Location : <?php echo $event->location; ?></p>
                </div>
                <div class="col-md-3">
                    <p>Start Date : <?php echo date('d-m-Y', strtotime($event->start_event_date)); ?></p>
                </div>
                <div class="col-md-3">
                    <p>Start Time : <?php echo $event->start_event_time; ?></p>
                </div>
                <div class="col-md-3">
                    <p>End Date : <?php echo date('d-m-Y', strtotime($event->end_event_date)); ?></p>
                </div>
                <div class="col-md-3">
                    <p>End Time : <?php echo $event->end_event_time; ?></p>
                </div>
                <div class="col-md-12">
                    <p>Event Description : <?php echo $event->event_note; ?></p>
                </div>
                <div class="col-md-12">
                    <p>Event Remarks : <?php echo $event->event_remark; ?></p>
                </div>
                <div class="col-md-12">
                    <?php $lastObject = end($participants); ?>
                    <p>Participants : <span style="color: #4589b3;font-size: 15px; font-weight: 500;">
                        <?php foreach ($participants as $participant) {
                            if ($lastObject->user_id == $participant->user_id) {
                                echo $participant->first_name.' '.$participant->last_name;
                            } else {
                                echo $participant->first_name.' '.$participant->last_name.', ';
                            }
                        }?>
                    </span></p>
                </div>
            </div>
        </div>
    </div>
</div>
