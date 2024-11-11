<?php
require_once 'config.php';
require_once 'fpdf.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['id_reporte'])) {
    $id_reporte = intval($_GET['id_reporte']);
    $sql = "SELECT tipo_reporte, formato_reporte FROM Reportes WHERE id_reporte = $id_reporte";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $reporte = $result->fetch_assoc();
        $tipo_reporte = $reporte['tipo_reporte'];
        $formato_reporte = $reporte['formato_reporte'];

        if ($formato_reporte == 'PDF') {
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16); // Cambiado a Arial
            $pdf->Cell(0, 10, "Reporte de $tipo_reporte", 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Ln(10);
            $pdf->MultiCell(0, 10, "Este es un ejemplo de reporte generado en formato PDF para el tipo de reporte: $tipo_reporte");
            $pdf->Output("D", "reporte_$id_reporte.pdf");
        } elseif ($formato_reporte == 'Excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', "Reporte de $tipo_reporte");
            $sheet->setCellValue('A2', "Este es un ejemplo de reporte generado en formato Excel para el tipo de reporte: $tipo_reporte");

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=reporte_$id_reporte.xlsx");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }
    } else {
        echo "Reporte no encontrado.";
    }
} else {
    echo "ID de reporte no especificado.";
}
