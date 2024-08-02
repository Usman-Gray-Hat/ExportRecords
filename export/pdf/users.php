<?php
include("../../dbConnect.php");
include("../../pdf/fpdf.php");

if (isset($_POST['rd']) && isset($_POST['rd'])!=="") 
{
    $from = $_POST['from'];
    $to = $_POST['to'];
    if($from==="" || $to==="")
    {
        // Query to fetch data without date range
        $query = "SELECT * FROM users";
        $heading_text = "USERS RECORD";
    }
    else
    {
        // Query to fetch data with date range
        $query = "SELECT * FROM users WHERE created_at BETWEEN '$from' AND '$to'";
        $heading_text = "USERS RECORD FROM (".strtoupper(date('F-d-Y',strtotime($from))).") TO (".strtoupper(date('F-d-Y',strtotime($to))).")";
    }

    $result = mysqli_query($conn,$query);

    $data = [];
    while($row = mysqli_fetch_assoc($result)) 
    {
        $data[] = $row;
    }

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $heading_text, 0, 1, 'C');
    $pdf->Ln(10);

    // Set header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(20, 10, 'SR.NO', 1, 0, 'C');
    $pdf->Cell(100, 10, 'Name', 1, 0, 'C');
    $pdf->Cell(20, 10, 'Age', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Date', 1, 1, 'C');

    // Set data rows
    $pdf->SetFont('Arial', '', 12);

    $i = 1;
    foreach($data as $row) 
    {
        $pdf->Cell(20, 10, $i++, 1, 0, 'C');
        $pdf->Cell(100, 10, $row['name'], 1, 0, 'C');
        $pdf->Cell(20, 10, $row['age'], 1, 0, 'C');
        $pdf->Cell(50, 10, date('M-d-Y',strtotime($row['created_at'])), 1, 1, 'C');
    }

    // Output PDF
    $pdf_file = 'UserRecords.pdf';
    $pdf->Output('F', $pdf_file);

    echo $pdf_file;
} 
?>