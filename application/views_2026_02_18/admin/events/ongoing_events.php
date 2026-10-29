<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<div class="row m-b-1 <?php echo $get_animate;?>">
    <!-- ================= EVENT LIST ================= -->
    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"> Ongoing Events </h3>
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
