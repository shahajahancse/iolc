<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Movement Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style type="text/css">
        body { background: #fff; padding: 20px; }
	table thead tr th {
		font-size:12px;
		padding: 5px !important;
        background-color: #f4f6f9;
        border-bottom: 2px solid #ddd;
	}
	table tbody tr td {
		font-size: 12px;
		padding: 5px !important;
        vertical-align: middle !important;
	}
</style>

<div class="box-datatable">
    <div style="text-align: center; margin-bottom: 20px;">
        <?php $this->load->view('admin/head_bangla'); ?>
        <h4 style="margin: 5px 0; font-weight: bold;">Movement Report</h4>
        <p>Date: <?php echo date('d M Y', strtotime($start_date)); ?> 
        <?php if($end_date): ?> to <?php echo date('d M Y', strtotime($end_date)); ?><?php endif; ?></p>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Employee Name</th>
                    <th>Date</th>
                    <th>Out Time</th>
                    <th>In Time</th>
                    <th>Reason / Purpose</th>
                    <th>Location / Address</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($report)): ?>
                    <tr><td colspan="8" class="text-center">No records found</td></tr>
                <?php else: ?>
                    <?php $sl=1; foreach($report as $row): ?>
                    <tr>
                        <td class="text-center"><?php echo $sl++; ?></td>
                        <td style="font-weight: bold;"><?php echo $row->first_name . ' ' . $row->last_name; ?> <small class="text-muted">(<?php echo $row->employee_id; ?>)</small></td>
                        <td><?php echo date('d M Y', strtotime($row->created_at)); ?></td>
                        <td><?php echo date('h:i A', strtotime($row->start_time)); ?></td>
                        <td><?php echo $row->end_time ? date('h:i A', strtotime($row->end_time)) : '-'; ?></td>
                        <td><?php echo $row->purpose; ?></td>
                        <td>
                            <?php echo $row->start_location; ?>
                            <?php if($row->travel_count > 0 || $row->meeting_count > 0): ?>
                                <br><small class="text-info"><?php echo $row->meeting_count; ?> Meetings, <?php echo $row->travel_count; ?> Trips</small>
                            <?php endif; ?>
                        </td>
                        <td>
                             <?php if($row->status == 'active'): ?>
                                <span class="label label-success">Active</span>
                            <?php else: ?>
                                <span class="label label-default">Completed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
