<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Movement Card Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #fff; padding: 20px; }
    </style>
</head>
<body>
<?php
// Movement Card View - Iterates through employees
// Expects: $employees (array of objects), $start_date, $end_date
// And uses Model to get data for each.
?>
<div class="container" align="center">
<?php
foreach ($employees as $emp) {
    echo "<div style='min-height:700px; overflow:hidden; page-break-after: always;'>";
    
    $this->load->view('admin/head_bangla');
    
    echo "<h4 style='margin: 5px 0; font-weight: bold;'>Movement Card Report</h4>";
    echo "<p>From  $start_date -TO- $end_date</p>";
    echo "<br />";

    // Employee Info Table
    echo "<table border='0' style='font-size:13px; margin-bottom: 15px;' width='100%'>";
        echo "<tr>";
            echo "<td width='10%'><strong>Emp ID:</strong></td>";
            echo "<td width='25%'>{$emp->employee_id}</td>";
            echo "<td width='10%'><strong>Name:</strong></td>";
            echo "<td width='25%'>{$emp->first_name} {$emp->last_name}</td>";
             echo "<td width='10%'><strong>Dept:</strong></td>";
            echo "<td width='20%'>{$emp->department_name}</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td><strong>Desig:</strong></td>";
            echo "<td>{$emp->designation_name}</td>";
            echo "<td><strong>DOJ:</strong></td>";
            echo "<td>" . date("d-M-Y", strtotime($emp->date_of_joining)) . "</td>";
            echo "<td><strong>DOB:</strong></td>";
            echo "<td>" . date("d-M-Y", strtotime($emp->date_of_birth)) . "</td>";
        echo "</tr>";
    echo "</table>";

    // Data Table
    // Fetch data for this employee
    // Using CI instance to call model since we are in a view file (and mimicking legacy code)
    $CI =& get_instance();
    // Reusing get_report_data_v2 but filtering for this user
    $movements = $CI->New_movement_model->get_report_data_v2($start_date, $end_date, [$emp->user_id]);

    echo "<table class='table table-bordered table-sm table-striped' style='text-align:center; font-size:12px;'>
          <thead>
              <tr>
                  <th>Date</th>
                  <th>Start Time</th>
                  <th>End Time</th>
                  <th>Duration</th>
                  <th>Total KM</th>
                  <th>TA Claimed</th>
                  <th>Status</th>
                  <th>Purpose</th>
              </tr>
          </thead>
          <tbody>";

    $total_ta = 0;
    $total_km = 0;

    if(!empty($movements)) {
        // Since get_report_data_v2 sorts DESC, let's sort ASC for the card? usually cards are chronological.
        // We can array_reverse or sort. Let's keep it simpl.
        $movements = array_reverse($movements);

        foreach($movements as $row) {
            // Get detailed stats if not in $row (v2 query includes travel_count, meeting_count)
            // Need actual KM and TA amount.
            // Model method get_report_data_v2 doesn't return TA amount sum.
            // I should probably add a helper or separate query.
            // For now, let's assume we update the model slightly OR fetch here.
            
            // Let's do a quick query here as this is "Report Logic in View" style :(
            $expenses = $CI->db->select_sum('amount')->where('movement_id', $row->id)->get('new_movement_travel_expenses')->row()->amount;
            $dist = $CI->db->select_sum('distance_km')->where('movement_id', $row->id)->get('new_movement_travels')->row()->distance_km;
            
            $total_ta += $expenses;
            $total_km += $dist;

            $duration = '-';
            if($row->end_time) {
                $start = new DateTime($row->start_time);
                $end = new DateTime($row->end_time);
                $diff = $start->diff($end);
                $duration = $diff->format('%h h %i m');
            }

            echo "<tr>";
            echo "<td>" . date('d M Y', strtotime($row->created_at)) . "</td>";
            echo "<td>" . date('h:i A', strtotime($row->start_time)) . "</td>";
            echo "<td>" . ($row->end_time ? date('h:i A', strtotime($row->end_time)) : '-') . "</td>";
            echo "<td>$duration</td>";
            echo "<td>" . ($dist > 0 ? round($dist, 2) : '-') . "</td>";
            echo "<td>" . ($expenses > 0 ? number_format($expenses, 2) : '-') . "</td>";
            echo "<td>" . ucfirst($row->status) . "</td>";
            echo "<td>{$row->purpose}</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No movements found</td></tr>";
    }

    echo "</tbody>";
    
    // Footer / Totals
    echo "<tfoot>
            <tr style='font-weight:bold; background:#eee;'>
                <td colspan='4' class='text-right'>Total:</td>
                <td>" . number_format($total_km, 2) . "</td>
                <td>" . number_format($total_ta, 2) . "</td>
                <td colspan='2'></td>
            </tr>
          </tfoot>";
    
    echo "</table>";
    echo "</div>"; // End Page Container
}
?>
</div>
</body>
</html>
