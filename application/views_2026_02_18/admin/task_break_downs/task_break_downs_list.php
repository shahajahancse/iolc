<?php
$session = $this->session->userdata('username');
$get_animate = $this->Xin_model->get_content_animate();
$role_resources_ids = $this->Xin_model->user_role_resource();
?>

<div class="box mb-4 <?php echo $get_animate;?>">
  <div id="accordion">
    <div class="box-header with-border">
      <h3 class="box-title">Add New Task Breakdown</h3>
      <div class="box-tools pull-right">
        <a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
        <button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> Add New</button>
        </a> </div>
    </div>
    <div id="add_form" class="collapse add-form <?php echo $get_animate;?>" data-parent="#accordion" style="">
      <div class="box-body">
        <?php $attributes = array('name' => 'add_task_break_down_batch', 'id' => 'xin-form-batch', 'autocomplete' => 'off');?>
        <?php echo form_open('admin/task_break_downs/add_task_break_down_batch', $attributes);?>
        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="emp_id">Employee</label>
                        <select name="emp_id" class="form-control" data-plugin="select_hrm" data-placeholder="Choose an Employee">
                            <option value=""></option>
                            <?php foreach($all_employees as $employee) {?>
                            <option value="<?php echo $employee->user_id;?>"> <?php echo $employee->first_name.' '.$employee->last_name;?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                      <label for="date">Date</label>
                      <div style="display: flex; gap: 10px">
                        <input class="form-control date" placeholder="Date" name="date" type="text">
                        <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#add_client_modal">Add New Client</button>
                        <button type="button" class="btn btn-primary" id="add_row">Add Row</button>
                      </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-hover" id="task_breakdown_table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Employee Remark</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="form-actions box-footer">
            <?php echo form_button(array('name' => 'hrsale_form', 'type' => 'submit', 'class' => $this->Xin_model->form_button_class(), 'content' => '<i class="fa fa fa-check-square-o"></i> Save')); ?>
        </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>
<div class="box <?php echo $get_animate;?>">
  <div class="box-header with-border">
    <h3 class="box-title">List All Task Breakdowns</h3>
  </div>
  <div class="box-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Employee</th>
            <th>Client</th>
            <th>Date</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Employee Remark</th>
            <th>Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<div class="modal fade edit-modal-data" id="edit-modal-data" tabindex="-1" role="dialog" aria-labelledby="edit-modal-data" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" id="ajax_modal">
    </div>
  </div>
</div>

<div class="modal fade delete-task-modal" id="delete-task-modal" tabindex="-1" role="dialog" aria-labelledby="delete-task-modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
      </div>
      <div class="modal-body">
        <p>Do you want to delete this record?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm_delete">Delete</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add_client_modal" tabindex="-1" role="dialog" aria-labelledby="add_client_modal_label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_client_modal_label">Add New Client</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php $attributes = array('name' => 'add_client_modal_form', 'id' => 'add_client_modal_form', 'autocomplete' => 'off');?>
        <?php echo form_open('admin/clients/add_client_modal', $attributes);?>
          <div class="form-group">
            <label for="name">Client/Company Name</label>
            <input type="text" class="form-control" name="name" placeholder="Enter Name">
          </div>
          <div class="form-group">
            <label for="country">Address</label>
            <input type="text" class="form-control" name="address" placeholder="Enter Address">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" placeholder="Enter Email">
          </div>
        <?php echo form_close(); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="save_client_modal">Save</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    var xin_table = $('#xin_table').dataTable({
        "bDestroy": true,
        "ajax": {
            url : "<?php echo site_url("admin/task_break_downs/task_break_downs_list") ?>",
            type : 'GET'
        },
                "fnDrawCallback": function(settings){
                $('[data-toggle="tooltip"]').tooltip();
                },
                "order": [[ 3, "desc" ]]    });

    $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
    $('[data-plugin="select_hrm"]').select2({ width: '100%' });
    $('.date').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat:'yy-mm-dd',
        yearRange: '2020:2030'
    });

    var client_options = '';
    <?php foreach($all_clients->result() as $client) {?>
        client_options += '<option value="<?php echo $client->client_id;?>"><?php echo $client->name;?></option>';
    <?php } ?>

    var status_options = '<option value="pending">Pending</option><option value="on process">On Process</option><option value="complete">Complete</option><option value="in-complete">Incomplete</option><option value="cancel">Cancel</option>';

    // Add row
    $('#add_row').click(function() {
        var new_row = '<tr>' +
            '<td><select name="client_id[]" class="form-control client-select" data-plugin="select_hrm" data-placeholder="Choose a Client">' + client_options + '</select></td>' +
            '<td><select name="status[]" class="form-control" data-plugin="select_hrm" data-placeholder="Status">' + status_options + '</select></td>' +
            '<td><textarea class="form-control" placeholder="Remarks" name="remarks[]" rows="1"></textarea></td>' +
            '<td><textarea class="form-control" placeholder="Employee Remark" name="emp_remark[]" rows="1"></textarea></td>' +
            '<td><button type="button" class="btn btn-danger remove_row">X</button></td>' +
            '</tr>';
        $('#task_breakdown_table tbody').append(new_row);
        $('#task_breakdown_table tbody tr:last .client-select').select2({ width: '100%' });
        $('#task_breakdown_table tbody tr:last select[name="status[]"]').select2({ width: '100%' });
    });

    // Remove row
    $(document).on('click', '.remove_row', function() {
        $(this).closest('tr').remove();
    });

    /* Add data */ /*Form Submit*/
    $("#xin-form-batch").submit(function(e){
        e.preventDefault();
        var obj = $(this), action = obj.attr('name');
        $('.save').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('admin/task_break_downs/add_task_break_down_batch'); ?>",
            data: obj.serialize()+"&is_ajax=1&add_type=task_break_down_batch&form="+action,
            cache: false,
            success: function (JSON) {
                if (JSON.error != '') {
                    toastr.error(JSON.error);
                    $('.save').prop('disabled', false);
                } else {
                    xin_table.api().ajax.reload(function(){
                        toastr.success(JSON.result);
                    }, true);
                    $('.add-form').removeClass('in');
                    $('.save').prop('disabled', false);
                    // Clear the form
                    $('#xin-form-batch')[0].reset();
                    $('#task_breakdown_table tbody').html('');
                }
            }
        });
    });

    // edit
    $('.edit-modal-data').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var modal = $(this);
        $.ajax({
            url : "<?php echo site_url('admin/task_break_downs/read'); ?>",
            type: "GET",
            data: 'jd=1&is_ajax=1&mode=modal&data=task_break_down&id='+id,
            success: function (response) {
                if(response) {
                    $("#ajax_modal").html(response);
                }
            }
        });
    });

    /* Delete data */
	$(document).on("click", ".delete", function(){
		$('#delete-task-modal').data('record-id', $(this).data('record-id'));
	});

    $(document).on('click', '#confirm_delete', function(e){
        var id = $('#delete-task-modal').data('record-id');
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('admin/task_break_downs/delete'); ?>",
            data: { _token: id, is_ajax: 2, type: 'delete', form: 'delete_record' },
            cache: false,
            success: function (JSON) {
              if (JSON.error != '') {
                toastr.error(JSON.error);
              } else {
                $('.delete-task-modal').modal('toggle');
                xin_table.api().ajax.reload(function(){
                  toastr.success(JSON.result);
                }, true);
              }
            }
        });
    });

    $(document).on('click', '#save_client_modal', function(e) {
        var form = $('#add_client_modal_form');
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('admin/clients/add_client_modal'); ?>",
            data: form.serialize() + "&add_type=client_modal",
            cache: false,
            success: function (JSON) {
              if (JSON.error != '') {
                  toastr.error(JSON.error);
              } else {
                var new_option_html = '<option value="' + JSON.client_id + '">' + JSON.client_name + '</option>';
                // Update the client_options variable
                client_options += new_option_html;

                $('.client-select').each(function() {
                  $(this).append(new_option_html);
                });

                // Reinitialize select2 on the newly added row
                var last_row = $('.client-select').last();
                last_row.val(JSON.client_id).trigger('change');
                last_row.select2({ width: '100%' }); // Re-initialize select2

                // Close the modal
                $('#add_client_modal').modal('toggle');
                toastr.success(JSON.result);

                // Clear the form
                $('#add_client_modal_form')[0].reset();
              }
            }
        });
    });

    $('.edit-modal-data').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
    });

    $('#add_client_modal').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
    });
});
</script>
