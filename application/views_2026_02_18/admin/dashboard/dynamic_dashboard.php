<style>
    .dashboard-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        margin-bottom: 25px;
        background: #fff;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .card-header-custom {
        padding: 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title-custom {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-body-custom {
        padding: 25px;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        text-align: center;
    }

    .stat-item {
        flex: 1;
        padding: 10px;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 5px;
        display: block;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #8898aa;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* Gradients */
    .bg-gradient-attend {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .bg-gradient-move {
        background: linear-gradient(135deg, #2af598 0%, #009efd 100%);
        color: white;
    }

    .bg-gradient-event {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
        color: #555;
    }

    /* Softer for events */

    .list-group-custom .list-item {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .list-group-custom .list-item:last-child {
        border-bottom: none;
    }

    .user-avatar-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: #eee;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-weight: bold;
        color: #555;
        font-size: 12px;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: auto;
    }

    .badge-late {
        background: #ffeeba;
        color: #856404;
    }

    .badge-absent {
        background: #f8d7da;
        color: #721c24;
    }

    .badge-meeting {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-office {
        background: #d4edda;
        color: #155724;
    }

    .badge-travel {
        background: #e2e3e5;
        color: #383d41;
    }

    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #444;
        margin-top: 15px;
        margin-bottom: 10px;
        border-left: 3px solid #667eea;
        padding-left: 10px;
    }
</style>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 style="font-weight: 300; colro: #333; margin-top: -5px !important">Dashboard
            <small style="font-size: 0.6em; color: #777;"><?php echo date('d M, Y', strtotime($selected_date)); ?></small>
        </h2>
    </div>
</div>

<div class="row">
    <!-- Attendance Section -->
    <div class="col-lg-4 col-md-6">
        <div class="dashboard-card">
            <div class="card-header-custom" style="background: linear-gradient(45deg, #4e73df, #224abe); color: white;">
                <h5 class="card-title-custom"><i class="fa fa-users mr-2"></i> Attendance</h5>
            </div>
            <div class="card-body-custom">
                <div class="stat-row">
                    <div class="stat-item">
                        <span class="stat-value text-success"><?php echo $present_count; ?></span>
                        <span class="stat-label">Present</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value text-warning"><?php echo $late_count; ?></span>
                        <span class="stat-label">Late</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value text-danger"><?php echo $absent_count; ?></span>
                        <span class="stat-label">Absent</span>
                    </div>
                </div>

                <div style="overflow-y: auto; max-height: 300px;">
                    <?php if ($late_count > 0): ?>
                        <div class="section-title text-warning">Late Arrivals</div>
                        <div class="list-group-custom">
                            <?php foreach ($late_list as $l): ?>
                                    <div class="list-item">
                                        <span
                                            class="user-avatar-circle"><?php echo substr($l->first_name, 0, 1) . substr($l->last_name, 0, 1); ?></span>
                                        <div>
                                            <strong><?php echo $l->first_name . ' ' . $l->last_name; ?></strong><br>
                                            <small class="text-muted"><?php echo $l->department_name; ?></small>
                                        </div>
                                        <span class="status-badge badge-late"><?php echo date('H:i', strtotime($l->clock_in)); ?></span>
                                    </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($absent_count > 0): ?>
                        <div class="section-title text-danger">Absent</div>
                        <div class="list-group-custom">
                            <?php foreach ($absent_list as $a): ?>
                                    <div class="list-item">
                                        <span class="user-avatar-circle"
                                            style="background:#ffecec; color:#d63384;"><?php echo substr($a->first_name, 0, 1) . substr($a->last_name, 0, 1); ?></span>
                                        <?php echo $a->first_name . ' ' . $a->last_name; ?>
                                        <span class="status-badge badge-absent">ABS</span>
                                    </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Movement Section -->
    <div class="col-lg-4 col-md-6">
        <div class="dashboard-card">
            <div class="card-header-custom" style="background: linear-gradient(45deg, #1cc88a, #13855c); color: white;">
                <h5 class="card-title-custom"><i class="fa fa-map-marker-alt mr-2"></i> Movement</h5>
            </div>
            <div class="card-body-custom">
                <div class="stat-row">
                    <div class="stat-item">
                        <span class="stat-value text-primary"><?php echo count($in_office); ?></span>
                        <span class="stat-label">In Office</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value text-info"><?php echo count($on_meeting); ?></span>
                        <span class="stat-label">Meeting</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value text-secondary"><?php echo count($on_travel); ?></span>
                        <span class="stat-label">Field</span>
                    </div>
                </div>

                <div style="overflow-y: auto; max-height: 300px;">
                    <?php if (count($on_meeting) > 0): ?>
                        <div class="section-title text-info">On Meeting</div>
                        <div class="list-group-custom">
                            <?php foreach ($on_meeting as $m): ?>
                                    <div class="list-item">
                                        <span
                                            class="user-avatar-circle"><?php echo substr($m->first_name, 0, 1) . substr($m->last_name, 0, 1); ?></span>
                                        <div style="flex:1;">
                                            <strong><?php echo $m->first_name . ' ' . $m->last_name; ?></strong>
                                            <div
                                                style="font-size: 0.8rem; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;">
                                                <i class="fa fa-map-pin"></i>
                                                <?php echo isset($m->current_status_detail['location']) ? $m->current_status_detail['location'] : 'Location'; ?>
                                            </div>
                                        </div>
                                    </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Expanded List for Office/Field could go here if needed, or keeping it clean -->
            </div>
        </div>
    </div>

    <!-- Events Section -->
    <div class="col-lg-4 col-md-6">
        <div class="dashboard-card">
            <div class="card-header-custom" style="background: linear-gradient(45deg, #36b9cc, #258391); color: white;">
                <h5 class="card-title-custom"><i class="fa fa-calendar-day mr-2"></i> Today's Events</h5>
            </div>
            <div class="card-body-custom">
                <div style="overflow-y: auto; max-height: 385px;">
                    <?php if (count($todays_birthdays) > 0): ?>
                            <div class="section-title" style="border-color: #ff6b6b; color: #ff6b6b;"><i class="fa fa-birthday-cake"></i> Birthdays</div>
                            <div class="list-group-custom">
                                <?php foreach ($todays_birthdays as $b): ?>
                                    <div class="list-item">
                                        <img src="<?php echo base_url() . 'uploads/profile/' . $b->profile_picture; ?>"
                                            style="width:35px; height:35px; border-radius:50%; margin-right:12px; object-fit: cover;"
                                            onerror="this.src='<?php echo base_url(); ?>uploads/profile/default_male.jpg'">
                                        <div>
                                            <strong style="color: #333;"><?php echo $b->first_name . ' ' . $b->last_name; ?></strong>
                                            <div class="text-xs text-muted">Happy Birthday!</div>
                                        </div>
                                        <i class="fa fa-gift text-danger ml-auto" style="font-size: 1.2rem;"></i>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">No birthdays today.</div>
                    <?php endif; ?>

                    <div class="section-title mt-4" style="border-color: #4e73df; color: #4e73df;"><i class="fa fa-handshake"></i> Scheduled Meetings</div>
                    <?php if (count($todays_meetings) > 0): ?>
                        <div class="list-group-custom">
                            <?php foreach ($todays_meetings as $tm): ?>
                                <div class="list-item">
                                    <div
                                        style="background: #eef2ff; color: #4e73df; padding: 5px 10px; border-radius: 5px; margin-right: 10px; text-align: center; min-width: 50px;">
                                        <span
                                            style="font-size: 0.7rem; display: block;"><?php echo date('M', strtotime($tm->created_at)); ?></span>
                                        <span
                                            style="font-weight: bold; font-size: 1rem; line-height: 1;"><?php echo date('d', strtotime($tm->created_at)); ?></span>
                                    </div>
                                    <div>
                                        <strong
                                            style="color:#333;"><?php echo isset($tm->meeting_topic) ? $tm->meeting_topic : 'Meeting'; ?></strong>
                                        <?php if (isset($tm->first_name)): ?>
                                                <div style="font-size: 0.8rem; color: #666;">with
                                                    <?php echo $tm->first_name . ' ' . $tm->last_name; ?>
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">No specific meetings scheduled.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Tables Section -->
<div class="row">
    <div class="col-md-12">
        <div class="dashboard-card">
            <div class="card-header-custom" style="padding: 15px 25px;">
                <h5 class="card-title-custom text-primary">Detailed Reports</h5>
                <ul class="nav nav-pills" id="dashboardTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="attendance-tab" data-toggle="tab" href="#attendance" role="tab">Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="movement-tab" data-toggle="tab" href="#movement" role="tab">Movement</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="events-tab" data-toggle="tab" href="#events" role="tab">Events</a>
                    </li>
                </ul>
            </div>
            <div class="card-body-custom">
                <div class="tab-content" id="dashboardTabContent">

                    <!-- Attendance Table -->
                    <div class="tab-pane fade show active" id="attendance" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Clock In</th>
                                        <th>Clock Out</th>
                                        <th>Late?</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $all_attendance = array_merge(isset($present_list) ? $present_list : [], isset($late_list) ? $late_list : [], isset($absent_list) ? $absent_list : []);
                                    if (empty($all_attendance)): ?>
                                            <tr><td colspan="6" class="text-center">No attendance data today.</td></tr>
                                    <?php else:
                                        foreach ($all_attendance as $att):
                                            // Determine Badge
                                            $status_badge = '<span class="badge badge-success">Present</span>';
                                            $is_late = false;

                                            // Check if absent (might be in separate absent list without clock info)
                                            if (in_array($att, $absent_list)) {
                                                $status_badge = '<span class="badge badge-danger">Absent</span>';
                                            } elseif (in_array($att, $late_list)) {
                                                $status_badge = '<span class="badge badge-warning">Late</span>';
                                                $is_late = true;
                                            }
                                            ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="user-avatar-circle" style="width:30px; height:30px; font-size:10px;">
                                                                    <?php echo substr($att->first_name, 0, 1) . substr($att->last_name, 0, 1); ?>
                                                                </div>
                                                                <?php echo $att->first_name . ' ' . $att->last_name; ?>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $att->department_name; ?></td>
                                                        <td><?php echo $status_badge; ?></td>
                                                        <td><?php echo isset($att->clock_in) ? date('h:i A', strtotime($att->clock_in)) : '-'; ?></td>
                                                        <td><?php echo isset($att->clock_out) && $att->clock_out != '' ? date('h:i A', strtotime($att->clock_out)) : '-'; ?></td>
                                                        <td><?php echo $is_late ? '<span class="text-danger">Yes</span>' : 'No'; ?></td>
                                                    </tr>
                                            <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Movement Table -->
                    <div class="tab-pane fade" id="movement" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Current Status</th>
                                        <th>Location / Topic</th>
                                        <th>Start Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $all_movement = array_merge($on_meeting, $on_travel);
                                    if (empty($all_movement)): ?>
                                            <tr><td colspan="4" class="text-center">No active movements.</td></tr>
                                    <?php else:
                                        foreach ($all_movement as $mov):
                                            $m_status = isset($mov->current_status) ? $mov->current_status : 'Unknown';
                                            $badge_class = ($m_status == 'Meeting') ? 'badge-info' : 'badge-secondary';
                                            ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="user-avatar-circle" style="width:30px; height:30px; font-size:10px;">
                                                                    <?php echo substr($mov->first_name, 0, 1) . substr($mov->last_name, 0, 1); ?>
                                                                </div>
                                                                <?php echo $mov->first_name . ' ' . $mov->last_name; ?>
                                                            </div>
                                                        </td>
                                                        <td><span class="badge <?php echo $badge_class; ?>"><?php echo $m_status; ?></span></td>
                                                        <td>
                                                            <?php
                                                            // Location for travel, Topic for Meeting?
                                                            // Actually model logic enriches 'location' or similar in current_status_detail
                                                            echo isset($mov->current_status_detail['location']) ? $mov->current_status_detail['location'] : '-';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            // Start time of the movement or specific leg?
                                                            // Use created_at if no specific start time available
                                                            echo date('h:i A', strtotime($mov->created_at));
                                                            ?>
                                                        </td>
                                                    </tr>
                                            <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Events Table -->
                     <div class="tab-pane fade" id="events" role="tabpanel">
                         <div class="row">
                             <div class="col-md-6">
                                 <h6>Scheduled Meetings</h6>
                                 <table class="table table-sm">
                                     <thead><tr><th>Time</th><th>Topic</th><th>With</th></tr></thead>
                                     <tbody>
                                         <?php if (empty($todays_meetings)): ?>
                                                 <tr><td colspan="3" class="text-center text-muted">No meetings.</td></tr>
                                         <?php else:
                                             foreach ($todays_meetings as $tm): ?>
                                                         <tr>
                                                             <td><?php echo date('h:i A', strtotime($tm->created_at)); ?></td>
                                                             <td><?php echo isset($tm->meeting_topic) ? $tm->meeting_topic : 'Meeting'; ?></td>
                                                             <td><?php echo $tm->first_name . ' ' . $tm->last_name; ?></td>
                                                         </tr>
                                                 <?php endforeach; endif; ?>
                                     </tbody>
                                 </table>
                             </div>
                              <div class="col-md-6">
                                 <h6>Birthdays</h6>
                                 <table class="table table-sm">
                                     <thead><tr><th>Employee</th><th>Designation</th></tr></thead>
                                     <tbody>
                                         <?php if (empty($todays_birthdays)): ?>
                                                 <tr><td colspan="2" class="text-center text-muted">No birthdays.</td></tr>
                                         <?php else:
                                             foreach ($todays_birthdays as $bd): ?>
                                                         <tr>
                                                             <td><i class="fa fa-birthday-cake text-danger"></i> <?php echo $bd->first_name . ' ' . $bd->last_name; ?></td>
                                                             <td><?php echo 'Employee'; // Need designation join if available ?></td>
                                                         </tr>
                                                 <?php endforeach; endif; ?>
                                     </tbody>
                                 </table>
                             </div>
                         </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
