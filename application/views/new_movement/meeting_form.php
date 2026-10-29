<style>
    .meeting-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        padding: 50px;
        border: none;
        max-width: 900px;
        margin: 0 auto;
    }

    .form-control-custom {
        height: 50px;
        border-radius: 10px;
        border: 1px solid #e9ecef;
        background-color: #f8f9fe;
        padding-left: 15px;
        transition: all 0.3s;
    }

    .form-control-custom:focus {
        background-color: #fff;
        border-color: #5e72e4;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11);
    }

    .form-label-custom {
        font-weight: 600;
        color: #525f7f;
        margin-bottom: 8px;
        text-transform: uppercase;
        font-size: 11px;
    }

    .btn-start-meeting {
        background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%);
        border-radius: 30px;
        padding: 12px 30px;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11);
        color: white;
        transition: all 0.3s;
        width: 100%;
    }

    .btn-start-meeting:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1);
        color: white;
    }
</style>

<div class="meeting-card">
    <h2 style="font-weight: 800; color: #32325d; text-align: center; margin-bottom: 30px;">👔 Start Meeting</h2>

    <form action="<?php echo site_url('new_movement/process_start_meeting'); ?>" method="post">

        <!-- Meeting Source -->
        <div class="form-group text-center mb-4">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="meeting_source" id="source_new" value="new" checked
                        onchange="toggleSource('new')"> Start New Meeting
                </label>
                <!-- <label class="btn btn-secondary">
                    <input type="radio" name="meeting_source" id="source_existing" value="existing"
                        onchange="toggleSource('existing')"> Start from Schedule
                </label> -->
            </div>
        </div>

        <!-- Hidden Fields -->
        <input type="hidden" name="crm_lead_id" id="crm_lead_id">

        <!-- Schedule Fetcher (Hidden by default) -->
        <div id="schedule_section"
            style="display:none; background: #f4f5f7; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <h5 class="text-uppercase text-muted font-weight-bold" style="font-size: 12px;">Select Scheduled Meeting
            </h5>

            <!-- Removed Button, auto-fetch on selection -->
            <div id="fetch_status" class="text-center text-muted small mt-2 mb-2"></div>

            <div class="form-group" id="schedule_list_container" style="display:none;">
                <label class="form-label-custom">Select Schedule</label>
                <select class="form-control form-control-custom" id="schedule_select" onchange="populateSchedule(this)">
                    <option value="">-- Select a Schedule --</option>
                </select>
            </div>
        </div>

        <!-- Entity Type -->
        <div class="form-group text-center mb-4" id="entity_type_section">
            <label class="radio-inline mr-3">
                <input type="radio" name="entity_type" value="organization" checked
                    onchange="toggleEntityLabel(this.value)"> 🏢 Organization
            </label>
            <label class="radio-inline">
                <input type="radio" name="entity_type" value="person" onchange="toggleEntityLabel(this.value)"> 👤
                Person
            </label>
        </div>

        <div id="manual_fields">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group position-relative">
                        <label class="form-label-custom" id="client_name_label">Company Name/Person Name</label>
                        <input type="text" name="client_name" id="client_name" class="form-control form-control-custom"
                            required placeholder="Start typing to search..." autocomplete="off">
                        <div id="search_results"
                            style="display:none; position: absolute; z-index: 999; width: 100%; max-height: 250px; overflow-y: auto; background: white; border: 1px solid #e9ecef; border-radius: 0 0 10px 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="text-muted text-uppercase font-weight-bold mt-3 mb-3"
                style="font-size: 12px; letter-spacing: 1px;">Contact Person Details</h5>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">First Name</label>
                        <input type="text" name="contact_person" id="contact_person"
                            class="form-control form-control-custom" placeholder="First Name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Job Title</label>
                        <input type="text" name="contact_job_title" id="contact_job_title"
                            class="form-control form-control-custom" placeholder="Job Title">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Email</label>
                        <input type="email" name="contact_email" id="contact_email"
                            class="form-control form-control-custom" placeholder="Email Address">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Phone Number</label>
                        <input type="text" name="contact_phone" id="contact_phone"
                            class="form-control form-control-custom" placeholder="Phone Number">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Meeting Type</label>
                        <select name="meeting_type" class="form-control form-control-custom">
                            <option value="Sales">Sales</option>
                            <option value="Technical">Technical</option>
                            <option value="General">General</option>
                            <option value="Training">Training</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Spacing -->
                </div>
            </div>

            <div class="form-group">
                <label class="form-label-custom">Remarks / Agenda</label>
                <textarea name="remarks" class="form-control form-control-custom" rows="3"
                    style="height: auto; padding-top:15px;"></textarea>
            </div>
        </div>

        <input type="hidden" name="location" id="location">

        <div class="form-group mt-4">
            <button type="submit" id="submit_btn" class="btn btn-start-meeting">
                <i class="fa fa-clock-o"></i> START MEETING TIMER
            </button>
        </div>
    </form>
</div>

<style>
    .search-item {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }

    .search-item:hover {
        background: #f8f9fe;
        color: #5e72e4;
    }

    .search-item strong {
        display: block;
        font-size: 14px;
    }

    .search-item small {
        color: #8898aa;
    }
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 250px;
        width: 100%;
        border-radius: 8px;
        margin-top: 15px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <label>Verify Location</label>
        <div id="map"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const map = L.map('map').setView([23.8103, 90.4125], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, function (error) {
                console.error("GPS Error: " + error.message);
                // Default to empty or manual input if error
            });
        }

        function showPosition(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            // Reverse Geocode
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById('location').value = data.display_name;
                        document.getElementById('gps_status').innerHTML = '<i class="fa fa-check-circle"></i> Location: ' + data.display_name;
                    } else {
                        document.getElementById('location').value = lat + ", " + lng;
                        document.getElementById('gps_status').innerHTML = '<i class="fa fa-check-circle"></i> Coordinates Found';
                    }
                    if (document.getElementById('submit_btn')) {
                        document.getElementById('submit_btn').disabled = false;
                    }
                    if (document.getElementById('gps_status')) {
                        document.getElementById('gps_status').className = "text-success font-weight-bold";
                    }
                })
                .catch(error => {
                    document.getElementById('location').value = lat + ", " + lng;
                    if (document.getElementById('submit_btn')) {
                        document.getElementById('submit_btn').disabled = false;
                    }
                    if (document.getElementById('gps_status')) {
                        document.getElementById('gps_status').innerText = "Location Found (Address unavailable)";
                        document.getElementById('gps_status').className = "text-success";
                    }
                });

            map.setView([lat, lng], 15);
            L.marker([lat, lng]).addTo(map).bindPopup("Current Location").openPopup();
        }
        function toggleEntityLabel(type) {
            if (type === 'person') {
                document.getElementById('client_name_label').innerText = 'Name';
            } else {
                document.getElementById('client_name_label').innerText = 'Company Name';
            }
        }

        // Global functions for inline usage
        window.toggleSource = function (source) {
            if (source === 'existing') {
                document.getElementById('schedule_section').style.display = 'block';
                document.getElementById('entity_type_section').style.display = 'none';
                document.getElementById('manual_fields').style.display = 'none';
                document.querySelector('input[name="client_name"]').required = false;

                // Auto fetch events
                fetchSchedules();
            } else {
                document.getElementById('schedule_section').style.display = 'none';
                document.getElementById('entity_type_section').style.display = 'block';
                document.getElementById('manual_fields').style.display = 'block';
                document.querySelector('input[name="client_name"]').required = true;
                resetForm();
            }
        }

        window.fetchSchedules = function () {
            const status = document.getElementById('fetch_status');
            const container = document.getElementById('schedule_list_container');
            const select = document.getElementById('schedule_select');

            // Remove button logic
            // btn.disabled = true;

            status.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Fetching schedules...';
            container.style.display = 'none';

            // Call local proxy, email is handled in backend
            fetch('<?php echo site_url('new_movement/fetch_schedules_proxy'); ?>', {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {

                    if (data.success) {
                        if (data.schedules.length > 0) {
                            select.innerHTML = '<option value="">-- Select a Schedule --</option>';
                            // Store full data in option dataset
                            data.schedules.forEach(sch => {
                                const opt = document.createElement('option');
                                opt.value = sch.id;
                                opt.text = sch.start_time + ' - ' + sch.client_name + ' (' + sch.title + ')';
                                opt.dataset.json = JSON.stringify(sch);
                                select.appendChild(opt);
                            });
                            container.style.display = 'block';
                            status.innerText = 'Found ' + data.schedules.length + ' schedules.';
                        } else {
                            status.innerText = 'No schedules found for today.';
                        }
                    } else {
                        status.innerText = 'Error: ' + data.message;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    status.innerText = 'Failed to connect to CRM.';
                });
        }

        window.populateSchedule = function (select) {
            if (select.value && select.options[select.selectedIndex].dataset.json) {
                const data = JSON.parse(select.options[select.selectedIndex].dataset.json);

                // Auto fill
                document.querySelector('input[name="client_name"]').value = data.client_name;
                document.querySelector('#crm_lead_id').value = data.lead_id;

                // Trigger entity label update if needed
                // Default to organization for now

                // Add remarks if description exists
                if (data.description) {
                    document.querySelector('textarea[name="remarks"]').value = data.description;
                }
            }
        }

        function resetForm() {
            document.querySelector('#crm_lead_id').value = '';
            document.querySelector('input[name="client_name"]').value = '';
            document.querySelector('textarea[name="remarks"]').value = '';
        }

        // --- Autocomplete Logic ---
        const searchInput = document.getElementById('client_name');
        const resultsBox = document.getElementById('search_results');
        let timeout = null;

        if (searchInput) {
            searchInput.addEventListener('keyup', function () {
                const query = this.value;
                if (query.length < 2) {
                    resultsBox.style.display = 'none';
                    return;
                }

                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    fetch('<?php echo site_url('new_movement/search_clients_proxy'); ?>?query=' + encodeURIComponent(query))
                        .then(response => response.json())
                        .then(data => {
                            resultsBox.innerHTML = '';
                            if (data.length > 0) {
                                data.forEach(client => {
                                    const div = document.createElement('div');
                                    div.className = 'search-item';
                                    div.innerHTML = `<strong>${client.client_name}</strong><small>${client.contact_person || ''}</small>`;
                                    div.onclick = function () {
                                        populateForm(client);
                                        resultsBox.style.display = 'none';
                                    };
                                    resultsBox.appendChild(div);
                                });
                                resultsBox.style.display = 'block';
                            } else {
                                resultsBox.style.display = 'none';
                            }
                        })
                        .catch(err => {
                            console.error("Search Error: ", err);
                        });
                }, 300);
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function (e) {
                if (e.target !== searchInput && e.target !== resultsBox) {
                    resultsBox.style.display = 'none';
                }
            });
        }

        function populateForm(data) {
            document.getElementById('client_name').value = data.client_name;
            if (document.getElementById('contact_person')) document.getElementById('contact_person').value = data.contact_person || '';
            if (document.getElementById('contact_job_title')) document.getElementById('contact_job_title').value = data.contact_job_title || '';
            if (document.getElementById('contact_email')) document.getElementById('contact_email').value = data.contact_email || '';
            if (document.getElementById('contact_phone')) document.getElementById('contact_phone').value = data.contact_phone || '';
        }

    });
</script>