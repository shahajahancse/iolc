<?php
/* Device List View */
$session = $this->session->userdata('username');
?>

<div class="box box-block bg-white" style="padding: 15px;">
    <h2><strong>
            <?php echo $this->lang->line('xin_list_all'); ?>
        </strong> Biometric Devices
        <div class="add-record-btn">
            <button class="btn btn-sm btn-primary add-new-form"><i class="fa fa-plus icon"></i> Add Biometric
                Device</button>
            <a href="<?php echo site_url('admin/biometric/devices'); ?>" class="btn btn-sm btn-info"><i
                    class="fa fa-refresh"></i> Refresh</a>
        </div>
    </h2>
    <div class="box-body">
        <div class="row m-b-1">
            <div class="col-md-12">
                <!-- Device Configuration Guide -->
                <div class="box box-block bg-gray-light">
                    <div class="box-header with-border">
                        <h4 class="box-title">Device Configuration Guide <small>Click to Expand</small></h4>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="alert alert-warning">
                            <strong>Important Note:</strong> This configuration guide is for ZKTeco devices supporting
                            ADMS
                            mode.
                        </div>
                        <h4><i class="fa fa-check-circle text-success"></i> Check Device ADMS Support</h4>
                        <p>Your ZKTeco must support ADMS mode. Look for "ADMS" or "Cloud Server Settings" in the menu.
                        </p>
                        <h4><i class="fa fa-cog text-primary"></i> Configure ADMS Settings</h4>
                        <p>Steps: 1. Menu -> 2. Communication -> 3. Cloud Server Settings</p>
                        <ul>
                            <li>Server Mode: <strong>ADMS</strong></li>
                            <li>Enable Domain Name: <strong>ON</strong></li>
                            <li>Server Address: <strong>
                                    <?php echo $_SERVER['HTTP_HOST']; ?>
                                </strong> (or your PC's Local IP: 192.168.10.107)</li>
                            <li>Enable Proxy Server: <strong>OFF</strong></li>
                            <li>HTTPS: <strong>ON</strong> (depends on your server)</li>
                        </ul>
                        <p class="text-info">Note: After adding your device, simply clock in using your device so that
                            it
                            recognizes your system.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive" data-pattern="priority-columns">
            <table class="table table-striped table-bordered dataTable" id="xin_table">
                <thead>
                    <tr>
                        <th>Device Name</th>
                        <th>Serial Number</th>
                        <th>Device IP</th>
                        <th>Last Online</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="add_modal" tabindex="-1" role="dialog" aria-labelledby="add_modal" aria-hidden="true"
    style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                        aria-hidden="true">×</span> </button>
                <h4 class="modal-title" id="add_modal">Add Biometric Device</h4>
            </div>
            <?php $attributes = array('name' => 'add_device', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
            <?php $hidden = array('user_id' => $session['user_id'], 'add_type' => 'device'); ?>
            <?php echo form_open('admin/biometric/add_device', $attributes, $hidden); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="device_name">Device Name</label>
                    <input type="text" class="form-control" name="device_name" placeholder="Device Name">
                </div>
                <div class="form-group">
                    <label for="serial_number">Serial Number</label>
                    <input type="text" class="form-control" name="serial_number"
                        placeholder="Device Serial Number (e.g. VGU6250...)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var xin_table = $('#xin_table').dataTable({
            "bDestroy": true,
            "ajax": {
                url: "<?php echo site_url("admin/biometric/device_list"); ?>",
                type: 'GET'
            },
            "fnDrawCallback": function (settings) {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        /* Add data */
        $('.add-new-form').on('click', function () {
            // Reset form
            $('#xin-form')[0].reset();
            $('.add_modal_data').modal('show');
            $('#add_modal').modal('show'); // Ensure correct modal is shown
        });

        /* Add data submission */
        $("#xin-form").submit(function (e) {
            e.preventDefault();
            var obj = $(this), action = obj.attr('name');
            $('.save').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: e.target.action,
                data: obj.serialize() + "&is_ajax=1&add_type=device&form=" + action,
                cache: false,
                success: function (JSON) {
                    if (JSON.error) {
                        toastr.error(JSON.error);
                        $('.save').prop('disabled', false);
                    } else {
                        xin_table.api().ajax.reload(function () {
                            toastr.success(JSON.result);
                        }, true);
                        $('#add_modal').modal('toggle');
                        $('#xin-form')[0].reset(); // Reset form
                        $('.save').prop('disabled', false);
                    }
                }
            });
        });

        /* Delete data */
        $(document).on("click", ".delete", function () {
            var id = $(this).data('record-id'); // Use record-id as per controller logic
            // Simple confirm for now, or use sweetalert if available in project
            if (confirm('Are you sure you want to delete this device?')) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url("admin/biometric/delete_device"); ?>",
                    data: { id_entry: id, type: 'delete' },
                    success: function (JSON) {
                        if (JSON.error != '') {
                            toastr.error(JSON.error);
                        } else {
                            toastr.success(JSON.result);
                            xin_table.api().ajax.reload(function () {
                            }, true);
                        }
                    }
                });
            }
        });

        // Ping Command
        $("#xin_table").on("click", ".ping-device", function () {
            var device_id = $(this).data('device_id');
            // Show loading or queuing message
            toastr.info('Queuing Ping command...');

            $.ajax({
                url: "<?php echo site_url("admin/biometric/send_command") ?>",
                type: "POST",
                data: { device_id: device_id, command_type: 'CHECK' },
                success: function (response) {
                    var parse = JSON.parse(response);
                    if (parse.result) {
                        toastr.success(parse.result);
                    } else if (parse.error) {
                        toastr.error(parse.error);
                    }
                },
                error: function () {
                    toastr.error('Server error.');
                }
            });
        });
    });
</script>