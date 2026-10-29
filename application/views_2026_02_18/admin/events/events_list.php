<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<div class="row m-b-1 <?php echo $get_animate;?>">
    <!-- ================= ACCORDION : ADD NEW EVENT ================= -->
    <div class="col-md-12">
        <div class="box box-primary">

            <!-- Accordion Header -->
            <div class="box-header with-border" style="cursor:pointer" data-toggle="collapse" data-target="#addEventAccordion" aria-expanded="false">
                <h3 class="box-title">
                    <i class="fa fa-plus-circle"></i>
                    <?php echo $this->lang->line('xin_add_new'); ?>
                    <?php echo $this->lang->line('xin_hr_event'); ?>
                </h3>
                <div class="box-tools pull-right">
                    <!-- <i class="fa fa-chevron-down"></i> -->
                    <button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> Add New</button>
                </div>
            </div>

            <!-- Accordion Body -->
            <div id="addEventAccordion" class="collapse">
                <div class="box-body">

                    <?php
                    $attributes = array('name' => 'add_event', 'autocomplete' => 'off');
                    $hidden = array('user_id' => $session['user_id']);
                    echo form_open('admin/events/add_event', $attributes, $hidden);
                    ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('xin_hr_event_title'); ?> <i class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                <input type="text" class="form-control" name="event_title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Event Location</label>
                                <input type="text" class="form-control" name="location" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Notification</label>
                                <select class="form-control" name="notification">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input class="form-control date" name="start_event_date" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Time</label>
                                <input class="form-control" type="time" step="60" name="start_event_time" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date</label>
                                <input class="form-control date" name="end_event_date" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Time</label>
                                <input class="form-control" type="time" step="60" name="end_event_time" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Event Description</label>
                                <textarea class="form-control textarea" name="event_note" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Event Remarks</label>
                                <textarea class="form-control textarea" name="event_remark"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Participant Type</label>
                                <select class="form-control" name="participant_type" required onchange="ptype(this.value)" >
                                    <option value=""> Select Participant Type </option>
                                    <option value="1">All</option>
                                    <option value="2">Department</option>
                                    <option value="3">Individual</option>
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
                            <i class="fa fa-check-square-o"></i>
                            <?php echo $this->lang->line('xin_save'); ?>
                        </button>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= EVENT LIST ================= -->
    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"> <?php echo $this->lang->line('xin_list_all');?>
                    <?php echo $this->lang->line('xin_hr_events');?> </h3>
            </div>
            <div class="box-body">
                <div class="box-datatable table-responsive" style="height: 45vh;">
                    <table class="datatables-demo table table-striped table-bordered" id="dtable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Start Date </th>
                                <th>End Date</th>
                                <th>Duration</th>
                                <th>Details</th>
                                <th>Place Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($allevent as $kay=>$data){
                                $enddatetime=date('d-M-Y H:i:s' , strtotime($data->end_event_date.' '. $data->end_event_time))
                            ?>
                            <tr>
                                <td><?= $kay+1 ?></td>
                                <td><?= date('d-M-Y H:i:s' , strtotime($data->start_event_date.' '. $data->start_event_time))?>
                                </td>
                                <td><?= date('d-M-Y H:i:s' , strtotime($data->end_event_date.' '. $data->end_event_time))?>
                                </td>
                                <td><?= $data->event_duration ?></td>
                                <td><?= $data->event_note ?></td>
                                <td><?= $data->location ?></td>
                                <td><?= ($enddatetime > date('d-M-Y H:i:s'))? 'Pending': 'Complete' ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> Actions <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="<?= base_url('admin/events/event_details/').$data->event_id ?>">
                                                    Details
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('admin/events/edit_event/').$data->event_id ?>">
                                                    Edit
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
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
