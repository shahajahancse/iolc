<?php
$stats = isset($warning_stats) ? $warning_stats : (object) array(
    'total_count' => 0,
    'viewed_count' => 0,
);
?>
<style>
body {
    font-family: 'Fira Mono', monospace;
}

</style>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&amp;display=swap">
<link rel="stylesheet" href="<?= base_url('skin/hrsale_assets/css/emp_attandenc.css') ?>">

<div class="col-md-12">
    <h3>Employee Warning List</h3>
    <table class="table table-responsive table-striped table-bordered" style="border-top: 1px solid #d6d2d2;">
        <thead>
            <tr>
                <th style="border: 1px solid #d6d2d2;">Sl.</th>
                <th style="border: 1px solid #d6d2d2;">Date</th>
                <th style="border: 1px solid #d6d2d2;">Warning Type</th>
                <th style="border: 1px solid #d6d2d2;">Remarks</th>
                <th style="border: 1px solid #d6d2d2;">Warning By</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($alldata)) { ?>
                <?php foreach($alldata as $key => $row) { ?>
                    <?php
                        $warning_type = !empty($row->warning_type_name) ? $row->warning_type_name : ((string)$row->subject !== '' ? $row->subject : '--');
                        $warning_by = trim(((string)$row->warning_by_first_name) . ' ' . ((string)$row->warning_by_last_name));
                        if ($warning_by === '') {
                            $warning_by = 'N/A';
                        }
                        if ((string)$row->status === '0') {
                            $status_text = 'Pending';
                        } elseif ((string)$row->status === '1') {
                            $status_text = 'Accepted';
                        } elseif ((string)$row->status === '2') {
                            $status_text = 'Rejected';
                        } else {
                            $status_text = 'N/A';
                        }
                        $remarks = (string)$row->description !== '' ? $row->description : $row->subject;
                    ?>
                    <tr>
                        <td style="border: 1px solid #d6d2d2;"><?php echo $key + 1; ?></td>
                        <td style="border: 1px solid #d6d2d2;"><?php echo !empty($row->warning_date) ? date('d-M-Y', strtotime($row->warning_date)) : '--'; ?></td>
                        <td style="border: 1px solid #d6d2d2;"><?php echo htmlspecialchars((string)$warning_type, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td style="border: 1px solid #d6d2d2;"><?php echo htmlspecialchars((string)$remarks, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td style="border: 1px solid #d6d2d2;"><?php echo htmlspecialchars((string)$warning_by, ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="7" class="text-center">No warning report found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>