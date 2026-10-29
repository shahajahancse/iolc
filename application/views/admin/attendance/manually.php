<?php $session = $this->session->userdata('username');?>
<span class="h4" id="manu_form">Manually Entry Form</span>
<button id="back_report" class="btn btn-sm btn-primary col-6"  style="float:right;padding: 6px 10px !important;">Back Report</button>

<div id="form_manually" style="margin-top:30px">


<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item active">
    <a class="nav-link active" id="insert-tab" data-toggle="tab" href="#insert" role="tab" aria-controls="insert" aria-selected="true">Insert</a>
  </li>
  <li class="nav-item">
    <a style="color:red" class="nav-link" id="delete-tab" data-toggle="tab" href="#delete" role="tab" aria-controls="delete" aria-selected="false">Delete</a>
  </li>
  <li class="nav-item">
    <a style="color:red" class="nav-link" id="punishment-tab" data-toggle="tab" href="#punishment" role="tab" aria-controls="punishment" aria-selected="false">Punishment</a>
  </li>

</ul>



<div class="tab-content" id="myTabContent" style="margin-top:20px">
  <div class="tab-pane fade active in" id="insert" role="tabpanel" aria-labelledby="insert-tab">
    <div class="row">
        <p style="padding: 0px 15px;" id="checked_value">
          <span style="padding: 0px 20px"><label >Punch Miss</label> <input checked type="radio" name="check_value" value="0"></span>
          <span><label >Meeting</label> <input class="" type="radio" name="check_value" value="1">
          </span>
        </p>

        <div class="col-md-12">
          <div class="form-group col-lg-3">
            <label>In Time</label>
            <input type="text" name="in_time" id="in_time" class="form-control timepicker clear-1" placeholder="HH:MM">
          </div>
          <div class="form-group col-lg-3">
            <label>Out Time</label>
            <input type="text" name="out_time" id="out_time" class="form-control timepicker clear-1" placeholder="HH:MM">
          </div>
        </div>
        <div class="col-md-12" id="extra_field">
          <div class="form-group col-lg-6">
            <label>Project/Client name</label>
            <input type="text" name="project_name" id="project_name" class="form-control  clear-1" placeholder="">
          </div>
          <div class="form-group col-lg-6">
            <label>Contact Person</label>
            <input type="text" name="contact_person" id="contact_person" class="form-control  clear-1" placeholder="">
          </div>

        </div>
        <div class="col-md-12">
          <div class="col-lg-6" id="reason">
            <div class="form-group">
              <label>Reason</label>
              <textarea type="text" id="reason_value" class="form-control" name="reason"></textarea>
            </div>
          </div>
          <div class="col-lg-6" id="location">
            <div class="form-group">
              <label>Location</label>
              <textarea type="text" id="location_va" class="form-control" name="location_va"></textarea>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <span onclick="manual_entry_ajax(event)" class="btn btn-sm btn-success" style="float: right;margin-right: 15px;">Entry</span>
        </div>
    </div>
  </div>


  <div class="tab-pane fade" id="delete" role="tabpanel" aria-labelledby="delete-tab">
    <div class="form-group col-lg-6">
      <button onclick="present_to_absent()" type="submit" class="btn btn-sm btn-danger" style="padding: 6px 10px !important;margin-right:16px;margin-top:5px"> Present to Absent </button>
    </div>
  </div>

  <div class="tab-pane fade" id="punishment" role="tabpanel" aria-labelledby="punishment-tab">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label>Reason</label>
          <textarea type="text" id="reason_punish" class="form-control" name="reason_punish"></textarea>
        </div>
      </div>
      <div class="col-lg-12">
        <span onclick="manual_punishment_ajax()" class="btn btn-sm btn-success" style="float: right;margin-right: 15px;">Submit</span>
        <!-- <span onclick="delete_punishment_ajax()" class="btn btn-sm btn-danger" style="float: right;margin-right: 15px;">Delete</span> -->
        <span onclick="list_punishment_ajax()" class="btn btn-sm btn-primary" style="float: right;margin-right: 15px;">List</span>
      </div>
    </div>
  </div>

  <!-- <div class="form-group col-lg-6">
      <button type="submit" class="btn btn-sm btn-success" style="padding: 6px 10px !important;margin-right:16px;margin-top:5px">Holiday Delete</button>
  </div> -->
</div>


</div>

<script type="text/javascript" src="<?php echo base_url() ?>skin/hrsale_assets/js/hrm.js"></script>
<script>
  function list_punishment_ajax(){
    var url_ab = "<?php echo base_url('admin/attendance/list_punishment_ajax'); ?>";
    from_date = document.getElementById('process_date').value;
    to_date = document.getElementById('second_date').value;

    if(from_date == ''){
      alert('Please select First Date');
      return ;
    }

    if(to_date == ''){
      alert('Please select Second Date');
      return ;
    }

    $.ajax({
      url: url_ab,
      type: 'POST',
      data: {
          from_date: from_date,
          to_date: to_date
      },
      // Handle the response from the server
      success: function(jsonArray){
          // Clear the contents of two HTML elements with IDs 'total' and 'empfrom'
          $('#punish_from').empty();
          // Parse the response from the server, which is expected to be a JSON array
          const response = JSON.parse(jsonArray);
          // Create a variable named "item" that will hold the HTML code for each row in the employee table
          let item = '';

          // Loop through each element in the JSON array and create HTML code for each row in the employee table
          $.each(response, function(index, employee) {
            // Convert to Date object
            let d = employee.date ? new Date(employee.date) : null;
            // Format as dd-mm-yyyy
            let formattedDate = ("0" + d.getDate()).slice(-2) + "-" +
                                ("0" + (d.getMonth() + 1)).slice(-2) + "-" +
                                d.getFullYear();

            const row = `<tr>
              <td> ${index + 1} </td>
              <td> ${employee.first_name} ${employee.last_name} </td>
              <td> ${formattedDate} </td>
              <td> ${employee.reason} </td>
              <td> <button onclick="delete_punishment(${employee.id})" type="submit" class="btn btn-sm btn-danger" style="padding: 6px 10px !important;margin-right:16px;margin-top:5px">Delete</button> </td>
            </tr>`;
            item += row;
          });

          // Add the HTML code for each row to an HTML element with ID 'empfrom'
          $('#punish_from').append(item);
          // Add count after the last row in #punish_from
          $('#punish_modal').modal('show');
      }
    });
  }
  function delete_punishment(id){
    var url_ab = "<?php echo base_url('admin/attendance/delete_punishment'); ?>";
    var okyes;
    okyes=confirm('Are you sure you want to delete?');
    if(okyes==false){
      return;
    };

    $.ajax({
      url: url_ab,
      type: 'POST',
      data: {
        "id": id,
      },
      success: function(response) {
        if (response == 'Successfully') {
          showSuccessAlert('Record Successfully Updated','no');
        }else{
          showErrorAlert(response);
        }}
    });
  }

</script>


<script>
  function delete_punishment_ajax(){
    var url_ab = "<?php echo base_url('admin/attendance/delete_punishment_ajax'); ?>";
    date = document.getElementById('process_date').value;
    var emp_id = document.getElementsByName('select_emp_id[]');
    var sql = get_checked_value(emp_id);

    if(sql == ''){
      alert('Please select employee Id');
      return ;
    }

    if(date == ''){
      alert('Please select date');
      return ;
    }

    var okyes;
    okyes=confirm('Are you sure you want to delete?');
    if(okyes==false){
      return;
    };

    $.ajax({
      url: url_ab,
      type: 'POST',
      data: {
        "date": date,
        "sql": sql,
      },
      success: function(response) {
        if (response == 'Successfully') {
          showSuccessAlert('Record Successfully Updated','no');
        }else{
          showErrorAlert(response);
        }}
    });
  }
  function manual_punishment_ajax(){
    var url_ab = "<?php echo base_url('admin/attendance/manual_punishment_ajax'); ?>";
    date = document.getElementById('process_date').value;
    reason_punish = document.getElementById('reason_punish').value;
    var emp_id = document.getElementsByName('select_emp_id[]');
    var sql = get_checked_value(emp_id);

    if(sql == ''){
      alert('Please select employee Id');
      return ;
    }

    if(date == ''){
      alert('Please select date');
      return ;
    }

    if(reason_punish == ''){
      alert('Please enter the reason');
      return ;
    }

    var okyes;
    okyes=confirm('Are you sure you want to Punishment?');
    if(okyes==false){
      return;
    };

    $.ajax({
      url: url_ab,
      type: 'POST',
      data: {
        "date": date,
        "sql": sql,
        "reason_punish": reason_punish,
      },
      success: function(response) {
        if (response == 'Successfully') {
          showSuccessAlert('Record Successfully Updated','no');
        }else{
          showErrorAlert(response);
        }}
    });
  }
</script>

<script>
  $(document).ready(function(){
      $('#reason').hide();
      $('#location').hide();
      $('#extra_field').hide();
      $('.clockpicker').clockpicker();
      var input = $('.timepicker').clockpicker({
          placement: 'bottom',
          align: 'left',
          autoclose: true,
          'default': 'now',
          twelvehour: true // Enable 12-hour format

      });

      $("#back_report").click(function(){
          $('#emp_report').show();
          $('#report_title').show();
          $("#back_report").hide();
          $('#manu_form').hide();
          $("#form_manually").hide();
      });

      $("#checked_value").click(function(){
        var value = document.querySelector("input[type='radio'][name=check_value]:checked").value;
        if (value == 1) {
          $('#reason').show();
          $('#location').show();
          $('#extra_field').show();

        } else {
          $('#reason').hide();
          $('#location').hide();
          $('#extra_field').hide();
        }
        // alert(value);
      });
  });
</script>

<script>
  function present_to_absent(){
    var url_ab = "<?php echo base_url('admin/attendance/present_to_absent'); ?>";
    date = document.getElementById('process_date').value;
    var emp_id = document.getElementsByName('select_emp_id[]');
    var sql = get_checked_value(emp_id);

    if(sql == ''){
      alert('Please select employee Id');
      return ;
    }

    if(date == ''){
      alert('Please select date');
      return ;
    }

    var okyes;
    okyes=confirm('Are you sure you want to Delete?');
    if(okyes==false){
      return;
    };

    $.ajax({
      url: url_ab,
      type: 'POST',
      data: {
        "date": date,
        "sql": sql,
      },
      success: function(response) {
        if (response == 'Successfully') {
          showSuccessAlert('Record Successfully Updated','no');
        }else{
          showErrorAlert(response);
        }}
    });
  }

  function manual_entry_ajax(e){
    var url_b = "<?php echo base_url('admin/attendance/manual_attendance'); ?>";

    status = document.querySelector("input[type='radio'][name=check_value]:checked").value;
    date = document.getElementById('process_date').value;
    in_time = document.getElementById('in_time').value;
    out_time = document.getElementById('out_time').value;
    reason = document.getElementById('reason_value').value;
    locationa = document.getElementById('location_va').value;
    project_name = document.getElementById('project_name').value;
    contact_person = document.getElementById('contact_person').value;

    var emp_id = document.getElementsByName('select_emp_id[]');
    var sql = get_checked_value(emp_id);
    // alert(sql); return;

    if(sql == ''){
      alert('Please select employee Id');
      return ;
    }

    var okyes;
    okyes=confirm('Are you sure you want to Insert?');
    if(okyes==false){
      return;
    };

    $.ajax({
      url: url_b,
      type: 'POST',
      data: {
        "date": date,
        "in_time": in_time,
        "out_time": out_time,
        "reason": reason,
        "location": locationa,
        "sql": sql,
        "status": status,
        "project_name": project_name,
        "contact_person": contact_person
      },
      success: function(response) {
        if (response == 'Successfully') {
          showSuccessAlert('Successfully Added Attendance','no');
        }else{
          showErrorAlert(response);
        }}
    });
  }
</script>
