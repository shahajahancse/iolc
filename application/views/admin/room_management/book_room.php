<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Book Meeting Room</h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo site_url('admin/room_management/approvals'); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Room List
                    </a>
                </div>
            </div>
            <div class="box-body">
                <form action="<?php echo site_url('admin/room_management/submit_booking'); ?>" method="post" id="bookingForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Room</label>
                                <select name="room_id" id="room_id" class="form-control" required>
                                    <option value="">-- Select Room --</option>
                                    <?php foreach($rooms as $room): ?>
                                        <option value="<?php echo $room->id; ?>"><?php echo $room->name; ?> (Cap: <?php echo $room->capacity; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="meeting_date" id="meeting_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div id="room-schedule-box" class="callout callout-info" style="display:none; margin-bottom: 15px;">
                                <h4>Booked Slots for Selected Date:</h4>
                                <ul id="schedule-list" style="padding-left: 20px;"></ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Start Time</label>
                                <input type="time" name="start_time" id="start_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                             <div class="form-group">
                                <label>End Time</label>
                                <input type="time" name="end_time" id="end_time" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Attendees (Expected)</label>
                        <select name="attendees[]" class="form-control select2" multiple="multiple" style="width: 100%;">
                            <?php foreach($all_employees as $emp): ?>
                                <option value="<?php echo $emp->user_id; ?>"><?php echo $emp->first_name . ' ' . $emp->last_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Message to HR (Optional)</label>
                        <textarea name="message" class="form-control" rows="2" placeholder="Any special requirements?"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Purpose</label>
                        <textarea name="purpose" class="form-control" rows="3" placeholder="e.g. Weekly Team Sync" required></textarea>
                    </div>

                    <!-- Availability Status Alert -->
                    <div id="availability-alert" class="alert d-none text-center"></div>

                    <div class="row">
                        <div class="col-md-6">
                             <button type="button" id="checkBtn" class="btn btn-info btn-block">
                                <i class="fa fa-search"></i> Check Availability
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" id="submitBtn" class="btn btn-success btn-block" disabled>
                                Confirm Booking
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select Attendees",
        allowClear: true,
        width: '100%'
    });

    // 1. Fetch Schedule on Room or Date Change
    $('#room_id, #meeting_date').change(function() {
        var room_id = $('#room_id').val();
        var date = $('#meeting_date').val();

        // Hide previous alerts if main params change
        $('#availability-alert').addClass('d-none');
        $('#submitBtn').prop('disabled', true);

        if(room_id && date) {
             $.ajax({
                url: '<?php echo site_url("admin/room_management/get_room_schedule"); ?>',
                type: 'POST',
                dataType: 'json',
                data: { room_id: room_id, date: date },
                success: function(data) {
                    var html = '';
                    if(data.length > 0) {
                        $.each(data, function(i, item){
                            var statusLabel = item.status === 'Approved' ? '<span class="label label-danger">Booked</span>' : '<span class="label label-warning">Pending</span>';
                            html += '<li>' + statusLabel + ' <strong>' + item.start + ' - ' + item.end + '</strong> (by ' + item.org + ')</li>';
                        });
                        $('#room-schedule-box').removeClass('callout-success').addClass('callout-warning');
                        $('#room-schedule-box h4').text('Occupied Slots on ' + date + ':');
                    } else {
                        html = '<li>No bookings for this day yet. Entire day is free!</li>';
                        $('#room-schedule-box').removeClass('callout-warning').addClass('callout-success');
                        $('#room-schedule-box h4').text('Status for ' + date + ':');
                    }
                    $('#schedule-list').html(html);
                    $('#room-schedule-box').slideDown();
                }
             });
        } else {
            $('#room-schedule-box').slideUp();
        }

        // Also trigger availability check if times are already set
        check_realtime_availability();
    });

    // 2. Realtime Check on Time Change
    $('#start_time, #end_time').on('change blur', function() {
         check_realtime_availability();
    });

    // Manual Check Button (Keep it or remove? User asked for realtime)
    // We can keep it but it basically calls the same function
    $('#checkBtn').click(function() {
        check_realtime_availability();
    });

    function check_realtime_availability() {
        var room_id = $('#room_id').val();
        var date = $('#meeting_date').val();
        var start = $('#start_time').val();
        var end = $('#end_time').val();

        if(!room_id || !date || !start || !end) return;

        $('#availability-alert').addClass('d-none').removeClass('alert-success alert-danger');
        $('#checkBtn').html('<i class="fa fa-spinner fa-spin"></i> Checking...');

        $.ajax({
            url: '<?php echo site_url("admin/room_management/check_availability"); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                room_id: room_id,
                date: date,
                start_time: start,
                end_time: end
            },
            success: function(response) {
                $('#checkBtn').html('<i class="fa fa-search"></i> Check Availability');
                $('#availability-alert').removeClass('d-none');

                if(response.status === true) {
                    $('#availability-alert').removeClass('alert-danger').addClass('alert-success').html('<strong><i class="fa fa-check-circle"></i> ' + response.message + '</strong>');
                    $('#submitBtn').prop('disabled', false);
                } else {
                    $('#availability-alert').removeClass('alert-success').addClass('alert-danger').html('<strong><i class="fa fa-times-circle"></i> ' + response.message + '</strong>');
                    $('#submitBtn').prop('disabled', true);
                }
            }
        });
    }
});
</script>
