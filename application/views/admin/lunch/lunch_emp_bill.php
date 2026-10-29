<?php
    if (count($empdata)>0){
        $data1=$empdata[0];
        $taken_meal=0;
        $taken_amount=0;
        $first_date = date('Y-m-d', strtotime($data1->end_date . ' +1 day'));
        $second_date = date('Y-m-d', strtotime($data1->next_date . ' +1 day'));

        $this->load->model("Lunch_model");
        $emp_data = $this->Lunch_model->get_data_date_wise($first_date, $second_date, $data1->emp_id);

        foreach ($emp_data['emp_data'] as $r) {
            $lunch_package=lunch_package($r->date);
            $taken_meal+=$r->meal_amount;
            $taken_amount+=$r->meal_amount*$lunch_package->stuf_give_tk;
        }
        $paymeal=$data1->probable_meal;
        $balanceMeal= $paymeal-$taken_meal;
    }
?>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&amp;display=swap">
<link rel="stylesheet" href="<?= base_url('skin/hrsale_assets/css/lunch_emp_bill.css') ?>">
<style>
    .modal-dialog {
    width: 600px;
    margin: 95px auto;
}
</style>
<div class="monthname">
    <?php if (count($empdata)>0){ ?>
    <?= date('d-M-Y', strtotime($first_date)) ?> to <?= date('d-M-Y', strtotime($second_date)) ?> Lunch Bill Summery
    <?php } ?>
</div>



<div class="modal fade" id="jobcardinput" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Job Card Form</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="">Start Date</label>
                        <input type="Date" class="form-control" id="emp_j_from_date">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">To Date</label>
                        <input type="Date" class="form-control" id="emp_j_to_date">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="get_job_card()" class="btn btn-primary">Get</button>

            </div>
        </div>

    </div>
</div>
<div class="divrow col-md-12">
    <div class="divstats-info col-md-3" style="background-color: #d1ecf1;">
        <div class="heading">Total Lunch</div>
        <div class="heading2"><?= (isset($data1->pay_amount))? $data1->probable_meal:'0' ?></div>
    </div>
    <div class="divstats-info col-md-3" style="background-color: #F1CFEE;">
        <div class="heading">Total Payment</div>
        <div class="heading2"><?= (isset($paymeal))? $data1->pay_amount:'0' ?></div>
    </div>
    <div class="divstats-info col-md-3" style="background-color: #E5E5E5;">
        <div class="heading">Taken Lunch</div>
        <div class="heading2"><?= (isset($paymeal))? $taken_meal:'0'?></div>
    </div>
    <div class="divstats-info col-md-3" style="background-color: #D2F9EE;">
        <div class="heading">Taken Amount</div>
        <div class="heading2"><?= (isset($paymeal))? $taken_amount :'0'?></div>
    </div>
</div>

<div class="titel_sec_head col-md-3">
    <div>Lunch Bill Information</div>

</div>
<div class="titel_sec_head2 col-md-6">
    <input type="month" class="datesec" id="monthYearInput" value="<?= date('Y-m')?>">
    <a class="btn btns" data-toggle="modal" data-target="#today_lunch_off">Lunch Off/On</a>
    <a class="btn btns" data-toggle="modal" data-target="#jobcardinput">
        <div class="input serceb">
            <span class="label label-danger" style="animation: pulse 1s infinite;">New</span>
            Get Lunch Card
        </div>
    </a>
</div>

<div class="modal fade" id="today_lunch_off" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Lunch Off Form</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="">Date</label>
                        <input type="date" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" class="form-control" id="p_date">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="">Off / On</label>
                        <select class="form-control" id="lunch_status" onchange="lunch_status_cn(this.value)">
                            <option value="">Select one</option>
                            <option value="1">Off</option>
                            <option value="2">On</option>
                        </select>
                    </div>
                </div>
                <div class="row" id="extra_lunch">
                    <div class="form-group col-md-6">
                        <label for="">Extra Lunch</label> <br>
                        <label class="switch">
                            <input type="checkbox" id="extra_check" checked onchange="onOffLunch()">
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div class="form-group col-md-6" id="extra_field">
                        <label for="">Extra Quantity</label>
                        <input type="number" class="form-control" id="extra_quantity">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" onclick="today_lunch_off()" data-dismiss="modal" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 80px;
        /* height: 19px; */
        margin: 0px 1px;
    }

    #extra_lunch {
        display: none;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #3C3C3C;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        /* height: 13px; */
        /* width: 13px; */
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #0E6EB8;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(47px);
    }

    /*------ ADDED CSS ---------*/
    .slider:after {
        content: 'No';
        color: white;
        display: block;
        position: absolute;
        transform: translate(-50%, -50%);
        top: 50%;
        left: 50%;
        font-size: 10px;
        font-family: Verdana, sans-serif;
    }

    input:checked+.slider:after {
        content: 'Yes';
        right: 15px;
    }
</style>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>SN</th>
                <th>From Date</th>
                <th>End Date</th>
                <th>Previous Meal</th>
                <th>Previous Cost</th>
                <th>Previous Pay</th>
                <th>Previous Balance</th>
                <th>From date</th>
                <th>Next probable date Date</th>
                <th>Probable Meal</th>
                <th>Pay Amount</th>
                <th>Collection Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($empdataall as $key=>$value){ ?>
            <tr>
                <td><?= $key+1 ?></td>
                <td><?= $value->from_date ?></td>
                <td><?= $value->end_date ?></td>
                <td><?= $value->prev_meal ?></td>
                <td><?= $value->prev_cost ?></td>
                <td><?= $value->prev_pay ?></td>
                <td><?= $value->prev_amount ?></td>
                <td><?= date('Y-m-d', strtotime($value->end_date . ' +1 day')) ?></td>
                <td><?= $value->next_date ?></td>
                <td><?= $value->probable_meal ?></td>
                <td><?= $value->pay_amount ?></td>
                <td><?= $value->collection_amount ?></td>
            </tr>
            <?php } ?>

        </tbody>
    </table>
</div>

<script>
    function lunch_status_cn(val) {
        if (val == '1') {
            document.getElementById('extra_lunch').style.display = 'none';
            document.getElementById('extra_check').checked = false;
            document.getElementById('extra_quantity').value = '';
            document.getElementById('extra_field').style.display = 'none';
        } else {
            document.getElementById('extra_lunch').style.display = 'block';
        }
    }

    function onOffLunch() {
        var extra_check = document.getElementById('extra_check').checked;
        if (extra_check) {
            document.getElementById('extra_field').style.display = 'block';
        } else {
            document.getElementById('extra_quantity').value = '';
            document.getElementById('extra_field').style.display = 'none';
        }
    }

    function today_lunch_off() {
        var ajaxRequest; // The variable that makes Ajax possible!
        ajaxRequest = new XMLHttpRequest();

        p_date = document.getElementById('p_date').value;
        if (p_date == '') {
            alert('Please select date');
            return;
        }

        lunch_status = document.getElementById('lunch_status').value;
        if (lunch_status == '') {
            alert('Please select lunch status');
            return;
        }

        extra_quantity = document.getElementById('extra_quantity').value;
        extra_check = document.getElementById('extra_check').checked;
        if (extra_check && extra_quantity == '') {
            alert('Please enter extra quantity');
            return;
        }
        var data = "p_date=" + p_date + '&lunch_status=' + lunch_status + '&extra_check=' + extra_check + '&extra_quantity=' + extra_quantity;
        url = '<?php echo base_url('admin/lunch/today_lunch_off'); ?>';
        ajaxRequest.open("POST", url, true);
        ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
        ajaxRequest.send(data);
        ajaxRequest.onreadystatechange = function() {
            if (ajaxRequest.readyState == 4) {
                alert(ajaxRequest.responseText);
            }
        }
    }
</script>

<script>
    function get_job_card() {
        var ajaxRequest; // The variable that makes Ajax possible!
        ajaxRequest = new XMLHttpRequest();

        first_date = document.getElementById('emp_j_from_date').value;
        second_date = document.getElementById('emp_j_to_date').value;

        if (first_date == '') {
            alert('Please select first date');
            return;
        }
        if (second_date == '') {
            alert('Please select second date');
            return;
        }

        var data = "first_date=" + first_date + '&second_date=' + second_date;

        url = '<?php echo base_url('admin/lunch/get_job_card_emp'); ?>';
        ajaxRequest.open("POST", url, true);
        ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
        ajaxRequest.send(data);
        // alert(url); return;

        ajaxRequest.onreadystatechange = function() {
            if (ajaxRequest.readyState == 4) {
                // console.log(ajaxRequest.responseText); return;
                var resp = ajaxRequest.responseText;
                a = window.open('', '_blank', 'menubar=1,resizable=1,scrollbars=1,width=1600,height=800');
                a.document.write(resp);
                // a.close();
            }
        }
    }
</script>
