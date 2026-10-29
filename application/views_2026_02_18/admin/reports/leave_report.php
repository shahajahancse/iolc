<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<?php
$this->load->model('Attendance_model');

$stype = "Monthly Leave report";
?>
<link rel="stylesheet"
    href="<?php echo base_url(); ?>skin/hrsale_assets/theme_assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_assets/css/hrsale/xin_hrsale_custom.css">

<style type="text/css">
    .box-tools {
        margin-right: -5px !important;
    }

    .table-sm td,
    .table-sm th {
        padding: .6rem .8rem !important;
        /* Increased padding */
        font-size: 12px !important;
        /* Slightly larger font size */
        border-color: #f0f2f5;
        /* Very light border for table cells */
    }

    .compact-table {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        /* More diffused shadow */
        border-radius: 12px;
        /* More rounded corners */
        margin-bottom: 30px;
        /* Increased margin */
        background-color: #ffffff;
        /* Ensure white background for table */
    }

    .compact-table tbody tr:hover {
        background-color: #f8faff;
        /* Very light blue on hover */
        transition: background-color 0.3s ease-in-out;
        /* Smoother transition */
    }

    .compact-table thead th {
        background-color: #f0f2f5;
        /* Soft grey for table headers */
        color: #495057;
        /* Darker text for contrast */
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        /* More prominent bottom border */
        text-transform: uppercase;
        /* Uppercase for headers */
        letter-spacing: 0.05em;
        /* Slight letter spacing */
    }

    .btn-icon {
        padding: .4rem .8rem;
        /* Increased padding */
        font-size: .85rem;
        /* Slightly larger font */
        line-height: 1.5;
        border-radius: .3rem;
        /* More rounded buttons */
        transition: all 0.2s ease-in-out;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Subtle shadow for buttons */
    }

    .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        /* More pronounced shadow on hover */
    }

    .edit-form-row td {
        padding: 20px !important;
        /* Increased padding */
        background-color: #fdfdfd;
        border: 1px solid #eee;
        /* Lighter border */
        border-radius: 8px;
        /* Rounded corners for edit row */
    }

    .form-group label {
        font-weight: 600;
        /* Bolder labels */
        color: #495057;
        /* Darker label color */
        margin-bottom: .5rem;
        /* Spacing below label */
    }

    .form-control {
        /* Styling for input fields */
        border: 1px solid #ced4da;
        border-radius: .25rem;
        padding: .5rem .75rem;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
        /* Subtle inner shadow */
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25);
        /* Bootstrap-like focus ring */
    }

    /* New styles for card-like structure */
    .card {
        border: none;
        border-radius: 15px;
        /* Even more rounded corners */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        /* More prominent and diffused shadow */
        margin-bottom: 30px;
        overflow: hidden;
        background-color: #ffffff;
        /* Ensure white background for card */
    }

    .card-header {
        background: linear-gradient(135deg, #6dd5ed 0%, #2193b0 100%);
        /* More vibrant gradient */
        border-bottom: none;
        /* Remove border */
        padding: 1.5rem 2rem;
        /* Increased padding */
        color: #ffffff;
        /* White text for contrast */
        font-weight: 700;
        font-size: 1.2em;
        /* Larger font size */
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        /* Subtle text shadow */
    }

    .card-body {
        padding: 0;
        /* Table will provide its own padding */
    }

    .box-body {
        padding: 30px;
    }

    /* Status Badges - refine colors */
    .badge {
        padding: .4em .6em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25rem;
        transition: all .2s ease-in-out;
    }

    .badge-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .badge-success {
        background-color: #28a745;
        color: #fff;
    }

    .badge-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    /* Darker text for warning */

    @media print {
        body {
            zoom: 80% !important;
        }
    }

    body {
        /* Global font smoothing and modern font family */
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        font-family: 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;

    }

    body {
        line-height: 1.45 !important;
        color: #000000 !important;
        background-color: #ffffff !important;
    }

    @media print {
        body {
            zoom: 80% !important;
        }
    }
</style>

<?php $user = $this->session->userdata('username');




?>

<div class="box" id="print_area">
    <div style="text-align: center;">
        <?php  $this->load->view('admin/head_bangla'); ?>
        <h4>Leave Report</h4>
    </div>
    <div class="container-fluid">
        <div class="box-body">
            <div class="table-responsive">
                <?php foreach ($employee_id as $key => $employee) {
                    $this->load->model('Xin_model');
                    $employee_info = $this->Xin_model->read_user_info($employee);
                    $lead_user_id = $employee_info[0]->lead_user_id;
                    $lead_user = $this->Xin_model->read_user_info($lead_user_id);
                    $leave_report = $this->Attendance_model->leavesm_singale($employee, $first_date, $second_date);
                    // dd($leave_report);
                    ?>
                    <table class="table table-bordered table-sm compact-table">
                        <tbody>
                            <tr>
                                <th colspan="11" style="background: #e8e8e8;color: blue;font-size: 18px !important;">
                                    <?php echo $employee_info[0]->first_name . ' ' . $employee_info[0]->last_name; ?>
                                </th>
                                <td rowspan="<?= count($leave_report) + 3 ?>"
                                    style="vertical-align: top !important;align-content: center;">
                                    <?php
                                    $employee_id = $employee;
                                    $year = date('Y', strtotime($first_date));
                                    $total_leave = cals_leave($employee_id, $year);
                                    ?>
                                    <table class="table table-bordered table-sm" style="background: #f7f7f7;margin: 0;">
                                        <tr>
                                            <th colspan="3">Leave Details</th>
                                        </tr>
                                        <tr>
                                            <th>Type</th>
                                            <th>EL</th>
                                            <th>SL</th>
                                        </tr>
                                        <tr>
                                            <td>Total</td>
                                            <td id="el_total_<?= $employee_id ?>">
                                                <?= (empty($total_leave)) ? 0 : $total_leave->el_total ?>
                                            </td>
                                            <td id="sl_total_<?= $employee_id ?>">
                                                <?= (empty($total_leave)) ? 0 : $total_leave->sl_total ?>
                                            </td>
                                        </tr>
                                        <tr id="balance<?= $employee_id ?>">
                                            <td>Balance</td>
                                            <td id="el_balance_<?= $employee_id ?>"
                                                style="color: #0b24e7; font-weight: bold;">
                                                <?= (empty($total_leave)) ? 0 : $total_leave->el_balanace ?>
                                            </td>
                                            <td id="sl_balance_<?= $employee_id ?>"
                                                style="color: #0b24e7; font-weight: bold;">
                                                <?= (empty($total_leave)) ? 0 : $total_leave->sl_balanace ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <th>Sl</th>
                                <th>Applied On</th>
                                <th>Applied Duration</th>
                                <th>Approved Duration</th>
                                <th>Days</th>
                                <th>Leave Type</th>
                                <th>Reason</th>
                                <th style="max-width: 172px;">Remarks</th>
                                <th>Team Leader Feedback</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>


                            <?php $tqty = 0;
                            foreach ($leave_report as $key => $value) { ?>
                                <tr id="leave-row-<?php echo $value->leave_id; ?>">
                                    <td><?php echo $key + 1; ?></td>
                                    <td class="applied_on"><?php echo date('d-M-y', strtotime($value->applied_on)); ?></td>
                                    <td class="applied_duration">
                                        <strong><?php echo date('d-M-y', strtotime($value->applyed_from_date)); ?></strong> to
                                        <strong><?php echo date('d-M-y', strtotime($value->applyed_to_date)); ?></strong>

                                        <p>
                                            <?php
                                            $start = strtotime($value->applyed_from_date);
                                            $end = strtotime($value->applyed_to_date);

                                            while ($start <= $end) {
                                                echo date('l', $start) . ', '; // l = Full day name (e.g. Monday)
                                                $start = strtotime("+1 day", $start);
                                            }
                                            ?>
                                        </p>

                                    </td>
                                    <td class="duration">
                                        <strong><?php echo date('d-M-y', strtotime($value->from_date)); ?></strong> to
                                        <strong><?php echo date('d-M-y', strtotime($value->to_date)); ?></strong>

                                        <p>
                                            <?php
                                            $start = strtotime($value->from_date);
                                            $end = strtotime($value->to_date);

                                            while ($start <= $end) {
                                                echo date('l', $start) . ', '; // l = Full day name (e.g. Monday)
                                                $start = strtotime("+1 day", $start);
                                            }
                                            ?>
                                        </p>
                                    </td>
                                    <td class="qty"><?php $tqty = $tqty + $value->qty;
                                    echo $value->qty; ?></td>
                                    <td class="leave_type">
                                        <?php echo ($value->leave_type_id == 1) ? '<span>Earn Leave</span>' : '<span>Sick Leave</span>'; ?>
                                    </td>
                                    <td class="reason"><?php echo $value->reason; ?></td>
                                    <td class="remarks" style="max-width: 172px;"><?php echo $value->remarks; ?></td>
                                    <td class="team_leader_feedback">
                                        <?php echo ($value->team_lead_approved == 1) ? '<span class="text-success">Approved</span>' : '<span class="text-danger">Pending</span>'; ?>
                                        <br> <small><?php echo $value->team_lead_comment; ?></small>
                                    </td>
                                    <td class="status">
                                        <?php if ($value->status == 1) {
                                            echo $statss = '<span class="badge badge-danger current_status">Pending</span>';
                                        } elseif ($value->status == 2) {
                                            echo $statss = '<span class="badge badge-success current_status">Approved</span>';
                                        } elseif ($value->status == 3) {
                                            echo $statss = '<span class="badge badge-danger current_status">Rejected</span>';
                                        } elseif ($value->status == 4) {
                                            echo $statss = '<span class="badge badge-info current_status">First Level Approval</span>';
                                        } else {
                                            echo $statss = '<span class="badge badge-warning current_status">Team Leader Approve</span>';
                                        } ?>
                                    </td>

                                    <td>
                                        <div class="btn-group"
                                            style="display: flex;flex-direction: column;gap: 8px;align-items: center;">
                                            <?php if ($user['role_id'] == 1) { ?>
                                                <button
                                                    onclick="edit_leave_row(this, <?php echo htmlspecialchars(json_encode($value)); ?>)"><i
                                                        class="fa fa-pencil"></i> Edit</button>
                                            <?php } ?>
                                            <div class="dropdown" style="display: inline-block;">

                                                <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                                    data-toggle="dropdown"><i class="fa fa-cog"></i> Status
                                                    <span class="caret"></span></button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($user['role_id'] == 1) { ?>
                                                        <li><a onclick="leave_status_change(<?= $value->leave_id ?>, 2,this, <?= $user['role_id'] ?>)"
                                                                href="#">Approved</a></li>
                                                        <li><a onclick="leave_status_change(<?= $value->leave_id ?>, 3,this, <?= $user['role_id'] ?>)"
                                                                href="#">Rejected</a></li>
                                                        <li><a onclick="leave_status_change(<?= $value->leave_id ?>, 4,this, <?= $user['role_id'] ?>)"
                                                                href="#">First Level Approval</a></li>
                                                        <li><a onclick="leave_status_change(<?= $value->leave_id ?>, 1,this, <?= $user['role_id'] ?>)"
                                                                href="#">Pending</a></li>
                                                    <?php } elseif (in_array($user['role_id'], array(2, 4, 99))) { ?>
                                                        <li><a onclick="leave_status_change(<?= $value->leave_id ?>, 4,this, <?= $user['role_id'] ?>)"
                                                                href="#">First Level Approval</a></li>
                                                        <li><a onclick="leave_status_change(<?= $value->leave_id ?>, 1,this, <?= $user['role_id'] ?>)"
                                                                href="#">Pending</a></li>
                                                        <li><a onclick="leave_status_change(<?= $value->leave_id ?>, 3,this, <?= $user['role_id'] ?>)"
                                                                href="#">Rejected</a></li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="edit-form-row" id="edit-form-<?php echo $value->leave_id; ?>" style="display: none;">
                                    <td colspan="10"></td>
                                </tr>
                            <?php } ?>

                            <tr>
                                <th colspan="4" style="text-align:right;">Total</th>
                                <th><?= $tqty ?></th>
                                <td colspan="6"></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"
    src="<?php echo base_url(); ?>skin/hrsale_assets/vendor/jquery/jquery-3.2.1.min.js"></script>
<script
    src="<?php echo base_url(); ?>skin/hrsale_assets/theme_assets/bower_components/jquery/dist/jquery.min.js"></script>
<script
    src="<?php echo base_url(); ?>skin/hrsale_assets/theme_assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script>
    $(document).ready(function () {
        $('.table-responsive').on('show.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "inherit");
        });

        $('.table-responsive').on('hide.bs.dropdown', function () {
            $('.table-responsive').css("overflow", "auto");
        })
    });

    var open_edit_form = null;

    var leave_start_date = null;
    var leave_end_date = null;
    var leave_type_old = null;
    var description = '';
    var date_change_description = '';
    var type_change_description = '';

    function make_remark(leave_id) {

        var make_remark_input = $('#remarks_' + leave_id);
        var remark = $('#remark').val();
        var remark_text = type_change_description + ' ' + date_change_description;
        if (remark_text && remark_text.length > 6) {
            remark_text += ' Please confirm to HR if this is acceptable or not.';
            make_remark_input.val(remark_text);
        } else {
            make_remark_input.val('');
        }
    }

    function work_of_type(leave_id, leave_type_id) {
        if (leave_type_old == leave_type_id) {
            type_change_description = '';
        } else {

            if (leave_type_id == 1) {
                type_change_description = 'We have changed the leave type to Earn Leave.';
            } else if (leave_type_id == 2) {
                type_change_description = 'We have changed the leave type to Sick Leave.';
            } else {
                type_change_description = '';
            }
        }
        make_remark(leave_id);
    }


    function edit_leave_row(btn, leave_data) {

        description = '';
        date_change_description = '';
        type_change_description = '';
        leave_type_old = '';

        if (open_edit_form) {
            $(open_edit_form).hide();
            $(open_edit_form).prev().show();
        }

        var row = $(btn).closest('tr');
        var edit_row = $('#edit-form-' + leave_data.leave_id);

        leave_start_date = leave_data.from_date;
        leave_end_date = leave_data.to_date;
        leave_type_old = leave_data.leave_type_id;

        var form_html = `
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Leave Type</label>
                        <select class="form-control" id="leave_type_${leave_data.leave_id}" onchange="work_of_type(${leave_data.leave_id}, this.value)">
                            <option value="1" ${leave_data.leave_type_id == 1 ? 'selected' : ''}>Earn Leave</option>
                            <option value="2" ${leave_data.leave_type_id == 2 ? 'selected' : ''}>Sick Leave</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" id="from_date_${leave_data.leave_id}" value="${moment(leave_data.from_date).format('YYYY-MM-DD')}" onchange="calculate_days(${leave_data.leave_id})">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" id="to_date_${leave_data.leave_id}" value="${moment(leave_data.to_date).format('YYYY-MM-DD')}" onchange="calculate_days(${leave_data.leave_id})">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Days</label>
                        <input type="number" class="form-control" id="qty_${leave_data.leave_id}" value="${leave_data.qty}">
                    </div>
                </div>
            </div>
            <div class="row">
                 <div class="col-md-10">
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" id="remarks_${leave_data.leave_id}">${leave_data.remarks}</textarea>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group" style="margin-top: 25px;">
                        <button class="btn btn-success btn-icon" onclick="update_leave(${leave_data.leave_id})"><i class="fa fa-save"></i> Save</button>
                        <button class="btn btn-danger btn-icon" onclick="cancel_edit(${leave_data.leave_id})"><i class="fa fa-times"></i> Cancel</button>
                    </div>
                </div>
            </div>
        `;

        edit_row.find('td').html(form_html);
        row.hide();
        edit_row.show();
        open_edit_form = edit_row;
    }

    function cancel_edit(leave_id) {
        $('#edit-form-' + leave_id).hide().prev().show();
        open_edit_form = null;
    }

    function update_leave(leave_id) {
        var from_date = $('#from_date_' + leave_id).val();
        var to_date = $('#to_date_' + leave_id).val();
        var qty = $('#qty_' + leave_id).val();
        var leave_type_id = $('#leave_type_' + leave_id).val();
        var remarks = $('#remarks_' + leave_id).val();

        $.ajax({
            type: "POST",
            url: "<?= base_url('admin/timesheet/update_leave_details') ?>",
            data: {
                leave_id: leave_id,
                from_date: from_date,
                to_date: to_date,
                qty: qty,
                leave_type_id: leave_type_id,
                remarks: remarks
            },
            success: function (data) {
                if (data == 'success') {

                    let start = moment(from_date);
                    let end = moment(to_date);
                    let days = [];

                    while (start <= end) {
                        days.push(start.format('dddd')); // Full day name (e.g. Monday)
                        start.add(1, 'days');
                    }


                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Leave details updated successfully!',
                        timer: 1500,
                        showConfirmButton: false
                    });


                    var row = $('#leave-row-' + leave_id);
                    row.find('.duration').html(`
                        <strong>${moment(from_date).format('DD-MMM-YY')}</strong> to 
                        <strong>${moment(to_date).format('DD-MMM-YY')}</strong>
                        <p>${days.join(', ')}</p>
                    `);
                    row.find('.qty').text(qty);
                    row.find('.leave_type').html(leave_type_id == 1 ? '<span>Earn Leave</span>' : '<span>Sick Leave</span>');
                    row.find('.remarks').text(remarks);

                    cancel_edit(leave_id);
                    update_balance(leave_id);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }
    function normalizeDate(date) {
        const d = new Date(date); // Convert string/number to Date object
        return new Date(d.getFullYear(), d.getMonth(), d.getDate());
    }

    function calculate_days(leave_id) {
        var from_date = new Date($('#from_date_' + leave_id).val());
        var to_date = new Date($('#to_date_' + leave_id).val());
        if (!isNaN(from_date.getTime()) && !isNaN(to_date.getTime()) && from_date <= to_date) {
            var time_diff = to_date.getTime() - from_date.getTime();
            var days_diff = Math.ceil(time_diff / (1000 * 3600 * 24)) + 1;
            $('#qty_' + leave_id).val(days_diff);
        } else {
            var days_diff = 0;
            $('#qty_' + leave_id).val(0);
        }




        const originalStartDate = new Date(leave_start_date);
        const originalEndDate = new Date(leave_end_date);
        const newStartDate = new Date($('#from_date_' + leave_id).val());
        const newEndDate = new Date($('#to_date_' + leave_id).val());


        const origStart = normalizeDate(originalStartDate);
        const origEnd = normalizeDate(originalEndDate);
        const newStart = normalizeDate(newStartDate);
        const newEnd = normalizeDate(newEndDate);

        if (origStart.getTime() === newStart.getTime() && origEnd.getTime() === newEnd.getTime()) {
            date_change_description = '';
        } else {
            const leave_start = normalizeDate(leave_start_date);
            const leave_end = normalizeDate(leave_end_date);

            let old_days_diff = 0;
            if (!isNaN(leave_start) && !isNaN(leave_end) && leave_start <= leave_end) {
                const old_time_diff = leave_end.getTime() - leave_start.getTime();
                old_days_diff = Math.ceil(old_time_diff / (1000 * 3600 * 24)) + 1;
            }
            date_change_description =
                `You have applied for leave from ${moment(leave_start).format('DD-MMM-YY')} to ${moment(leave_end).format('DD-MMM-YY')} for ${old_days_diff} days.` +
                ` But we decided to change it to ${moment(from_date).format('DD-MMM-YY')} to ${moment(to_date).format('DD-MMM-YY')} for ${days_diff} days.`;
        }

        make_remark(leave_id);

    }

    function update_balance(leave_id) {
        $.ajax({
            type: "POST",
            url: "<?= base_url('admin/timesheet/update_leave_balance') ?>",
            data: { leave_id: leave_id },
            success: function (data) {
                var total_leave = JSON.parse(data);
                $('#el_total_' + total_leave.emp_id).text(total_leave.el_total);
                $('#sl_total_' + total_leave.emp_id).text(total_leave.sl_total);
                $('#el_balance_' + total_leave.emp_id).text(total_leave.el_balanace);
                $('#sl_balance_' + total_leave.emp_id).text(total_leave.sl_balanace);
            }
        });
    }

    function leave_status_change(leave_id, status, obj, role) {
        event.preventDefault();
        var $btn_group = $(obj).closest('.btn-group');

        if (status == 3) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to reject this leave?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: `Why are you refusing this leave? Please tell the reason so they don't make this mistake again`,
                        input: 'text',
                        showCancelButton: true,
                        confirmButtonText: 'Reject',
                    }).then((inputResult) => {
                        if (inputResult.isConfirmed) {
                            var why = inputResult.value;
                            if (!why) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Please enter why you are rejecting this leave',
                                });
                                return false;
                            }

                            $btn_group.hide();
                            $btn_group.after('<img src="<?= base_url('skin/img/loading.gif') ?>" class="loading-gif" style="height: 20px;">');
                            $.ajax({
                                type: "POST",
                                url: "<?= base_url('admin/timesheet/leave_status_change') ?>",
                                data: {
                                    leave_id: leave_id,
                                    status: status,
                                    reason: why
                                },
                                success: function (data) {
                                    if (data == 'error') {
                                        alert('Something went wrong');
                                        $btn_group.show();
                                        $btn_group.next('.loading-gif').remove();
                                    } else {
                                        updateStatus($btn_group, leave_id, status, role);
                                    }
                                },
                                error: function (xhr, status, error) {
                                    console.log(xhr.responseText);
                                }
                            });
                        }
                    });
                }
            });
        } else {
            $btn_group.hide();
            $btn_group.after('<img src="<?= base_url('skin/img/loading.gif') ?>" class="loading-gif" style="height: 20px;">');
            $.ajax({
                type: "POST",
                url: "<?= base_url('admin/timesheet/leave_status_change') ?>",
                data: {
                    leave_id: leave_id,
                    status: status
                },
                success: function (data) {
                    if (data == 'error') {
                        alert('Something went wrong');
                        $btn_group.show();
                        $btn_group.next('.loading-gif').remove();
                    } else {
                        updateStatus($btn_group, leave_id, status, role);
                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        }
    }

    function updateStatus(btn_group, leave_id, status, role) {
        var stext = '';
        if (status == 1) {
            stext = `<span class="badge badge-danger current_status">Pending</span>`;
        } else if (status == 2) {
            stext = `<span class="badge badge-success current_status">Approved</span>`;
        } else if (status == 3) {
            stext = `<span class="badge badge-danger current_status">Rejected</span>`;
        } else if (status == 4) {
            stext = `<span class="badge badge-info current_status">First Level Approval</span>`;
        } else {
            stext = `<span class="badge badge-warning current_status">Team Leader Approve</span>`;
        }

        var tr = $(btn_group).closest('tr');
        tr.find('.status').html(stext);

        $(btn_group).show();
        $(btn_group).next('.loading-gif').remove();

        update_balance(leave_id);
    }

</script>