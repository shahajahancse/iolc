<?php
defined('BASEPATH') OR exit('No direct script access allowed');
if(isset($_GET['jd']) && isset($_GET['id']) && $_GET['data']=='task_break_down') {
?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
  <h4 class="modal-title" id="edit-modal-data">Edit Task Breakdown</h4>
</div>
<?php $attributes = array('name' => 'edit_task_break_down', 'id' => 'edit_task_break_down', 'autocomplete' => 'off', 'class'=>'m-b-1');?>
<?php $hidden = array('_method' => 'EDIT', '_token' => $id, 'ext_name' => $id);?>
<?php echo form_open('admin/task_break_downs/update/'.$id, $attributes, $hidden);?>
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="emp_id">Employee</label>
          <select name="emp_id" class="form-control" data-plugin="select_hrm" data-placeholder="Choose an Employee">
            <option value=""></option>
            <?php foreach($all_employees as $employee) {?>
            <option value="<?php echo $employee->user_id;?>" <?php if($emp_id==$employee->user_id):?> selected <?php endif;?>> <?php echo $employee->first_name.' '.$employee->last_name;?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="client_id">Client</label>
          <select name="client_id" class="form-control" data-plugin="select_hrm" data-placeholder="Choose a Client">
            <option value=""></option>
            <?php foreach($all_clients->result() as $client) {?>
            <option value="<?php echo $client->client_id;?>" <?php if($client_id==$client->client_id):?> selected <?php endif;?>> <?php echo $client->name;?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="date">Date</label>
          <input class="form-control" placeholder="Date" name="date" type="text" value="<?php echo $date;?>" readonly="readonly" />
        </div>
      </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" class="form-control" data-plugin="select_hrm" data-placeholder="Status">
                    <option value="pending" <?php if($status=='pending'):?> selected <?php endif;?>>Pending</option>
                    <option value="on process" <?php if($status=='on process'):?> selected <?php endif;?>>On Process</option>
                    <option value="complete" <?php if($status=='complete'):?> selected <?php endif;?>>Complete</option>
                    <option value="in-complete" <?php if($status=='in-complete'):?> selected <?php endif;?>>Incomplete</option>
                    <option value="cancel" <?php if($status=='cancel'):?> selected <?php endif;?>>Cancel</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="remarks">Remarks</label>
          <textarea class="form-control" placeholder="Remarks" name="remarks" rows="3"><?php echo $remarks;?></textarea>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="emp_remark">Employee Remark</label>
          <textarea class="form-control" placeholder="Employee Remark" name="emp_remark" rows="3"><?php echo $emp_remark;?></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Update</button>
  </div>
<?php echo form_close(); ?>
<script type="text/javascript">
 $(document).ready(function(){

	$('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
	$('[data-plugin="select_hrm"]').select2({ width: '100%' });
	$('.date').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat:'yy-mm-dd',
		yearRange: '-50:+0'
	});

	$("#edit_task_break_down").submit(function(e){
		e.preventDefault();
		var obj = $(this), action = obj.attr('name');
		$('.save').prop('disabled', true);

		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize()+"&is_ajax=1&edit_type=task_break_down&form="+action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != '') {
					toastr.error(JSON.error);
					$('.save').prop('disabled', false);
				                } else {
									$('.edit-modal-data').modal('toggle');
									// Reload the datatable
									$('#xin_table').DataTable().ajax.reload(function(){
										toastr.success(JSON.result);
									}, true);
									$('.save').prop('disabled', false);
								}			}
		});
	});
});
</script>
<?php } ?>
