<style>
    .decision-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-title {
        font-weight: 800;
        color: #32325d;
        font-size: 2.5rem;
        letter-spacing: -1px;
    }

    .page-subtitle {
        color: #8898aa;
        font-size: 1.1rem;
    }

    /* Action Tiles */
    .action-tile-btn {
        border: none;
        background: none;
        padding: 0;
        width: 100%;
        text-align: left;
        transition: transform 0.3s ease;
    }

    .action-tile-btn:hover {
        transform: translateY(-10px);
    }

    .action-tile {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 280px;
        position: relative;
    }

    /* Next Destination Tile */
    .tile-travel {
        background: linear-gradient(135deg, #0177bc 0%, #0099ff 100%);
        color: white;
    }

    .tile-travel .icon-bg {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 15rem;
        opacity: 0.1;
        transform: rotate(-15deg);
    }

    /* Return Office Tile */
    .tile-office {
        background: #fff;
        color: #32325d;
        border: 1px solid #e9ecef;
    }

    .tile-office:hover {
        border-color: #f5365c;
    }

    .tile-office .icon-bg {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 15rem;
        opacity: 0.05;
        color: #f5365c;
        transform: rotate(-15deg);
    }

    .tile-content {
        padding: 30px;
        z-index: 2;
        position: relative;
    }

    .tile-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        display: block;
    }

    .tile-title {
        font-size: 1.8rem;
        font-weight: 800;
        display: block;
        line-height: 1.2;
        margin-bottom: 10px;
    }

    .tile-desc {
        font-size: 1rem;
        opacity: 0.8;
        display: block;
    }

    /* GPS Badge */
    .gps-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #2dce89;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
    }

    .gps-badge.error {
        color: #f5365c;
    }

    .gps-badge.loading {
        color: #8898aa;
    }

    #map {
        height: 250px;
        width: 100%;
        border-radius: 20px;
        margin-top: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 5px solid #fff;
    }
</style>

<div class="decision-container">
    <div class="page-header">
        <h2 class="page-title">What's Next?</h2>
        <p class="page-subtitle">Select your next activity to continue tracking.</p>
    </div>

    <form action="<?php echo site_url('new_movement/handle_decision'); ?>" method="post" id="decision_form">
        <input type="hidden" name="current_location" id="current_location">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <div class="row">
            <div class="row">
                <!-- Option 1: Next Destination -->
                <div class="col-md-3 mb-4">
                    <button type="submit" name="choice" value="travel" class="action-tile-btn">
                        <div class="action-tile tile-travel">
                            <i class="fa fa-map-o icon-bg"></i>
                            <div class="tile-content">
                                <i class="fa fa-motorcycle tile-icon"></i>
                                <span class="tile-title">Start Next Travel</span>
                                <span class="tile-desc">Moving to another client location or site visit.</span>

                                <!-- GPS Status inside card -->
                                <div id="gps_status_travel" class="gps-badge loading">
                                    <i class="fa fa-circle-o-notch fa-spin mr-2"></i> Locating...
                                </div>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Option 2: Start Meeting -->
                <div class="col-md-3 mb-4">
                    <button type="submit" name="choice" value="meeting" class="action-tile-btn">
                        <div class="action-tile tile-meeting"
                            style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%); color: white;">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tile-content">
                                <i class="fa fa-handshake-o tile-icon"></i>
                                <span class="tile-title">Start Meeting</span>
                                <span class="tile-desc">Met a client? Start the meeting timer/log here.</span>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Option 3: Log Visit (No Meeting) -->
                <div class="col-md-3 mb-4">
                    <a href="<?php echo site_url('new_movement/log_visit_form'); ?>" class="action-tile-btn d-block"
                        style="text-decoration: none;">
                        <div class="action-tile tile-visit"
                            style="background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%); color: white;">
                            <i class="fa fa-address-card-o icon-bg"></i>
                            <div class="tile-content">
                                <i class="fa fa-calendar-check-o tile-icon"></i>
                                <span class="tile-title">Log Visit Only</span>
                                <span class="tile-desc">Client busy or not available? Record the visit here.</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Option 4: Return to Office / Go Home -->
                <div class="col-md-3 mb-4">
                    <!-- Split actions -->
                    <div class="row h-100">
                        <div class="col-6 pr-1">
                            <button type="submit" name="choice" value="office" class="action-tile-btn h-100">
                                <div class="action-tile tile-office h-100" style="min-height: auto; justify-content: center; align-items: center; text-align: center;">
                                    <div class="tile-content p-2">
                                        <i class="fa fa-building-o tile-icon mb-2"
                                            style="color: #f5365c; font-size: 2rem; margin: 0 auto;"></i>
                                        <span class="tile-title mt-2" style="color: #32325d; font-size: 1rem; margin-bottom: 0;">Return to Office</span>
                                    </div>
                                </div>
                            </button>
                        </div>
                        <div class="col-6 pl-1">
                            <button type="submit" name="choice" value="home" class="action-tile-btn h-100">
                                <div class="action-tile tile-office h-100" style="min-height: auto; justify-content: center; align-items: center; text-align: center;">
                                    <div class="tile-content p-2">
                                        <i class="fa fa-home tile-icon mb-2" style="color: #2dce89; font-size: 2rem; margin: 0 auto;"></i>
                                        <span class="tile-title mt-2" style="color: #32325d; font-size: 1rem; margin-bottom: 0;">Go Home</span>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </form>

    <!-- Map Preview -->
    <h5 class="text-muted mt-4 mb-3 ml-2"
        style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Current Location
    </h5>
    <div id="map"></div>
</div>

<!-- LEAFLET & LOGIC -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Map
        const map = L.map('map', { zoomControl: false }).setView([23.8103, 90.4125], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        function updateLocation(lat, lng) {
            // Update Form Inputs
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('current_location').value = lat + "," + lng;

            // Update Map
            map.setView([lat, lng], 15);
            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>You are here</b>").openPopup();

            // Update UI Badge
            const badge = document.getElementById('gps_status_travel');
            if (badge) {
                badge.className = 'gps-badge';
                badge.innerHTML = '<i class="fa fa-check-circle mr-2"></i> GPS Active';
            }
        }

        function handleLocationError() {
            const badge = document.getElementById('gps_status_travel');
            if (badge) {
                badge.className = 'gps-badge error';
                badge.innerHTML = '<i class="fa fa-times-circle mr-2"></i> GPS Failed';
            }
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    updateLocation(position.coords.latitude, position.coords.longitude);
                },
                function (error) {
                    console.error("GPS Error:", error);
                    handleLocationError();
                },
                { enableHighAccuracy: true }
            );
        } else {
            handleLocationError();
        }
    });
</script>