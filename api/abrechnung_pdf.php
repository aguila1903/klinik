<?php

date_default_timezone_set('Europe/berlin');
require('../fpdf/fpdf.php');

session_start();

require_once('adodb5/adodb.inc.php');

$ADODB_CACHE_DIR = 'C:/php/cache';
require_once('db_psw_klinik.php'); 


$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; // Liefert ein assoziatives Array, das der geholten Zeile entspricht 

$ADODB_COUNTRECS = true;

$dbSyb = ADONewConnection("mysqli");

// DB-Abfragen NICHT cachen
$dbSyb->memCache = false;
$dbSyb->memCacheHost = array('localhost'); /// $db->memCacheHost = $ip1; will work too
$dbSyb->memCacheCompress = false; /// Use 'true' arbeitet unter Windows nicht
//$dsn = "'localhost','root',psw,'vitaldb'";
$dbSyb->Connect('localhost', user, psw, db); //=>>> Verbindungsaufbau mit der DB

$out = array();

if (!$dbSyb->IsConnected()) {

    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('errors' => ($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;

if (isset($_REQUEST["beleg_nr"])) {
    $beleg_nr = $_REQUEST["beleg_nr"];
    if ($beleg_nr != "null" && $beleg_nr != "") {
        if ((preg_match("/^[0-9\/]{1,45}?$/", trim($beleg_nr))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('errors' => "Bitte die Abrechnungsnr. prüfen. " . $beleg_nr);

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('errors' => "Die Abrechnungsnr. fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('errors' => "Die Abrechnungsnr. fehlt!");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["verkauf_an"])) {
    $kunden_nr = $_REQUEST["verkauf_an"];
    if ($kunden_nr != "null" && $kunden_nr != "") {
        if ((preg_match("/^[0-9]{1,11}?$/", trim($kunden_nr))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('menge' => "Bitte die Menge prüfen. Maximal 11 Zeichen erlaubt");

            print json_encode($out);
            return;
        }
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('kunden_nr' => "Kunden-Nr fehlt!");

    print json_encode($out);
    return;
}
if (isset($_REQUEST["art"])) {
    $art = $_REQUEST["art"];
} else {
    $art = "vorschau";
}


//Statement für die Rechnungsdetails
$sqlQuery = "Select v.lfd_nr, v.prod_kz, p.bezeichnung, verkauf_an, 
concat(vorname,' ',name) as name,  
menge, preis_kat, 
einzelpr_brutto as brutto_preis,
v.mwst,
einzelpr_brutto * v.menge as gesamtpr_brutto,
DATE_FORMAT(v.datum,GET_FORMAT(DATE,'ISO')) as datum, 
bemerkung, 
beleg_nr, 
zahlungsziel
-- DATE_FORMAT(DATE_ADD(v.datum,INTERVAL k.zahlfrist DAY),GET_FORMAT(DATE,'EUR')) as zahlfrist
FROM klinikdb.verkaeufe v left join produkte p on v.prod_kz = p.prod_kz 
left join kunden k on v.verkauf_an = k.lfd_nr left join mwst_tab m on m.lfd_nr = p.mwst where v.beleg_nr = " . $dbSyb->Quote($beleg_nr) . ";";

//file_put_contents("addProdukte.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
$i = 0;
//$mwst7 = 0;
//$mwst19 = 0;
//$mwst0 = 1;
$mwst = 0;
$araToplam = 0;
$mwst_satz = 0;
$gesamtpr_brutto = 0;
while (!$rs->EOF) {

    $value{$i}{"lfd_nr"} = $rs->fields{'lfd_nr'};
    $value{$i}{"prod_kz"} = ($rs->fields{'prod_kz'});
    $value{$i}{"bezeichnung"} = mb_convert_encoding($rs->fields{'bezeichnung'}, 'CP1254', 'UTF-8');
    $value{$i}{"bemerkung"} = mb_convert_encoding($rs->fields{'bemerkung'}, 'CP1254', 'UTF-8');
    $value{$i}{"name"} = mb_convert_encoding($rs->fields{'name'}, 'CP1254', 'UTF-8');
    $value{$i}{"verkauf_an"} = $rs->fields{'verkauf_an'};
    $value{$i}{"menge"} = $rs->fields{'menge'};
    $value{$i}{"preis_kat"} = $rs->fields{'preis_kat'};
    $value{$i}{"mwst"} = number_format($rs->fields{'mwst'}, 0, ',', '.');
    $value{$i}{"brutto_preis"} = number_format($rs->fields{'brutto_preis'}, 2, ',', '.');
    $value{$i}{"gesamtpr_brutto"} = number_format($rs->fields{'gesamtpr_brutto'}, 2, ',', '.');
    $value{$i}{"datum"} = $rs->fields{'datum'};
    $value{$i}{"beleg_nr"} = $rs->fields{'beleg_nr'};
    $value{$i}{"zahlungsziel"} = $rs->fields{'zahlungsziel'};

    // Bei mehreren Steuersätzen die ausgewiesen werden müssen (alte Lösung)
    /* if ($rs->fields{'mwst'} == 7.00) {
      $mwst7 = $mwst7 + $rs->fields{'mwst_gesamtpr'};
      }
      if ($rs->fields{'mwst'} == 18.00) {
      $mwst19 = $mwst19 + $rs->fields{'mwst_gesamtpr'};
      }
      if ($rs->fields{'mwst'} < 1) {
      $mwst0 = $rs->fields{'mwst_gesamtpr'};
      } */

    $mwst1 = 100 - $rs->fields{'mwst'};
    $mwst2 = ($mwst1 * $rs->fields{'gesamtpr_brutto'}) / 100;
    $mwst3 = $rs->fields{'gesamtpr_brutto'} - $mwst2;
    $mwst += $mwst3;
    $araToplam += $mwst2;
    $mwst_satz = number_format($rs->fields{'mwst'}, 0, ',', '.');
    $gesamtpr_brutto += $rs->fields{'gesamtpr_brutto'};

    $i++;

    // den n�chsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();


// Statement für das Adressfeld
$sqlQuery3 = "SELECT strasse as strasse
from kunden where lfd_nr = " . $dbSyb->Quote($value{0}{"verkauf_an"}) . ";";

//file_put_contents("addProdukte.txt", $sqlQuery);

$rs3 = $dbSyb->Execute($sqlQuery3);


if (!$rs3) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
$value3 = array();

while (!$rs3->EOF) {

    $value3{"strasse"} = mb_convert_encoding($rs3->fields{'strasse'}, 'CP1254', 'UTF-8');
//    $value3{"adresszusatz"} = ($rs3->fields{'adresszusatz'});
//    $value3{"plz"} = ($rs3->fields{'plz'});
//    $value3{"ort"} = ($rs3->fields{'ort'});
    // den n�chsten Datensatz lesen
    $rs3->MoveNext();
}

$rs3->Close();

/*
 * ****************************PDF**********************************************
 * =============================================================================
 */

class PDF extends FPDF {

// Page header
    function Header() {
        // Logo
        $this->Image('images/logos/logo.png', 45, 0);
        // Calibri bold 15
        $this->AddFont('Calibri', 'B', 'calibri.php');
        $this->SetFont('Calibri', 'B', 15);
        // Move to the right
        $this->Cell(80);
        // Title
        // $this->Cell(30,10,'Title',1,0,'C');
        // Line break
        $this->Ln(20);

        //***************** ANFANG SEITEN-INFOS **************************

        if (isset($_REQUEST["beleg_nr"])) {
            $beleg_nr = $_REQUEST["beleg_nr"];
            if ($beleg_nr != "null" && $beleg_nr != "") {
                if ((preg_match("/^[0-9\/]{1,45}?$/", trim($beleg_nr))) == 0) {

                    $out{'response'}{'status'} = -4;
                    $out{'response'}{'errors'} = array('errors' => "Bitte die Abrechnungsnr. prüfen. " . $beleg_nr);

                    print json_encode($out);
                    return;
                }
            } else {
                $out{'response'}{'status'} = -1;
                $out{'response'}{'errors'} = array('errors' => "Die Abrechnungsnr. fehlt!");

                print json_encode($out);

                return;
            }
        } else {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('errors' => "Die Abrechnungsnr. fehlt!");

            print json_encode($out);

            return;
        }
        if (isset($_REQUEST["verkauf_an"])) {
            $kunden_nr = $_REQUEST["verkauf_an"];
            if ($kunden_nr != "null" && $kunden_nr != "") {
                if ((preg_match("/^[0-9]{1,11}?$/", trim($kunden_nr))) == 0) {

                    $out{'response'}{'status'} = -4;
                    $out{'response'}{'errors'} = array('menge' => "Bitte die Menge prüfen. Maximal 11 Zeichen erlaubt");

                    print json_encode($out);
                    return;
                }
            }
        } else {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('kunden_nr' => "Kunden-Nr fehlt!");

            print json_encode($out);
            return;
        }
        if (isset($_REQUEST["datum"])) {
            $datum = $_REQUEST["datum"];
        } else {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('datum' => "Datum fehlt!");

            print json_encode($out);

            return;
        }

        $this->AddFont('Calibri', 'BU', 'calibri.php');
        $this->SetFont('Calibri', 'BU', 14);
        $ih = 60;
        $iw = 130;
        $this->AddFont('Calibri', 'B', 'calibri.php');
        $this->Text($iw + 20, $ih - 5, 'FATURA                      ');
        $this->SetFont('Calibri', 'B', 9);

//Feste Titel
        $this->Text($iw + 20, $ih, 'Fatura no.:');
        $this->Text($iw + 20, $ih + 5, 'Hasta no.:');
        $this->Text($iw + 20, $ih + 10, 'Fatura tarihi:');
//Dynamische Titel
        $this->Text($iw + 40, $ih, $beleg_nr);
        $this->Text($iw + 40, $ih + 5, $kunden_nr);
        $this->Text($iw + 40, $ih + 10, $datum);
//***************** ENDE SEITEN-INFOS **************************
    }

// Page footer
    function Footer() {
        // Position at 1 cm from bottom        
        $this->SetY(-10);
//        $this->Image('images/logos/3cstreifen.png', 0, 287, 210, 12);
        // Calibri italic 9        
        $this->AddFont('Calibri', 'B', 'calibri.php');
        $this->SetFont('Calibri', 'B', 9);
        $fh = 270;
        $fb = 10;
        $this->Text($fb, $fh, mb_convert_encoding('DOĞAL TEDAVİLER KLİNİĞİ', 'CP1254', 'UTF-8'), 1, 0, 'C');
        $this->AddFont('Calibri', '', 'calibri.php');
        $this->SetFont('Calibri', '', 9);
        $this->Text($fb, $fh + 4, mb_convert_encoding('Tıbbi Sülük Ve Hacamat', 'CP1254', 'UTF-8'), 1, 0, 'C');
        $this->Text($fb, $fh + 8, mb_convert_encoding('Başakşehir 1.Etap', 'CP1254', 'UTF-8'), 1, 0, 'C');
        $this->Text($fb, $fh + 12, mb_convert_encoding('Sebahattin Zaim Cd. No:8S', 'CP1254', 'UTF-8'), 1, 0, 'C');
        $this->Text($fb, $fh + 16, mb_convert_encoding('Başakşehir/İstanbul', 'CP1254', 'UTF-8'), 1, 0, 'C');
//        $this->Text($fb, $fh + 12, 'Amtsgericht Hamburg', 1, 0, 'C');
//        $this->Text($fb, $fh + 16, 'HRB 10851', 1, 0, 'C');
//        $this->Text($fb, $fh + 16, 'USt-Id-Nr: DE 243 038 438', 1, 0, 'C');
//        $this->Text($fb, $fh + 20, 'Steuer-Nr.: 326/5901/0369', 1, 0, 'C');


        $this->Text($fb + 140, $fh + 4, 'Telefon: 0212 4864545', 1, 0, 'C');
        $this->Text($fb + 140, $fh + 8, 'Cep: 0542 6370260', 1, 0, 'C');
        $this->Text($fb + 140, $fh + 12, 'www.dogaltedavilermerkezi.com', 1, 0, 'C');
        $this->Text($fb + 140, $fh + 16, 'info@dogaltedavilermerkezi.com', 1, 0, 'C');

//        $this->Text($fb + 140, $fh + 8, 'Hamburger Sparkasse', 1, 0, 'C');
//        $this->Text($fb + 140, $fh + 12, 'IBAN: DE76200505501127815239', 1, 0, 'C');
//        $this->Text($fb + 140, $fh + 16, 'SWIFT-BIC: HASPDEHHXXX', 1, 0, 'C');
        // Page number
        $this->Cell(0, 10, 'Sayfa ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
//***************** ANFANG ADRESSFELD **************************
$ah = 55;
$aw = 10;
$pdf->SetY($ah); // Höhe der Adressfeld-Zelle
$pdf->Cell(80, 47, '', 'T', 2, 'L'); // Adressfeld-Zelle

$pdf->AddFont('Calibri', '', 'calibri.php');
$pdf->SetFont('Calibri', '', 8); //Schrift für Absender-Adresse
$pdf->Text($aw, $ah - 1, mb_convert_encoding('Başakşehir 1.Etap, Sebahattin Zaim Cd. No:8S, Başakşehir/İstanbul', 'CP1254', 'UTF-8'));

$pdf->AddFont('Calibri', '', 'calibri.php');
$pdf->SetFont('Calibri', '', 11); // Schrift für Adressfeld-Zelle
// ANFANG Empfänger-Adresse
$pdf->Text($aw, $ah + 8, $value{0}{"name"});
$adresse = array();
$adresse = explode("\n", $value3{"strasse"});
$a = 0;
while (count($adresse) > $a) {
    $pdf->Text($aw, $ah + 13, $adresse[$a]);
    $a++;
    $ah +=5;
}


//if (strlen($value3{"adresszusatz"}) > 2) {
//    $pdf->Text($aw, $ah + 20, ($value3{"adresszusatz"}));
//    $pdf->Text($aw, $ah + 32, $value3{"plz"} . " " . ($value3{"ort"}));
//} else {
//    $pdf->Text($aw, $ah + 25, $value3{"plz"} . " " . ($value3{"ort"}));
//}
// ENDE Empfänger-Adresse
//***************** ENDE ADRESSFELD **************************
//
//
//***************** ANFANG RECHNUNGSRUMPF **************************
// Betreff
$text = "FATURA " . $value{0}{"beleg_nr"};

$pdf->AddFont('Calibri', 'B', 'calibri.php');
$pdf->SetFont('Calibri', 'B', 11);
//$pdf->Text(10, 125, $text);
// Anrede und Einleitungstext

$pdf->AddFont('Calibri', '', 'calibri.php');
$pdf->SetFont('Calibri', '', 11);
//$pdf->Text(10, 140, 'Sehr geehrte ' . ($value{0}{"name"}) . " Leitung,");
//$pdf->Text(10, 120, mb_convert_encoding('Sayın ' . $value{0}{"name"} . '', 'CP1254', 'UTF-8'));
//$pdf->Text(10, 150, mb_convert_encoding('wir erlauben uns wie folgt zu berechnen:', 'CP1254', 'UTF-8'));

// Rechnungsdetails tabellarisch
//Feste Überschriften
// Höhe der Adressfeld-Zelle
$pdf->SetFillColor(200);

$pdf->AddFont('Calibri', 'B', 'calibri.php');
$pdf->SetFont('Calibri', 'B', 8);
$rw = 10;
$rh = 130;
$pdf->SetY($rh);
$pdf->SetX($rw);
$pdf->Cell(30, 5, 'No', 0, 0, 'L', true);
$pdf->SetX($rw + 10);
//$pdf->Cell(50, 5, 'Artikel-Nr.', 0, 0, 'L', true);
//$pdf->SetX($rw + 30);
$pdf->Cell(100, 5, mb_convert_encoding('Açıklama', 'CP1254', 'UTF-8'), 0, 0, '', true);
$pdf->SetX($rw + 92);
$pdf->Cell(30, 5, '', 0, 0, '', true);
$pdf->SetX($rw + 100);
$pdf->Cell(60, 5, 'Miktar', 0, 0, '', true);
$pdf->SetX($rw + 133);
$pdf->Cell(50, 5, 'Birim Fiyat', 0, 0, '', true);
$pdf->SetX($rw + 173);
$pdf->Cell(20, 5, 'Toplam', 0, 0, '', true);
// Dynamische Positionen
$ii = 0;
$counter = 0;
$rh = $rh + 5;

$pdf->AddFont('Calibri', 'B', 'calibri.php');
$pdf->SetFont('Calibri', '', 8);
while ($ii < count($value)) {
    if ($ii == count($value) - 1) {// Die letzte Position soll einen Unterstrich bekommen
        $pdf->SetY($rh);
        $pdf->Cell(180, 5, $ii + 1, 'B', 'L', false);
        $pdf->SetX($rw + 10);
//        $pdf->Cell(30, 5, ($value[$ii]["prod_kz"]), 'B', 'L', false);
//        $pdf->SetX($rw + 30);
        $pdf->Cell(30, 5, ($value[$ii]["bezeichnung"]), 'B', 0, '', false);
        $pdf->SetX($rw + 86);
        $pdf->Cell(20, 5, $value[$ii]["menge"] . "", 'B', 0, 'R', false);
        $pdf->SetX($rw + 126);
        $pdf->Cell(20, 5, $value[$ii]["brutto_preis"] . " TL", 'B', 0, 'R', false);
//        $pdf->SetX($rw + 133);
//        $pdf->Cell(33, 5, "(" . $value[$ii]["mwst"] . "%)  " . $value[$ii]["mwst_einzelpr"] . " TL", 'B', 0, 'R', false);
        $pdf->SetX($rw + 157);
        $pdf->Cell(29, 5, $value[$ii]["gesamtpr_brutto"] . " TL", 'B', 0, 'R', false);
    } else {
        $pdf->SetY($rh);
        $pdf->Cell(180, 5, $ii + 1, 0, 0, 'L', false);
        $pdf->SetX($rw + 10);
//        $pdf->Cell(30, 5, ($value[$ii]["prod_kz"]), 0, 'L', false);
//        $pdf->SetX($rw + 30);
        $pdf->Cell(30, 5, ($value[$ii]["bezeichnung"]), 0, 0, '', false);
        $pdf->SetX($rw + 86);
        $pdf->Cell(20, 5, $value[$ii]["menge"] . "", 0, 0, 'R', false);
        $pdf->SetX($rw + 126);
        $pdf->Cell(20, 5, $value[$ii]["brutto_preis"] . " TL", 0, 0, 'R', false);
//        $pdf->SetX($rw + 133);
//        $pdf->Cell(33, 5, "(" . $value[$ii]["mwst"] . "%)  " . $value[$ii]["mwst_einzelpr"] . " TL", 0, 0, 'R', false);
        $pdf->SetX($rw + 157);
        $pdf->Cell(29, 5, $value[$ii]["gesamtpr_brutto"] . " TL", 0, 0, 'R', false);
    }
    $rh = $rh + 5;
    $ii++;
    $counter++;
    // Wenn mehr als 20 Positionen auftreten, wird eine neue Sayfa angefangen
    if ($counter == 20) {
        $pdf->Text($rw, $rh + 20, mb_convert_encoding("Devami bir sonraki sayfada", 'CP1254', 'UTF-8'));
        $pdf->AddPage();
        $rw = 10;
        $rh = 95;

        $pdf->AddFont('Calibri', 'B', 'calibri.php');
        $pdf->SetFont('Calibri', 'B', 8);
        $counter = 0;
        $pdf->SetY($rh);
        $pdf->SetX($rw);
        $pdf->Cell(30, 5, 'No', 0, 0, 'L', true);
        $pdf->SetX($rw + 10);
//        $pdf->Cell(50, 5, 'Artikel-Nr.', 0, 0, 'L', true);
//        $pdf->SetX($rw + 30);
        $pdf->Cell(100, 5, mb_convert_encoding('Açıklama', 'CP1254', 'UTF-8'), 0, 0, '', true);
        $pdf->SetX($rw + 100);
        $pdf->Cell(60, 5, 'Miktar', 0, 0, '', true);
        $pdf->SetX($rw + 133);
        $pdf->Cell(50, 5, 'Birim Fiyat', 0, 0, '', true);        
        $pdf->SetX($rw + 173);
        $pdf->Cell(20, 5, 'Toplam', 0, 0, '', true);
        $rh = $rh + 5;

        $pdf->AddFont('Calibri', 'B', 'calibri.php');
        $pdf->SetFont('Calibri', '', 8);
    }

    if ($ii == count($value)) { // Nach der letzten Position kommt der Gesamtpreis und der Schlussatz
//  $pdf->SetX(150); 
        //Bezeichnungen
        $pdf->AddFont('Calibri', 'B', 'calibri.php');
        $pdf->SetFont('Calibri', 'B', 8);
        $pdf->SetY($rh);
        $pdf->SetX($rw + 135);
        $pdf->Cell(20, 5, "Ara Toplam:", 0, 0, 'L', false);
//        if ($mwst7 > 0 && $mwst19 == 0 && $mwst0 == 1) {// Multi Steuersatz-Problem ANFANG // Nur Produkt mit 7%
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, "MwSt 7%: ", 0, 0, 'L', false);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, mb_convert_encoding("Ödeme Tutari (brüt): ", 'CP1254', 'UTF-8'), 0, 0, 'L', false);
//        } else if ($mwst7 == 0 && $mwst19 > 0 && $mwst0 == 1) {// Nur Produkt mit 19%
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, "MwSt 19%: ", 0, 0, 'L', false);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, mb_convert_encoding("Ödeme Tutari (brüt): ", 'CP1254', 'UTF-8'), 0, 0, 'L', false);
//        } else if ($mwst7 == 0 && $mwst19 == 0 && $mwst0 < 1) {// Nur Produkt mit 0%
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, "MwSt: ", 0, 0, 'L', false);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, mb_convert_encoding("Ödeme Tutari (brüt): ", 'CP1254', 'UTF-8'), 0, 0, 'L', false);
//        } else if ($mwst7 > 0 && $mwst19 > 0 && $mwst0 == 1) {// Produkt mit 19% und 7%
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, "MwSt 7%: ", 0, 0, 'L', false);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, "MwSt 19%: ", 0, 0, 'L', false);
//            $pdf->SetY($rh + 15);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, mb_convert_encoding("Ödeme Tutari (brüt): ", 'CP1254', 'UTF-8'), 0, 0, 'L', false);
//        } else if ($mwst7 > 0 && $mwst19 > 0 && $mwst0 < 1) {// Produkt mit 19% und 7% und 0%
////        $pdf->SetY($rh + 5);
////        $pdf->SetX($rw + 120);
////        $pdf->Cell(20, 5, "MwSt 0%: ", 0, 0, 'L', false);
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, "MwSt 7%: ", 0, 0, 'L', false);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, "MwSt 19%: ", 0, 0, 'L', false);
//            $pdf->SetY($rh + 15);
//            $pdf->SetX($rw + 120);
//            $pdf->Cell(20, 5, mb_convert_encoding("Ödeme Tutari (brüt): ", 'CP1254', 'UTF-8'), 0, 0, 'L', false);
//        }                   // Multi Steuersatz-Problem ENDE
        $pdf->SetY($rh + 5);
        $pdf->SetX($rw + 135);
        $pdf->Cell(20, 5, "Toplam K.D.V (%" . $mwst_satz . "): ", 0, 0, 'L', false);
        $pdf->SetY($rh + 10);
        $pdf->SetX($rw + 135);
        $pdf->Cell(20, 5, "Genel Toplam: ", 0, 0, 'L', false);


        $pdf->AddFont('Calibri', 'B', 'calibri.php');
        $pdf->SetFont('Calibri', 'B', 8);
        $pdf->SetY($rh);
        $pdf->SetX($rw + 157);
        $pdf->Cell(29, 5, number_format($araToplam, 2, ',', '.') . " TL", 0, 0, 'R', false);
        // Beträge // Multi Steuersatz-Problem ANFANG
//        if ($mwst7 > 0 && $mwst19 == 0 && $mwst0 == 1) { // Nur Produkt mit 7%
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($mwst7, 2, ',', '.') . " TL", 0, 0, 'R', false);
//            $pdf->SetFont('Calibri', 'BU', 8);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($gesamtpr_brutto, 2, ',', '.') . " TL", 0, 0, 'R', false);
//        } else if ($mwst7 == 0 && $mwst19 > 0 && $mwst0 == 1) {// Nur Produkt mit 19%
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($mwst19, 2, ',', '.') . " TL", 0, 0, 'R', false);
//            $pdf->SetFont('Calibri', 'BU', 8);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($gesamtpr_brutto, 2, ',', '.') . " TL", 0, 0, 'R', false);
//        } else if ($mwst7 == 0 && $mwst19 == 0 && $mwst0 < 1) {// Nur Produkt mit 0%
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($mwst0, 2, ',', '.') . " TL", 0, 0, 'R', false);
//            $pdf->SetFont('Calibri', 'BU', 8);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($gesamtpr_brutto, 2, ',', '.') . " TL", 0, 0, 'R', false);
//        } else if ($mwst7 > 0 && $mwst19 > 0 && $mwst0 == 1) {// Produkt mit 19% und 7%
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($mwst7, 2, ',', '.') . " TL", 0, 0, 'R', false);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($mwst19, 2, ',', '.') . " TL", 0, 0, 'R', false);
//            $pdf->SetFont('Calibri', 'BU', 8);
//            $pdf->SetY($rh + 15);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($gesamtpr_brutto, 2, ',', '.') . " TL", 0, 0, 'R', false);
//        } else if ($mwst7 > 0 && $mwst19 > 0 && $mwst0 < 1) {// Produkt mit 19% und 7% und 0%
////        $pdf->SetY($rh + 5);
////        $pdf->SetX($rw + 163);
////        $pdf->Cell(29, 5, number_format($mwst0, 2, ',', '.') . " TL", 0, 0, 'R', false);
//            $pdf->SetY($rh + 5);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($mwst7, 2, ',', '.') . " TL", 0, 0, 'R', false);
//            $pdf->SetY($rh + 10);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($mwst19, 2, ',', '.') . " TL", 0, 0, 'R', false);
//            $pdf->SetFont('Calibri', 'BU', 8);
//            $pdf->SetY($rh + 15);
//            $pdf->SetX($rw + 163);
//            $pdf->Cell(29, 5, number_format($gesamtpr_brutto, 2, ',', '.') . " TL", 0, 0, 'R', false);
//        }        // Multi Steuersatz-Problem ENDE
        $pdf->SetY($rh + 5);
        $pdf->SetX($rw + 157);
        $pdf->Cell(29, 5, number_format($mwst, 2, ',', '.') . " TL", 0, 0, 'R', false);

        $pdf->AddFont('Calibri', 'B', 'calibri.php');
        $pdf->SetFont('Calibri', 'BU', 8);
        $pdf->SetY($rh + 10);
        $pdf->SetX($rw + 157);
        $pdf->Cell(29, 5, number_format($gesamtpr_brutto, 2, ',', '.') . " TL", 0, 0, 'R', false);


        $pdf->AddFont('Calibri', 'B', 'calibri.php');
        $pdf->SetFont('Calibri', '', 11);
//        if ($value{0}{"zahlungsziel"} == "Z") {
//            $pdf->Text($rw, $rh + 30, "Bitte überweisen Sie den fälligen Betrag von " . number_format($gesamtpr_brutto, 2, ',', '.') . " TLo auf unser u.a. Konto.");
//        } else {
//            $pdf->Text($rw, $rh + 30, ("Der Betrag " . number_format($gesamtpr_brutto, 2, ',', '.') . " TL wurde bar bezahlt."));
//        $pdf->Text($rw, $rh + 30, mb_convert_encoding("Ödemeniz icin tessekür ederiz.", 'CP1254', 'UTF-8'));
//        }
    }
}

// $pdf->Cell(192,5,'',0,0,'',true); // Adressfeld-Zelle
//***************** ENDE RECHNUNGSRUMPF **************************
//
//PDF wird geschrieben
//$pdf->Output($beleg_nr. ".pdf", "D"); // Schreibe PDF
//$pdf->Output("C:/xampp/htdocs/erdo/erdo_api/".str_replace("/","_",$beleg_nr) . ".pdf", "F"); // Schreibe PDF mit Pfad
//$pdf->Output(); // Schreibe PDF als doc.pdf

$path = getcwd() . "\Abrechnungen\\";



if ($art == "vorschau") {// Bei einer Vorschau wird nur die PDF geöffnet
    $pdf->Output(str_replace("/", "_", $beleg_nr) . ".pdf", "I"); // Öffnen der PDF
}
if ($art == "buchen") { // Bei einer Buchung wird die Rechnungs-PDF in den Abrechnungen-Ordner gespeichert. Existiert der Ordner nicht, wird er erstellt.
    if (is_dir($path) != 1) {
        mkdir($path, 0777, true);
        chmod($path, 0777);
    }

    $pdf->Output($path . str_replace("/", "_", $beleg_nr) . ".pdf", "F"); // Speichern der PDF im Ordner

    $sqlQuery = "Update verkaeufe set beleg_pfad = " . $dbSyb->Quote(str_replace("/", "_", $beleg_nr) . ".pdf") . " Where beleg_nr = " . $dbSyb->Quote($beleg_nr) . ";";


    $rs = $dbSyb->Execute($sqlQuery);

    $value = array();

    if (!$rs) {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('errors' => ($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }

    $rs->Close();
}
?>