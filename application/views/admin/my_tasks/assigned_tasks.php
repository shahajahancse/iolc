<?php
$session = $this->session->userdata('username');
$get_animate = $this->Xin_model->get_content_animate();
$role_resources_ids = $this->Xin_model->user_role_resource();
?>

<div class="box <?php echo $get_animate;?>">
  <div class="box-header with-border">
    <h3 class="box-title">My Assigned Tasks</h3>
  </div>
  <div class="box-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table_assigned_tasks">
        <thead>
          <tr>
            <th>ID</th>
            <th>Employee</th>
            <th>Client</th>
            <th>Date</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Employee Remark</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    var xin_table_assigned_tasks = $('#xin_table_assigned_tasks').dataTable({
        "bDestroy": true,
        "ajax": {
            url : "<?php echo site_url("admin/my_tasks/assigned_tasks_list") ?>",
            type : 'GET'
        },
        "order": [[ 3, "desc" ]], // Order by the 4th column (Date) in descending order
        "fnDrawCallback": function(settings){
        $('[data-toggle="tooltip"]').tooltip();
        }
    });
});
</script>