<?php
/* Attendance Logs View */
?>

<div class="box box-block bg-white" style="padding: 15px;">
    <h2><strong>Biometric</strong> Attendance Logs</h2>
    <div class="box-body">
        <div class="table-responsive" data-pattern="priority-columns">
            <table class="table table-striped table-bordered dataTable" id="xin_table_logs">
                <thead>
                    <tr>
                        <th>Sl No</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Device Name</th>
                        <th>Status</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <?php echo @$i=$i+1; ?>
                            </td>
                            <td>
                                <?php echo $log->proxi_id; ?>
                            </td>
                            <td>
                                <?php echo $log->first_name.' '.$log->last_name; ?>
                            </td>
                            <td>
                                <?php echo $log->dev_name ? $log->dev_name : 'Unknown Device'; ?>
                            </td>
                            <td><span class="badge bg-green">Clock In</span></td>
                            <!-- Assuming all are check-ins for now or logic needed -->
                            <td>
                                <?php echo date("d-m-Y h:i A", strtotime($log->date_time)); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>