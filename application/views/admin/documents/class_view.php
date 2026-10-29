<?php
/* Warning view
*/
?>
<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
<?php if(in_array('225',$role_resources_ids)) {?>
<?php $user_info = $this->Xin_model->read_user_info($session['user_id']);?>
<div class="box mb-4 <?php echo $get_animate;?>">
  <div id="accordion">
    <div class="box-header  with-border">
      <h3 class="box-title"><?php echo $this->lang->line('xin_add_new');?> Classification</h3>
      <div class="box-tools pull-right"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
        <button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new');?></button>
        </a> </div>
    </div>
    <div id="add_form" class="collapse add-form <?php echo $get_animate;?>" data-parent="#accordion" style="">
      <div class="box-body">
        <?php $attributes = array('name' => 'add_warning', 'id' => 'xin-form', 'autocomplete' => 'off');?>
        <?php $hidden = array('user_id' => $session['user_id']);?>
        <?php echo form_open('admin/documents/save_classification', $attributes, $hidden);?>
        <input type="hidden" name="classification_id" id="classification_id">

        <div class="bg-white">
          <div class="box-block">
            <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="code">Select Discipline Code</label>
                    <select class="form-control" name="discipline_code" id="discipline_code" required>
                      <option value="">-- Select Discipline Code --</option>
                      <?php
                        $disciplines = $this->db->get('disciplines')->result();
                        foreach($disciplines as $discipline) {
                          echo '<option value="'.htmlspecialchars($discipline->code).'">'.htmlspecialchars($discipline->code).'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="class_code">Classification Code</label>
                    <input type="text" class="form-control" name="class_code" id="class_code" placeholder="Enter Code" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="description">Title</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" placeholder="Enter Description" rows="3"></textarea>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="description">Status</label><br>
                    <input type="radio" name="status" id="status_active" value="1" required> Active
                    <input type="radio" name="status" id="status_inactive" value="0" required> Inactive
                  </div>
                </div>

            </div>

            <div class="form-actions box-footer">
              <button type="submit" class="btn btn-primary"> <i class="fa fa-check-square-o"></i> <?php echo $this->lang->line('xin_save');?> </button>
            </div>
          </div>
        </div>
        <?php echo form_close(); ?> </div>
    </div>
  </div>
</div>
<?php } ?>


<?php if($this->session->flashdata('success')): ?>
  <div class="alert alert-success" role="alert">
    <?php echo $this->session->flashdata('success'); ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
  <div class="alert alert-danger" role="alert">
    <?php echo $this->session->flashdata('error'); ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php endif; ?>

<script>
  setTimeout(function() {
    $('.alert').alert('close');
  }, 4000);
</script>

<?php $all_classification = $this->db->get('discipline_classification')->result(); ?>



<div class="box <?php echo $get_animate;?>">
  <div class="box-header with-border">
    <h3 class="box-title"> <?php echo $this->lang->line('xin_list_all');?> Classifications </h3>
  </div>
  <div class="box-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
              <th>SL.</th>
              <th>Discipline Code</th>
              <th>Classification Code</th>
              <th>Title</th>
              <th>Status</th>
              <th>Description</th>
              <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
              <?php if(!empty($all_classification)): ?>
                <?php $sl = 1; foreach($all_classification as $classification): ?>
                  <tr>
                    <td><?php echo $sl++; ?></td>
                    <td><?php echo htmlspecialchars($classification->discipline_code); ?></td>
                    <td><?php echo htmlspecialchars($classification->class_code); ?></td>
                    <td><?php echo htmlspecialchars($classification->title); ?></td>
                    <td>
                      <?php if($classification->status == 1): ?>
                        <span class="badge badge-success" style="background-color: #28a745; color: #fff;">Active</span>
                      <?php else: ?>
                        <span class="badge badge-secondary" style="background-color: #6c757d; color: #fff;">Inactive</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($classification->description); ?></td>
                    <td>
                      <button onclick="edit_classification(<?php echo $classification->id; ?>)"  class="btn btn-xs btn-primary">Edit</button>
                      <a href="<?php echo site_url('admin/documents/delete_classification/'.$classification->id); ?>" class="btn btn-xs btn-danger">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">No records found.</td>
                </tr>
              <?php endif; ?>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>



<script>
  function edit_classification(id) {
    $.ajax({
      url: '<?php echo site_url('admin/documents/get_classification'); ?>',
      type: 'POST',
      data: { id: id },
      dataType: 'json',
      success: function(res) {
        if (res.result === 'success') {
          var d = res.data;

          $('#add_form').collapse('show'); // open form
          $('#discipline_code').val(d.discipline_code);
          $('#class_code').val(d.class_code);
          $('#title').val(d.title);
          $('#description').val(d.description);
          $('input[name="status"][value="' + d.status + '"]').prop('checked', true);

          $('#classification_id').val(d.id); // set hidden id
        } else {
          alert(res.message || 'Something went wrong.');
        }
      },
      error: function(xhr, status, error) {
        console.error(error);
        alert('AJAX error: ' + error);
      }
    });
  }
</script>
