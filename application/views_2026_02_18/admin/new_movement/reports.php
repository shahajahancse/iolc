<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Movement Reports</h3>
            </div>
            <div class="box-body">
                <form id="reportForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Employee (Optional)</label>
                                <select name="user_id" id="user_id" class="form-control select2">
                                    <option value="">All Employees</option>
                                    <?php foreach($employees as $emp): ?>
                                        <option value="<?php echo $emp->user_id; ?>"><?php echo $emp->first_name . ' ' . $emp->last_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-success btn-block" id="genBtn"><i class="fa fa-filter"></i> Generate Report</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Report Results</h3>
                 <div class="box-tools pull-right">
                    <button class="btn btn-default btn-sm" onclick="printDiv('report-content')"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
            <div class="box-body" id="report-content">
                <p class="text-center text-muted">Select filters and click "Generate Report" to view data.</p>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2();

    $('#reportForm').submit(function(e) {
        e.preventDefault();
        var btn = $('#genBtn');
        var originalText = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> Generating...').prop('disabled', true);

        $.ajax({
            url: '<?php echo site_url("new_movement/generate_report"); ?>',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function(response) {
                if(response.status) {
                    $('#report-content').html(response.html);
                } else {
                    alert(response.message);
                }
                btn.html(originalText).prop('disabled', false);
            },
            error: function() {
                alert('Error generating report');
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
});

function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;
     document.body.innerHTML = printContents;
     window.print();
     document.body.innerHTML = originalContents;
     // Reload to restore events
     location.reload(); 
}
</script>
