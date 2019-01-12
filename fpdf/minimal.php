<?php
require('fpdf.php');
class PDF extends FPDF

$pdf = new FPDF();
// Page header
function Header()
{
    // Logo
    $this->Image('logo.png',10,6,30);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    $this->Cell(80);
    // Title
    $this->Cell(30,10,'Title',1,0,'C');
    // Line break
    $this->Ln(20);
}


$pdf->AddPage();

$pdf->SetFont('Arial','B',12);
$pdf->SetY(40);
$pdf->Cell(100,50,'','TB',2,'L');
$pdf->SetFont('Arial','B',12);
$pdf->Text(10,53,'Max Mustermann');
$pdf->Text(10,60,'Musterstrasse 3');
$pdf->Text(10,74,'22154 Musterort');
$pdf->Output();

?>