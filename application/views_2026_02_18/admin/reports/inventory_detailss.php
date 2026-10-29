<!-- < ? php  dd( $reports)?> -->
<?php
	if(empty($reports)) {
		echo "<h3 style='text-align:center;margin-top:20px;color:red'><b>No Data Available</b></h3>";
		return;
	}
?>
<link rel="stylesheet" href="<?php echo base_url();?>skin/hrsale_assets/theme_assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
<style>
	thead td {font-size :11px;font-weight:bold;text-align:center;}
	tbody td{font-size:11px;text-align:center;}
</style>	
<div class="box" id="print_area">
	<div style="text-align: center;">
		<?php  $this->load->view('admin/head_bangla'); ?>
	</div>
	<div class="container " style="margin-top:15px; width: 70%;">
		<?php 
			$grouped_reports = [];
			foreach($reports as $report) {
				$grouped_reports[$report->status][] = $report;
			}
			$status_labels = [
				'1' => 'On Working',
				'2' => 'Store',
				'3' => 'Servicing',
				'4' => 'Damage',
				'5' => 'Movement'
			];
		?>
		<?php foreach($status_labels as $status_key => $status_label): ?>
			<?php if(isset($grouped_reports[$status_key])): ?>
				<h5 class="text-center" style="background: #298bbc;padding: 5px;color: white;font-weight: bold;"><?php echo $status_label; ?></h5>
				<div class="table-responsive table-striped">
					<table class="table table-bordered bg-light ">
						<thead>
							<td style="padding: 3px;">Sl. No.</td>
							<?php if( $status_labels[$status_key] != 'Store'){ ?>
								<td style="padding: 3px;">User</td>
							<?php } ?>
							<td style="padding: 3px;">Category</td>
							<td style="padding: 3px;">Model</td>
							<td style="padding: 3px;">Item Name</td>
							<td style="padding: 3px;">Details</td>
						</thead>
						<tbody>    
							<?php $i=1; foreach($grouped_reports[$status_key] as $report): ?>
								<tr>
									<td style="padding: 3px;line-height:10px"><?php echo $i++; ?></td>
									<?php if( $status_labels[$report->status] != 'Store'){ ?>
										<td style="padding: 3px;line-height:10px"><?php echo $report->first_name.' '.$report->last_name; ?></td>
									<?php } ?>
									<td style="padding: 3px;line-height:10px"><?php echo $report->cat_name; ?></td>
									<td style="padding: 3px;line-height:10px"><?php echo $report->model_name; ?></td>
									<td style="padding: 3px;line-height:10px;text-transform: uppercase;"><?php echo 'MHL '.$report->cat_short_name.'-'.$report->device_name_id; ?></td>
									<td style="padding: 3px;line-height:10px"><?php echo $report->description; ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
