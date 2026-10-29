<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Summary Report </title>
    <style>
        h5 {
            margin: 0;
            padding: 0;
        }
        .flex-container {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            justify-content: space-around;
        }
        .report-box {
            width: 48%;
        }
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
            font-size: 12px;
        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php $this->load->view('admin/head_bangla'); ?>
    <h3 style="text-align:center">Product Summary Report <button onclick="location.href='<?php echo site_url('admin/reports/summary_report_excel'); ?>'">Export to excel</button></h3>
    <div style="margin:0 auto; width:900px">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="text-align:center">Sl. No.</th>
                    <th style="text-align:center">Product Name</th>
                    <th style="text-align:center">Quantity</th>
                    <th style="text-align:center">Rate</th>
                    <th style="text-align:center">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1; 
                $total_amount = 0;  

                foreach ($results as $row) {
                    // Use avg_approx_amount from the query as Rate
                    $rate = isset($row->avg_approx_amount) ? $row->avg_approx_amount : 0;  // fallback 0 if null
                    $amount = $rate * $row->quantity;  
                    $total_amount += $amount;
                ?>
                <tr>
                    <td style="text-align:center"><?php echo $i++; ?></td>
                    <td style="text-align:center"><?php echo $row->product_name; ?></td>
                    <td style="text-align:center"><?php echo $row->quantity; ?></td>
                    <td style="text-align:center"><?php echo number_format($rate, 2); ?></td>
                    <td style="text-align:center"><?php echo number_format($amount, 2); ?></td>
                </tr>
                <?php } ?>
                </tbody>

                <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right"><strong>Total Amount</strong></td>
                    <td style="text-align:center"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
                </tr>
                </tfoot>

        </table>
    </div>
</body>
</html>



