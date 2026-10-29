
    <style>
        .main-container { display: flex; flex-wrap: wrap; gap: 20px; }

        /* Map Container - ম্যাপ যাতে আগে জায়গা দখল করে থাকে */
        .map-section {
            flex: 2; min-width: 350px; background: #fff;
            border-radius: 20px; padding: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            height: 600px; /* নির্দিষ্ট হাইট */
        }
        #map { height: 100%; width: 100%; border-radius: 15px; background: #eee; }

        /* Timeline Styling */
        .timeline-section { flex: 1; min-width: 300px; }
        .details-card {
            background: #fff; border-radius: 20px; padding: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        }
        .timeline-modern { list-style: none; padding: 0; position: relative; }
        .timeline-modern:before {
            content: ''; position: absolute; top: 0; bottom: 0;
            left: 20px; width: 2px; background: #e9ecef;
        }
        .timeline-item { position: relative; padding-left: 50px; margin-bottom: 25px; }
        .timeline-icon {
            position: absolute; left: 0; top: 0; width: 40px; height: 40px;
            border-radius: 50%; text-align: center; line-height: 40px; color: white;
        }
        .timeline-icon-start { background: #0177bc; }
        .timeline-icon-meeting { background: #fff; border: 2px solid #0177bc; color: #0177bc; line-height: 36px; }
        .timeline-icon-end { background: #000; }

        .location-text { font-size: 13px; color: #525f7f; margin: 5px 0; font-weight: bold; }
        .timeline-time { font-size: 12px; color: #8898aa; background: #f6f9fc; padding: 2px 8px; border-radius: 4px; }

        @keyframes dash { to { stroke-dashoffset: -100; } }
        .flow-animation { animation: dash 3s linear infinite; }
    </style>
</head>
<body>

    <div class="row mb-3">
        <div class="col-md-12">
            <?php $session = $this->session->userdata('username'); ?>
            <?php if($session['role_id'] == 3){ ?>
            <a href="<?php echo site_url('new_movement/emp_dashboard'); ?>" class="btn btn-outline-primary" style="border-radius: 20px;"><i class="fa fa-arrow-left"></i> Back to Dashboard
            </a>
            <?php } else { ?>
            <?php if(!empty($ftype)){ ?>
            <a href="<?php echo site_url('new_movement/ta_list'); ?>" class="btn btn-outline-primary" style="border-radius: 20px;">
                <i class="fa fa-arrow-left"></i> Back to TA List
            </a>
            <?php } else { ?>
            <a href="<?php echo site_url('new_movement'); ?>" class="btn btn-outline-primary" style="border-radius: 20px;">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
            </a>
            <?php } } ?>
        </div>
    </div>

    <div class="main-container">
    <div class="map-section">
        <div id="map"></div>
    </div>

    <div class="timeline-section">
        <!-- Calculate Total Distance -->
        <?php
            $total_km = 0;
            if(isset($travels)) {
                foreach($travels as $t) {
                    $total_km += floatval($t->distance_km);
                }
            }
        ?>
        <div class="details-card mb-4">
            <h4 style="margin-top:0;">
                Movement Summary #<?php echo $movement->id; ?>
                <span class="float-right badge badge-info" style="font-size: 14px; background-color: #11cdef; color: white;">
                    <i class="fa fa-road"></i> <?php echo isset($total_km) ? round($total_km, 2) : 0; ?> km
                </span>
            </h4>
            <ul class="timeline-modern">
                <li class="timeline-item" onclick="focusLocation('start')" style="cursor: pointer;">
                    <div class="timeline-icon timeline-icon-start"><i class="fa fa-map-marker"></i></div>
                    <div class="timeline-content">
                        <h5>Start Point</h5>
                        <p class="location-text" data-lat="<?php echo $movement->start_latitude; ?>" data-lng="<?php echo $movement->start_longitude; ?>">
                            <?php if(!empty($movement->start_location) && strpos($movement->start_location, ',') === false): ?>
                                <?php echo $movement->start_location; ?>
                            <?php else: ?>
                                <i class="fa fa-spinner fa-spin"></i> Finding address...
                            <?php endif; ?>
                        </p>
                        <span class="timeline-time"><?php echo date('h:i A', strtotime($movement->start_time)); ?></span>
                    </div>
                </li>

                <?php
                $t_index = 0;
                foreach($meetings as $meeting):
                    $travel_info = isset($travels[$t_index]) ? $travels[$t_index] : null;
                    $t_index++;
                ?>
                <li class="timeline-item" onclick="focusLocation('meeting_<?php echo $meeting->id; ?>')" style="cursor: pointer;">
                    <div class="timeline-icon timeline-icon-meeting"><i class="fa fa-briefcase"></i></div>
                    <div class="timeline-content">
                        <h5>Meeting: <?php echo $meeting->client_name; ?></h5>
                        <!-- Distance Info -->
                        <?php if($travel_info && floatval($travel_info->distance_km) >= 0): ?>
                            <small class="d-block text-muted mb-1">
                                <i class="fa fa-road"></i> Traveled: <strong><?php echo $travel_info->distance_km; ?> km</strong>
                            </small>
                        <?php endif; ?>

                        <p class="location-text" data-lat="<?php echo $meeting->latitude; ?>" data-lng="<?php echo $meeting->longitude; ?>">
                             <?php if(!empty($meeting->location) && strpos($meeting->location, ',') === false): ?>
                                <?php echo $meeting->location; ?>
                            <?php else: ?>
                                <i class="fa fa-spinner fa-spin"></i> Finding address...
                            <?php endif; ?>
                        </p>

                            <?php if(!empty($meeting->feedback)): ?>
                                <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-left: 3px solid #ffc107; border-radius: 4px;">
                                    <small class="text-muted d-block" style="font-weight: 600; text-transform: uppercase; font-size: 10px;">Meeting Feedback</small>
                                    <span style="font-size: 13px; color: #333; font-style: italic;">"<?php echo nl2br($meeting->feedback); ?>"</span>
                                </div>
                            <?php endif; ?>

                            <div class="mt-2 d-flex align-items-center justify-content-between">
                                <span class="timeline-time"><?php echo date('h:i A', strtotime($meeting->start_time)); ?></span>
                                <button class="btn btn-sm btn-outline-info" style="border-radius: 15px; font-size: 11px; padding: 2px 10px;"
                                        onclick='viewMeetingDetails(<?php echo json_encode($meeting); ?>)'>
                                    <i class="fa fa-info-circle"></i> View Info
                                </button>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>

                <?php if($movement->end_time):
                    $last_travel = isset($travels[$t_index]) ? $travels[$t_index] : null;
                ?>
                <li class="timeline-item">
                    <div class="timeline-icon timeline-icon-end"><i class="fa fa-home"></i></div>
                    <div class="timeline-content">
                        <h5>Movement Ended</h5>
                        <?php if($last_travel && floatval($last_travel->distance_km) >= 0): ?>
                            <small class="d-block text-muted mb-1">
                                <i class="fa fa-road"></i> Traveled: <strong><?php echo $last_travel->distance_km; ?> km</strong>
                            </small>
                        <?php endif; ?>
                        <span class="timeline-time"><?php echo date('h:i A', strtotime($movement->end_time)); ?></span>
                    </div>
                </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- TA CLAIM TABLE -->
        <?php if(!empty($expenses)): ?>
        <div class="details-card">
            <h4 style="margin-top:0; border-bottom: 2px solid #f6f9fc; padding-bottom: 15px; margin-bottom: 20px;">
                <i class="fa fa-money text-success"></i> TA Claim Details
                <span class="float-right badge badge-primary" style="background:#0177bc;">
                    <?php echo ucfirst(str_replace('_', ' ', $movement->ta_status)); ?>
                </span>
            </h4>

            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Type</th>
                            <th>Note</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; ?>
                        <?php foreach($expenses as $exp): ?>
                        <tr>
                            <td>
                                <?php echo $exp->transport_type; ?>
                                <?php if(isset($travels)): ?>
                                    <?php foreach($travels as $t): ?>
                                        <?php if($t->id == $exp->travel_id && $t->distance_km > 0): ?>
                                            <br><small class="text-muted">(<?php echo $t->distance_km; ?> km)</small>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td><small><?php echo $exp->note; ?></small></td>
                            <td class="text-right font-weight-bold"><?php echo number_format($exp->amount, 2); ?></td>
                        </tr>
                        <?php $total += $exp->amount; ?>
                        <?php endforeach; ?>
                        <tr style="background: #f6f9fc; border-top: 2px solid #000;">
                            <td colspan="2" class="font-weight-bold text-uppercase">Total Claim</td>
                            <td class="text-right font-weight-bold" style="color: #0177bc; font-size: 16px;">
                                ৳ <?php echo number_format($total, 2); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Meeting Details Modal -->
<div class="modal fade" id="meetingDetailsModal" tabindex="-1" role="dialog" aria-labelledby="mdmLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mdmLabel">Meeting Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-striped">
            <tr>
                <th width="35%">Client Name</th>
                <td id="m_client"></td>
            </tr>
            <tr>
                <th>Contact Person</th>
                <td id="m_contact"></td>
            </tr>
            <tr>
                <th>Meeting Type</th>
                <td id="m_type"></td>
            </tr>
            <tr>
                <th>Start Time</th>
                <td id="m_start"></td>
            </tr>
            <tr>
                <th>End Time</th>
                <td id="m_end"></td>
            </tr>
            <tr>
                <th>Location</th>
                <td id="m_location"></td>
            </tr>
            <tr>
                <th>Remarks</th>
                <td id="m_remarks"></td>
            </tr>
            <tr>
                <th>Feedback</th>
                <td id="m_feedback" class="text-warning"></td>
            </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function viewMeetingDetails(meeting) {
    // Populate Modal
    $('#m_client').text(meeting.client_name || '-');
    $('#m_contact').text(meeting.contact_person || '-');
    $('#m_type').text(meeting.meeting_type || 'General');
    $('#m_start').text(new Date(meeting.start_time).toLocaleTimeString());
    $('#m_end').text(meeting.end_time ? new Date(meeting.end_time).toLocaleTimeString() : 'Ongoing');
    $('#m_location').text(meeting.location || '-');
    $('#m_remarks').text(meeting.remarks || '-');
    $('#m_feedback').text(meeting.feedback || 'No Feedback');

    // Show Modal
    $('#meetingDetailsModal').modal('show');
}
</script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ১. ম্যাপ রেন্ডারিং সবার আগে
    var map = L.map('map', {
        zoomSnap: 0.5,
        wheelDebounceTime: 150
    }).setView([23.8103, 90.4125], 13);

    // টাইলস লোড হওয়া নিশ্চিত করা
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OSM',
        keepBuffer: 4
    }).addTo(map);

    // রেন্ডারিং ফিক্স: ম্যাপ লোড হওয়ার ৫০০ মিলি সেকেন্ড পর ইনভ্যালিডেট সাইজ কল করা
    setTimeout(function() {
        map.invalidateSize();
    }, 500);

    // ২. ডাটা অ্যাড করা
    var markers = {}; // Store markers globally

    function addMarkers() {
        var latlngs = []; // Initialize array

        // Start Point
        <?php if($movement->start_latitude): ?>
            var start = [<?php echo $movement->start_latitude; ?>, <?php echo $movement->start_longitude; ?>];
            markers['start'] = L.marker(start).addTo(map).bindPopup("<b>Start Point</b><br><?php echo $movement->start_time; ?>");
            latlngs.push(start);
        <?php endif; ?>

        // Meetings
        <?php foreach($meetings as $meeting): ?>
            <?php if($meeting->latitude): ?>
                var m = [<?php echo $meeting->latitude; ?>, <?php echo $meeting->longitude; ?>];
                markers['meeting_<?php echo $meeting->id; ?>'] = L.marker(m).addTo(map).bindPopup("<b>Meeting</b><br><?php echo $meeting->client_name; ?>");
                latlngs.push(m);
            <?php endif; ?>
        <?php endforeach; ?>

        if (latlngs.length > 1) {
            var path = L.polyline(latlngs, {
                color: '#0177bc', weight: 5, dashArray: '10, 15', className: 'flow-animation'
            }).addTo(map);
            map.fitBounds(path.getBounds(), {padding: [50, 50]});
        }
    }

    addMarkers();

    // Map Focus Function
    window.focusLocation = function(id) {
        var marker = markers[id];
        if (marker) {
            var latLng = marker.getLatLng();
            map.flyTo(latLng, 16, {
                animate: true,
                duration: 1.5
            });
            setTimeout(function() {
                marker.openPopup();
            }, 1500);

            // Scroll to map on mobile
            if(window.innerWidth < 768) {
                document.getElementById('map').scrollIntoView({behavior: 'smooth'});
            }
        }
    };

    // ৩. রিভার্স জিওকোডিং (এড্রেস কনভার্ট করা)
    async function fetchAddresses() {
        const elements = document.querySelectorAll('.location-text');
        for (let el of elements) {
            const lat = el.getAttribute('data-lat');
            const lng = el.getAttribute('data-lng');

            if (lat && lng) {
                try {
                    await new Promise(r => setTimeout(r, 1200));
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                    const data = await res.json();
                    if (data && data.display_name) {
                        const parts = data.display_name.split(',');
                        el.innerHTML = `<i class="fa fa-map-marker"></i> ` + parts.slice(0, 2).join(',') + '<br><small>' + parts.slice(2, 4).join(',') + '</small>';
                    }
                } catch (e) { console.log("Geocode error"); }
            }
        }
    }
    fetchAddresses();
</script>
