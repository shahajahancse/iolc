<link rel="stylesheet"
    href="<?php echo base_url();?>skin/hrsale_assets/theme_assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>skin/hrsale_assets/css/hrsale/xin_hrsale_custom.css">

<style type="text/css">
.box-tools {
    margin-right: -5px !important;
}

table thead tr th {
    font-size: 12px;
    padding: 3px !important;
}

table tbody tr td {
    font-size: 12px;
    padding: 3px !important;
}

@media print {
    .box-tools {
        margin-right: -5px !important;
    }

    table thead tr th {
        font-size: 12px;
        padding: 3px !important;
    }

    table tbody tr td {
        font-size: 12px;
        padding: 3px !important;
    }

    body {
        zoom: 80% !important;
    }
}
</style>

<div class="box" id="print_area">
    <div style="text-align: center;">
        <?php $this->load->view('admin/head_bangla'); ?>
        <h4 class="box-title">Warning Report (Date Wise)</h4>
        <p>Report date: <?php echo htmlspecialchars((string)$first_date, ENT_QUOTES, 'UTF-8');?> to
            <?php echo htmlspecialchars((string)$second_date, ENT_QUOTES, 'UTF-8');?></p>
        <span class="box-tools">Only employees having warning records are shown.</span><br><br>
    </div>

    <div class="container">
        <div class="box-body">
            <div class="box-datatable table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Sl. No.</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Department Name</th>
                            <th>Designation Name</th>
                            <th>Warning Type</th>
                            <th>Remarks</th>
                            <th>Warning Date</th>
                            <th>Warning By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($warning_list)) { ?>
                            <?php foreach($warning_list as $key => $row) { ?>
                                <?php
                                    $subject_text = !empty($row->warning_type_name) ? $row->warning_type_name : (string)$row->subject;
                                    $warning_by_name = trim(((string)$row->warning_by_first_name).' '.((string)$row->warning_by_last_name));
                                    if($warning_by_name === '') {
                                        $warning_by_name = 'N/A';
                                    }
                                    if((string)$row->status === '0') {
                                        $status_text = 'Pending';
                                    } elseif((string)$row->status === '1') {
                                        $status_text = 'Accepted';
                                    } elseif((string)$row->status === '2') {
                                        $status_text = 'Rejected';
                                    } else {
                                        $status_text = 'N/A';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo $key + 1;?></td>
                                    <td><?php echo htmlspecialchars((string)$row->employee_id, ENT_QUOTES, 'UTF-8');?></td>
                                    <td><?php echo htmlspecialchars(trim(((string)$row->first_name).' '.((string)$row->last_name)), ENT_QUOTES, 'UTF-8');?></td>
                                    <td><?php echo htmlspecialchars((string)$row->department_name, ENT_QUOTES, 'UTF-8');?></td>
                                    <td><?php echo htmlspecialchars((string)$row->designation_name, ENT_QUOTES, 'UTF-8');?></td>
                                    <td><?php echo htmlspecialchars((string)$subject_text, ENT_QUOTES, 'UTF-8');?></td>
                                    <td><?php echo htmlspecialchars((string)$row->description, ENT_QUOTES, 'UTF-8');?></td>
                                    <td><?php echo htmlspecialchars((string)$row->warning_date, ENT_QUOTES, 'UTF-8');?></td>
                                    <td><?php echo htmlspecialchars((string)$warning_by_name, ENT_QUOTES, 'UTF-8');?></td>
                                    <td><?php echo htmlspecialchars((string)$status_text, ENT_QUOTES, 'UTF-8');?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="10" class="text-center">No warning records found for selected date range.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
