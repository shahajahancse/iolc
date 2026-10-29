<style>
    .movement-box {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        padding: 40px;
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
<div class="row mb-4">
    <div class="col-md-3">
        <div class="movement-box p-4" style="border-left: 5px solid #11cdef; background: linear-gradient(to right, #ffffff, #f4f6f9);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase text-muted mb-2" style="font-size: 11px; letter-spacing: 1px;">Total TA Claimed</h6>
                    <h3 class="mb-0 font-weight-bold text-info"><?php echo number_format($stats['total_ta']); ?> BDT</h3>
                </div>
                <div class="icon-shape bg-info text-white rounded-circle shadow p-3">
                    <i class="fa fa-money fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="movement-box p-4" style="border-left: 5px solid #fb6340; background: linear-gradient(to right, #ffffff, #f4f6f9);">
             <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase text-muted mb-2" style="font-size: 11px; letter-spacing: 1px;">Pending Approval</h6>
                    <h3 class="mb-0 font-weight-bold text-warning"><?php echo number_format($stats['pending_ta']); ?> BDT</h3>
                </div>
                <div class="icon-shape bg-warning text-white rounded-circle shadow p-3">
                    <i class="fa fa-clock-o fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="movement-box p-4" style="border-left: 5px solid #2dce89; background: linear-gradient(to right, #ffffff, #f4f6f9);">
             <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase text-muted mb-2" style="font-size: 11px; letter-spacing: 1px;">Approved Payment</h6>
                    <h3 class="mb-0 font-weight-bold text-success"><?php echo number_format($stats['approved_ta']); ?> BDT</h3>
                </div>
                <div class="icon-shape bg-success text-white rounded-circle shadow p-3">
                    <i class="fa fa-check-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
     <div class="col-md-3">
        <div class="movement-box p-4" style="border-left: 5px solid #5e72e4; background: linear-gradient(to right, #ffffff, #f4f6f9);">
             <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase text-muted mb-2" style="font-size: 11px; letter-spacing: 1px;">Handed Over</h6>
                    <h3 class="mb-0 font-weight-bold text-primary"><?php echo number_format($stats['handed_over_ta']); ?> BDT</h3>
                </div>
                <div class="icon-shape bg-primary text-white rounded-circle shadow p-3">
                    <i class="fa fa-handshake-o fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($is_employee) && $is_employee): ?>
<div class="movement-box text-center">
  <h2 style="font-weight: 800; color: #32325d; margin-bottom: 10px;">Movement Dashboard</h2>
      <p class="lead" style="color: #8898aa; margin-bottom: 30px;">Track your daily journey efficiently.</p>
      <a href="<?php echo site_url('new_movement/start'); ?>" class="btn btn-movement-start">
          <i class="fa fa-play-circle mr-2"></i> Start New Day
      </a>
</div>
<?php endif; ?>

<div class="movement-box">
    <h3 style="font-weight: 700; color: #32325d; margin-bottom: 25px;">
        <?php echo (isset($is_employee) && $is_employee) ? 'My Movement History' : 'All Employee Movements'; ?>
    </h3>
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Date</th>
                    <?php if(!$is_employee): ?><th>Employee</th><?php endif; ?>
                    <th>Purpose</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Mov Status</th>
                    <th>TA Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($movements)): ?>
                    <?php foreach($movements as $key => $mov): ?>
                        <tr>
                            <td><?php echo ++$key; ?></td>
                            <td><?php echo date('d M, Y', strtotime($mov->created_at)); ?></td>
                            <?php if(!isset($is_employee) || !$is_employee): ?>
                                <td><?php echo $mov->first_name . ' ' . $mov->last_name; ?></td>
                            <?php endif; ?>
                            <td><?php echo $mov->purpose; ?></td>
                            <td><?php echo date('h:i A', strtotime($mov->start_time)); ?></td>
                            <td><?php echo $mov->end_time ? date('h:i A', strtotime($mov->end_time)) : '-'; ?></td>
                            <td>
                                <?php
                                    // Use calculated status if available, else default to mov->status
                                    $displayStatus = isset($mov->current_status_text) ? $mov->current_status_text : ucfirst($mov->status);

                                    // Map text to classes
                                    $statusClass = 'status-running'; // default
                                    if(stripos($displayStatus, 'completed') !== false) $statusClass = 'status-completed';
                                    elseif(stripos($displayStatus, 'active') !== false) $statusClass = 'status-active';
                                    elseif(stripos($displayStatus, 'traveling') !== false) $statusClass = 'status-active'; // Use active color for traveling
                                    elseif(stripos($displayStatus, 'meeting') !== false) $statusClass = 'status-active'; // Use active color for meeting
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php if(isset($mov->status_icon)): ?>
                                        <i class="fa <?php echo $mov->status_icon; ?>"></i>
                                    <?php endif; ?>
                                    <?php echo $displayStatus; ?>
                                </span>
                                <?php if(isset($mov->current_location) && $mov->status == 'active'): ?>
                                    <div style="font-size: 11px; margin-top: 5px; color: #555; max-width: 200px; line-height: 1.2;">
                                        <button class="btn btn--primary ml-1" onclick="viewLocationModal(<?php echo $mov->current_lat; ?>, <?php echo $mov->current_lng; ?>)" style="padding: 0px 5px; font-size: 10px;">
                                            <i class="fa fa-eye"></i> View Location
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($mov->ta_status != 'not_applied'): ?>
                                    <span class="badge badge-info mr-4">
                                      <?php echo ucfirst(str_replace('_', ' ', $mov->ta_status)); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo site_url('new_movement/details/'.$mov->id); ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 20px; padding: 5px 15px;">
                                    View Details
                                </a>
                                <?php if($mov->status == 'completed' && isset($is_employee) && $is_employee && $mov->ta_status == 'not_applied'): ?>
                                    <a href="<?php echo site_url('new_movement/apply_ta/'.$mov->id); ?>" class="btn btn-sm btn-primary" style="border-radius: 20px; padding: 5px 15px; margin-left: 5px; background-color: #0177bc; border-color: #0177bc;">
                                        Apply TA
                                    </a>
                                <?php endif; ?>

                                <!-- ADMIN / HR ACTIONS -->
                                <?php if(!$is_employee): ?>
                                    <?php $my_role = $this->session->userdata('username')['role_id']; ?>

                                    <!-- Role 4 (HR): Pending -> HR Approved -->
                                    <?php if(in_array($my_role,[2,4]) && $mov->ta_status == 'pending'): ?>
                                        <a href="<?php echo site_url('new_movement/update_ta_status/'.$mov->id.'/approved'); ?>" class="btn btn-sm btn-success" onclick="return confirm('Approve this TA?')"><i class="fa fa-check"></i> Approved
                                        </a>
                                        <a href="<?php echo site_url('new_movement/update_ta_status/'.$mov->id.'/rejected'); ?>" class="btn btn-sm btn-success" onclick="return confirm('Approve this TA?')"><i class="fa fa-check"></i> Rejected
                                        </a>
                                        <!-- Role 1 (Admin): Accounts Approved -> paid -->
                                    <?php elseif(($my_role == 1) && $mov->ta_status == 'approved'): ?>
                                        <a href="<?php echo site_url('new_movement/update_ta_status/'.$mov->id.'/paid'); ?>" class="btn btn-sm btn-success" onclick="return confirm('Paid this TA?')">
                                            <i class="fa fa-check-double"></i> Paid
                                        </a>
                                        <a href="<?php echo site_url('new_movement/update_ta_status/'.$mov->id.'/rejected'); ?>" class="btn btn-sm btn-success" onclick="return confirm('Paid this TA?')">
                                            <i class="fa fa-check-double"></i> Rejected
                                        </a>
                                    <?php endif; ?>

                                    <!-- Role 4 (HR): Final Approved -> Handover ?? User said 4 can handover -->
                                    <?php if(in_array($my_role,[2,4]) && $mov->ta_status == 'paid'): ?>
                                        <a href="<?php echo site_url('new_movement/update_ta_status/'.$mov->id.'/handed_over'); ?>" class="btn btn-sm btn-info" onclick="return confirm('Mark as Handed Over?')"> <i class="fa fa-hand-lizard-o"></i> Handover
                                        </a>
                                    <?php endif; ?>

                                    <!-- EDIT TA AMOUNT (HR/Admin) -->
                                     <?php if(in_array($mov->ta_status, ['pending', 'hr_approved', 'approved']) && ($my_role != 3)): ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="editTAModal(<?php echo $mov->id; ?>)" style="border-radius: 20px; padding: 5px 15px; margin-top: 5px;"><i class="fa fa-pencil"></i> Edit Amount
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
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

</script>

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
