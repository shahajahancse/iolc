<?php
/* Attendance view
*/
?>
<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']);?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">

<style>
#loading {
    visibility: hidden;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 3;
    /* set z-index higher than other elements */
    background-color: rgba(255, 255, 255, 0.8);
    /* semi-transparent background */
}

#loading img {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.sbtn {
    background: #2393e3eb;
    color: white;
    margin-right: 10px;
    padding: 6px 10px !important;
    margin-top: 13px;
}
</style>
<div id="loading">

    <img src="<?php echo base_url()?>skin/hrsale_assets/img/loding.gif">

</div>

<div class="col-lg-8">
    <div class="box mb-4 <?php echo $get_animate;?>">
        <div class="box-body">
            <div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="process_date">First Date</label>
                    <input class="form-control attendance_date"
                        placeholder="<?php echo $this->lang->line('xin_select_date');?>" id="process_date"
                        name="process_date" type="text" value="<?php echo date('Y-m-d');?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="second_date">Second Date</label>
                    <input class="form-control attendance_date" placeholder="Second Date" id="second_date"
                        name="second_date" type="text" autocomplete="off"> 
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <button class="btn btn-sm mr-5 sbtn mt-5" style="margin-top:25px" onclick="doc_reportt()">Show Report</button>
                </div>
            </div>
        </div>
    </div>
</div>


        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js"></script>

<script>



function doc_reportt() {
    var ajaxRequest; // The variable that makes Ajax possible!
    ajaxRequest = new XMLHttpRequest();

    first_date = document.getElementById('process_date').value;
    second_date = document.getElementById('second_date').value;
   
    if (first_date == '') {
        alert('Please select first date');
        return;
    }
    var data = "first_date=" + first_date + '&second_date=' + second_date ;
    url = base_url + "/doc_reportt";
    ajaxRequest.open("POST", url, true);
    ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
    ajaxRequest.send(data);
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