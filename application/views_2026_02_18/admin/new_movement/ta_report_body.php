<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>TA Report</title>
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
    </head>
    <body>
        <div class="box-datatable">
            <div style="text-align: center; margin-bottom: 20px;">
                <?php $this->load->view('admin/head_bangla'); ?>
                <h4 style="margin: 5px 0; font-weight: bold;">Movement TA Report</h4>
                <p style="margin-bottom: 1px;">Date: <?php echo date('d M Y', strtotime($start_date)); ?>
                <?php if($end_date): ?> to <?php echo date('d M Y', strtotime($end_date)); ?><?php endif; ?></p>
                <p>TA Type: <?= $ta_status; ?></p>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Employee Name</th>
                            <th>Date</th>
                            <th>Reason / Purpose</th>
                            <th>Applied TA</th>
                            <th>modified TA</th>
                            <th>Status</th>
                            <th>Hr Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grand_total = 0;
                        $grand_total_amt = 0;
                        if(empty($report)):
                        ?>
                            <tr><td colspan="6" class="text-center">No records found</td></tr>
                        <?php else: ?>
                            <?php $sl=1; foreach($report as $row):
                                $grand_total += $row->ta_amount;
                                $grand_total_amt += $row->ta_app_amt;
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $sl++; ?></td>
                                <td style="font-weight: bold;"><?php echo $row->first_name . ' ' . $row->last_name; ?> <small class="text-muted">(<?php echo $row->employee_id; ?>)</small></td>
                                <td><?php echo date('d M Y', strtotime($row->created_at)); ?></td>
                                <td><?php echo $row->purpose; ?></td>
                                <td class="text-center" style="font-weight: bold;"><?php echo $row->ta_amount > 0 ? number_format($row->ta_amount, 2) : '-'; ?></td>
                                <td class="text-center" style="font-weight: bold;"><?php echo $row->ta_app_amt > 0 ? number_format($row->ta_app_amt, 2) : '-'; ?></td>
                                <td><?php echo $row->ta_status; ?></td>
                                <td><?php echo $row->ta_admin_note; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold; background-color: #eee;">
                            <td colspan="4" class="text-right">Grand Total:</td>
                            <td class="text-center"><?php echo number_format($grand_total, 2); ?></td>
                            <td class="text-center"><?php echo number_format($grand_total_amt, 2); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </body>
</html>
