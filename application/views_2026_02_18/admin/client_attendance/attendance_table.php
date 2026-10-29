<?php
/* Client Attendance Table View */
?>
<table class="table table-striped table-bordered dataTable" id="xin_table_client_attendance">
    <thead>
        <tr>
            <th>Date</th>
            <th>Employee</th>
            <th>Client Info</th>
            <th>Visit Details</th>
            <th>Location</th>
            <th>Image</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($attendance_list)): ?>
            <?php foreach($attendance_list as $row): ?>
            <tr>
                <td style="white-space: nowrap;">
                    <i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($row->date)); ?><br>
                    <small class="text-muted"><?php echo date('l', strtotime($row->date)); ?></small>
                </td>
                <td>
                    <strong style="color: #000;"><?php echo $row->first_name . ' ' . $row->last_name; ?></strong><br>
                    <small class="text-muted">ID: <?php echo $row->employee_id; ?></small>
                </td>
                <td>
                    <span style="font-weight: bold; font-size: 14px;"><?php echo $row->client_name; ?></span><br>
                    <span class="label label-info" style="margin-top:2px;">Type: <?php echo $row->type; ?></span>
                    <div style="margin-top: 5px; font-size: 11px;">
                        <i class="fa fa-user"></i> <?php echo $row->representative_name; ?> <br>
                        <i class="fa fa-phone"></i> <?php echo $row->mobile; ?>
                    </div>
                </td>
                <td>
                    <strong><?php echo $row->division; ?></strong><br>
                    <small class="text-muted"><?php echo $row->district; ?></small>
                </td>
                <td>
                    <?php if($row->location_latitude && $row->location_longitude): ?>
                        <a href="https://maps.google.com/?q=<?php echo $row->location_latitude; ?>,<?php echo $row->location_longitude; ?>" target="_blank" class="btn btn-xs btn-default" title="View on Map">
                            <i class="fa fa-map-marker text-danger"></i> View
                        </a>
                        <div class="text-muted small mt-1" style="max-width:150px;"><?php echo substr($row->address, 0, 30); ?>...</div>
                    <?php else: ?>
                        <span class="text-muted">No Loc</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if($row->live_image): ?>
                        <a href="<?php echo base_url().$row->live_image; ?>" target="_blank">
                           <img src="<?php echo base_url().$row->live_image; ?>" style="width: 50px; height: 50px; border-radius: 5px; object-fit: cover; border: 1px solid #ddd;" class="img-thumbnail">
                        </a>
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td><small><?php echo $row->remarks; ?></small></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">No records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    // Initialize DataTable if needed, or keeping it simple
    if ($.fn.DataTable.isDataTable('#xin_table_client_attendance')) {
        $('#xin_table_client_attendance').DataTable().clear().destroy();
    }
    $('#xin_table_client_attendance').DataTable({
        responsive: true,
        "bDestroy": true,
        "order": [[ 0, "desc" ]]
    });
</script>
