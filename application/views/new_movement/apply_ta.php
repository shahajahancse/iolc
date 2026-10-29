<style>
    .ta-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        padding: 30px;
        border: none;
        margin-bottom: 30px;
    }
    .travel-row {
        background: #f8f9fe;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 5px solid #0177bc;
    }
    .travel-title {
        color: #32325d;
        font-weight: 700;
        margin-bottom: 15px;
    }
    .expense-row {
        background: #fff;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }
    .btn-add-exp {
        background: #000;
        color: white;
        border-radius: 20px;
        font-size: 12px;
        padding: 5px 15px;
        border: none;
    }
    .btn-submit-ta {
        background: #0177bc;
        color: white;
        padding: 15px 40px;
        border-radius: 30px;
        font-weight: bold;
        border: none;
        box-shadow: 0 4px 6px rgba(1, 119, 188, 0.3);
        width: 100%;
    }
    .btn-submit-ta:hover {
        background: #000;
        color: white;
    }
</style>

<div class="header-body mb-4">
    <h2 style="font-weight: 800; color: #32325d;">Apply Travel Allowance (TA)</h2>
    <p class="text-muted">Movement #<?php echo $movement->id; ?> | Date: <?php echo date('d M, Y', strtotime($movement->created_at)); ?></p>
</div>

<div class="row">
    <div class="col-md-12">
        <form action="<?php echo site_url('new_movement/save_ta'); ?>" method="post">
            <input type="hidden" name="movement_id" value="<?php echo $movement->id; ?>">
            
            <div class="ta-card">
                <?php if(empty($travels)): ?>
                    <p class="text-center text-muted">No travel records found for this movement.</p>
                <?php else: ?>
                    <?php foreach($travels as $travel): ?>
                        <div class="travel-row">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="travel-title">
                                        <i class="fa fa-car mr-2"></i> 
                                        <span class="location-text" data-lat="<?php echo $travel->start_lat; ?>" data-lng="<?php echo $travel->start_lng; ?>">
                                            <i class="fa fa-spinner fa-spin"></i> Loading...
                                        </span> 
                                        <i class="fa fa-long-arrow-right mx-2 text-muted"></i> 
                                        <?php if($travel->to_location): ?>
                                            <span class="location-text" data-lat="<?php echo $travel->end_lat; ?>" data-lng="<?php echo $travel->end_lng; ?>">
                                                <i class="fa fa-spinner fa-spin"></i> Loading...
                                            </span>
                                        <?php else: ?>
                                            <span class="text-warning">Running...</span>
                                        <?php endif; ?>
                                    </h5>
                                    <small class="text-muted">
                                        Time: <?php echo date('h:i A', strtotime($travel->start_time)); ?>
                                        | Distance: <strong><?php echo floatval($travel->distance_km) > 0 ? $travel->distance_km . ' km' : 'N/A'; ?></strong>
                                    </small>
                                </div>
                                <button type="button" class="btn-add-exp" onclick="addExpenseRow(<?php echo $travel->id; ?>)">
                                    <i class="fa fa-plus"></i> Add Vehicle
                                </button>
                            </div>
                            
                            <div id="expenses_container_<?php echo $travel->id; ?>">
                                <!-- Default one row -->
                                <div class="expense-row row">
                                    <div class="col-md-4">
                                        <select name="expenses[<?php echo $travel->id; ?>][0][type]" class="form-control form-control-sm">
                                            <option value="Bus">Bus</option>
                                            <option value="Rickshaw">Rickshaw</option>
                                            <option value="CNG">CNG</option>
                                            <option value="Uber/Pathao">Uber/Pathao</option>
                                            <option value="Train">Train</option>
                                            <option value="Boat">Boat</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" step="0.01" name="expenses[<?php echo $travel->id; ?>][0][amount]" class="form-control form-control-sm" placeholder="Amount (Tk)" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="expenses[<?php echo $travel->id; ?>][0][note]" class="form-control form-control-sm" placeholder="Remarks/Rate">
                                    </div>
                                    <div class="col-md-1 text-right">
                                        <button type="button" class="btn btn-sm btn-danger btn-circle" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn-submit-ta">
                        <i class="fa fa-check-circle"></i> Submit TA Claim
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function addExpenseRow(travelId) {
        const container = document.getElementById('expenses_container_' + travelId);
        const index = container.children.length;
        
        const html = `
            <div class="expense-row row">
                <div class="col-md-4">
                    <select name="expenses[${travelId}][${index}][type]" class="form-control form-control-sm">
                        <option value="Bus">Bus</option>
                        <option value="Rickshaw">Rickshaw</option>
                        <option value="CNG">CNG</option>
                        <option value="Uber/Pathao">Uber/Pathao</option>
                        <option value="Train">Train</option>
                        <option value="Boat">Boat</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" name="expenses[${travelId}][${index}][amount]" class="form-control form-control-sm" placeholder="Amount (Tk)" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="expenses[${travelId}][${index}][note]" class="form-control form-control-sm" placeholder="Remarks/Rate">
                </div>
                <div class="col-md-1 text-right">
                    <button type="button" class="btn btn-sm btn-danger btn-circle" onclick="removeRow(this)"><i class="fa fa-times"></i></button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }
    
    function removeRow(btn) {
        if(confirm('Remove this expense line?')) {
            btn.closest('.expense-row').remove();
        }
    }
</script>
<script>
    async function fetchAddresses() {
        const elements = document.querySelectorAll('.location-text');
        for (let el of elements) {
            const lat = el.getAttribute('data-lat');
            const lng = el.getAttribute('data-lng');
            
            if (lat && lng && lat != 0 && lng != 0) {
                try {
                    // Small delay to prevent rate limits or visual clash
                    await new Promise(r => setTimeout(r, 500)); 
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                    const data = await res.json();
                    if (data && data.display_name) {
                        const parts = data.display_name.split(',');
                        // Show first 2 parts (Area, Thana) + small 3rd part (District)
                        // User wanted concise: "Mollapara, Pirerbag, Dhaka"
                        // I will slice 0,2 and join.
                        el.innerHTML = `<i class="fa fa-map-marker text-danger"></i> ` + parts.slice(0, 3).join(',');
                    }
                } catch (e) { console.log("Geocode error"); }
            } else {
                 el.innerHTML = "Unknown Location";
            }
        }
    }
    // Call it
    fetchAddresses();
</script>
