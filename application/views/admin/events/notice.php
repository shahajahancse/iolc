<?php
/* Holidays view
*/
?>
<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<div class="row m-b-1 <?php echo $get_animate;?>">
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php if(in_array('269',$role_resources_ids)) {?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']);?>
  <div class="col-md-4">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title"> Add New Notice </h3>
      </div>
      <div class="box-body">
        <?php $attributes = array('name' => 'add_notice', 'id' => 'xin-notice', 'autocomplete' => 'off');?>
        <?php $hidden = array('user_id' => $session['user_id']);?>
        <?php echo form_open('admin/events/add_notice', $attributes, $hidden);?>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="event_title">Notice Title </label>
              <input type="text" class="form-control" name="notice_title" placeholder="notice title">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="notice_date">Date </label>
              <input type="text" class="form-control date" name="notice_date" placeholder="notice date">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="status">Status </label>
              <select class="form-control" name="status">
                <option value="1">Published</option>
                <option value="0">Unpublished</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Participant Type</label><br>
                    <label class="radio-inline">
                        <input type="radio" name="participant_type" value="1" checked onclick="toggleParticipants(1)"> All Employees
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="participant_type" value="2" onclick="toggleParticipants(2)"> Specific Employees
                    </label>
                </div>
            </div>
        </div>

        <div class="row" id="participants_row" style="display:none;">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="participants">Select Employees</label>
                    <select class="form-control" multiple="multiple" name="participants[]" data-plugin="select_hrm" data-placeholder="Select Employees">
                        <?php foreach($all_employees as $employee): ?>
                            <option value="<?php echo $employee->user_id; ?>">
                                <?php echo $employee->first_name . ' ' . $employee->last_name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="event_note">Notice Description</label>
              <textarea class="form-control textarea" placeholder="Notice Description" name="description"></textarea>
            </div>
          </div>
        </div>
        <div class="form-actions box-footer">
          <button type="submit" class="btn btn-primary"> <i class="fa fa-check-square-o"></i> <?php echo $this->lang->line('xin_save');?> </button>
        </div>
        <?php echo form_close(); ?> </div>
    </div>
  </div>
  <?php $colmdval = 'col-md-8';?>
  <?php } else {?>
  <?php $colmdval = 'col-md-12';?>
  <?php } ?>

  <div class="<?php echo $colmdval;?>">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title"> Notice List </h3>
      </div>
      <div class="box-body">
      <div class="box-datatable table-responsive">
        <table class="datatables-demo table table-striped table-bordered" id="xin_notice">
          <thead>
            <tr>
              <th style="width:100px;"><?php echo $this->lang->line('xin_action');?></th>
              <th>Title</th>
              <th>Description</th>
              <th>Date</th>
            </tr>
          </thead>
        </table>
      </div>
      </div>
    </div>
  </div>
</div>
<style type="text/css">
.trumbowyg-editor { min-height:110px !important; }
</style>
<script type="text/javascript">
function toggleParticipants(type) {
    if (type == 2) {
        $('#participants_row').show();
    } else {
        $('#participants_row').hide();
    }
}
</script>
