<style>
    .movement-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08); /* Stronger shadow */
        padding: 50px; /* Increased padding */
        border: none;
        max-width: 900px;
        margin: 0 auto;
    }
    .form-control-custom {
        height: 55px;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background-color: #f8f9fe;
        padding-left: 20px;
        font-size: 15px;
        transition: all 0.3s;
    }
    .form-control-custom:focus {
        background-color: #fff;
        border-color: #5e72e4;
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    .form-label-custom {
        font-weight: 600;
        color: #525f7f;
        margin-bottom: 10px;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }
    #map {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border: 5px solid #fff;
    }
    .btn-submit-custom {
        background: #0177bc !important;
        border-radius: 30px;
        padding: 12px 30px;
        font-weight: 600;
        letter-spacing: 0.5px;
        font-size: 16px;
        border: none;
        box-shadow: 0 4px 6px rgba(1, 119, 188, 0.4);
        transition: all 0.3s;
        width: 100%;
        color: white;
    }
    .btn-submit-custom:hover:not(:disabled) {
        background: #000 !important;
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(0,0,0, 0.2);
    }
    .btn-submit-custom:disabled {
        background: #e9ecef !important;
        cursor: not-allowed;
        color: #8898aa;
    }
</style>

<div class="movement-card">
  <h2 style="font-weight: 800; color: #32325d; text-align: center; margin-bottom: 40px;">🚀 Start Your Journey</h2>
  <form action="<?php echo site_url('new_movement/start'); ?>" method="post" enctype="multipart/form-data">
      
      <div class="row mb-4">
          <div class="col-md-12">
               <!-- Map Container -->
               <div id="map" style="height: 350px; width: 100%;"></div>
          </div>
      </div>

      <div class="row">
          <div class="col-md-6">
              <input type="hidden" name="start_location" id="start_location">
          </div>
          <div class="col-md-6">
              <!-- Removed visible start location input as requested, keeping structure clean -->
          </div>
      </div>

      <div class="row">
          <div class="col-md-6">
              <div class="form-group">
                  <label class="form-label-custom">Movement Type</label>
                  <select name="type" class="form-control form-control-custom">
                      <option value="inside_dhaka">Inside Dhaka</option>
                      <option value="outside_dhaka">Outside Dhaka</option>
                  </select>
              </div>
          </div>
          <div class="col-md-6">
              <div class="form-group">
                  <label class="form-label-custom">Purpose</label>
                  <input type="text" name="purpose" class="form-control form-control-custom" required placeholder="e.g. Client Visits, Training">
              </div>
          </div>
      </div>
      
      <!-- Hidden GPS Fields -->
      <input type="hidden" name="latitude" id="latitude">
      <input type="hidden" name="longitude" id="longitude">
      
      <div class="form-group mt-4 text-center">
          <button type="submit" name="submit" value="Start Movement" class="btn btn-submit-custom" id="submit_btn" disabled>
             <i class="fa fa-location-arrow"></i> START MOVEMENT
          </button>
          <div class="mt-3">
            <span id="gps_status" class="text-warning font-weight-bold"><i class="fa fa-satellite-dish fa-spin"></i> Locating GPS...</span>
          </div>
      </div>
  </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 300px; width: 100%; border-radius: 8px; margin-bottom: 20px; }
</style>

<div class="row">
    <div class="col-md-12">
        <div id="map"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize Map
    const map = L.map('map').setView([23.8103, 90.4125], 13); // Default Dhaka
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        alert("Geolocation is not supported by this browser.");
    }

    function showPosition(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        
        // Reverse Geocode
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('start_location').value = data.display_name;
                    document.getElementById('gps_status').innerHTML = '<i class="fa fa-check-circle"></i> Location Found: ' + data.display_name;
                } else {
                     document.getElementById('start_location').value = lat + ", " + lng;
                     document.getElementById('gps_status').innerHTML = '<i class="fa fa-check-circle"></i> Location Coordinates Found';
                }
                 // Enable button
                document.getElementById('submit_btn').disabled = false;
                document.getElementById('gps_status').className = "text-success font-weight-bold";
            })
            .catch(error => {
                console.error('Geocoding error:', error);
                document.getElementById('start_location').value = lat + ", " + lng;
                // Enable button anyway
                document.getElementById('submit_btn').disabled = false;
                document.getElementById('gps_status').innerHTML = '<i class="fa fa-check-circle"></i> Location Found (Address lookup failed)';
                document.getElementById('gps_status').className = "text-success";
            });
        
        // Update Map
        map.setView([lat, lng], 15);
        if(marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map).bindPopup("You are here").openPopup();
        }
    }

    function showError(error) {
        let msg = "";
        switch(error.code) {
            case error.PERMISSION_DENIED:
                msg = "User denied the request for Geolocation."
                break;
            case error.POSITION_UNAVAILABLE:
                msg = "Location information is unavailable."
                break;
            case error.TIMEOUT:
                msg = "The request to get user location timed out."
                break;
            case error.UNKNOWN_ERROR:
                msg = "An unknown error occurred."
                break;
        }
        document.getElementById('gps_status').innerText = msg;
        document.getElementById('gps_status').className = "text-danger";
    }
});
</script>
