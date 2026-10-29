<?php
/* Employees Mapping View */
?>

<div class="box box-block bg-white" style="padding: 15px;">
    <h2><strong>Employees</strong> Mapping</h2>
    <div class="box-body">
        <div class="alert alert-info">
            <strong>Biometric Configuration Instructions:</strong> Configure employee biometric IDs to connect with your
            biometric attendance devices. Each employee must have a unique ID that matches their ID in the biometric
            device
            system.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered dataTable" id="xin_table_mapping">
                <thead>
                    <tr>
                        <th>Employee Details</th>
                        <th>Biometric User ID (Proxi ID)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?php echo $emp->first_name . ' ' . $emp->last_name; ?>
                                </strong><br>
                                <small>ID:
                                    <?php echo $emp->employee_id; ?>
                                </small>
                            </td>
                            <td>
                                <input type="text" class="form-control mapping-input"
                                    data-user-id="<?php echo $emp->user_id; ?>" value="<?php echo $emp->proxi_id; ?>"
                                    placeholder="Enter Device User ID">
                            </td>
                            <td id="status_<?php echo $emp->user_id; ?>">
                                <?php if ($emp->proxi_id): ?>
                                    <span class="text-success"><i class="fa fa-check-circle"></i> Configured</span>
                                <?php else: ?>
                                    <span class="text-warning"><i class="fa fa-exclamation-circle"></i> Not Configured</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-sm btn-update-mapping"
                                    data-user-id="<?php echo $emp->user_id; ?>">
                                    <i class="fa fa-save"></i> Save
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.btn-update-mapping').click(function () {
            var btn = $(this);
            var user_id = btn.data('user-id');
            var proxi_id = $('input[data-user-id="' + user_id + '"]').val();

            $.ajax({
                url: '<?php echo site_url("admin/biometric/update_mapping"); ?>',
                type: 'POST',
                dataType: 'json',
                data: { user_id: user_id, proxi_id: proxi_id },
                success: function (response) {
                    // simplistic success update
                    $('#status_' + user_id).html('<span class="text-success"><i class="fa fa-check-circle"></i> Configured</span>');
                    toastr.success('Mapping updated successfully.');
                },
                error: function () {
                    toastr.error('Error updating mapping.');
                }
            });
        });
    });
</script>