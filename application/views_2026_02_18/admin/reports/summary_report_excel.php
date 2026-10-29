<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Summary Report Export</title>
    <?php
    $filename = "Product_Summary_Report_" . date('Ymd') . ".xls";
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    ?>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }
        td, th {
            border: 1px solid #000000;
            text-align: left;
            padding: 8px;
        }
    </style>
</head>
<body>
    <h3 style="text-align:center">Product Summary Report</h3>
    <table>
        <thead>
            <tr>
                <th style="text-align:center; font-weight:bold;">Sl. No.</th>
                <th style="text-align:center; font-weight:bold;">Product Name</th>
                <th style="text-align:center; font-weight:bold;">Quantity</th>
                <th style="text-align:center; font-weight:bold;">Rate</th>
                <th style="text-align:center; font-weight:bold;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1; 
            $total_amount = 0;  

            foreach ($results as $row) {
                // Use avg_approx_amount from the query as Rate
                $rate = $row->avg_approx_amount ?? 0;  // fallback 0 if null
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
</body>
</html>
