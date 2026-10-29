<!-- < ?php dd($reports)?> -->
<link rel="stylesheet" href="<?php echo base_url();?>skin/hrsale_assets/theme_assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
<style>
	thead td {font-size :12px;font-weight:bold;text-align:center;}
	tbody td{font-size:12px;text-align:center;}
</style>	
<div class="box" id="print_area">
  <div style="text-align: center;">
	<?php $this->load->view('admin/head_bangla'); ?>
  </div>
  
  <?php 
	// Group reports by category
	$grouped_reports = [];
	foreach($reports as $report) {
	  $cat_name = !empty($report->cat_name) ? $report->cat_name : 'Uncategorized';
	  if(!isset($grouped_reports[$cat_name])) {
		$grouped_reports[$cat_name] = [];
	  }
	  $grouped_reports[$cat_name][] = $report;
	}
  ?>
  
  <?php foreach($grouped_reports as $category => $items): ?>
	<h5 class="text-center" style="margin-top: 30px; margin-bottom: 15px;">
	  Store Report: <?php echo htmlspecialchars($category); ?>
	</h5>
	
	<div class="container" style="margin-top:15px">
	  <div class="table-responsive table-striped">
		<table class="table table-bordered bg-light">
		  <thead>
			<tr>
			  <td>Sl. No.</td>
			  <td>Item Name</td>
			  <td>Model</td>
			  <td>Description</td>
			  <td>Status</td>
			  <td>Move Status</td>
			</tr>
		  </thead>
		  <tbody>
			<?php $i = 1; foreach($items as $report): ?>
			  <tr>
				<td><?php echo $i++; ?></td>
				<td><?php echo htmlspecialchars(!empty($report->device_name_id) ? 'MHL '.$report->cat_short_name.'-'.$report->device_name_id : '-'); ?></td>
				<td><?php echo htmlspecialchars(!empty($report->model_name) ? $report->model_name : '-'); ?></td>
				<td><?php echo htmlspecialchars(!empty($report->description) ? $report->description : '-'); ?></td>
				<td><?php echo $report->status == 1 ? 'Using' : ($report->status == 2 ? 'Stored' : 'Damaged'); ?></td>
				<td><?php echo $report->move_status == 0 ? 'Not Moved' : ($report->move_status == 1 ? 'In Transit' : 'Moved'); ?></td>
			  </tr>
			<?php endforeach; ?>
		  </tbody>
		</table>
	  </div>
	</div>
  <?php endforeach; ?>
</div>
