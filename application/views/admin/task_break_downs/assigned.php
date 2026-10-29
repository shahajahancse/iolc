<?php
$session = $this->session->userdata('username');
$get_animate = $this->Xin_model->get_content_animate();
$role_resources_ids = $this->Xin_model->user_role_resource();
?>

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


<script type="text/javascript">
    $(document).ready(function() {
        var xin_table = $('#xin_table').dataTable({
            "bDestroy": true,
            "ajax": {
                url : "<?php echo site_url("admin/task_break_downs/task_assigned_list") ?>",
                type : 'GET'
            },
            "fnDrawCallback": function(settings){
                $('[data-toggle="tooltip"]').tooltip();
            },
            "order": [[ 3, "desc" ]]
        });

        $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
        $('[data-plugin="select_hrm"]').select2({ width: '100%' });
        $('.date').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:'yy-mm-dd',
            yearRange: '2020:2030'
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
    });
</script>
