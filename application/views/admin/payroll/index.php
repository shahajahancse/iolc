<?php
/* Attendance view
*/
?>
<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']);?>
<link rel="stylesheet" href="<?php echo base_url() ?>skin/hrsale_assets/css/loader.css">

<div class="col-lg-8">
<div class="box mb-4 <?php echo $get_animate;?>">
  <div class="box-body">
    <div class="row">

      <div class="col-md-3">
        <div class="form-group">
          <label>Select Month</label>
          <input type="month" class="form-control input-sm" id="sal_month" name="sal_month">
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <label>Select Type</label>
          <select class="form-control input-sm" name="process_type" id="process_type">
            <option value="">Select one</option>
            <option value="1">Only Salary Process</option>
            <option value="2">Salary with Lunch Process</option>
            <!-- <option value="3">Final Process</option> -->
          </select>
        </div>
      </div>

      <div class="col-md-5">
        <div class="form-group">
          <label>Select Status</label>
          <div class="input-group input-group-sm">
            <select class="form-control" name="status" id="status">
              <option value="">Select one</option>
              <option value="1">Active</option>
              <option value="2">Inactive</option>
            </select>
            <span class="input-group-btn" style="">
              <button class="btn btn-success" type="button" onclick="salary_process()">Process</button>
              <button style="margin-left: 5px;" class="btn btn-primary" type="button" onclick="salary_final()">Final</button>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- modal -->
<div class="modal fade bd-example-modal-lg" id="my_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button  type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modify Employee Salary</h4>
      </div>



      <div class="modal-body">


            <form  id="salaryForm">
                <div id="total" class="col-md-12" style="display: inline-flex;">
              </div>
              <div class="col-md-12" style="display: inline-flex;margin-bottom: -16px;">
                        <div class="form-group col-md-3">
                          <label style="margin-left: -22px;">Employee name</label>
                        </div>


                        <!-- hh -->
                        <div class="form-group col-md-2">
                          <label>Basic Salary</label>
                        </div>
                        <div class="form-group col-md-1" style="padding: 0;text-align-last: center;">
                          <label>Late</label>
                          </div>
                        <div class="form-group col-md-1" style="padding: 0;text-align-last: center;">
                          <label >d day</label>
                          </div>

                        <div class="form-group col-md-2" style="padding: 0;text-align-last: center;">
                          <label>L Deduct</label>
                        </div>
                        <div class="form-group col-md-1" style="padding: 0;text-align-last: center;">
                          <label style="margin-left: 16px;">m day</label>
                        </div>
                        <!-- hh -->


                        <div class="form-group col-md-2">
                          <label style="margin-left: 27px;">Modify</label>
                        </div>
              </div>
                 <div id="empfrom"></div>
            <div class="modal-footer" >
                <button type="button" name="btn" onclick=save_modify_salary() class="btn btn-sm btn-success" style="margin-top:10px !important">Save</button>
                <button type="button" class="btn btn-sm btn-danger" style="margin-top:10px !important" data-dismiss="modal">Close</button>
            </div>
        </form>
      </div>

    </div>

  </div>
</div>
<!-- modal close -->


<div id="loader"  align="center" style="margin:0 auto; width:600px; overflow:hidden; display:none; margin-top:10px;"><img src="<?php echo base_url();?>/uploads/ajax-loader.gif" /></div>

<div class="box <?php echo $get_animate;?>">
  <div class="box-header with-border" id="report_title">
    <h3 class="box-title" id="report"> Salary Report
      <!-- < ?php echo $this->lang->line('xin_daily_attendance_report');?> -->
   </h3>
     <button onclick=modify_salary()  class="btn btn-sm btn-primary pull-right" style="padding: 6px 10px !important;">Modify Salary</button>
     <a href="<?= base_url('admin/payroll/bank_salary_config') ?>" class="btn btn-sm btn-primary pull-right" style="padding: 6px 10px !important;margin: 0 9px;">Bank salary configuration</a>
  </div>

  <div class="box-body" id="emp_report">
    <ul class="nav nav-tabs " id="myTab" role="tablist">
        <li class="nav-item active">
          <a class="nav-link " id="daily-tab" data-toggle="tab" href="#daily" role="tab" aria-controls="daily" aria-selected="true">Report</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab" aria-controls="monthly" aria-selected="false">Excel</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="continuously-tab" data-toggle="tab" href="#continuously" role="tab" aria-controls="continuously" aria-selected="false">Continuously</a>
        </li>
    </ul>
    <!-- hh -->

    <div class="tab-content" id="myTabContent">


      <div class="tab-pane fade active in" id="daily" role="tabpanel" aria-labelledby="daily-tab" style="margin-top: 30px;">
          <button class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px !important;" onclick="Actual_salary_sheet_excel()">Salary Sheet</button>
          <button class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px !important;" onclick="Actual_salary_sheet_excel_bank(2,1)">Bank Salary Sheet</button>
          <button class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px !important;" onclick="Actual_salary_sheet_excel_bank(2,0)">Cash Salary Sheet</button>
          <button class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px !important;" onclick="report_salary_sheet()">Salary Report</button>
          <!-- <button class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px !important;" onclick="genarate_payslip()">Genarate Payslip</button>
         <br>
         <br>
        <button  class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px  !important;"  onclick="movReport(2)">Unpaid Movement</button>
        <button  class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px !important;" onclick="movReport(1)">Process Movement</button>
        <button  class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px !important;" onclick="movReport(3)">Reject Movement</button>
        <button  class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px !important;"  onclick="movReport(4)">Paid Movement</button> -->

      </div>

      <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab" style="margin-top: 30px;">
        <button class="btn btn-sm mr-5 rounded" style="background: #2393e3eb; color: white;margin-right: 10px;padding:6px 10px !important;" onclick="salary_sheet_excel()">Salary Sheet</button>

      </div>

      <div class="tab-pane fade" id="continuously" role="tabpanel" aria-labelledby="continuously-tab" style="margin-top: 30px;">
        <button class="btn btn-sm btn-success rounded" style="padding:6px 10px !important;" onclick="jobCard()">Job Card</button>

      </div>
      <!-- h -->
    </div>

  </div>


  <div  class="box-body" id="entry_form">

  </div>



</div>
</div>

<div class="col-lg-4">
<div class="box" style="height: 74vh;overflow-y: scroll;">
<table class="table table-striped table-hover" id="fileDiv">
  <tr style="position: sticky;top: 0;z-index:1">
      <th class="active" style="width:10%"><input type="checkbox" id="select_all" class="select-all checkbox" name="select-all" /></th>
      <th class="" style="width:10%;background:#0177bcc2;color:white">Id</th>
      <th class=" text-center" style="background:#0177bc;color:white">Name</th>
  </tr>
</table>
</div>
</div>



<script type="text/javascript" src="<?php echo base_url() ?>skin/hrsale_assets/js/hrm.js"></script>
<!-- <script type="text/javascript" src="< ?php echo base_url() ?>skin/hrsale_assets/js/salary.js"></script> -->
<script>
  // salary final process
  function salary_final()
  {
    var ajaxRequest;  // The variable that makes Ajax possible!
    ajaxRequest = new XMLHttpRequest();

    sal_month = document.getElementById('sal_month').value;
    if(sal_month =='')
    {
      alert('Please select process Month');
      return ;
    }
    process_month = sal_month +'-'+ '01'

    var okyes;
    okyes=confirm('Are you sure you want to Final Process?');
    if(okyes==false) return;

    $("#loader").show();
    var data = "process_month="+process_month;

    url = base_url + "/salary_final";
    var url = "<?php echo base_url('admin/payroll/salary_final'); ?>";
    ajaxRequest.open("POST", url, true);
    ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
    ajaxRequest.send(data);

    ajaxRequest.onreadystatechange = function(){
      if(ajaxRequest.readyState == 4){
        var resp = ajaxRequest.responseText;
        $("#loader").hide();
        alert(resp);
      }
    }
  }
</script>

<script>
  $(document).ready(function(){

    // select all item or deselect all item
    $("#select_all").click(function(){
      $('input:checkbox').not(this).prop('checked', this.checked);
    });

    // on load employee
    $("#status").change(function(){
      status = document.getElementById('status').value;
      sal_month = document.getElementById('sal_month').value;
      if(sal_month =='')
      {
        alert('Please select salary month');
        return ;
      }
      $('#fileDiv #removeTr').remove();

      var url = "<?php echo base_url('admin/attendance/get_employee_ajax_request'); ?>";
      $.ajax({
        url: url,
        type: 'GET',
        data: {
          "status":status,
          "sal_month":sal_month
        },
        contentType: "application/json",
        dataType: "json",

        success: function(response){
          arr = response.employees;
          // console.log(arr);
          if (arr.length != 0) {
            var items = '';
            $.each(arr, function(index,value) {
              items += '<tr id="removeTr">';
              items += '<td><input type="checkbox" class="checkbox" id="select_emp_id" name="select_emp_id[]" value="'+value.emp_id+'" ></td>';
              items += '<td class="success">'+value.emp_id+'</td>';
              items += '<td class="warning ">'+value.first_name +' '+ value.last_name +'</td>';
              items += '</tr>';
            });
            $('#fileDiv tr:last').after(items);
          } else {
            $('#fileDiv #removeTr').remove();
          }
        }
      });
    });
   });

   $("#emp_name option").filter(function() {
        return $id= $(this).val() == $("#gross_salary").val();
     }).attr('selected', true);




 function save_modify_salary() {

            // retrieve form data
            var formData = $('#salaryForm').serializeArray();

            // define the URL for the server-side PHP script that will handle the AJAX request
            var url = "<?php echo base_url('admin/payroll/save_modify_salary_all');?>";

            // send an AJAX request to the server-side PHP script
            $.ajax({
              url: url, // specify the URL of the PHP script
              type: 'POST', // specify the HTTP method (POST in this case)
              data: formData, // include the form data in the request
              success: function(response) {

                $('#total').empty();
                $('#empfrom').empty();
                modify_salary()
                $('#my_modal').modal('show');
                alert("Operation successful!");
              }
            });

  }


 $(document).ready(function(){
    $("#modify_salary").on('input',function(){
      $("#modify_salary").attr('style', 'border: 1px solid #ccd6e6 !important');
      if($("#modify_salary").val() ==''){
        $("#modify_salary").attr('style', 'border: 1px solid red !important');
        return false;
      }
    });

    $("#emp_name").on('input',function(){
      $("#emp_name").attr('style', 'border: 1px solid #ccd6e6 !important');
      if($("#emp_name").val() ==''){
        $("#emp_name").attr('style', 'border: 1px solid red !important');
        return false;
      }
    });
  });
</script>

<script>
  function chenge_s() {
    $("#status").trigger('change');

  }
</script>

<script>
          // get check box select value
    function get_checked_value(checkboxes) {
      var vals = "";
      for (var i=0, n=checkboxes.length;i<n;i++) 
      {
          if (checkboxes[i].checked) 
          {
              vals += ","+checkboxes[i].value;
          }
      }
      if (vals) vals = vals.substring(1);
      return vals;
    }


    // salary process
    function salary_process()
    {
      // alert(csrf_token); return;
      var ajaxRequest;  // The variable that makes Ajax possible!
      ajaxRequest = new XMLHttpRequest();


      sal_month = document.getElementById('sal_month').value;
      if(sal_month =='')
      {
        alert('Please select process Month');
        return ;
      }


      process_month =  sal_month +'-'+ '01'

      status = document.getElementById('status').value;
      if(status =='')
      {
        alert('Please select status');
        return ;
      }

      var checkboxes = document.getElementsByName('select_emp_id[]');
      var sql = get_checked_value(checkboxes);
      if(sql =='')
      {
        alert('Please select employee Id');
        return ;
      }
 
      var okyes;
      okyes=confirm('Are you sure you want to start process?');
      if(okyes==false) return;

      $("#loader").show();
       var data = "process_month="+process_month+"&status="+status+'&sql='+sql;
  
      // console.log(data); return;
      url = base_url + "/salary_process";
      ajaxRequest.open("POST", url, true);
      ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
      ajaxRequest.send(data);

      ajaxRequest.onreadystatechange = function(){
        if(ajaxRequest.readyState == 4){
          // console.log(ajaxRequest);
          var resp = ajaxRequest.responseText;
          $("#loader").hide();
          alert(resp);
        }
      }
    }

    // salary process
    function salary_sheet_excel()
    {
      // alert(csrf_token); return;
      var ajaxRequest;  // The variable that makes Ajax possible!
      ajaxRequest = new XMLHttpRequest();


      sal_month = document.getElementById('sal_month').value;
      if(sal_month =='')
      {
        alert('Please select salary month');
        return ;
      }

      salary_month =  sal_month+ "-01"

      status = document.getElementById('status').value;
      if(status =='')
      {
        alert('Please select status');
        return ;
      }

      var checkboxes = document.getElementsByName('select_emp_id[]');
      var sql = get_checked_value(checkboxes);
      if(sql =='')
      {
        alert('Please select employee Id');
        return ;
      }
 
      /*var okyes;
      okyes=confirm('Are you sure you want to generate excel sheet?');
      if(okyes==false) return;*/

       var data = "salary_month="+salary_month+"&status="+status+'&sql='+sql+"&excel="+0;
  
      // console.log(data); return;
      url = base_url + "/salary_sheet_excel";
      ajaxRequest.open("POST", url, true);
      ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
      ajaxRequest.send(data);

      ajaxRequest.onreadystatechange = function(){
        if(ajaxRequest.readyState == 4){
          // console.log(ajaxRequest);
          var resp = ajaxRequest.responseText;
          a = window.open('', '_blank', 'menubar=1,resizable=1,scrollbars=1,width=1200,height=800');
          a.document.write(resp);
        }
      }
    }

    // Actual_salary_sheet_excel

    function Actual_salary_sheet_excel()
    {
      // alert(csrf_token); return;
      var ajaxRequest;  // The variable that makes Ajax possible!
      ajaxRequest = new XMLHttpRequest();


      sal_month = document.getElementById('sal_month').value;
      if(sal_month =='')
      {
        alert('Please select salary month');
        return ;
      }



      salary_month = sal_month + "-01"

      status = document.getElementById('status').value;
      if(status =='')
      {
        alert('Please select status');
        return ;
      }

      var checkboxes = document.getElementsByName('select_emp_id[]');
      var sql = get_checked_value(checkboxes);
      if(sql =='')
      {
        alert('Please select employee Id');
        return ;
      }
      document.getElementById('loader').style.display = 'block';

 
      /*var okyes;
      okyes=confirm('Are you sure you want to generate excel sheet?');
      if(okyes==false) return;*/

       var data = "salary_month="+salary_month+"&status="+status+'&sql='+sql+"&excel="+0;
  
      // console.log(data); return;
      url = base_url + "/Actual_salary_sheet_excel";
      ajaxRequest.open("POST", url, true);
      ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
      ajaxRequest.send(data);

      ajaxRequest.onreadystatechange = function(){
        document.getElementById('loader').style.display = 'none'; 
        if(ajaxRequest.readyState == 4){
          // console.log(ajaxRequest);
          var resp = ajaxRequest.responseText;
          a = window.open('', '_blank', 'menubar=1,resizable=1,scrollbars=1,width=1200,height=800');
          a.document.write(resp);
        }
      }
    }
    function report_salary_sheet()
    {
      // alert(csrf_token); return;
      var ajaxRequest;  // The variable that makes Ajax possible!
      ajaxRequest = new XMLHttpRequest();
      sal_month = document.getElementById('sal_month').value;
      if(sal_month =='')
      {
        alert('Please select salary month');
        return ;
      }

      salary_month = sal_month + "-01";
      status = document.getElementById('status').value;
      if(status =='')
      {
        alert('Please select status');
        return ;
      }
      var checkboxes = document.getElementsByName('select_emp_id[]');
      var sql = get_checked_value(checkboxes);
      if(sql =='')
      {
        alert('Please select employee Id');
        return ;
      }
      document.getElementById('loader').style.display = 'block';
      /*var okyes;
      okyes=confirm('Are you sure you want to generate excel sheet?');
      if(okyes==false) return;*/
       var data = "salary_month="+salary_month+"&status="+status+'&sql='+sql+"&excel="+0;
      // console.log(data); return;
      url = base_url + "/report_salary_sheet";
      ajaxRequest.open("POST", url, true);
      ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
      ajaxRequest.send(data);
      ajaxRequest.onreadystatechange = function(){
        document.getElementById('loader').style.display = 'none'; 
        if(ajaxRequest.readyState == 4){
          // console.log(ajaxRequest);
          var resp = ajaxRequest.responseText;
          a = window.open('', '_blank', 'menubar=1,resizable=1,scrollbars=1,width=1200,height=800');
          a.document.write(resp);
        }
      }
    }
function Actual_salary_sheet_excel_bank(s,bank)
    {
      // alert(csrf_token); return;
      var ajaxRequest;  // The variable that makes Ajax possible!
      ajaxRequest = new XMLHttpRequest();


      sal_month = document.getElementById('sal_month').value;
      if(sal_month =='')
      {
        alert('Please select salary month');
        return 
      }



      salary_month = sal_month + "-01";

      status = document.getElementById('status').value;
      if(status =='')
      {
        alert('Please select status');
        return ;
      }

      var checkboxes = document.getElementsByName('select_emp_id[]');
      var sql = get_checked_value(checkboxes);
      if(sql =='')
      {
        alert('Please select employee Id');
        return ;
      }
 
      /*var okyes;
      okyes=confirm('Are you sure you want to generate excel sheet?');
      if(okyes==false) return;*/

       var data = "salary_month="+salary_month+"&status="+status+'&sql='+sql+"&excel="+s+"&bank="+bank;
  
      // console.log(data); return;
      url = base_url + "/Actual_salary_sheet_excel_bank";
      ajaxRequest.open("POST", url, true);
      ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded;charset=utf-8");
      ajaxRequest.send(data);

      ajaxRequest.onreadystatechange = function(){
        if(ajaxRequest.readyState == 4){
          // console.log(ajaxRequest);
          var resp = ajaxRequest.responseText;
          a = window.open('', '_blank', 'menubar=1,resizable=1,scrollbars=1,width=1200,height=800');
          a.document.write(resp);
        }
      }
    }

    $(document).on('change', '.commonclass', function() {
      // Get the data attributes
      const mdayid = $(this).data('id'); // Retrieve 'data-id'
      const bsid = $(this).data('bsid'); // Retrieve 'data-bsid'
      const inputchangeid = $(this).attr('id'); // Retrieve 'id'
    
      // Get the values from input elements
      const inputchange = $(`#${inputchangeid}`).val(); // Retrieve value based on 'inputchangeid'
      const bs = $(`#${bsid}`).val(); // Retrieve value based on 'bsid'
      const date = $('#date').val(); // Retrieve value based on 'date'

      // Extract year and month from the date
      const [year, month] = date.split('-').map(Number);
    
      // Get the last day of the current month
      const nextMonth = new Date(year, month, 1);
      const lastDayOfMonth = new Date(nextMonth.getTime() - 1);
    
      // Get the total number of days in the current month
      const totalDays = lastDayOfMonth.getDate();
    
      // Calculate and log the value of l_inputdata
      const l_inputdata = parseInt(((bs / totalDays) * inputchange).toFixed(2));
      console.log(l_inputdata);
      
    
      // Set the value of the element with 'mdayid' to l_inputdata
      $(`#${mdayid}`).val(l_inputdata);
    });   
    
    
    // Define a function named "modify_salary"
  function modify_salary() {
    // Retrieve the values of two HTML input elements with IDs 'sal_month' and 
    const sal_month = document.getElementById('sal_month').value;

    // Concatenate the retrieved values into a single string variable named "salary_month"
    const salary_month =  sal_month + "-01";

    // Send an AJAX request to a server-side script named "modify_salary"
    $.ajax({
      url: 'modify_salary',
      type: 'POST',
      data: {
        salary_month: salary_month,
      },
      // Handle the response from the server
      success: function(jsonArray){
        // Clear the contents of two HTML elements with IDs 'total' and 'empfrom'
        $('#total').empty();
        $('#empfrom').empty();

        // Parse the response from the server, which is expected to be a JSON array
        const response = JSON.parse(jsonArray);
      
      

        // Create an empty array named "sql" and a variable named "count"
        const sql = [];
        const count = response.length;

        // Extract the salary month from the first element of the JSON array
        if(count>0){
        const salary_month = response[0].salary_month;}

        // Create a variable named "item" that will hold the HTML code for each row in the employee table
        let item = '';

        // Loop through each element in the JSON array and create HTML code for each row in the employee table
     

        $.each(response, function(index, employee) {
          const row = '<div class="row" style="margin-top: 10px;"><div class="col-md-3"><input type="text" readonly class="form-control" value="' + employee.first_name + ' ' + employee.last_name + '" disabled><input type="hidden" name="modifydataid[]" class="form-control" value="' + employee.user_id+ '" ></div><div class="col-md-2"><input type="text" readonly class="form-control" value="' + employee.basic_salary + '" id="bs' + employee.user_id+ '" ></div><div class="col-md-1"><input type="number" readonly class="form-control" style="padding: 0;text-align-last: center;" value="' + employee.late_count + '"></div><div class="col-md-1"><input type="number"readonly class="form-control" style="padding: 0;text-align-last: center;" value="' + employee.d_day + '" id="deductday"></div><div class="col-md-2"><input type="text" readonly class="form-control" value="' + employee.late_deduct + '"></div><div class="col-md-1"><input type="number"  class="form-control commonclass" id="mid' + employee.user_id+ '" data-bsid="bs' + employee.user_id+ '" data-id="' + employee.user_id+ '" name="modifyday[]" value="' + employee.m_pay_day + '"  style="padding: 0;text-align-last: center;"></div><div class="col-md-2"><input type="number"  class="form-control" id="' + employee.user_id+ '" name="modifydata[]"   value="' + employee.modify_salary + '"></div></div>';
          item += row;
       
        });

        // Add the HTML code for each row to an HTML element with ID 'empfrom'
        $('#empfrom').append(item);

        // Concatenate the contents of the "sql" array into a single string variable named "sqld"
        const sqld = sql.join(',');

        // Add count after the last row in #empfrom
        var countCol = $('<div class="col-md-12 " style="display: inline-flex;"><p style="color:#004cff;font-size: 17px;margin-left: -26px;font-weight: bold;">Total Employee ' + count + '</p><input id="temp" type="hidden" readonly class="form-control" value="' + count + '" name="total_emp"><p style="color:#004cff;font-size: 17px;font-weight: bold;right: 0;position: absolute;margin-left:5px;">Date ' + salary_month + '</p><input id="date" type="hidden" readonly class="form-control" value="' + salary_month + '" name="date"><input id="sql" type="hidden" readonly class="form-control" value="'+ sqld +'"></div>');
        $('#total').append(countCol);
        $('#my_modal').modal('show');
      }
    });

  }
    





</script>


