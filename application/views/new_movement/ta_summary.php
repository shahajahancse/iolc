<style>
    .movement-box {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        padding: 5px 15px;
        border: none;
        margin-bottom: 30px;
        transition: transform 0.3s ease;
    }
    .movement-box:hover {
        transform: translateY(-5px);
    }
    .btn-movement-start {
        background: #0177bc;
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 50px;
        font-size: 18px;
        font-weight: 600;
        letter-spacing: 1px;
        box-shadow: 0 10px 20px rgba(1, 119, 188, 0.3);
        transition: all 0.3s ease;
    }
    .btn-movement-start:hover {
        background: #000;
        box-shadow: 0 15px 25px rgba(0,0,0, 0.2);
        transform: scale(1.05);
        color: white;
    }
    .table-modern {
        border-collapse: separate;
        border-spacing: 0 15px;
    }
    .table-modern thead th {
        border: None;
        text-transform: uppercase;
        color: #000;
        font-size: 12px;
        letter-spacing: 1px;
        padding: 15px;
    }
    .table-modern tbody tr {
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        border-radius: 10px;
    }
    .table-modern tbody td {
        border: none;
        padding: 20px;
        vertical-align: middle;
        font-weight: 500;
        color: #000;
    }
    .table-modern tbody td:first-child {
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }
    .table-modern tbody td:last-child {
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
    }
    .status-badge {
        padding: 8px 15px;
        border-radius: 30px;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        border: 1px solid #0177bc;
    }
    .status-active { background: #0177bc; color: #fff; }
    .status-completed { background: #fff; color: #0177bc; }
    .status-running { background: #000; color: #fff; border-color: #000; }

    .form-group {
        margin-bottom: 0px !important;
    }
</style>
<div class="movement-box">
    <div style="display: inline-flex; gap: 60px">
        <h3 style="font-weight: 700; color: #32325d; margin-right: 10px;"> TA Summary List </h3>
        <div style="display: inline-block;">
            <div class="form-group" style="display: inline-block; margin-right: 10px;">
                <label for="start_date">Start Date</label>
                <input oninput="get_ta_summary_ajax()" type="date" id="start_date" name="start_date" class="form-control" value="<?php echo isset($start_date) ? $start_date : ''; ?>">
            </div>
            <div class="form-group" style="display: inline-block; margin-right: 10px;">
                <label for="end_date">End Date</label>
                <input oninput="get_ta_summary_ajax()" type="date" id="end_date" name="end_date" class="form-control" value="<?php echo isset($end_date) ? $end_date : ''; ?>">
            </div>
            <div class="form-group" style="display: inline-block; margin-right: 10px;">
                <label for="ta_status">TA Status</label>
                <select onchange="get_ta_summary_ajax()" id="ta_status" name="ta_status" class="form-control">
                    <option value="approved" <?php if(isset($ta_status) && $ta_status == 'approved') echo 'selected'; ?>>Approved</option>
                    <option value="paid" <?php if(isset($ta_status) && $ta_status == 'paid') echo 'selected'; ?>>Paid</option>
                    <option value="pending" <?php if(isset($ta_status) && $ta_status == 'pending') echo 'selected'; ?>>Pending</option>
                </select>
            </div>
            <!-- <button type="submit" class="btn btn-movement-start" style="margin-left: 10px;">Search</button> -->
        </div>
    </div>

    <div class="table-responsive" id="auto-modern">
        <table class="table table-modern" id="auto-modern-body">
            <thead style="background: #c1e4ef; color: #fff;">
                <tr>
                    <th>SL</th>
                    <th>Employee</th>
                    <th>Meetings</th>
                    <th>Claims</th>
                    <th>M.Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <?php $apply_amt = 0; $approve_amt = 0; ?>
            <tbody style="height: 60vh !important; overflow-y: auto;">
                <?php if(!empty($movements)): ?>
                    <?php foreach($movements as $key => $mov): ?>
                        <?php
                            $apply_amt += $mov->applyed_amount;
                            $approve_amt += $mov->approved_amount;
                        ?>
                        <tr>
                            <td><?php echo ++$key; ?></td>
                            <td><?php echo $mov->first_name . ' ' . $mov->last_name; ?></td>
                            <td><?php echo $mov->total_movements; ?></td>
                            <td><?php echo $mov->applyed_amount; ?></td>
                            <td><?php echo $mov->approved_amount; ?></td>
                            <td>
                                <span class="label label-success">
                                    <?php echo ucfirst($ta_status); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php echo $this->lang->line('xin_action');?>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" role="menu">
                                        <li>
                                            <a href="javascript:void(0)" style="border-radius: 6px;" onclick="ta_summary_details('<?= $start_date ?>','<?= $end_date ?>', '<?= $ta_status ?>', '<?= $mov->employee_id ?>')" > <i class="fa fa-eye"></i> View Details</a>
                                        </li>
                                        <li class="divider"></li>
                                        <?php if($ta_status == 'approved') { ?>
                                        <li>
                                            <a onclick="ta_summary_approve('<?= $start_date ?>','<?= $end_date ?>', 'paid', '<?= $mov->employee_id ?>')" style="border-radius: 6px;" href="javascript:void(0)" > <i class="fa fa-check"></i> Paid </a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center" style="color: #8898aa;">No movements found. Start one above!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot style="font-weight: bold; background: #c1e4ef;">
                <tr>
                    <th colspan="3" class="text-right">Total : </th>
                    <th class=""><?= number_format($apply_amt, 2) ?></th>
                    <th class=""><?= number_format($approve_amt, 2) ?></th>
                    <?php if($ta_status == 'approved') { ?>
                        <th></th>
                        <th class="">
                            <a class="btn btn-primary btn-sm" onclick="ta_summary_approve('<?= $start_date ?>','<?= $end_date ?>', 'paid')" >Paid All</a>
                        </th>
                    <?php } else { ?>
                    <th colspan="2"></th>
                    <?php } ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Leaflet CSS/JS (CDN fallback for modal) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>


<!-- Edit TA Modal -->
<div class="modal fade" id="ta_summary_details" tabindex="-1" role="dialog" aria-labelledby="editTAModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="ta_summary_modal_content">
            <div class="modal-body text-center">
                <i class="fa fa-spinner fa-spin fa-3x"></i><br>Loading...
            </div>
        </div>
    </div>
</div>

<script>
    function ta_summary_details(start_date, end_date, ta_status, employee_id) {
        $('#ta_summary_details').modal('show');
        // loading state
        $('#ta_summary_modal_content').html('<div class="modal-body text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br>Loading...</div>');

        $.ajax({
            url: '<?php echo site_url("new_movement/ta_summary_details"); ?>',
            type: 'GET',
            data: {
                start_date: start_date,
                end_date: end_date,
                ta_status: ta_status,
                employee_id: employee_id
            },
            success: function(response) {
                $('#ta_summary_modal_content').html(response);
            },
            error: function() {
                $('#ta_summary_modal_content').html('<div class="modal-body text-center text-danger">Error loading data</div>');
            }
        });
    }
</script>

<script>
    function ta_summary_approve(start_date, end_date, ta_status, employee_id = null) {
        $.ajax({
            url: '<?php echo site_url("new_movement/ta_summary_approve"); ?>',
            type: 'POST',
            data: {
                start_date: start_date,
                end_date: end_date,
                ta_status: ta_status,
                employee_id: employee_id
            },
            success: function(response) {
                alert(response);
                get_ta_summary_ajax();
                // $('#auto-modern').load(location.href + " #auto-modern>*", "");
            },
            error: function() {
                alert('Error');
             }
        });
    }
</script>


<script>
    function get_ta_summary_ajax() {
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        ta_status = $('#ta_status').val();

        // যদি শুধু একটা দেওয়া থাকে → STOP
        if ((start_date && !end_date) || (!start_date && end_date)) {
            return false;
        }

        $.ajax({
            url: '<?php echo site_url("new_movement/get_ta_summary_ajax"); ?>',
            type: 'POST',
            data: {
                start_date: start_date,
                end_date: end_date,
                ta_status: ta_status
            },
            success: function(response) {
                $('#auto-modern-body').html(response);
            },
            error: function() {
                $('#auto-modern-body').html('<div class="modal-body text-center text-danger">Error loading data</div>');
            }
        });
    }
</script>
