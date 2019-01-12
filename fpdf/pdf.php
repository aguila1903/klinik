<?php
require('fpdf.php');

class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    $this->Image('logo.png',80,5,40,20);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    $this->Cell(80);
    // Title
    // $this->Cell(30,10,'Title',1,0,'C');
    // Line break
    $this->Ln(20);
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',9);
	$fh = 260;
	$fb = 10;
	$this->Text($fb,$fh-5,'Erdoganlar',1,0,'C');
	$this->SetFont('Arial','',9);
	$this->Text($fb,$fh,'Sitz: Hamburg',1,0,'C');
	$this->Text($fb,$fh+4,'Flurstr. 208a',1,0,'C');
	$this->Text($fb,$fh+8,'22549 Hamburg',1,0,'C');
	$this->Text($fb,$fh+12,'Amtsgericht Hamburg',1,0,'C');
	$this->Text($fb,$fh+16,'HRB 10851',1,0,'C');
	$this->Text($fb,$fh+20,'USt-Id-Nr: DE 285 958 925',1,0,'C');
	$this->Text($fb,$fh+24,'Steuer-Nr.: 326/5901/0369',1,0,'C');
	
	
	$this->Text($fb+50,$fh+4,'Telefon 040/236 696 58',1,0,'C');
	$this->Text($fb+50,$fh+8,'Telefax 040/236 696 55',1,0,'C');
	$this->Text($fb+50,$fh+12,'www.erdoding.de',1,0,'C');
	$this->Text($fb+50,$fh+16,'info@erdoding.de',1,0,'C');
	
	$this->Text($fb+100,$fh+4,'Hamburger Sparkasse',1,0,'C');
	$this->Text($fb+100,$fh+8,'IBAN: DE37200505501313475590',1,0,'C');
	$this->Text($fb+100,$fh+12,'SWIFT-BIC: HASPDEHHXXX',1,0,'C');
    // Page number
    $this->Cell(0,10,'Seite '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
//***************** ANFANG ADRESSFELD **************************
$pdf->SetY(55); // Höhe der Adressfeld-Zelle
$pdf->Cell(80,47,'','T',2,'L'); // Adressfeld-Zelle
$pdf->SetFont('Arial','I',11); //Schrift für Absender-Adresse
$pdf->Text(10,52,'Erdoganlar, Flurstr. 208a, 22549 Hamburg'); 
$pdf->SetFont('Arial','',12);// Schrift für Adressfeld-Zelle
// ANFANG Empfänger-Adresse
$pdf->Text(10,68,'Max Mustermann');
$pdf->Text(10,75,'Musterstrasse 3');
$pdf->Text(10,89,'22154 Musterort');
// ENDE Empfänger-Adresse
//***************** ENDE ADRESSFELD **************************

//***************** ANFANG SEITEN-INFOS **************************
$pdf->SetFont('Arial','BU',8); 
$pdf->Text(130,55,'Bei Zahlungs/Schriftverkehr bitte immer angeben!'); 
$pdf->SetFont('Arial','B',10);
//Feste Titel
$pdf->Text(130,60,'Rechnungsnr.:'); 
$pdf->Text(130,65,'Kudennr.:'); 
$pdf->Text(130,70,'Rechnungsdatum:'); 
//Dynamische Titel
$pdf->Text(170,60,'2014/2'); 
$pdf->Text(170,65,'2'); 
$pdf->Text(170,70,'02.09.2014'); 
//***************** ENDE SEITEN-INFOS **************************

//***************** ANFANG RECHNUNGSRUMPF **************************
$text = utf8_decode('Rechnung für den Kauf von Iltis-Borsten');
$pdf->SetFont('Arial','B',12); 
$pdf->Text(10,120,$text);

$pdf->SetFont('Arial','',12); 
$pdf->Text(10,140,'Sehr geehrter Max Mustermann,'); 
$pdf->Text(10,150,'wir erlauben uns wie folgt zu berechnen:'); 
//Feste Überschriften
$pdf->SetY(156); // Höhe der Adressfeld-Zelle
$pdf->SetFillColor(200);
$pdf->SetFont('Arial','B',10);
$pdf->SetX(9); 
$pdf->Cell(30,5,'Pos.',0,0,'L',true); 
$pdf->SetX(19); 
$pdf->Cell(100,5,'Artikel',0,0,'',true);
$pdf->SetX(98); 
$pdf->Cell(30,5,'Stk',0,0,'',true);
$pdf->SetX(118); 
$pdf->Cell(35,5,'Nettobetrag',0,0,'',true);
$pdf->SetX(148); 
$pdf->Cell(35,5,'Umsatzsteuer',0,0,'',true);
$pdf->SetX(178); 
$pdf->Cell(25,5,'Bruttobetrag',0,0,'',true);
// Dynamische Positionen
$pdf->SetFont('Arial','',10); 
$pdf->Text(10,165,'1'); 
$pdf->Text(20,165,utf8_decode('Gefüllte Griessfrikadellen - Hackfleischfüllung'));
$pdf->Text(100,165,'3');
$pdf->Text(120,165,'0,80');
$pdf->Text(150,165,'0,26');
$pdf->Text(180,165,'1,06');


// $pdf->Cell(192,5,'',0,0,'',true); // Adressfeld-Zelle

 
//***************** ENDE RECHNUNGSRUMPF **************************


$pdf->AddPage(); // Neue Seite
$pdf->Output(); // Schreibe PDF

?>