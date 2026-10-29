<?php
$session = $this->session->userdata('username');
$user_data = $this->Xin_model->read_user_info($session['user_id']);
$user_data = $user_data[0];


$get_animate = $this->Xin_model->get_content_animate();
$userid = $session['user_id'];
/*
$result = $this->db->order_by('id', 'desc')->get('lunch_payment', 1)->row();

$empdata = $this->db
    ->select('lunch_payment.*, xin_employees.first_name, xin_employees.last_name,
                  xin_designations.designation_name')
    ->from('lunch_payment')
    ->join('xin_employees', 'lunch_payment.emp_id = xin_employees.user_id')
    ->join('xin_designations', 'xin_employees.designation_id = xin_designations.designation_id')
    ->where('lunch_payment.emp_id', $session['user_id'])
    ->order_by('lunch_payment.id', 'desc')
    ->get()
    ->result();

$data1 = isset($empdata[0]) ? $empdata[0] : 0;
$taken_meal = 0;
$paymeal = 0;
$balanceMeal = 0;

if (!empty($data1)) {
    $this->load->model("Lunch_model");
    $emp_data = $this->Lunch_model->get_data_date_wise($data1->end_date, $data1->next_date, $data1->emp_id);

    foreach ($emp_data['emp_data'] as $r) {
        $taken_meal += $r->meal_amount;
    }

    $paymeal = isset($data1->pay_amount) ? $data1->pay_amount / 45 : 0;
    $balanceMeal = $paymeal - $taken_meal;
}



// get 12 month to current month
$i = date("Y-m", strtotime("-11 months"));
$monthNames = array();
while ($i <= date("Y-m")) {
    $monthNames[] = date('F', strtotime($i));
    $i = date('Y-m', strtotime('+1 months' . $i));
}

// end

// get employee shift schedule information
$schedule = $this->db->get('xin_office_shift')->row();

// get employee salary from january to current month
$salarys = $this->db->select('salary_month,modify_salary,grand_net_salary,advanced_salary')
    ->where('employee_id', $userid)
    ->limit(12)
    ->order_by('payslip_id', 'desc')
    ->get('xin_salary_payslips')
    ->result();
$salarys = array_reverse($salarys);
$salary = array();
$salary_month = array();
foreach ($salarys as $salaryObj) {
    $salary[] = floor($salaryObj->grand_net_salary + $salaryObj->modify_salary + $salaryObj->advanced_salary);
    $salary_month[] = date('F', strtotime($salaryObj->salary_month));
}
// dd($salary_month);
// end
// dd($salary_month);
// punch time
$in_time = "00:00";
$out_time = "00:00";
$punch_time = $this->db->select('clock_in, clock_out,lunch_in,lunch_out')
    ->where('employee_id', $userid)
    ->where('attendance_date', date('Y-m-d'))
    ->get('xin_attendance_time')
    ->row();
// dd($punch_time);
if (!empty($punch_time)) {
    $in_time = date('h.i A', strtotime($punch_time->clock_in));
    $out_time = date('h.i A', strtotime($punch_time->clock_out));
}
// end punch time
*/

//get employee name
$name = $this->db->select('first_name,last_name')
    ->where('user_id', $userid)
    ->get('xin_employees')
    ->row();
// dd($in_time);

/*
// Get the current Unix timestamp
$currentTimestamp = time();

// Specify the other specific time
$inTime = strtotime($in_time);
$outTime = strtotime($out_time);
$out_Time = strtotime(date('h.i A', strtotime('6.30')));

// dd($outTime);

// Calculate the time difference in seconds
$workingHour = $currentTimestamp - $inTime;
$over_time = $outTime - $out_Time;
// dd($over_time);

$hours = floor($workingHour / 3600);
$minutes = floor(($workingHour % 3600) / 60);


$hourss = floor($over_time / 3600);
$minutess = floor(($over_time % 3600) / 60);
// $seconds = $workingHour % 60;

// Output the time difference
$total_working_hour = $hours . "." . $minutes;
$total_over_time_hour = $hourss . "." . $minutess;
$first_date = date('Y-m-01');
$second_date = date('Y-m-d');

$count_p = $this->db->select('COUNT(status) AS count_p')
    ->where('employee_id', $userid)
    ->where('status', 'present')
    ->where("attendance_date BETWEEN '" . $first_date . "' AND '" . $second_date . "'")
    ->get('xin_attendance_time')
    ->row()->count_p;
$count_a = $this->db->select('COUNT(status) AS count_a')
    ->where('employee_id', $userid)
    ->where('status', 'absent')
    ->where("attendance_date BETWEEN '" . $first_date . "' AND '" . $second_date . "'")
    ->get('xin_attendance_time')
    ->row()->count_a;
$count_late = $this->db->select('COUNT(status) AS count_late')
    ->where('employee_id', $userid)
    ->where('late_status', '1')
    ->where("attendance_date BETWEEN '" . $first_date . "' AND '" . $second_date . "'")
    ->get('xin_attendance_time')
    ->row()->count_late;


$holidays = $this->db->select('*')->get('xin_holidays')->result();
$holidayss = $this->db->select('*')->limit(5)->where("start_date > '" . date('Y-m-d') . "'")->get('xin_holidays')->result();
// dd($this->db->last_query());
$leave_calel = get_cal_leave($userid, 1);
$leave_calsl = get_cal_leave($userid, 2);
$totaluseleave = $leave_calel + $leave_calsl;
$all_notice = $this->db->select('*')->get('xin_events')->result();

$leavemonth = $this->Salary_model->leave_count_status($userid, date('Y-m-01'), date('Y-m-t'), 2);
$totalleavem = $leavemonth->el + $leavemonth->sl;
*/
?>



<style>
    .layout {
        background: var(--bg-2, #F8FBFF);
        margin-left: -56px;
        margin-top: -17px;
        padding-left: 45px;
        padding-top: 22px;
        border-radius: 0px;
    }

    .equal-height-row {
        display: flex;
        flex-wrap: wrap;
        /* margin-top:10px; */
    }

    .equal-height-row [class^="col-"] {
        display: flex;
        flex-direction: column;
    }

    .equal-height-row [class^="col-"]>* {
        flex-grow: 1;
    }

    .col_style {
        padding-right: 5px;
        padding-left: 10px;
    }

    .flex-container {
        display: flex;
    }

    .flex-item {
        flex: 1;
        /* padding-left: 10px; */
    }

    .card_layout {
        display: flex;
        padding: 10px 5px 10px 10px;
        flex-direction: column;
        align-items: flex-start;
        gap: -1px;
        border-radius: 0px;
        border: 1px solid #E3E3E3;
        background: #F9F9F9;
    }

    .containers {
        display: flex;
        justify-content: center;
        align-items: center;
        /* height: 100vh; */
    }

    .circle {
        width: 120px;
        height: 120px;
        background-color: #f1f1f1;
        border-radius: 50%;
        position: relative;
        border: 5px solid var(--b, #599AE7);
    }

    .text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;


        color: #1F1F1F;
        /* font-family: Roboto; */
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 27px;
    }


    .stats-box {
        display: flex;
        padding: 30px 0px;
        flex-direction: column;
        align-items: center;
        gap: -1px;
        border: 1px solid #E3E3E3;
        background: #F9F9F9;
        border-radius: 5px;
    }

    .stats-box-row {
        display: flex;
        padding: 0px 12px;
        align-items: flex-start;
        gap: 24px;
        margin-top: 14px;
        margin-bottom: 16px;
    }

    hr {
        border: none;
        height: 2px;
        background-color: #F3F3F3;
        margin: 8px 0;
        width: 94%;
        margin-left: 10px;
    }

    .text_s {
        color: #5442A8;
        text-align: center;
        /* font-family: Roboto; */
        font-size: 12px;
        font-style: normal;
        font-weight: 500;
        line-height: 18px;
        letter-spacing: -0.28px;
    }

    /* progress bar style */
    .step-wizard {
        /*background-color: #21d4fd;
        background-image: linear-gradient(19deg, #21d4fd 0%, #b721ff 100%);
        /*height: 100vh;
        width: 100%;
        display: flex;*/
        justify-content: center;
        align-items: center;
    }

    .step-wizard-list {
        /* background: #fff; */
        /* box-shadow: 0 15px 25px rgba(0,0,0,0.1); */
        color: #333;
        list-style-type: none;
        /* border-radius: 10px; */
        display: flex;
        padding: 0px 0px;
        position: relative;
        z-index: 10;
    }

    .step-wizard-item {
        padding: 0 0px;
        flex-basis: 0;
        -webkit-box-flex: 1;
        -ms-flex-positive: 1;
        flex-grow: 1;
        max-width: 100%;
        display: flex;
        flex-direction: column;
        text-align: center;
        min-width: 162px;
        position: relative;
    }

    .step-wizard-item+.step-wizard-item:after {
        content: "";
        position: absolute;
        left: -20%;
        top: 50px;
        background: #21d4fd;
        width: 100%;
        height: 2px;
        transform: translateX(-50%);
        z-index: -10;
    }

    .step-wizard-item+.step-wizard-item:before {
        content: "";
        position: absolute;
        right: -20%;
        top: 50px;
        background: #21d4fd;
        width: 100%;
        height: 2px;
        transform: translateX(-50%);
        z-index: -10;
    }

    .progress-label-top {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .progress-count {
        height: 40px;
        width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
        margin: 0 auto;
        position: relative;
        z-index: 10;
        color: transparent;
    }

    .progress-count:after {
        content: "";
        height: 40px;
        width: 40px;
        background: #21d4fd;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        z-index: -10;
    }

    .progress-count:before {
        content: "";
        height: 10px;
        width: 20px;
        border-left: 3px solid #fff;
        border-bottom: 3px solid #fff;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -60%) rotate(-45deg);
        transform-origin: center center;
    }

    .progress-label {
        font-size: 14px;
        font-weight: 600;
        margin-top: 10px;
    }

    .current-item .progress-count:before,
    .current-item~.step-wizard-item .progress-count:before {
        display: none;
    }

    .current-item~.step-wizard-item .progress-count:after {
        height: 10px;
        width: 10px;
    }

    .current-item~.step-wizard-item .progress-label {
        opacity: 0.5;
    }

    .current-item .progress-count:after {
        background: #fff;
        /*      border: 2px solid #21d4fd;*/
    }

    .current-item .progress-count {
        color: #21d4fd;
    }

    /* progress bar style */
    .sticky-heading {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: #f8f8f8;
        padding: 10px 0;
    }

    @media screen and (max-width: 1234px) {
        .equal-height-row {
            flex-direction: column;
        }

        .todayac {
            margin-left: 0 !important;
            margin: 1px 2px 23px 1px;
        }

        .timeac {
            width: 314px !important;
        }

        .col_style {
            width: 606px
        }
    }

    @media screen and (max-width: 992px) {
        .widght {
            width: 47% !important;
        }
    }

    @media screen and (max-width: 595px) {
        .widght {
            width: 100% !important;
        }

        .timeshet {}
    }
</style>

<div class=" <?php echo $get_animate; ?>">
    <!-- breadcrumb section -->
    <div class="box-widget widget-user-2 container">
        <!-- Add the bg col-md-3or to the header using any of the bg-* classes  -->
        <div class="widget-user-header layout">
            <h4 class="widget-user-username welcome-hrsale-user" style="margin-top:5px;">
                Welcome back, <span
                    style="color:#599AE7 "><?php echo $name->first_name . ' ' . $name->last_name ?>!</span>
            </h4>

            <?php /*
            <div class="col-md-4">
                <div class="right-group">
                    <div class="gp-1">
                        <a href="<?php echo base_url() ?>uploads/apk/Sheba_ERP_live_app.apk"
                            download="Sheba_ERP_live_app.apk" class="btn"
                            style="background-color: #0177bc; color: #fff;">
                            <img style="max-width: 20px;" src="<?php echo base_url() ?>uploads/apk/apk_logo.png">
                            Download Apk
                        </a>

                    </div>
                </div>
            </div>
            */ ?>

            <div class="breadcrumbs-hr-top">
                <div class="breadcrumb-wrapper col-xs-12">
                    <ol class="breadcrumb" style="margin-bottom: 10px; margin-left: -25px; margin-top: -5px;">
                        <li class="breadcrumb-item"><a
                                href="<?php echo site_url('admin/dashboard/'); ?>"><?php echo $this->lang->line('xin_e_details_home'); ?></a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <?php /*
    <!-- Today attendance info -->
    <div class="row equal-height-row " style="margin-top:10px">
        <div class="col-sm-4 col_style">
            <div class="card timeac" style="width: 250px;">
                <div class="card-body flex-container" style="margin-top: -8px;margin-bottom: -20px;">
                    <h5 class="card-title flex-item " style="font-weight: 600;">Timesheet</h5>
                    <h6 class="card-title flex-item " style="margin-left: 83px;"><?php echo date('d M Y') ?></h6>
                </div>
                <div class="card-body">
                    <div
                        style="border-radius: 4px;border: 1px solid #E3E3E3;background: #0177bccf;color:white; padding: 5px 0px;">
                        <span style="padding: 0px 0px 0px 30px"><b>Punch In At</b></span>
                        <?php if ($in_time == '00:00' || $in_time == '' || $in_time == null) {
                            $in_time = "<span class='text-danger' >00:00</span>";
                            $total_working_hour = '0.0';
                        } ?>
                        <span style="padding: 0px 0px 0px 0px"><b><?php echo $in_time ?></b><span>
                    </div>
                </div>

                <div class="containers">
                    <div class="circle">
                        <div class="text"><?= $total_working_hour . " hrs" ?></div>
                    </div>
                </div>
                <br>
            </div>
        </div>

        <!-- step bar -->
        <div class="col-sm-8 timeshet">
            <div class="col-sm-12 col_style">
                <div class="card todayac" style="margin-left: -82px;">
                    <div class="card-header" style="border-bottom: 0px !important;">
                        <h5 class="card-title flex-item " style="font-weight: 600;margin-left:10px;">Today Activity<i
                                class="fa icon-time"></i></h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $lunch_end = date('h:i A', strtotime("$schedule->lunch_minute minutes", strtotime($schedule->lunch_time)));
                        $in_time_class = '';
                        $lunch_start_class = '';
                        $lunch_end_class = '';
                        $out_time_class = '';
                        if (date('H:i:s') > $schedule->in_start_time && date('H:i:s') < $schedule->in_time) {
                            $in_time_class = 'current-item';
                        } elseif (date('H:i:s') > $schedule->in_time && date('H:i:s') < $schedule->lunch_time) {
                            $lunch_start_class = 'current-item';
                        } elseif (date('H:i:s') > $schedule->lunch_time && date('H:i:s') < date("H:i:s", strtotime($lunch_end))) {
                            $lunch_end_class = 'current-item';
                        } elseif (date('H:i:s') > date("H:i:s", strtotime($lunch_end)) && date('H:i:s') < $schedule->ot_start_time) {
                            $out_time_class = 'current-item';
                        }
                        ?>
                        <section class="step-wizard" style="margin-top: -15px;">
                            <ul class="step-wizard-list" style="margin-left: -48px;">
                                <li class="step-wizard-item <?= $in_time_class ?>">
                                    <span class="progress-label-top">Punch In</span>
                                    <span class="progress-count"><i class="icon-time"></i></span>
                                    <span
                                        class="progress-label"><?php echo !empty($punch_time) ? $in_time : "Not punch yet" ?></span>
                                </li>
                            
                                <li class="step-wizard-item <?= $out_time_class ?>">
                                    <span class="progress-label-top">Punch Out</span>
                                    <span class="progress-count"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                    <?php if (isset($punch_time) && isset($punch_time->out_time)): ?>
                                        <span class="progress-label"><?php echo $punch_time->out_time; ?></span>
                                    <?php else: ?>
                                        <span class="progress-label">Not punch yet</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </section>

                        <div class="row" style="margin-left: -8px;margin-top: 45px;">
                            <div class="col-sm-3">
                                <p><span style="font-weight:600;font-size: 14px;">Status : </span><span
                                        style="font-size: 14px;"><?php echo $in_time >= '09:35:00' ? '<span class="text-danger"><b>Late</b></span>' : '<span class="success"><b>On Time</b></span>' ?></span>
                                </p>
                            </div>
                   
                            <div class="col-sm-5">
                                <?php $in = date("h:i A", strtotime($schedule->in_time));
                                $out = date("h:i A", strtotime($schedule->ot_start_time)); ?>
                                <p>
                                    <span style="font-weight:600;font-size: 14px;">Working Time: </span>
                                    <span style="font-size: 14px;"><?= $in . '  -  ' . $out; ?></span>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly statistics -->
    <div class="row equal-height-row " style="margin-top:-10px">
        <div class="col-sm-8 col_style">
            <div class="card">
                <div class="card-body">
                    <div style="display:flex">
                        <h5>Salary Statistics</h5>

                        <!-- <h5 style="margin-right:0; margin-left: auto;">Yearly 1234M</h5> -->
                    </div>
                    <div id="my_div">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 col_style">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" style="    margin-left: 13px;font-weight:600">This Month Summary</h5>
                    <div class="stats-box-row " style="margin-top:40px">
                        <div class="stats-box col-md-6 widght">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35"
                                    fill="none">
                                    <path
                                        d="M30.634 4.15838H25.4981V1.59038C25.4981 1.11765 25.1149 0.734375 24.6421 0.734375C24.1694 0.734375 23.7862 1.11765 23.7862 1.59038V4.15838H11.8024V1.59038C11.8024 1.11765 11.4192 0.734375 10.9464 0.734375C10.4737 0.734375 10.0905 1.11765 10.0905 1.59038V4.15838H4.95374C2.59101 4.16089 0.676336 6.07561 0.673828 8.4384V30.6945C0.676336 33.0572 2.59101 34.972 4.95374 34.9745H30.634C32.9968 34.9723 34.9117 33.0573 34.9139 30.6945V8.4384C34.9117 6.07551 32.9968 4.16058 30.634 4.15838ZM33.202 30.6945C33.2004 32.1121 32.0516 33.2609 30.634 33.2625H4.95374C3.53612 33.2609 2.38736 32.1121 2.38579 30.6945V16.1424H33.202V30.6945ZM33.202 14.4304H2.38579V8.4384C2.38736 7.02075 3.53612 5.87196 4.95374 5.87039H10.0905V8.4384C10.0905 8.43871 10.0905 8.43902 10.0905 8.43944C10.0908 8.91185 10.4739 9.29471 10.9464 9.2944C10.9468 9.2944 10.9471 9.2944 10.9475 9.2944C11.4199 9.29408 11.8027 8.91091 11.8024 8.4384V5.87039H23.7862V8.4384C23.7862 8.43871 23.7862 8.43902 23.7862 8.43944C23.7865 8.91185 24.1696 9.29471 24.6421 9.2944C24.6425 9.2944 24.6428 9.2944 24.6432 9.2944C25.1156 9.29408 25.4984 8.91091 25.4981 8.4384V5.87039H30.634C32.0516 5.87196 33.2004 7.02075 33.202 8.4384V14.4304Z"
                                        fill="#599AE7" />
                                </svg>
                            </div>
                            <span class="text-center text-success"><b>Working Day</b></span>
                            <span class="text-center"><b><?php echo $count_p ?> Day</b></span>
                        </div>
                        <div class="stats-box col-md-6 widght">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35"
                                    fill="none">
                                    <path
                                        d="M17.1435 0.734131C7.69322 0.734131 0.0234375 8.40391 0.0234375 17.8542C0.0234375 27.3044 7.69322 34.9742 17.1435 34.9742C26.5938 34.9742 34.2635 27.3044 34.2635 17.8542C34.2635 8.40391 26.5938 0.734131 17.1435 0.734131ZM17.1435 31.5502C9.57642 31.5502 3.44745 25.4212 3.44745 17.8542C3.44745 14.687 4.52601 11.7766 6.34073 9.46535L25.5323 28.6569C23.2211 30.4717 20.3107 31.5502 17.1435 31.5502ZM27.9462 26.243L8.75466 7.05143C11.0659 5.2367 13.9763 4.15814 17.1435 4.15814C24.7105 4.15814 30.8395 10.2871 30.8395 17.8542C30.8395 21.0214 29.761 23.9318 27.9462 26.243Z"
                                        fill="#FF715B" />
                                </svg>
                            </div>
                            <span class="text-center accordion text-danger"><b>Absent</b></span>
                            <span class="text-center"><b><?php echo $count_a ?> Day</b></span>
                        </div>
                    </div>
                    <div class="stats-box-row">
                        <div class="stats-box col-md-6 widght">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35"
                                    fill="none">
                                    <path
                                        d="M23.0519 16.585H18.6734C18.5936 16.3612 18.465 16.158 18.297 15.99C18.129 15.822 17.9257 15.6934 17.7019 15.6136V9.09502C17.7019 8.95313 17.6456 8.81705 17.5452 8.71672C17.4449 8.61639 17.3088 8.56002 17.1669 8.56002C17.025 8.56002 16.889 8.61639 16.7886 8.71672C16.6883 8.81705 16.6319 8.95313 16.6319 9.09502V15.6136C16.3766 15.7034 16.1481 15.8564 15.968 16.0585C15.7878 16.2605 15.6618 16.5049 15.6018 16.7689C15.5418 17.0328 15.5496 17.3077 15.6247 17.5678C15.6997 17.8279 15.8395 18.0647 16.0309 18.2561C16.2223 18.4475 16.4591 18.5873 16.7192 18.6623C16.9793 18.7373 17.2541 18.7452 17.5181 18.6852C17.782 18.6251 18.0264 18.4991 18.2285 18.319C18.4305 18.1388 18.5836 17.9104 18.6734 17.655H23.0519C23.1938 17.655 23.3299 17.5987 23.4302 17.4983C23.5306 17.398 23.5869 17.2619 23.5869 17.12C23.5869 16.9782 23.5306 16.8421 23.4302 16.7417C23.3299 16.6414 23.1938 16.585 23.0519 16.585ZM17.1669 17.655C17.0611 17.655 16.9577 17.6237 16.8697 17.5649C16.7817 17.5061 16.7131 17.4225 16.6726 17.3248C16.6322 17.227 16.6216 17.1194 16.6422 17.0157C16.6628 16.9119 16.7138 16.8166 16.7886 16.7417C16.8634 16.6669 16.9588 16.616 17.0626 16.5953C17.1663 16.5747 17.2739 16.5853 17.3717 16.6258C17.4694 16.6663 17.553 16.7348 17.6118 16.8228C17.6706 16.9108 17.7019 17.0142 17.7019 17.12C17.7018 17.2619 17.6454 17.3979 17.5451 17.4982C17.4448 17.5986 17.3088 17.6549 17.1669 17.655ZM27.0261 7.3193C27.0168 7.30833 27.0132 7.29455 27.0028 7.28416C26.9924 7.27377 26.9786 7.27019 26.9677 7.26085C24.3656 4.66671 20.8412 3.21 17.1669 3.21C13.4926 3.21 9.96825 4.66671 7.36618 7.26085C7.35521 7.27019 7.34143 7.27377 7.33104 7.28416C7.32065 7.29455 7.31707 7.30832 7.30773 7.3193C4.71359 9.92136 3.25689 13.4458 3.25689 17.12C3.25689 20.7943 4.71359 24.3187 7.30773 26.9208C7.31707 26.9317 7.32065 26.9455 7.33104 26.9559C7.34143 26.9663 7.3552 26.9699 7.36618 26.9792C9.96825 29.5734 13.4926 31.0301 17.1669 31.0301C20.8412 31.0301 24.3656 29.5734 26.9677 26.9792C26.9786 26.9699 26.9924 26.9663 27.0028 26.9559C27.0132 26.9455 27.0168 26.9318 27.0261 26.9208C29.6202 24.3187 31.077 20.7943 31.077 17.12C31.077 13.4458 29.6202 9.92136 27.0261 7.3193ZM26.6021 25.7987L25.4897 24.6863C25.3891 24.5877 25.2536 24.5328 25.1126 24.5335C24.9717 24.5342 24.8367 24.5905 24.7371 24.6901C24.6374 24.7898 24.5811 24.9248 24.5804 25.0657C24.5797 25.2066 24.6346 25.3422 24.7333 25.4428L25.8456 26.5552C23.6181 28.6121 20.7314 29.8094 17.7019 29.933V28.3551C17.7019 28.2132 17.6456 28.0771 17.5452 27.9768C17.4449 27.8764 17.3088 27.8201 17.1669 27.8201C17.025 27.8201 16.889 27.8764 16.7886 27.9768C16.6883 28.0771 16.6319 28.2132 16.6319 28.3551V29.933C13.6025 29.8094 10.7158 28.6121 8.48824 26.5553L9.60063 25.4429C9.69928 25.3422 9.75421 25.2067 9.7535 25.0657C9.75279 24.9248 9.69649 24.7898 9.59683 24.6902C9.49717 24.5905 9.3622 24.5342 9.22126 24.5335C9.08032 24.5328 8.9448 24.5877 8.84414 24.6864L7.73172 25.7987C5.67486 23.5712 4.47757 20.6845 4.354 17.655H5.9319C6.07379 17.655 6.20987 17.5987 6.3102 17.4983C6.41053 17.398 6.4669 17.2619 6.4669 17.12C6.4669 16.9782 6.41053 16.8421 6.3102 16.7417C6.20987 16.6414 6.07379 16.585 5.9319 16.585H4.354C4.47757 13.5556 5.67486 10.6689 7.73172 8.44136L8.8441 9.55374C8.94476 9.65239 9.08029 9.70733 9.22123 9.70662C9.36217 9.70591 9.49713 9.6496 9.59679 9.54994C9.69645 9.45028 9.75276 9.31532 9.75347 9.17438C9.75418 9.03344 9.69924 8.89791 9.6006 8.79725L8.48824 7.68483C10.7158 5.62797 13.6025 4.43068 16.6319 4.30711V5.88501C16.6319 6.02691 16.6883 6.16299 16.7886 6.26332C16.889 6.36365 17.025 6.42002 17.1669 6.42002C17.3088 6.42002 17.4449 6.36365 17.5452 6.26332C17.6456 6.16299 17.7019 6.02691 17.7019 5.88501V4.30711C20.7314 4.43068 23.618 5.62797 25.8456 7.68483L24.7332 8.79722C24.6827 8.84671 24.6425 8.90572 24.615 8.97083C24.5874 9.03595 24.5731 9.10588 24.5727 9.17659C24.5724 9.24729 24.586 9.31736 24.6129 9.38275C24.6398 9.44814 24.6794 9.50756 24.7294 9.55755C24.7794 9.60755 24.8388 9.64714 24.9042 9.67403C24.9696 9.70092 25.0396 9.71458 25.1104 9.71423C25.1811 9.71387 25.251 9.6995 25.3161 9.67195C25.3812 9.6444 25.4402 9.60421 25.4897 9.55371L26.6021 8.44136C28.659 10.6689 29.8563 13.5556 29.9799 16.585H28.402C28.2601 16.585 28.124 16.6414 28.0237 16.7417C27.9233 16.8421 27.867 16.9782 27.867 17.12C27.867 17.2619 27.9233 17.398 28.0237 17.4983C28.124 17.5987 28.2601 17.655 28.402 17.655H29.9799C29.8563 20.6845 28.659 23.5712 26.6021 25.7987ZM17.1669 0C13.7809 0 10.4709 1.00407 7.65554 2.88525C4.84016 4.76642 2.64584 7.44021 1.35007 10.5685C0.0542928 13.6968 -0.284741 17.139 0.37584 20.46C1.03642 23.781 2.66695 26.8315 5.06123 29.2257C7.45551 31.62 10.506 33.2505 13.827 33.9111C17.1479 34.5717 20.5902 34.2327 23.7185 32.9369C26.8468 31.6411 29.5205 29.4468 31.4017 26.6314C33.2829 23.8161 34.287 20.5061 34.287 17.12C34.2818 12.5811 32.4764 8.22958 29.2669 5.02008C26.0574 1.81057 21.7059 0.00519099 17.1669 0V0ZM17.1669 33.1701C13.9925 33.1701 10.8894 32.2288 8.25 30.4652C5.61059 28.7016 3.55341 26.1949 2.33862 23.2621C1.12383 20.3294 0.805988 17.1022 1.42528 13.9888C2.04458 10.8754 3.57319 8.01559 5.81783 5.77095C8.06247 3.52631 10.9223 1.99769 14.0357 1.3784C17.1491 0.759105 20.3762 1.07695 23.309 2.29174C26.2418 3.50653 28.7484 5.5637 30.512 8.20312C32.2756 10.8425 33.217 13.9456 33.217 17.12C33.2122 21.3753 31.5196 25.4549 28.5107 28.4638C25.5018 31.4727 21.4222 33.1653 17.1669 33.1701Z"
                                        fill="#BB10AA" />
                                </svg>
                            </div>
                            <span class="text-warning"><b>Late Coming</b></span>
                            <span><b><?php echo $count_late ?> Day</b></span>
                        </div>
                        <div class="stats-box col-md-6 widght">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="33" viewBox="0 0 30 33"
                                    fill="none">
                                    <path
                                        d="M15.6525 1.66465V0.822585C15.6525 0.368316 15.2842 0 14.83 0C14.3757 0 14.0074 0.368316 14.0074 0.822585V1.66465C6.43572 1.89068 0.257802 7.96307 0.0234375 15.6291V15.63C0.0237387 16.0841 0.391954 16.452 0.846022 16.4517H9.1031C9.11404 16.4522 9.12509 16.4532 9.13614 16.4532C9.13885 16.4532 9.14095 16.4517 9.14367 16.4517H14.0074V28.3792C14.0074 29.9692 12.7184 31.2582 11.1283 31.2582C9.53829 31.2582 8.24929 29.9692 8.24929 28.3792C8.24929 27.9249 7.88097 27.5566 7.4267 27.5566C6.97243 27.5566 6.60412 27.9249 6.60412 28.3792C6.60663 30.8768 8.63076 32.9009 11.1283 32.9034C13.6259 32.9009 15.65 30.8768 15.6525 28.3792V16.4517H28.8149C29.2689 16.4514 29.6368 16.0832 29.6365 15.6291C29.4024 7.96297 23.2243 1.89048 15.6525 1.66465ZM8.33243 14.8065H1.69753C2.27691 9.25207 6.33451 4.85839 11.5439 3.63596C9.73841 6.06446 8.62674 10.3942 8.33243 14.8065ZM9.98071 14.8065C10.4613 7.67358 12.817 3.29034 14.83 3.29034C16.8429 3.29034 19.1993 7.67358 19.6807 14.8065H9.98071ZM21.3277 14.8065C21.0344 10.3943 19.9227 6.06476 18.1169 3.63626C23.3256 4.8591 27.3829 9.25257 27.9624 14.8065H21.3277Z"
                                        fill="#2DCA8C" />
                                </svg>
                            </div>
                            <span class="text-center"><b>Leave</b></span>
                            <span class="text-center" style="color:red"><b><?= $totalleavem ?></b></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    */ ?>




    <!-- lunch part -->
    
</div>
<?php /*
<!-- notice modal -->
<!-- Modal -->
<div id="myModals" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Notice</h4>
            </div>
            <div class="modal-body">
                <p id="title" class="h4 text-center"></p>
                <p id="description" class="text-justify"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- end notice modal -->


<!-- upcomming holidays modal -->

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <br>
            <h4 class="modal-title text-center sticky-heading"><b>Upcoming Holidays</b></h4><br>
            <table class="table table-bordered table-striped">
                <thead class="text-center">
                    <tr>
                        <th class="text-center" scope="col">Sl. No.</th>
                        <th class="text-center" scope="col">Name</th>
                        <th class="text-center" scope="col">Date</th>
                        <th class="text-center" scope="col">Day</th>
                        <th class="text-center" scope="col">Day Left</th>
                        <!-- <th class="text-center" scope="col">Total Day</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    // $holidays= $this->db->select('*')->get('xin_holidays')->result();
                    foreach ($holidays as $holiday) {
                        $today = new DateTime();  // Current date
                        $date = $holiday->start_date;
                        $futureDate = new DateTime($date);  // Future date

                        if ($futureDate < $today) {
                            $daysLeft = 0;
                        } else {
                            $interval = date_diff($today, $futureDate);
                            $daysLeft = $interval->format('%a');
                        }

                        ?>
                        <tr class="text-center">
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $holiday->event_name ?></td>
                            <td><?php echo $holiday->start_date ?></td>
                            <td><?php echo date("l", strtotime($holiday->start_date)) ?></td>
                            <td><?php echo $daysLeft ?></td>
                            <!-- <td><?php echo $daysLeft ?></td> -->
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br> <button type="button" class="btn btn-danger btn-sm" style="float:right;margin-right: 10px;"
                data-dismiss="modal">Close</button><br><br><br>
        </div>
    </div>
</div>
<!-- end upcomming holidays modal -->





<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1"></script>

<script>
    function myfunc(e) {
        var title = e.getAttribute('data-title');
        var description = e.getAttribute('data-description');
        $("#title").text(title);
        $("#description").text(description);
    }

    $('#year_id').on('change', function () {
        var year = $('#year_id').find(":selected").val();
        if (year) {
            var url = "<?php echo base_url() ?>" + "admin/dashboard/chart";
            $("#my_div").load(url);
        } else {
            alert("KO");
        }
    })
    $(document).ready(function () {
        $('#datatbale').DataTable();
    });
</script>



<script>
    const monthNames = <?php echo json_encode($monthNames); ?>;
    const dataValues = <?php echo json_encode($salary); ?>;
    console.log(dataValues);
    console.log(monthNames);

    const ctx = document.getElementById('myChart').getContext('2d');
    const zerosToPad = monthNames.length - dataValues.length;
    console.log(zerosToPad);
    const zeroArray = new Array(zerosToPad).fill(0);
    const paddedDataValues = zeroArray.concat(dataValues);


    function customTooltip({ dataIndex }) {

        if (dataIndex >= 1) { // Check if the dataIndex is greater than or equal to 1
            const previousMonth = monthNames[dataIndex - 1];
            const previousSalary = paddedDataValues[dataIndex];
            return `${previousMonth}\nSalary: ${previousSalary}`;
        }
        return ''; // Return an empty string for the first month (January)
    }

    // Create the chart
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthNames,
            datasets: [{
                label: 'Salary',
                data: paddedDataValues,
                backgroundColor: 'rgba(75, 192, 192, 0.8)'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: customTooltip // Use the custom tooltip function
                    }
                }
            }
        }
    });
</script>
*/ ?>
