<?php
/* Client Attendance List View */
?>
<div class="box box-block bg-white">
  <div class="box-header with-border">
    <h3 class="box-title"> <?php echo $this->lang->line('xin_list_all');?> Client Attendance </h3>
    <div class="box-tools pull-right">
        <button class="btn btn-success btn-sm" onclick="exportExcel()">
            <i class="fa fa-file-excel-o"></i> Export
        </button>
    </div>
  </div>

  <div class="box-body">
      <div class="row">
          <div class="col-md-3">
              <div class="form-group">
                <label for="from_date">From Date</label>
                <input class="form-control date" placeholder="Select Date" readonly id="from_date" name="from_date" type="text" value="<?php echo $from_date; ?>">
              </div>
          </div>
          <div class="col-md-3">
              <div class="form-group">
                <label for="to_date">To Date</label>
                <input class="form-control date" placeholder="Select Date" readonly id="to_date" name="to_date" type="text" value="<?php echo $to_date; ?>">
              </div>
          </div>
          <div class="col-md-3">
              <div class="form-group">
                <label for="employee_id">Employee</label>
                <select class="form-control" name="employee_id" id="employee_id" data-plugin="select_hrm" data-placeholder="All Employees">
                    <option value="">All Employees</option>
                    <?php foreach($all_employees as $employee): ?>
                        <option value="<?php echo $employee->user_id;?>"> <?php echo $employee->first_name.' '.$employee->last_name;?></option>
                    <?php endforeach; ?>
                </select>
              </div>
          </div>
          <div class="col-md-3">
               <div class="form-group">
                <label class="d-block">&nbsp;</label>
                <button type="button" onclick="filterData()" class="btn btn-primary btn-block"><i class="fa fa-search"></i> Filter</button>
               </div>
          </div>
      </div>
  </div>
  
  <div class="box-body">
    <div class="table-responsive" data-pattern="priority-columns" id="attendance_table_area">
        <div class="text-center p-4">Loading data...</div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    $('.date').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        yearRange: '1900:' + (new Date().getFullYear() + 15)
    });
    
    // Load initial data
    filterData();
});

function filterData() {
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    var employee_id = $('#employee_id').val();
    
    $('#attendance_table_area').html('<div class="text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    
    $.ajax({
        url: '<?php echo site_url("admin/client_attendance/get_list"); ?>',
        type: 'POST',
        data: {
            from_date: from_date,
            to_date: to_date,
            employee_id: employee_id
        },
        success: function(response) {
            $('#attendance_table_area').html(response);
        },
        error: function() {
            $('#attendance_table_area').html('<div class="alert alert-danger">Error loading data.</div>');
        }
    });
}

function exportExcel() {
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    var employee_id = $('#employee_id').val();
    
    var url = '<?php echo site_url("admin/client_attendance/export"); ?>?from_date='+from_date+'&to_date='+to_date+'&employee_id='+employee_id;
    window.location.href = url;
}
</script>
