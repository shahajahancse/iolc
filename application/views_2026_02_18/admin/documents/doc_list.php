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
      <h3 class="box-title"><?php echo $this->lang->line('xin_add_new');?> </h3>
      <div class="box-tools pull-right"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
        <button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span> <?php echo $this->lang->line('xin_add_new');?></button>
        </a> </div>
    </div>
    <div id="add_form" class="collapse add-form <?php echo $get_animate;?>" data-parent="#accordion" style="">
      <div class="box-body">
        <?php $attributes = array('name' => 'add_warning', 'id' => 'xin-form', 'autocomplete' => 'off');?>
        <?php $hidden = array('user_id' => $session['user_id']);?>
        <?php echo form_open('admin/documents/save_document', $attributes, $hidden);?>
        <input type="hidden" name="document_id" id="document_id">

        <div class="bg-white">
          <div class="box-block">
            <div class="row">
                <div class="col-md-3">
                <div class="form-group">
                  <label for="code">Select Discipline</label>
                  <select class="form-control" name="discipline_code" id="discipline_code" >
                    <option value="">-- Select Discipline --</option>
                    <?php
                      $disciplines = $this->db->get('disciplines')->result();
                      foreach($disciplines as $discipline) {
                        echo '<option value="'.$discipline->code.'">'.$discipline->code.'</option>';
                      }
                    ?>
                  </select>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group">
                  <label for="code">Select Classification Code</label>
                  <select class="form-control" name="class_code" id="class_code" >
                    <option value="">-- Select --</option>
                  </select>
                </div>
              </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="description">Title</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Enter the Title" >
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="description">Client</label>
                    <input type="text" class="form-control" name="user" id="user" placeholder="Enter the Client" >
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="description">Issue/Submit Date</label>
                    <input type="date" class="form-control" name="issue_date" id="issue_date" placeholder="Enter the Issue/Submit Date" >
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" placeholder="Enter Description" rows="3"></textarea>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label for="description">Status</label><br>
                    <input type="radio" name="status" id="status_active" value="1" > Active
                    <input type="radio" name="status" id="status_inactive" value="0" > Inactive
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

<?php $all_documents = $this->db->get('discipline_documents')->result(); ?>



<div class="box <?php echo $get_animate;?>">
  <div class="box-header with-border">
    <h3 class="box-title"> <?php echo $this->lang->line('xin_list_all');?>  &nbsp;&nbsp; <span style="font-size: 14px; color: #972005; font-weight: bold;">(D.code = Discipline Code, C.code = Classification Code)</span> </h3>
  </div>
  <div class="box-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="doc_table">
        <thead>
          <tr>
              <th>SL.</th>
              <th>D.code</th>
              <th>C.code</th>
              <th>Reference</th>
              <th>Rev#</th>
              <th style="width:345px">Description</th>
              <th>Status</th>
              <th>Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#doc_table').DataTable();
  });
</script>

<script>
  function edit_document(id) {
    $.ajax({
      url: '<?php echo site_url('admin/documents/get_discipline_document'); ?>', // better naming
      type: 'POST',
      data: { id: id },
      dataType: 'json',
      success: function(res) {
        if (res.result === 'success') {
          var d = res.data;

          // open form
          $('#add_form').collapse('show');

          // fill form fields
          $('#document_id').val(d.id);
          $('#discipline_code').val(d.discipline_code).trigger('change'); // trigger to reload classifications
          $('#class_code').val(d.class_code).trigger('change'); // trigger to reload classifications
          $('#title').val(d.title);
          $('#user').val(d.user);
          $('#issue_date').val(d.issue_date);
          $('#description').val(d.description);
          $('input[name="status"][value="' + d.status + '"]').prop('checked', true);

          // wait until classifications load then set selected
          setTimeout(function () {
            $('#class_code').val(d.class_code);
          }, 500);

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



<script>
  $(document).ready(function() {
    $('#discipline_code').change(function() {
      var id = $(this).val();
      if(id != '') {
        $.ajax({
          url: '<?php echo base_url("admin/documents/get_classifications"); ?>',
          type: 'POST',
          data: {id: id},
          dataType: 'json',
          success: function(response) {

            $('#class_code').html('<option value="">-- Select Classification Code --</option>');
            $.each(response, function(index, data) {
              $('#class_code').append('<option value="'+data.class_code+'">'+data.class_code+'</option>');
            });
          }
        });
      } else {
          $('#class_code').html('<option value="">-- Select Classification Code --</option>');
      }
    });
  });
</script>
