<?php $session = $this->session->userdata('username');?>
<?php $get_animate = $this->Xin_model->get_content_animate();?>
<style>
  .table {
    width: 92%;
    margin-bottom: 14px;
}
</style>
<div class="box mb-4 <?php echo $get_animate;?>">
  <div id="accordion">
    <div class="box-header with-border">
        <h3 class="box-title">Purchase Products Requisition</h3>
    </div>
      <div class="container pt-4">
        <div class="row">
          <div class="col-md-3">
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <!-- <label for="department"><?php echo $this->lang->line('left_department');?></label> -->
              <select class="form-control" id="select_item" name="select_item" data-plugin="select_hrm" placeholder="Search Item" >
                <option><-- Search Item --> </option>
                <?php foreach ($results as $key => $row) { ?>
                  <option value="<?= $row->id ?>"><?= $row->category_name .' >> '. $row->sub_cate_name .' >> '. $row->product_name ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>

        <?php $attributes = array('id' => 'product-form', 'autocomplete' => 'off', 'class' => 'm-b-1 add');?>
        <?php $hidden = array('user_id' => $session['user_id']);?>
        <?php echo form_open('admin/inventory/purchase', $attributes, $hidden);?>

        <?php if($user_role_id!=3){?>
          
        <?php }else{?>
           <input type="hidden" value="<?= date('Y-m-d') ?>" name="requisition_date">
        <?php } ?>
          <table class="table table-bordered table-sm table-striped " id="appRowDiv">
            <tr>
              <th class="text-left">Category Name</th>
              <th class="text-left">Sub Category Name</th>
              <th class="text-left">Product Name</th>
              <th class="text-left">Unit</th>
              <th class="text-left">Quantity</th>
              <th class="text-left">Approx amount</th>
              <th class="text-left">Total amount</th>
              <th class="text-left">Action</th>
            </tr>
            <tr></tr>
          </table>
          <button name="btn" type="submit" class="btn btn-primary btn-sm text-right" style="float: right;margin-right: 165px;margin-bottom: 20px;" > <i class="fa fa-check-square-o"></i><?php echo $this->lang->line('xin_save');?></button>
        <?php echo form_close(); ?> 
      </div>
  </div>
</div>

<?php if($success = $this->session->flashdata('success')):?>
  <div class="alert alert-success" id="flash_message">
    <?php echo $success?>
  </div>
<?php endif; ?> 
<script>
  $(function() {$("#flash_message").hide(2000);});
</script>  
<?php if($warning = $this->session->flashdata('warning')):?>
  <div class="alert alert-warning"id="flash_message1">
    <?php echo $warning;?>
  </div>
<?php endif; ?> 
<script>
  $(function() {$("#flash_message1").hide(2000);});
</script> 







<?php
  $category_data = '';
  $category_data .= '<option value="">--Select One--</option>';

  foreach ($categorys as $key => $value) {
    $category_data .= '<option value="'.$value->id.'">'.$value->category_name.'</option>';
  }
?>
<script type="text/javascript">
  $(document).ready(function(){
    // get designations
    $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
    $('[data-plugin="select_hrm"]').select2({ width:'100%' });

    $('#select_item').change(function(e) {
      product_id = $(this).val();
      $.ajax({
        type: "GET",
        url: "<?php echo base_url('admin/inventory/get_product_by_ajax/');?>" + product_id,
        success: function(response)
        {
          product_name = response.product_name;
          unit_name = response.unit_name;
          category_name = response.category_name;
          cat_id = response.cat_id;
          sub_cat_name = response.sub_cate_name;
          sub_cat_id = response.sub_cate_id;
          let items = '';
          items+= '<tr>';
          items+= '<input name="product_id[]" value="'+product_id+'" type="hidden" required>';
          items+= '<input name="sub_cate_id[]" value="'+sub_cat_id+'" type="hidden" required>';
          items+= '<input name="cat_id[]" value="'+cat_id+'" type="hidden" required>';
          items+= '<td>'+category_name+'</td>';
          items+= '<td>'+sub_cat_name+'</td>';
          items+= '<td>'+product_name+'</td>';
          items+= '<td>'+unit_name+'</td>';
          items+= '<td><input name="quantity[]" style="width: 73px;" class="form-control input-sm" required /></td>';
          items+= '<td><input name="approx_amount[]" id="approx_amount_'+product_id+'" step="0.01" min="0" value="0" type="number" class="form-control input-sm approx_amount" required onChange="change_amount(this)" ></td>';
          items+= '<td><input name="total_amount[]" id="total_amount_'+product_id+'" value="0" type="number" class="form-control input-sm" required></td>';
          items+= '<td> <a class="label label-important text-danger" onclick="removeRow(this)"><span style="color:#a94442;font-size:12px">Remove</span> </a></td>';
          items+= '</tr>';
          $('#appRowDiv tr:last').after(items);
        }
     });
    })
  });

  function removeRow(id){ 
    $(id).closest("tr").remove();
  }

  function change_amount(amount) {
    var quantity = $(amount).closest("tr").find('input[name="quantity[]"]').val();
    var total = quantity * amount.value;
    $(amount).closest("tr").find('input[name="total_amount[]"]').val(total);
  }

</script>

<script>
  function adddaily_req(){
    $.ajax({
        type: "GET",
        url: "<?php echo base_url('admin/inventory/adddaily_req/');?>",
        success: function(data)
        {
         var response=JSON.parse(data);
         console.log(response);
          let items = '';
          for (let i = 0; i < response.length; i++) {

              product_name = response[i].product_name;
              product_id = response[i].product_id;
              unit_name = response[i].unit_name;
              category_name = response[i].category_name;
              cat_id = response[i].cat_id;
              sub_cat_name = response[i].sub_cate_name;
              sub_cat_id = response[i].sub_cate_id;
              quantity = response[i].quantity;

         
          items+= '<tr>';
          items+= '<input name="product_id[]" value="'+product_id+'" type="hidden" required>';
          items+= '<input name="sub_cate_id[]" value="'+sub_cat_id+'" type="hidden" required>';
          items+= '<input name="cat_id[]" value="'+cat_id+'" type="hidden" required>';

          items+= '<td>'+category_name+'</td>';
          items+= '<td>'+sub_cat_name+'</td>';
          items+= '<td>'+product_name+'</td>';
          items+= '<td>'+unit_name+'</td>';

          items+= '<td><input name="quantity[]" class="form-control input-sm" style="width: 73px;"  value="'+quantity+'" required /></td>';

          items+= '<td> <a class="label label-important text-danger" type="number" onclick="removeRow(this)"><span style="color:#a94442;font-size:12px">Remove</span> </a></td>';
          items+= '</tr>';
        }
          $('#appRowDiv tr:last').after(items);

        }
     });
  
  }
</script>