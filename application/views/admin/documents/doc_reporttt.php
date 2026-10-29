<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document Reports</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body, table, th, td, h5 {
            font-family: 'Segoe UI', Arial, sans-serif !important;
        }
        h5 {
            font-size: 20px !important;
        }
        th, td {
            font-size: 12px !important;
        }
    </style>
</head>
<body>
   
    <?php $this->load->view('admin/head_bangla'); ?>
    <div class="d-flex justify-content-between align-items-center px-3">
        <h5 class="text-center flex-grow-1">Document Reports</h5>
    </div>

    <div class="px-3 my-2">
        <button id="downloadPdf" class="btn btn-sm btn-info"><i class="fa fa-file-pdf"></i> PDF</button>
        <button id="downloadExcel" class="btn btn-sm btn-success"><i class="fa fa-file-excel"></i> Excel</button>
    </div>

    <div class="container my-3">
        <div class="table-responsive">
            <table id="reportTable" class="table table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">SL.</th>
                        <th class="text-center">Discipline</th>
                        <th class="text-center">Classification</th>
                        <th class="text-center" style="width: 350px;">Description</th>
                        <th class="text-center">Client</th>
                        <th class="text-center">Reference</th>
                        <th class="text-center">Issue/Sub Date</th>
                        <th class="text-center">Rev#</th>
                        <th class="text-center">Issue/Sub BY</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1; 
                    foreach ($docs as $doc): 
                        $created_by = $this->db
                            ->select('first_name,last_name')
                            ->where('user_id', $doc->created_by)
                            ->get('xin_employees')
                            ->row();
                    ?>
                    <tr>
                        <td class="text-center"><?= $i++; ?></td>
                        <td class="fw-bold text-center"><?= $doc->discipline_id; ?></td>
                        <td class="fw-bold text-center"><?= $doc->classification_id; ?></td>
                        <td style="text-align: justify;"><?= $doc->description; ?></td>
                        <td class="text-center"><?= $doc->user; ?></td>  
                        <td><?= "MHL-".$doc->discipline_id."-".$doc->classification_id."-".date("y",strtotime($doc->created_at)).'-'.$doc->reference."/".$doc->rev_number; ?></td>
                        <td class="text-center"><?= date("d-m-Y",strtotime($doc->issue_date)); ?></td>
                        <td class="text-center"><?= $doc->rev_number; ?></td>
                        <td class="text-center"><?= $created_by->first_name . ' ' . $created_by->last_name; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
    // PDF Download
    document.getElementById("downloadPdf").addEventListener("click", function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("landscape", "pt", "a4");

        doc.setFontSize(19);
        doc.text("Mysoft Heaven (BD) Ltd", doc.internal.pageSize.getWidth() / 2, 30, { align: "center" });
        doc.setFontSize(11);
        doc.text("Shapla House 363/H/2 North Pirerbag, Raisa & Shikder Tower, Level-5, 3/8, Kamal Soroni, Mirpur, Dhaka-1207", doc.internal.pageSize.getWidth() / 2, 50, { align: "center" });

        doc.setFontSize(14);
        doc.text("Document Reports", doc.internal.pageSize.getWidth() / 2, 70, { align: "center" });

        doc.autoTable({
            html: "#reportTable",
            startY: 100,
            styles: {
                fontSize: 8,
                cellPadding: 3,
                halign: "center",
                valign: "middle",
                lineColor: [200, 200, 200],
                lineWidth: 0.1
            },
            headStyles: {
                fillColor: [63, 114, 175],
                textColor: 255,
                fontStyle: "bold",
                halign: "center"
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            didDrawPage: function (data) {
                let str = "Page " + doc.internal.getNumberOfPages();
                doc.setFontSize(8);
                doc.text(str, doc.internal.pageSize.getWidth() - 40, doc.internal.pageSize.getHeight() - 10);
            }
        });

        doc.save("document_reports.pdf");
    });

    // Excel Download
    document.getElementById("downloadExcel").addEventListener("click", function () {
        let table = document.getElementById("reportTable");
        let wb = XLSX.utils.table_to_book(table, { sheet: "Document Reports" });
        XLSX.writeFile(wb, "document_reports.xlsx");
    });
    </script>


</body>
</html>
