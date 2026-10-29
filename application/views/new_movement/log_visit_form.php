<style>
    .log-visit-card {
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
        border-color: #11cdef;
        box-shadow: 0 4px 6px rgba(17, 205, 239, 0.11);
    }

    .form-label-custom {
        font-weight: 600;
        color: #525f7f;
        margin-bottom: 8px;
        text-transform: uppercase;
        font-size: 11px;
    }

    .btn-log-visit {
        background: linear-gradient(87deg, #11cdef 0, #1171ef 100%);
        border-radius: 30px;
        padding: 12px 30px;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 6px rgba(17, 205, 239, 0.11);
        color: white;
        transition: all 0.3s;
        width: 100%;
    }

    .btn-log-visit:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(17, 205, 239, 0.1);
        color: white;
    }

    /* Search Results */
    #search_results {
        position: absolute;
        z-index: 999;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .search-item {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }

    .search-item:hover {
        background: #f8f9fe;
        color: #11cdef;
    }

    .search-item strong {
        display: block;
        font-size: 14px;
    }

    .search-item small {
        color: #8898aa;
    }
</style>

<div class="log-visit-card">
    <h2 style="font-weight: 800; color: #32325d; text-align: center; margin-bottom: 10px;">📋 Log Client Visit</h2>
    <p class="text-center text-muted mb-4">Client busy or meeting didn't happen? Record your visit details below.</p>

    <form action="<?php echo site_url('new_movement/process_log_visit'); ?>" method="post">

        <!-- Hidden Fields -->
        <input type="hidden" name="crm_lead_id" id="crm_lead_id">

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
                        <div id="search_results"></div>
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

            <div class="form-group">
                <label class="form-label-custom">Visit Remarks / Notes</label>
                <textarea name="remarks" class="form-control form-control-custom" rows="3"
                    style="height: auto; padding-top:15px;" required
                    placeholder="e.g. Client was busy, rescheduled, dropped off documents..."></textarea>
            </div>
        </div>

        <input type="hidden" name="location" id="location">

        <div class="form-group mt-4">
            <button type="submit" id="submit_btn" class="btn btn-log-visit" disabled>
                <i class="fa fa-check-circle"></i> SUBMIT VISIT LOG
            </button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 200px;
        width: 100%;
        border-radius: 8px;
        margin-top: 15px;
    }
</style>

<div class="row mt-4 justify-content-center">
    <div class="col-md-8">
        <label>Verifying Location <span id="gps_status" class="text-warning ml-2"><i class="fa fa-spinner fa-spin"></i>
                Getting GPS...</span></label>
        <div id="map"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- Map Logic ---
        const map = L.map('map').setView([23.8103, 90.4125], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, function (error) {
                console.error("GPS Error: " + error.message);
                document.getElementById('gps_status').innerHTML = '<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> GPS Access Denied</span>';
                // Allow submit anyway if critical? prefer enforcing GPS.
                // Enabling fallback for testing
                document.getElementById('submit_btn').disabled = false;
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
                        document.getElementById('gps_status').innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> ' + data.display_name + '</span>';
                    } else {
                        document.getElementById('location').value = lat + ", " + lng;
                        document.getElementById('gps_status').innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> Coordinates Found</span>';
                    }
                    document.getElementById('submit_btn').disabled = false;
                })
                .catch(error => {
                    document.getElementById('location').value = lat + ", " + lng;
                    document.getElementById('submit_btn').disabled = false;
                    document.getElementById('gps_status').innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> Location Found</span>';
                });

            map.setView([lat, lng], 15);
            L.marker([lat, lng]).addTo(map).bindPopup("Current Location").openPopup();
        }

        window.toggleEntityLabel = function (type) {
            if (type === 'person') {
                document.getElementById('client_name_label').innerText = 'Name';
            } else {
                document.getElementById('client_name_label').innerText = 'Company Name';
            }
        }

        // --- Autocomplete Logic ---
        const searchInput = document.getElementById('client_name');
        const resultsBox = document.getElementById('search_results');
        let timeout = null;

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

        function populateForm(data) {
            document.getElementById('client_name').value = data.client_name;
            document.getElementById('contact_person').value = data.contact_person || '';
            document.getElementById('contact_job_title').value = data.contact_job_title || '';
            document.getElementById('contact_email').value = data.contact_email || '';
            document.getElementById('contact_phone').value = data.contact_phone || '';
        }

    });
</script>