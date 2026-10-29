<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
             <div class="box-header with-border">
                <h3 class="box-title">Booking Management List</h3>
                <div class="box-tools pull-right">
                    <?php if($is_admin): ?>
                    <a href="<?php echo site_url('admin/room_management'); ?>" class="btn btn-primary">
                        <i class="fa fa-calendar-plus-o"></i>  Room List
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo site_url('admin/room_management/book_room'); ?>" class="btn btn-success">
                        <i class="fa fa-calendar-plus-o"></i> Book Room
                    </a>
                </div>
            </div>
            <div class="box-body">
                <?php
                    $pending_count = 0;
                    foreach($bookings as $b) {
                        if($b->status == 'pending') $pending_count++;
                    }
                ?>
                <?php if($pending_count > 0): ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-warning"></i> Attention!</h4>
                    You have <strong><?php echo $pending_count; ?></strong> pending booking request(s) requiring approval.
                </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="approvalTable">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Room details</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($bookings as $book): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $book->first_name . ' ' . $book->last_name; ?></strong><br>
                                        <small class="text-muted">Requester</small>
                                    </td>
                                    <td>
                                        <strong><?php echo $book->room_name; ?></strong><br>
                                        <small><?php echo $book->purpose; ?></small>
                                        <?php if(!empty($book->message)): ?>
                                            <br><small class="text-danger" style="font-style: italic;"><strong>Note:</strong> <?php echo $book->message; ?></small>
                                        <?php endif; ?>
                                        <?php if(!empty($book->attendees)): ?>
                                            <div style="margin-top: 5px;">
                                                <small class="text-info"><i class="fa fa-users"></i> Attendees:</small><br>
                                                <?php
                                                    $attendee_ids = explode(',', $book->attendees);
                                                    foreach($attendee_ids as $aid) {
                                                        if(isset($employee_map[$aid])) {
                                                            echo '<span class="label label-info" style="margin-right:2px; display:inline-block; margin-bottom:2px;">' . $employee_map[$aid] . '</span> ';
                                                        }
                                                    }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo date('d M, Y', strtotime($book->meeting_date)); ?><br>
                                        <small>
                                            <i class="fa fa-clock-o"></i>
                                            <?php echo date('h:i A', strtotime($book->start_time)); ?> -
                                            <?php echo date('h:i A', strtotime($book->end_time)); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                            // Status Labels
                                            $status_label = '<span class="label label-warning">Pending</span>';
                                            if($book->status == 'approved') $status_label = '<span class="label label-success">Approved</span>';
                                            if($book->status == 'rejected') $status_label = '<span class="label label-danger">Rejected</span>';
                                            echo $status_label;
                                        ?>
                                    </td>
                                    <td>
                                        <?php if($book->status == 'pending'): ?>
                                            <a href="<?php echo site_url('admin/room_management/update_status/'.$book->id.'/approved'); ?>"
                                               class="btn btn-success btn-sm" title="Approve"
                                               onclick="return confirm('Approve this booking? This will lock the slot.')">
                                                <i class="fa fa-check"></i>
                                            </a>
                                            <a href="<?php echo site_url('admin/room_management/update_status/'.$book->id.'/rejected'); ?>"
                                               class="btn btn-danger btn-sm" title="Reject"
                                               onclick="return confirm('Reject this booking?')">
                                                <i class="fa fa-times"></i>
                                            </a>

                                            <button type="button" class="btn btn-primary btn-sm edit-time-btn"
                                                data-id="<?php echo $book->id; ?>"
                                                data-start="<?php echo $book->start_time; ?>"
                                                data-end="<?php echo $book->end_time; ?>"
                                                data-toggle="modal" data-target="#editTimeModal" title="Edit Time">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fa fa-lock"></i> Locked</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Time Modal -->
<div class="modal fade" id="editTimeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <form action="<?php echo site_url('admin/room_management/edit_booking_time'); ?>" method="post">
            <input type="hidden" name="booking_id" id="modal_booking_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Timing</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" name="start_time" id="modal_start" class="form-control" required>
                    </div>
                     <div class="form-group">
                        <label>End Time</label>
                        <input type="time" name="end_time" id="modal_end" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Update Time</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.edit-time-btn').click(function(){
        $('#modal_booking_id').val($(this).data('id'));
        $('#modal_start').val($(this).data('start'));
        $('#modal_end').val($(this).data('end'));
    });
});
</script>
