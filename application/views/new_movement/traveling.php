<style>
    .travel-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        padding: 50px;
        border: none;
        max-width: 800px;
        margin: 0 auto;
        position: relative;
        overflow: hidden;
    }

    .pulse-ring {
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #2dce89;
        margin-right: 10px;
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(45, 206, 137, 0.7);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(45, 206, 137, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(45, 206, 137, 0);
        }
    }

    .info-card {
        background: #f6f9fc;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        border-left: 5px solid #5e72e4;
    }

    .btn-reached-custom {
        background: linear-gradient(87deg, #f5365c 0, #f56036 100%);
        border: none;
        border-radius: 30px;
        padding: 15px 40px;
        color: white;
        font-weight: 700;
        font-size: 18px;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
        width: 100%;
        max-width: 400px;
    }

    .btn-reached-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        color: white;
    }

    .btn-success-custom {
        background: linear-gradient(87deg, #2dce89 0, #2dcecc 100%) !important;
    }

    #map {
        border-radius: 15px;
        border: 4px solid white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="travel-card text-center">
    <div style="margin-bottom: 30px;">
        <h1 style="font-weight: 800; color: #32325d;"><span class="pulse-ring"></span> Traveling...</h1>
        <p class="lead" style="color: #8898aa;">Enjoy your journey. Drive safely!</p>
    </div>

    <div class="info-card text-left">
        <div class="row">
            <div class="col-md-6">
                <p style="margin:0; font-size: 14px; color: #8898aa; text-transform: uppercase; font-weight: 600;">
                    Travel Duration</p>
                <p style="font-size: 24px; font-weight: 800; color: #32325d; font-family: monospace;" id="travel_timer">
                    00:00:00</p>
            </div>
            <div class="col-md-6">
                <p style="margin:0; font-size: 14px; color: #8898aa; text-transform: uppercase; font-weight: 600;">
                    Started At</p>
                <p style="font-size: 18px; font-weight: 700; color: #32325d;">
                    <?php echo date('h:i A', strtotime($travel->start_time)); ?></p>
            </div>
        </div>
    </div>

    <form action="<?php echo site_url('new_movement/reached_destination'); ?>" method="post">
        <input type="hidden" name="current_location" id="current_location">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <?php if ($this->session->flashdata('is_office_return')): ?>
            <input type="hidden" name="is_office_return" value="1">
            <button type="submit" class="btn btn-reached-custom btn-success-custom" id="reached_btn">
                <i class="fa fa-building-o"></i> Reached Office
            </button>
        <?php elseif ($this->session->flashdata('is_home_return')): ?>
            <input type="hidden" name="is_home_return" value="1">
            <button type="submit" class="btn btn-reached-custom btn-success-custom" id="reached_btn">
                <i class="fa fa-home"></i> Reached Home
            </button>
        <?php else: ?>
            <button type="submit" class="btn btn-reached-custom" id="reached_btn">
                <i class="fa fa-map-marker"></i> Reached Destination
            </button>
        <?php endif; ?>

        <p id="gps_status" class="mt-3 text-muted" style="font-size: 12px;"><i class="fa fa-crosshairs"></i> Updating
            precise location...</p>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 350px;
        width: 100%;
        border-radius: 8px;
        margin-top: 20px;
    }
</style>

<div class="row">
    <div class="col-md-12">
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

        let marker;

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(updatePosition, showError, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            });
        }

        function updatePosition(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.getElementById('current_location').value = position.coords.latitude + ", " + position.coords.longitude;
            document.getElementById('gps_status').innerText = "";

            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            map.setView([lat, lng], 16);
            if (marker) {
                marker.setLatLng([lat, lng]).bindPopup("Current Location").openPopup();
            } else {
                marker = L.marker([lat, lng]).addTo(map).bindPopup("Current Location").openPopup();
            }
        }

        function showError(error) {
            document.getElementById('gps_status').innerText = "GPS Error: " + error.message;
        }
    });
</script>