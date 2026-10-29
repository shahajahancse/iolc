
<link rel="stylesheet"
    href="<?php echo base_url();?>skin/hrsale_assets/theme_assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>skin/hrsale_assets/css/hrsale/xin_hrsale_custom.css">

<style type="text/css">
    .box-tools {
        margin-right: -5px !important;
    }

    table thead tr th {
        font-size: 12px;
        padding: 3px !important;
    }

    table tbody tr td {
        font-size: 12px;
        padding: 3px !important;
    }

    @media print {
        .box-tools {
            margin-right: -5px !important;
        }

        table thead tr th {
            font-size: 12px;
            padding: 3px !important;
        }

        table tbody tr td {
            font-size: 14px;
            padding: 3px !important;
        }

        body {
            zoom: 80% !important;
        }
    }
</style>

<div class="box" id="print_area">
    <div style="text-align: center;">
        <?php  $this->load->view('admin/head_bangla'); ?>
        <h4 class="box-title">Task Breakdown Report</h4>
        <p>Report date: <?php echo date('d-m-Y'); ?> </p> <br>
    </div>

    <div class="container">
        <div class="box-body">
            <div class="box-datatable table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Employee</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th>Employee Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; foreach($results as $row){?>
                        <tr>
                            <td><?php echo $i++;?></td>
                            <td><?php echo $row->first_name.' '.$row->last_name?></td>
                            <td><?php echo $row->client_name?></td>
                            <td><?php echo date('d-m-Y', strtotime($row->date)); ?></td>
                            <td><?php echo $row->status?></td>
                            <td><?php echo $row->remarks;?></td>
                            <td><?php echo $row->emp_remark;?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery 3 -->
<script type="text/javascript" src="<?php echo base_url();?>skin/hrsale_assets/vendor/jquery/jquery-3.2.1.min.js">
</script>
<script src="<?php echo base_url();?>skin/hrsale_assets/theme_assets/bower_components/jquery/dist/jquery.min.js">
</script>

<script
    src="<?php echo base_url();?>skin/hrsale_assets/theme_assets/bower_components/bootstrap/dist/js/bootstrap.min.js">
</script>
<!-- Morris.js charts -->
