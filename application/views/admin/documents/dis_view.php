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
      <h3 class="box-title"><?php echo $this->lang->line('xin_add_new');?> Discipline</h3>
      <div class="box-tools pull-right"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
        <button id="add_discipline" type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new');?></button>
        </a> </div>
    </div>
    <div id="add_form" class="collapse add-form <?php echo $get_animate;?>" data-parent="#accordion" style="">
      <div class="box-body">
       <?php $attributes = array('name' => 'add_discipline', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
        <?php $hidden = array('user_id' => $session['user_id']); ?>
        <?php echo form_open('admin/documents/save_discipline', $attributes, $hidden); ?>
        <input type="hidden" name="discipline_id" id="discipline_id">
        <div class="bg-white">
          <div class="box-block">
            <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="code">Code</label>
                    <input type="text" class="form-control" name="code" id="code" placeholder="Enter Code" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="status">Status</label><br>
                    <input type="radio" name="status" id="status_active" value="1" required> Active
                    <input type="radio" name="status" id="status_inactive" value="0" required> Inactive
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" placeholder="Enter Description" rows="3"></textarea>
                  </div>
                </div>
            </div>


            <div class="form-actions box-footer">
              <button type="submit" class="btn btn-primary">
                <i class="fa fa-check-square-o"></i> <?php echo $this->lang->line('xin_save');?>
              </button>
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

<?php $all_discipline = $this->db->get('disciplines')->result(); ?>



<div class="box <?php echo $get_animate;?>">
  <div class="box-header with-border">
    <h3 class="box-title"> <?php echo $this->lang->line('xin_list_all');?> Displines </h3>
  </div>
  <div class="box-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="">
        <thead>
          <tr>
              <th>SL.</th>
              <th>Code</th>
              <th>Title</th>
              <th>Status</th>
              <th>Description</th>
              <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
              <?php if(!empty($all_discipline)): ?>
                <?php $sl = 1; foreach($all_discipline as $discipline): ?>
                  <tr>
                    <td><?php echo $sl++; ?></td>
                    <td><?php echo htmlspecialchars($discipline->code); ?></td>
                    <td><?php echo htmlspecialchars($discipline->title); ?></td>
                    <td>
                      <?php if($discipline->status == 1): ?>
                        <span class="badge badge-success" style="background-color: #28a745; color: #fff;">Active</span>
                      <?php else: ?>
                        <span class="badge badge-secondary" style="background-color: #6c757d; color: #fff;">Inactive</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($discipline->description); ?></td>
                    <td>
                      <button onclick="edit_discipline(<?php echo $discipline->id; ?>)"  class="btn btn-xs btn-primary">Edit</button>
                      <a href="<?php echo site_url('admin/documents/delete_discipline/'.$discipline->id); ?>" class="btn btn-xs btn-danger">Delete</a>
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
  function edit_discipline(id) {
    $.ajax({
      url: '<?php echo site_url('admin/documents/get_discipline'); ?>',
      type: 'POST',
      data: { id: id },
      dataType: 'json',
      success: function(res) {
        if (res.result === 'success') {
          var d = res.data;

          $('#add_form').collapse('show'); // open form
          $('#code').val(d.code);
          $('#title').val(d.title);
          $('#description').val(d.description);
          $('input[name="status"][value="' + d.status + '"]').prop('checked', true);

          $('#discipline_id').val(d.id); // set hidden id
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