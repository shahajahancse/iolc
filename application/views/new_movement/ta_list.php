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
</style>
<div class="movement-box">
    <?php $emps = $this->db->where_not_in('status', [2,3])->where('user_role_id', 3)->get('xin_employees')->result(); ?>
    <div style="display: inline-flex; gap: 10px">
        <h3 style="font-weight: 700; color: #32325d; margin-right: 10px;">Employee TA List </h3>
        <div style="display: inline-block;">
            <div class="form-group" style="display: inline-block; margin-right: 10px;">
                <label for="start_date">Start Date</label>
                <input oninput="get_ta_list_ajax()" type="date" id="start_date" name="start_date" class="form-control" value="<?php echo isset($start_date) ? $start_date : ''; ?>">
            </div>
            <div class="form-group" style="display: inline-block; margin-right: 10px;">
                <label for="end_date">End Date</label>
                <input oninput="get_ta_list_ajax()" type="date" id="end_date" name="end_date" class="form-control" value="<?php echo isset($end_date) ? $end_date : ''; ?>">
            </div>
            <div class="form-group" style="display: inline-block; margin-right: 10px;">
                <label for="ta_status">TA Status</label>
                <select onchange="get_ta_list_ajax()" id="ta_status" name="ta_status" class="form-control">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            <div class="form-group" style="display: inline-block; margin-right: 10px;">
                <label for="employee_id">Employee</label>
                <select onchange="get_ta_list_ajax()" id="employee_id" name="employee_id" class="form-control" style="width: 200px !important">
                    <option value="">All</option>
                    <?php foreach($emps as $emp): ?>
                        <option value="<?php echo $emp->user_id; ?>" <?php if(isset($employee_id) && $employee_id == $emp->user_id) echo 'selected'; ?>><?php echo $emp->first_name . ' ' . $emp->last_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- <button type="submit" class="btn btn-movement-start" style="margin-left: 10px;">Search</button> -->
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-modern" id="ta_list_container">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Purpose</th>
                    <th>Amount</th>
                    <th>M.Amount</th>
                    <th>TA Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody style="height: 60vh !important; overflow-y: auto;">
                <?php if(!empty($movements)): ?>
                    <?php foreach($movements as $key => $mov): ?>
                        <tr>
                            <td><?php echo ++$key; ?></td>
                            <td><?php echo date('d M, Y', strtotime($mov->created_at)); ?></td>
                            <td><?php echo $mov->first_name . ' ' . $mov->last_name; ?></td>
                            <td><?php echo $mov->purpose; ?></td>
                            <td><?php echo $mov->ta_amount; ?></td>
                            <td><?php echo $mov->ta_app_amt; ?></td>

                            <td>
                                <?php if($mov->ta_status == 'pending'): ?>
                                    <span class="label label-warning">
                                      <?php echo ucfirst(str_replace('_', ' ', $mov->ta_status)); ?>
                                    </span>
                                <?php elseif ($mov->ta_status == 'rejected') : ?>
                                    <span class="label label-danger">
                                      <?php echo ucfirst(str_replace('_', ' ', $mov->ta_status)); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="label label-success">
                                      <?php echo ucfirst(str_replace('_', ' ', $mov->ta_status)); ?>
                                    </span>
                                <?php endif; ?>
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
                                            <a href="<?php echo site_url('new_movement/details/'.$mov->id .'/'. 1); ?>"> <i class="fa fa-eye"></i>View Details</a>
                                        </li>
                                        <li class="divider"></li>
                                        <!-- Accounts / HR Approve -->
                                        <?php $my_role = $this->session->userdata('username')['role_id']; ?>
                                        <?php if(in_array($my_role, [1,2,4]) && $mov->ta_status == 'pending' ): ?>
                                            <li>
                                                <?php /*
                                                href="<?php echo site_url('new_movement/update_ta_status/'.$mov->id.'/approved'); ?>" onclick="return confirm('Approve this TA?')" */ ?>
                                                <a href="javascript:void(0)" onclick="editTAModal(<?php echo $mov->id; ?>)"><i class="fa fa-check"></i> Approved
                                                </a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="javascript:void(0)" onclick="if(confirm('Reject this TA?')){update_ta_status(<?php echo $mov->id; ?>, 'rejected')}" ><i class="fa fa-check"></i> Rejected </a>
                                            </li>
                                            <li class="divider"></li>
                                        <?php endif; ?>
                                        <!-- SUPPER ADMIN ACTIONS to PAY -->
                                        <?php if($my_role == 1 && $mov->ta_status == 'approved' ): ?>
                                            <li>
                                                <a href="javascript:void(0)" onclick="if(confirm('Paid this TA?')){update_ta_status(<?php echo $mov->id; ?>, 'paid')}" ><i class="fa fa-check"></i> Paid </a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="javascript:void(0)" onclick="if(confirm('Reject this TA?')){update_ta_status(<?php echo $mov->id; ?>, 'rejected')}" ><i class="fa fa-check"></i> Rejected </a>
                                            </li>
                                            <li class="divider"></li>
                                        <?php endif; ?>
                                        <!-- HR/Accounts ACTIONS to handover -->
                                        <?php if(in_array($my_role, [2,4]) && $mov->ta_status == 'paid' ): ?>
                                            <li>
                                                <!-- <a href="<?php echo site_url('new_movement/update_ta_status/'.$mov->id.'/handed_over'); ?>" onclick="return confirm('Approve this TA?')"><i class="fa fa-check"></i> Handover
                                                </a> -->
                                                <a href="javascript:void(0)" onclick="if(confirm('Handover this TA?')){update_ta_status(<?php echo $mov->id; ?>, 'handed_over')}" ><i class="fa fa-check"></i> Handover </a>
                                            </li>
                                            <li class="divider"></li>
                                        <?php endif; ?>

                                        <!-- EDIT TA AMOUNT (HR/Admin) -->
                                        <?php if(in_array($mov->ta_status, ['pending', 'hr_approved', 'approved'])): ?>
                                            <li>
                                                <a href="javascript:void(0)" onclick="editTAModal(<?php echo $mov->id; ?>)"><i class="fa fa-pencil"></i> Edit Amount
                                                </a>
                                            </li>
                                            <li class="divider"></li>
                                        <?php endif; ?>
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
        </table>
    </div>
</div>

<!-- Leaflet CSS/JS (CDN fallback for modal) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<!-- Location View Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="locationModalLabel">Current Location</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0">
         <div id="modalMap" style="width: 100%; height: 450px;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Edit TA Modal -->
<div class="modal fade" id="editTAModal" tabindex="-1" role="dialog" aria-labelledby="editTAModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="edit_ta_modal_content">
            <div class="modal-body text-center">
                <i class="fa fa-spinner fa-spin fa-3x"></i><br>Loading...
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.btn-update-expense', function () {
        if(confirm('Are you sure?')) {
            update_expenses();
        }
    });

    $(document).on('click', '.btn-update-approve', function () {
        if(confirm('Are you sure?')) {
            update_expenses(1);
        }
    });
</script>

<script>
    function update_ta_status(movement_id, type) {
        $.ajax({
            url: '<?php echo site_url("new_movement/update_ta_status"); ?>',
            type: 'POST',
            data: {
                movement_id: movement_id,
                type: type
            },
            success: function(response) {
                alert(response);
                get_ta_list_ajax();
            },
            error: function() {
                alert('Error');
            }
        });
    }
</script>

<script>
    function update_expenses(type = null) {
        var from_data = $('#form_edit_expenses_data').serialize();
        if (type) {
            from_data += '&type=' + type;
        }
        $('#editTAModal').modal('hide');
        $.ajax({
            url: '<?php echo site_url("new_movement/update_expenses"); ?>',
            type: 'POST',
            data: from_data,
            success: function(response) {
                alert(response);
                get_ta_list_ajax();
                // $('#auto-modern').load(location.href + " #auto-modern>*", "");
            },
            error: function() {
                alert('Error');
             }
        });
    }
</script>

<script>
var modalMap = null;
var locationMarker = null;

function viewLocationModal(lat, lng) {
    if(!lat || !lng) {
        alert("Coordinates not available");
        return;
    }

    $('#locationModal').modal('show');

    setTimeout(function() {
        if (!modalMap) {
            modalMap = L.map('modalMap').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(modalMap);
        } else {
            modalMap.setView([lat, lng], 15);
        }

        if (locationMarker) modalMap.removeLayer(locationMarker);

        locationMarker = L.marker([lat, lng]).addTo(modalMap);

        // Force map redraw after modal transition
        modalMap.invalidateSize();
    }, 500); // Wait for modal to slide down
}

function editTAModal(id) {
    $('#editTAModal').modal('show');
    // loading state
    $('#edit_ta_modal_content').html('<div class="modal-body text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br>Loading...</div>');

    $.ajax({
        url: '<?php echo site_url("new_movement/edit_expenses"); ?>',
        type: 'GET',
        data: {movement_id: id},
        success: function(response) {
            $('#edit_ta_modal_content').html(response);
        },
        error: function() {
            $('#edit_ta_modal_content').html('<div class="modal-body text-center text-danger">Error loading data</div>');
        }
    });
}
</script>

<script>
    function get_ta_list_ajax() {
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        ta_status = $('#ta_status').val();

        // যদি শুধু একটা দেওয়া থাকে → STOP
        if ((start_date && !end_date) || (!start_date && end_date)) {
            return false;
        }

        $.ajax({
            url: '<?php echo site_url("new_movement/get_ta_list_ajax"); ?>',
            type: 'POST',
            data: {
                start_date: start_date,
                end_date: end_date,
                ta_status: ta_status,
                employee_id: $('#employee_id').val()
            },
            success: function(response) {
                $('#ta_list_container').html(response);
            },
            error: function() {
                $('#ta_list_container').html('<div class="modal-body text-center text-danger">Error loading data</div>');
            }
        })
    }
</script>
