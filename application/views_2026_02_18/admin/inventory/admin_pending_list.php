<?php
  // dd($products);
  $session = $this->session->userdata('username');
  $get_animate = $this->Xin_model->get_content_animate();
?>
<style>
  .table {
    width: 92%;
    margin-bottom: 14px;
  }

  .using {
      display: inline-flex;
      padding: 4.5px 14.3px 5.5px 9px;
      align-items: center;
      gap: 9px;
      border-radius: 50px;
      border: 1px solid #CCC;
      background: #FFF;
      font-size:12px
  }
  .d-hidden {
    display: none !important;
  }
</style>
<?php if($this->session->flashdata('success')):?>
  <div class="alert alert-success" id="flash_message">
    <?php echo $this->session->flashdata('success');?>
  </div>
<?php endif; ?>
<script>
  $(function() {$("#flash_message").hide(2000);});
</script>
<?php if($this->session->flashdata('warning')):?>
  <div class="alert alert-warning"id="flash_message1">
    <?php echo $this->session->flashdata('warning');?>
  </div>
<?php endif; ?>
<script>
  $(function() {$("#flash_message1").hide(2000);});
</script>




<div class="box <?php echo $get_animate;?>" style="margin-top:20px">
  <div class="box-header with-border">
    <h3 class="box-title">
      <?php
        if(!empty($products)) {
          echo $products[0]->status == 4 ? "Rejected" : ( $products[0]->status == 2 ? "Approved":( $products[0]->status == 3 ? "Recived" :  "Pending" ));
        } else{
          echo "Rejected";
        }
      ?>
      List &nbsp;&nbsp;&nbsp;  <span style="font-size: 14px; font-weight: bold; color: #e76060;"> r.Qty = Request Quantity, a.Qty = Approve Quantity, s.Qty = Stock Quantity, R.Date = Requisition Date </span>
    </h3>
  </div>
  <div class="box-body">
    <div class="box-datatable " >
    <input type="hidden" value="1" id="count">
      <table class="datatables-demo table table-striped table-bordered" id="purchase_table" style="width:100%">
        <thead>
          <tr>
            <th class="text-left" style="width:40px;">No.</th>
              <th class="text-left" style="width:150px;">Requisition By</th>
              <th class="text-left" style="width:50px;">Item_Name</th>
              <th class="text-center" style="width:20px;">s.Qty</th>
              <th class="text-center" style="width:40px;">r.Qty</th>
              <th class="text-center" style="width:20px;">a.Qty</th>
              <th class="text-left" style="width:20px;">Status</th>
              <th class="text-left" style="width:20px;">R.Date</th>
              <th class="text-left" style="width:20px;">Purpose</th>
              <th class="text-left" style="width:50px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $key => $rows) {
            if ($rows->status == 5 ) {
              $status = "<span class='using' style='color:#28a745'>First Step Approved</span>";
            } else if ($rows->status == 1 ) {
              $status = "<span class='using' style='color:#ffc107'> <i class='fa fa-dot-circle-o'></i> Pending</span>";
            } else if ($rows->status == 2 ) {
              $status = "<span class='using' style='color:#28a745'> <i class='fa fa-dot-circle-o'></i> Approved</span>";
            } else if ($rows->status == 3 ) {
              $status = "<span class='using' style='color:#087a58'> <i class='fa fa-dot-circle-o'></i> Handover</span>";
            } else if ($rows->status == 4 ) {
              $status = "<span class='using' style='color:#d56666'> <i class='fa fa-dot-circle-o'></i> Rejected</span>";
            } else if ($rows->status == 6 ) {
              $status = "<span class='using' style='color:#0b70ed'> <i class='fa fa-dot-circle-o'></i> Admin Approved</span>";
            }
          ?>
            <tr>
              <td class="text-left"><?php echo ($key+1)."."; ?></td>
              <td class="text-left"><?php echo $rows->first_name." ".$rows->last_name; ?></td>
              <td class="text-left"><?= $rows->product_name?></td>
              <td class="text-center"><?= $rows->stock_qty?></td>
              <td class="text-center"><?= $rows->quantity?></td>

              <td class="text-center"><input type="number" id="app_qty<?= $rows->id ?>" min="1" style="width:100px" value="<?= $rows->approved_qty?>"></td>

              <td class="text-left">
                <select name="status" id="a_status<?= $rows->id ?>">
                  <option value="">Select Status</option>
                  <option value="2">Approved</option>
                  <option value="4">Rejected</option>
                </select>
              </td>

              <td class="text-left"><?php echo date('d-m-Y',strtotime($rows->created_at)); ?></td>
              <td style="cursor: pointer; color: #310bff" title="<?= $rows->note ?>" ><?= substr($rows->note, 0,10) ?></td>
              <td class="text-left"><a class="btn btn-sm btn-primary" onclick="approve_req(this, <?= $rows->id ?>, <?= $rows->stock_qty?>)">Submit</a></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function approve_req(e, id, stock_quantity) {
    var a_quantity = $("#app_qty"+id).val();
    var a_status = $("#a_status"+id).val();

    if (a_status == "") {
      alert('Please select status');
      return;
    }

    if (a_status == 2) {
      if (stock_quantity <= 0) {
        alert('Stock quantity must be greater than 0');
        return;
      }

      if (stock_quantity < a_quantity) {
        alert('Approved quantity must be less than or equal to stock quantity');
        return;
      }

      if (a_quantity <= 0) {
        alert('Approved quantity must be greater than 0');
        return;
      }
      app_status = 'approved';
    } else {
      app_status = 'rejected';
    }

    $.ajax({
      url: '<?php echo site_url("admin/inventory/admin_ajax_approved"); ?>',
      type: 'POST',
      data: {
        id: id,
        a_quantity: a_quantity,
        a_status: a_status,
        app_status: app_status
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
