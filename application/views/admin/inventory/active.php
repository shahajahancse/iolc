<style>
  .using {
    display: inline-flex;
    padding: 4.5px 14.3px 5.5px 9px;
    align-items: center;
    gap: 9px;
    border-radius: 50px;
    border: 1px solid #CCC;
    background: #FFF;
   
}
</style>


<!-- < ?php dd($requests);?> -->
<?php  $get_animate = $this->Xin_model->get_content_animate();?>
<div class="<?= $get_animate?>" style="margin-left:15px;margin-top:15px;margin-right: 15px;border-radius: 0px;">
  <div class="">
    <table class="datatables-demo table table-striped table-bordered" id="requested" style="width: 100%;background: white;margin-left: 0px;">
      <thead>
        <tr>
          <th class="text-center" style="width: 50px;">No.</th>
          <th class="text-center">Category</th>
          <th class="text-center">Device Name</th>
          <th class="text-center">Tag</th>
          <th class="text-center">User Name</th>
          <th class="text-center">Status</th>
          <th class="text-center">Floor</th>
          <th class="text-center">Purpose</th>
          <th class="text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requests as $key => $row) {?>
          <tr>
            <td class="text-center"><?= $key + 1 ?></td>
            <td class="text-center"><?= $row->cat_name ?></td>
            <td class="text-center"><?= $row->model_name ?></td>
            <td class="text-center"><?= "MHL ".$row->cat_short_name.'-'.$row->device_name_id ?></td>
            <td class="text-center"><?= $row->first_name.' '.$row->last_name?></td>
            <td class="text-center">
              <span class="using">
                <?= $row->move_status== 2?"<i class='fa fa-dot-circle-o' style='color:green'></i> Used": ""?>
              </span>
            </td>
            <td class="text-center"><?= isset($row->floor) && $row->floor == 3 ? "3rd Floor" : (isset($row->floor) && $row->floor == 5 ? "5th Floor" : "") ?></td>
            <td class="text-center"><?= isset($row->purpose) ? $row->purpose : ""?></td>
            <td class="text-center">
              <div class="dropdown">
                <button class="btn btn-link p-0 text-dark dropdown-toggle" 
                  type="button" 
                  id="dropdownMenuButton<?= $row->accessory_id ?>" 
                  data-toggle="dropdown" 
                  aria-haspopup="true" 
                  aria-expanded="false">
                    <i class="fa fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton<?= $row->accessory_id ?>">
                  <a class="dropdown-item text-info" href="<?= base_url('admin/inventory/free_device/'.$row->accessory_id); ?>">
                    <b>Free Device</b>
                  </a>
                </div>
              </div>  
            </td>

          </tr>
        <?php }?>
      </tbody>
    </table>
  </div>
</div>

 <script type="text/javascript">
    $(document).ready(function () {
      $('#requested').DataTable();
    });
</script>
