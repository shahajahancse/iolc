
<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<?php $xuser_info = $this->Xin_model->read_user_info($session['user_id']);?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource();?>

<div class="row m-b-1 <?php echo $get_animate;?>">
  <?php $user_info = $this->Xin_model->read_user_info($session['user_id']);?>
  <div class="col-md-4">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title"> Add Day </h3>
      </div>
      <div class="box-body">
        <?php $attributes = array('name' => 'add_long_day_absent', 'id' => 'xin-form', 'autocomplete' => 'off');?>
        <?php $hidden = array('user_id' => $session['user_id']);?>
        <?php echo form_open('admin/timesheet/add_long_day_absent', $attributes, $hidden);?>
        <div class="row">
          <div class="col-md-12">
            <?php $ecompany_id = $user_info[0]->company_id;?>
            <div class="form-group">
              <label for="first_name"><?php echo $this->lang->line('left_company');?></label>
              <select class="form-control" name="company_id" id="aj_company" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('left_company');?>">
                <option value=""> <?php echo $this->lang->line('xin_select_one');?> </option>
                <?php foreach($get_all_companies as $company) {?>
                  <option <?= $company->company_id == $ecompany_id ? 'selected' : ''; ?> value="<?php echo $company->company_id?>"><?php echo $company->name?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="start_date">Days </label>
              <input class="form-control " placeholder="Enter days" name="day" type="number">
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="start_date">Description </label>
              <textarea name="description" class="form-control" id=""></textarea>
            </div>
          </div>
        </div>

        <div class="form-actions box-footer">
          <button type="submit" class="btn btn-primary"> <i class="fa fa-check-square-o"></i> <?php echo $this->lang->line('xin_save');?> </button>
        </div>
        <?php echo form_close(); ?> </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">List </h3>
      </div>

      <div class="box-body">
        <div class="box-datatable table-responsive">
          <table class="datatables-demo table table-striped table-bordered" id="xin_table_long_day_absent">
            <thead>
              <tr>
                <th><?php echo $this->lang->line('xin_action');?></th>
                <th>Days</th>
                <th>Description</th>
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
