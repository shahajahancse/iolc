<?php
// =========================
// NO DATA CHECK
// =========================
// dd($reports);
if (empty($reports)) {
    echo "<h3 style='text-align:center;margin-top:20px;color:red'><b>No Data Available</b></h3>";
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Summary</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>skin/hrsale_assets/theme_assets/bower_components/bootstrap/dist/css/bootstrap.min.css">

    <style>
        body {
            font-size: 11px;
        }
        thead td {
            font-weight: bold;
            text-align: center;
            background: #298bbc;
            color: #fff;
            padding: 6px;
        }
        tbody td {
            text-align: center;
            padding: 5px;
        }
        .cat-name {
            text-align: left;
            font-weight: bold;
            background: #f8f9fa;
        }
        .box {
            padding: 10px;
        }
    </style>
</head>

<body>

<div class="box" id="print_area">

    <!-- HEADER -->
    <div style="text-align:center;">
        <?php $this->load->view('admin/head_bangla'); ?>
    </div>

    <div class="container" style="margin-top:15px; width:70%;">

        <?php
            $status_labels = [
                1 => 'On Working',
                2 => 'Store',
                3 => 'Servicing',
                4 => 'Damage',
                5 => 'Movement'
            ];
            $summary = [];
            $grand_total = array_fill_keys(array_keys($status_labels), 0);
            $overall_total = 0;

            foreach ($reports as $row) {

                // use cat_id as key (better than cat_name)
                if (!isset($summary[$row->cat_id])) {
                    $summary[$row->cat_id] = [
                        'cat_id'      => $row->cat_id,
                        'cat_name'    => $row->cat_name,
                        'device_model'=> $row->device_model, // may be empty
                        'counts'      => array_fill_keys(array_keys($status_labels), 0),
                        'row_total'   => 0
                    ];
                }

                if (isset($summary[$row->cat_id]['counts'][$row->status])) {
                    $summary[$row->cat_id]['counts'][$row->status]++;
                    $summary[$row->cat_id]['row_total']++;
                    $grand_total[$row->status]++;
                    $overall_total++;
                }
            }
        ?>
        <h5 class="text-center" style="background:#298bbc;padding:6px;color:#fff;font-weight:bold;">
            Inventory Summary Report
        </h5>
        <!-- TABLE -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <td>Category Name</td>
                        <?php foreach ($status_labels as $label): ?>
                            <td><?php echo $label; ?></td>
                        <?php endforeach; ?>
                            <td>Total</td>
                            <td>Details</td>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($summary as $data): ?>
                    <tr>
                        <td class="cat-name"><?php echo $data['cat_name']; ?></td>

                        <?php foreach ($status_labels as $status_id => $label): ?>
                            <td>
                                <a href="<?php echo base_url(
                                    'admin/reports/details?' .
                                    'cat_id=' . urlencode($data['cat_id']) .
                                    '&status=' . $status_id
                                ); ?>" target="_blank">
                                    <?php echo $data['counts'][$status_id]; ?>
                                </a>
                            </td>
                        <?php endforeach; ?>

                        <td><b><?php echo $data['row_total']; ?></b></td>

                        <td>
                            <a href="<?php echo base_url(
                                'admin/reports/details?' .
                                'cat_id=' . urlencode($data['cat_id'])
                            ); ?>" target="_blank">
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
                <tfoot>
                    <tr style="font-weight:bold;background:#f1f1f1;">
                        <td>Total</td>
                        <?php foreach ($status_labels as $status_id => $label): ?>
                            <td style="text-align:center;"><?php echo $grand_total[$status_id]; ?></td>
                        <?php endforeach; ?>
                        <td style="text-align:center;"><?php echo $overall_total; ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>

</body>
</html>
