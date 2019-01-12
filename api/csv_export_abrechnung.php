<?php

session_start();

require_once('adodb5/adodb.inc.php');
require_once('PHPExcel\Classes\EasyPHPExcel.php');
require_once('PHPExcel\Classes\PHPExcel.php');
require_once('db_psw_klinik.php');

$ADODB_CACHE_DIR = 'C:/php/cache';


$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; // Liefert ein assoziatives Array, das der geholten Zeile entspricht 

$ADODB_COUNTRECS = true;

$dbSyb = ADONewConnection("mysqli");

// DB-Abfragen NICHT cachen
$dbSyb->memCache = false;
$dbSyb->memCacheHost = array('localhost'); /// $db->memCacheHost = $ip1; will work too
$dbSyb->memCacheCompress = false; /// Use 'true' arbeitet unter Windows nicht
//$dsn = "'localhost','root',psw,'vitaldb'";
$dbSyb->Connect('localhost', user, psw, db); //=>>> Verbindungsaufbau mit der DB


if (!$dbSyb->IsConnected()) {


    print ("Anmeldung: " . $dbSyb->ErrorMsg());

    $data = array();

    return ($data);
}

$dbSyb->debug = false;
// Toplevel


$sqlQuery = "call selectVerkaeufeAll";


$rs = $dbSyb->Execute($sqlQuery);


if (!$rs) {
    print $dbSyb->ErrorMsg() . "\n";
    return;
}
$i = 0;

$data = array();


while (!$rs->EOF) {
    $mwst1 = 100 - $rs->fields{'mwst'};
    $mwst2 = ($mwst1 * $rs->fields{'gesamtpr_brutto'}) / 100;
    $mwst3 = $rs->fields{'gesamtpr_brutto'} - $mwst2;
    $araToplam = number_format($mwst2, 2, ',', '.');

//    $data{$i}{"lfd_nr"} = $rs->fields{'lfd_nr'};
    $data{$i}{0} = trim($rs->fields{'prod_kz'});
    $data{$i}{1} = trim($rs->fields{'bezeichnung'});
    $data{$i}{2} = $rs->fields{'verkauf_an'};
    $data{$i}{3} = trim($rs->fields{'name'});
    $data{$i}{4} = $rs->fields{'geburtstag'};
    $data{$i}{5} = $rs->fields{'menge'};
    $data{$i}{6} = $rs->fields{'preis_kat'};
    $data{$i}{7} = number_format($rs->fields{'mwst'}, 2, ',', '.');
    $data{$i}{8} = number_format($rs->fields{'brutto_preis'}, 2, ',', '.');
    $data{$i}{9} = $araToplam;
    $data{$i}{10} = number_format($mwst3, 2, ',', '.');
    $data{$i}{11} = number_format($rs->fields{'gesamtpr_brutto'}, 2, ',', '.');
    $data{$i}{12} = $rs->fields{'datum'};
    $data{$i}{13} = $rs->fields{'beleg_nr'};
//    $data{$i}{"bemerkung"} = trim($rs->fields{'bemerkung'});
//    $data{$i}{"beleg_pfad"} = trim($rs->fields{'beleg_pfad'});



//    $export .= $data{$i}{"prod_kz"} . ";" . $data{$i}{"bezeichnung"} . ";"
//            . $data{$i}{"verkauf_an"} . ";" . $data{$i}{"name"} . ";" . $data{$i}{"geburtstag"} . ";" . $data{$i}{"menge"} . ";"
//            . $data{$i}{"preis_kat"} . ";" . $data{$i}{"mwst"} . ";" . $data{$i}{"brutto_preis"} . ";" . $araToplam
//            . ";" . number_format($mwst3, 2, ',', '.') . ";" . $data{$i}{"gesamtpr_brutto"} . ";" . $data{$i}{"datum"} . ";"
//            . $data{$i}{"beleg_nr"} . "\r\n";

    $i++;

// den nächsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();

$header = array("Tedavi no", "Tedavi", "Hasta no", "Isim", "Dogum Tarihi", "Miktar", "Kategori", "K.D.V", "Fiyat", "Ara Toplam", "Toplam K.D.V", "Toplam Fiyat", "Tarih", "Fatura no",);


$excel = new EasyPHPExcel('Faturalar');
$excel->setHeader($header)
      ->addRows($data)
      ->save('../Excel/Faturalar.xlsx');

//file_put_contents("Faturalar.csv", utf8_decode($export));
//file_put_contents("KundenListe.txt", utf8_decode($export));



header("Location: ../Excel/Faturalar.xlsx");
?>