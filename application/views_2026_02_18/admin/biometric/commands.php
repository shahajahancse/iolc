<?php
/* Commands View */
?>

<div class="box box-block bg-white" style="padding: 15px;">
    <h2><strong>Device</strong> Commands</h2>

    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered dataTable" id="xin_table_commands">
                <thead>
                    <tr>
                        <th>Device Name</th>
                        <th>Command Type</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Executed At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commands as $cmd): ?>
                        <tr>
                            <td>
                                <?php echo $cmd->device_name; ?>
                            </td>
                            <td><span class="label label-primary">
                                    <?php echo $cmd->command_type; ?>
                                </span></td>
                            <td>
                                <?php if ($cmd->status == 0): ?>
                                    <span class="label label-warning">Pending</span>
                                <?php elseif ($cmd->status == 1): ?>
                                    <span class="label label-info">Sent</span>
                                <?php elseif ($cmd->status == 2): ?>
                                    <span class="label label-success">Executed</span>
                                <?php else: ?>
                                    <span class="label label-danger">Failed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo date("d-m-Y h:i A", strtotime($cmd->created_at)); ?>
                            </td>
                            <td>
                                <?php echo $cmd->executed_at ? date("d-m-Y h:i A", strtotime($cmd->executed_at)) : '-'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>