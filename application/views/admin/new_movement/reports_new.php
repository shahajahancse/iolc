<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<style>
    #loading {
        visibility: hidden; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999;
        background-color: rgba(255, 255, 255, 0.8);
    }
    #loading img {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    }
</style>

<div id="loading">
    <img src="<?php echo base_url()?>skin/hrsale_assets/img/loding.gif">
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="box mb-4 <?php echo $get_animate;?>">
            <div class="box-header with-border">
                <h3 class="box-title">Movement Reports Filters</h3>
            </div>
            <div class="box-body">
                <?php $this->load->view('admin/move_filter'); ?>
            </div>
        </div>

        <div class="box <?php echo $get_animate;?>">
            <div class="box-header with-border">
                <h3 class="box-title">Movement Report</h3>
                <div class="box-tools pull-right">
                     <!-- Print removed from here as it opens new window now -->
                </div>
            </div>
            <div class="box-body">
                <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                    <li class="active"><a href="#continuously" data-toggle="tab">Continuously</a></li>
                    <li><a href="#daily" data-toggle="tab">Daily</a></li>
                    <!-- <li><a href="#monthly" data-toggle="tab">Monthly</a></li> -->
                </ul>

                <div class="tab-content" style="margin-top: 20px;">
                    <div class="tab-pane" id="daily">
                        <button class="btn btn-sm btn-primary mr-2" onclick="generate_report('movement', 'daily')">Movement Report</button>
                        <button class="btn btn-sm btn-info mr-2" onclick="generate_report('ta', 'daily')">TA Report</button>
                    </div>
                    <!-- <div class="tab-pane" id="monthly">
                        <button class="btn btn-sm btn-primary mr-2" onclick="generate_report('movement', 'monthly')">Movement Report</button>
                        <button class="btn btn-sm btn-info mr-2" onclick="generate_report('ta', 'monthly')">TA Report</button>
                    </div> -->
                    <div class="tab-pane active" id="continuously">
                        <button class="btn btn-sm btn-primary mr-2" onclick="generate_report('movement', 'continuous')">Movement Report</button>
                        <button class="btn btn-sm btn-info mr-2" onclick="generate_report('ta', 'continuous')">TA Report</button>
                        <button class="btn btn-sm btn-primary mr-2" onclick="ta_summary_report()">TA Summary Report</button>
                        <button class="btn btn-sm btn-warning mr-2" onclick="generate_report('card', 'continuous')">Movement Card</button>
                    </div>
                </div>

                <hr>

                <div id="report_container"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <?php $this->load->view('admin/filtered_data'); ?>
    </div>
</div>

<script>
    function ta_summary_report() {
        var start_date = $('#process_date').val();
        var end_date = $('#second_date').val();
        var ta_status = $('#ta_status').val();

        if(start_date == '') {
            alert('Please select First Date');
            return;
        }
        if(end_date == '') {
            alert('Please select Second Date');
            return;
        }

        // Get Selected Employees
        var emp_ids = [];
        $('input[name="select_emp_id[]"]:checked').each(function() {
            emp_ids.push($(this).val());
        });

        $('#loading').css('visibility', 'visible');

        $.ajax({
            url: '<?php echo site_url("new_movement/ta_summary_report"); ?>',
            type: 'POST',
            data: {
                start_date: start_date,
                end_date: end_date,
                ta_status: ta_status,
                emp_ids: emp_ids.join(',')
            },
            success: function(response) {
                $('#loading').css('visibility', 'hidden');
                var a = window.open('', '_blank', 'menubar=1,resizable=1,scrollbars=1,width=1200,height=800');
                a.document.write(response);
                a.document.close();
            },
            error: function() {
                $('#loading').css('visibility', 'hidden');
                alert('Error generating report');
            }
        });
    }
</script>

<script>
    function generate_report(type, period) {
        if(!type) type = 'movement';
        var start_date_val = $('#process_date').val();
        var end_date_val = $('#second_date').val();
        var ta_status = $('#ta_status').val();
        var start_date = start_date_val;
        var end_date = end_date_val;

        if(start_date == '') {
            alert('Please select First Date');
            return;
        }

        // Logic per period
        if(period == 'daily') {
            end_date = start_date;
        } else if(period == 'monthly') {
            var d = new Date(start_date);
            var year = d.getFullYear();
            var month = d.getMonth();
            var firstDay = new Date(year, month, 1);
            var lastDay = new Date(year, month + 1, 0);

            // Format Y-m-d
            var mm1 = firstDay.getMonth() + 1;
            var dd1 = firstDay.getDate();
            start_date = firstDay.getFullYear() + '-' + (mm1>9?mm1:'0'+mm1) + '-' + (dd1>9?dd1:'0'+dd1);

            var mm2 = lastDay.getMonth() + 1;
            var dd2 = lastDay.getDate();
            end_date = lastDay.getFullYear() + '-' + (mm2>9?mm2:'0'+mm2) + '-' + (dd2>9?dd2:'0'+dd2);

        } else if(period == 'continuous') {
            if(end_date == '') {
                alert('Please select Second Date');
                return;
            }
        }

        // Get Selected Employees
        var emp_ids = [];
        $('input[name="select_emp_id[]"]:checked').each(function() {
            emp_ids.push($(this).val());
        });

        $('#loading').css('visibility', 'visible');

        $.ajax({
            url: '<?php echo site_url("new_movement/generate_custom_report"); ?>',
            type: 'POST',
            data: {
                start_date: start_date,
                end_date: end_date,
                emp_ids: emp_ids.join(','),
                report_type: type,
                ta_status: ta_status
            },
            success: function(response) {
                $('#loading').css('visibility', 'hidden');
                var a = window.open('', '_blank', 'menubar=1,resizable=1,scrollbars=1,width=1200,height=800');
                a.document.write(response);
                a.document.close();
            },
            error: function() {
                $('#loading').css('visibility', 'hidden');
                alert('Error generating report');
            }
        });
    }

    $(document).ready(function(){
        $("#select_all").click(function(){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    });
</script>
