<table class="table table-responsive table-striped" style="border-top: 1px solid #d6d2d2;">
    <thead>
        <tr>
            <th>Sl</th>
            <th>Employee ID</th>
            <th>Date</th>
            <th>Warning Type</th>
            <th>Remarks</th>
            <th>Warning By</th>
            <th>Status</th>
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
                    <td><?php echo $key + 1; ?></td>
                    <td><?php echo htmlspecialchars((string)($row->employee_id ?: '--'), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo !empty($row->warning_date) ? date('d-M-Y', strtotime($row->warning_date)) : '--'; ?></td>
                    <td><?php echo htmlspecialchars((string)$warning_type, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$remarks, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$warning_by, ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string)$status_text, ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="7" class="text-center">No warning report found.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>
