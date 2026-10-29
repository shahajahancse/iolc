<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<style>
.table {
    width: 92%;
    margin-bottom: 14px;
}
</style>
<?php if($user_role_id !=3) {?>
<?php if($success = $this->session->flashdata('success')):?>
<div class="alert alert-success" id="flash_message">
    <?php echo $success?>
</div>
<?php endif; ?>
<script>
$(function() {
    $("#flash_message").hide(2000);
});
</script>
<?php if($warning = $this->session->flashdata('warning')):?>
<div class="alert alert-warning" id="flash_message1">
    <?php echo $warning;?>
</div>
<?php endif; ?>
<script>
$(function() {
    $("#flash_message1").hide(2000);
});
</script>
<?php }?>
<div class="box <?php echo $get_animate;?>" style="margin-top:20px">
    <div class="box-header with-border">
        <h3 class="box-title">Product Purches Requisition List</h3>
    </div>
    <div class="box-body">
        <div class="box-datatable">
            <input type="hidden" value="1" id="count">
            <table class="datatables-demo table table-striped table-bordered" id="purchase_table" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width:20px;">No.</th>
                        <th class="text-center" style="width:100px;">Requisition By</th>
                        <th class="text-center" style="width:20px;">Product Name</th>
                        <th class="text-center" style="width:20px;">Stock Quantity</th>
                        <th class="text-center" style="width:20px;">Req. Quantity</th>
                        <th class="text-center" style="width:20px;">Appr. Quantity</th>
                        <th class="text-center" style="width:20px;">Status</th>
                        <th class="text-center" style="width:20px;">Request Date</th>
                        <th class="text-center" style="width:50px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  foreach ($products as $key => $rows) { ?>
                    <tr>
                        <td class="text-center"><?php echo($key+1)."."; ?></td>
                        <td class="text-center"><?php echo $rows->first_name." ".$rows->last_name; ?></td>
                        <td class="text-center"><?php echo $rows->product_name ?></td>
                        <td class="text-center"><?php echo $rows->stock_qty ?></td>
                        <td class="text-center"><?php echo $rows->quantity ?></td>
                        <td class="text-center"><input type="number" class="form-control" id="app_qty<?= $rows->id ?>" value="<?= $rows->quantity ?>"></td>
                        <td class="text-left">
                            <select name="status" id="a_status<?= $rows->id ?>">
                            <option value="2">Approved</option>
                            <option value="4">Rejected</option>
                            </select>
                        </td>
                        <td class="text-center"><?php echo date('d-m-Y', strtotime($rows->created_at)); ?></td>
                        <td class="text-left"><a class="btn btn-sm btn-primary" onclick="approve_req(this, <?= $rows->id ?>)">Submit</a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
  function approve_req(e, id) {
    var a_quantity = $("#app_qty"+id).val();
    var a_status = $("#a_status"+id).val();

    $.ajax({
      url: '<?php echo site_url("admin/inventory/product_approved_ajax"); ?>',
      type: 'POST',
      data: {
        id: id,
        a_quantity: a_quantity,
        a_status: a_status,
      },
      success: function(response) {
        var jsonData = JSON.parse(response);
        if (jsonData.status === 'success') {
          alert(jsonData.msg);
          e.closest('tr').remove();
        } else {
          alert(jsonData.msg);
        }
      },
      error: function(xhr, status, error) {
        alert("Error submitting the form. Please try again. Error" );
      }
    });
  }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#purchase_table').DataTable();
    });
</script>
