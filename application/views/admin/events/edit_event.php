<?php $session = $this->session->userdata('username'); ?>
<?php $get_animate = $this->Xin_model->get_content_animate(); ?>

<div class="row m-b-1 <?php echo $get_animate; ?>">
    <div class="col-md-12">
        <div class="box box-primary">

            <!-- Box Header -->
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-edit"></i>
                    Edit Event
                </h3>

                <div class="box-tools pull-right">
                    <a href="<?= base_url('admin/events'); ?>" class="btn btn-sm btn-primary">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>


            <!-- Box Body -->
            <div class="box-body">

                <?php
                $attributes = array('name' => 'edit_event', 'autocomplete' => 'off');
                $hidden = array(
                    'user_id'  => $session['user_id'],
                    'event_id' => $event->event_id // edit হলে event_id দরকার
                );
                echo form_open('admin/events/update_event', $attributes, $hidden);
                ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Event Title <span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="event_title"
                                   value="<?php echo $event->event_title; ?>" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Event Location</label>
                            <input type="text" class="form-control" name="location"
                                   value="<?php echo $event->location; ?>" required>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Notification</label>
                            <select class="form-control" name="notification">
                                <option value="0" <?php if($event->notification==0) echo 'selected'; ?>>No</option>
                                <option value="1" <?php if($event->notification==1) echo 'selected'; ?>>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input class="form-control date" name="start_event_date"
                                   value="<?php echo $event->start_event_date; ?>" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Start Time</label>
                            <input class="form-control" type="time" step="60"
                                   name="start_event_time"
                                   value="<?php echo $event->start_event_time; ?>" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>End Date</label>
                            <input class="form-control date" name="end_event_date"
                                   value="<?php echo $event->end_event_date; ?>" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>End Time</label>
                            <input class="form-control" type="time" step="60"
                                   name="end_event_time"
                                   value="<?php echo $event->end_event_time; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Event Description</label>
                    <textarea class="form-control textarea" name="event_note" required><?php echo $event->event_note; ?></textarea>
                </div>

                <div class="form-group">
                    <label>Event Remarks</label>
                    <textarea class="form-control textarea" name="event_remark"><?php echo $event->event_remark; ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Participant Type</label>
                            <select class="form-control" name="participant_type" required onchange="ptype(this.value)">
                                <option value=""> Select Participant Type </option>
                                <option value="1" <?php if($event->participant_type==1) echo 'selected'; ?>>All</option>
                                <option value="2" <?php if($event->participant_type==2) echo 'selected'; ?>>Department</option>
                                <option value="3" <?php if($event->participant_type==3) echo 'selected'; ?>>Individual</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-9" id="participant_list" style="display:none;">
                        <div class="form-group">
                            <label>Participants</label>
                            <select class="form-control"  name="participants[]" id="participants" multiple data-plugin="select_hrm" >
                                <!-- options will be appended dynamically -->
                            </select>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update Event
                    </button>
                    <a href="<?= base_url('admin/events'); ?>" class="btn btn-default">Cancel</a>
                </div>

                <?php echo form_close(); ?>

            </div>
        </div>
    </div>
</div>


<script>
    function ptype(val) {
        const list = document.getElementById('participant_list');
        const select = document.getElementById('participants');

        select.innerHTML = ''; // clear options

        if (val === '1') {
            list.style.display = 'none';
        }
        else if (val === '2') {
            list.style.display = 'block';
            get_department();
        }
        else if (val === '3') {
            list.style.display = 'block';
            get_participants();
        }
    }
</script>

<script>
    function get_department() {
        const selected = <?php echo json_encode($selected_participants ? $selected_participants : []); ?>;
        const xhr = new XMLHttpRequest();
        const url = "<?php echo base_url('admin/events/get_department'); ?>";

        xhr.open("GET", url, true);
        xhr.setRequestHeader("Accept", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const res = JSON.parse(xhr.responseText);
                const data = res.data ?? res;
                const select = document.getElementById('participants');
                select.innerHTML = '';
                data.forEach(dep => {
                    const opt = document.createElement('option');
                    opt.value = dep.department_id;
                    opt.textContent = dep.department_name;
                    //  auto select for edit
                    if (selected.includes(String(dep.department_id))) {
                        opt.selected = true;
                    }
                    select.appendChild(opt);
                });
                refreshSelectHRM();
            }
        };
        xhr.send();
    }
</script>

<script>
    function get_participants() {
        // 🔹 previously selected participants (from controller)
        const selected = <?php echo json_encode($selected_participants ? $selected_participants : []); ?>;
        const xhr = new XMLHttpRequest();
        const url = "<?php echo base_url('admin/events/get_participants'); ?>";

        xhr.open("GET", url, true);
        xhr.setRequestHeader("Accept", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const res = JSON.parse(xhr.responseText);
                const data = res.data ?? res;
                const select = document.getElementById('participants');

                select.innerHTML = '';
                data.forEach(emp => {
                    const opt = document.createElement('option');
                    opt.value = emp.user_id;
                    opt.textContent = emp.first_name + ' ' + emp.last_name;
                    // 🔹 auto select for edit
                    if (selected.includes(String(emp.user_id))) {
                        opt.selected = true;
                    }
                    select.appendChild(opt);
                });
                refreshSelectHRM();
            }
        };
        xhr.send();
    }
</script>


<script>
    function refreshSelectHRM() {
        if (window.jQuery && $('#participants').data('select2')) {
            $('#participants').trigger('change.select2');
        } else if (window.jQuery) {
            $('#participants').select2({
                width: '100%'
            });
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ptypeVal = "<?php echo $event->participant_type; ?>";
        if (ptypeVal) {
            ptype(ptypeVal);
        }
    });
</script>
