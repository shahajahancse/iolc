<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
             <div class="box-header with-border">
                <h3 class="box-title">My Room Bookings</h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo site_url('admin/room_management/book_room'); ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> New Booking
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Room details</th>
                                <th>Schedule</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($bookings)): ?>
                                <tr><td colspan="3" class="text-center p-4">No bookings found. <a href="<?php echo site_url('admin/room_management/book_room'); ?>">Book now!</a></td></tr>
                            <?php else: ?>
                                <?php foreach($bookings as $book): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $book->room_name; ?></strong>
                                            <?php if($book->employee_id != $user_id): ?>
                                                <span class="label label-info">Invited</span>
                                            <?php endif; ?>
                                            <br>
                                            <small><?php echo $book->purpose; ?></small>
                                        </td>
                                        <td>
                                            <?php echo date('d M, Y', strtotime($book->meeting_date)); ?><br>
                                            <small><i class="fa fa-clock-o"></i> 
                                                <?php echo date('h:i A', strtotime($book->start_time)); ?> - 
                                                <?php echo date('h:i A', strtotime($book->end_time)); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php 
                                                $status_label = '<span class="label label-warning">Pending</span>';
                                                if($book->status == 'approved') $status_label = '<span class="label label-success">Approved</span>';
                                                if($book->status == 'rejected') $status_label = '<span class="label label-danger">Rejected</span>';
                                                echo $status_label;
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
