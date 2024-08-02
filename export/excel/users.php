<?php
include("../../dbConnect.php");
include("../../excel/vendor/autoload.php");

// Import libraries
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment; // For cell alignment
use PhpOffice\PhpSpreadsheet\Style\Border;    // For border
use PhpOffice\PhpSpreadsheet\Style\Color;     // For font color
use PhpOffice\PhpSpreadsheet\Style\Fill;      // For header color

// Get request
$from = $_GET['from']??"";
$to = $_GET['to']??"";

// Check if date filter is selected or not
if($from!=="" || $to!=="")
{
    // Query to fetch records along with date range
    $query = "SELECT * FROM users
    WHERE created_at BETWEEN '$from' AND '$to'";
    // Pre-Header text
    $heading_text = "USERS RECORD FROM (".strtoupper(date('F-d-Y',strtotime($from))).") TO (".strtoupper(date('F-d-Y',strtotime($to))).")";
}
else
{
    // Query to fetch records without date range
    $query = "SELECT * FROM users";
    // Pre-Header text
    $heading_text = "USERS RECORD";
}

// Execute query
$result = mysqli_query($conn,$query);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set pre-header
$sheet->mergeCells('A1:D1');
$sheet->setCellValue('A1', $heading_text);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

// Set header cells (shifted down by one row)
$sheet->setCellValue('A2', 'SR.NO');
$sheet->setCellValue('B2', 'Name');
$sheet->setCellValue('C2', 'Age');
$sheet->setCellValue('D2', 'Created At');

// Set column widths
$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(35);
$sheet->getColumnDimension('C')->setWidth(10);
$sheet->getColumnDimension('D')->setWidth(20);

// Center-align all cells in columns A to D
$sheet->getStyle('A:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A:D')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

// Set header row style
$headerStyle = $sheet->getStyle('A1:D2');
$headerStyle->getFont()->setBold(true)->getColor()->setARGB(Color::COLOR_WHITE);
$headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1F4E79'); // Blue color

// For increment
$i = 1; // Incrementation starts from 1

$rowNumber = 3; // Start from row 3 because the header is in row 1 and 2
while ($row = mysqli_fetch_assoc($result)) 
{ 
    // Fill data
    $sheet->setCellValue('A' . $rowNumber, $i++);
    $sheet->setCellValue('B' . $rowNumber, $row['name']);
    $sheet->setCellValue('C' . $rowNumber, $row['age']);
    $sheet->setCellValue('D' . $rowNumber, date('M-d-Y',strtotime($row['created_at'])));
    $rowNumber++;
}

// Apply border to all cells (header + data)
$lastRow = $rowNumber - 1;
$range = 'A1:D' . $lastRow;
$sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('FF000000')); // Thin black border

// Save Excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Users.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');