<?php if(empty($report)): ?>
    <div class="alert alert-warning text-center">No movements found for the selected period.</div>
<?php else: ?>
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-map-marker"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Movements</span>
                    <span class="info-box-number"><?php echo count($report); ?></span>
                </div>
            </div>
        </div>
        <!-- Add more summary cards if needed -->
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="reportTable">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Start Location</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Activities</th>
                    <th>Duration</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $sl=1; foreach($report as $row): ?>
                    <tr>
                        <td><?php echo $sl++; ?></td>
                        <td><?php echo date('d M Y', strtotime($row->created_at)); ?></td>
                        <td><?php echo $row->first_name . ' ' . $row->last_name; ?></td>
                        <td>
                            <?php echo $row->start_location; ?><br>
                            <small class="text-muted"><?php echo date('h:i A', strtotime($row->created_at)); ?></small>
                        </td>
                        <td><?php echo $row->purpose; ?></td>
                        <td>
                            <?php if($row->status == 'active'): ?>
                                <span class="label label-success">Active</span>
                            <?php else: ?>
                                <span class="label label-default">Completed</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-purple"><i class="fa fa-users"></i> <?php echo $row->meeting_count; ?> Meetings</span>
                            <span class="badge bg-orange"><i class="fa fa-car"></i> <?php echo $row->travel_count; ?> Trips</span>
                        </td>
                        <td>
                            <?php 
                                if($row->end_time) {
                                    $start = new DateTime($row->start_time);
                                    $end = new DateTime($row->end_time);
                                    $diff = $start->diff($end);
                                    echo $diff->format('%h hr %i min');
                                } else {
                                    echo '<span class="text-success">Ongoing</span>';
                                }
                            ?>
                        </td>
                         <td>
                            <a href="<?php echo site_url('new_movement/details_text/'.$row->id); ?>" target="_blank" class="btn btn-sm btn-info icon-btn"><i class="fa fa-eye"></i> Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
