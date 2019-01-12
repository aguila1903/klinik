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

$out = array();
$data = array();

if (!$dbSyb->IsConnected()) {

    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('name' => trim($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;


$querySQL = $sqlQuery = "SELECT "
."  prod_kz " 
.", bezeichnung " 
.", brutto_preis1 "
.", brutto_preis2 " 
.", m.mwst "
.", case when aktiv = 0 then 'Inaktiv' else 'Aktiv' end as status "
."  from "
."  produkte p, mwst_tab m where p.mwst = m.lfd_nr;"
;


/* @var $rs string */
$rs = $dbSyb->Execute($querySQL); //=>>> Abfrage wird an den Server �bermittelt / ausgef�hrt?
// Ausgabe initialisieren



if (!$rs) {
    // keine Query hat nicht funtioniert

    print("Query 1: " . $dbSyb->ErrorMsg());

    return;
}
// das else MUSS nicht sein, da ein Fehler vorher Stoppt


$i = 0;

while (!$rs->EOF) { // =>>> End OF File
    $data{$i}{0} = trim($rs->fields{'prod_kz'});
//    $data{$i}{1} = $rs->fields{'status'};
    $data{$i}{1} = trim($rs->fields{'bezeichnung'});
    $data{$i}{2} = number_format($rs->fields{'brutto_preis1'},2, ',', '.');
    $data{$i}{3} = number_format($rs->fields{'brutto_preis2'},2, ',', '.');
    $data{$i}{4} = number_format($rs->fields{'mwst'},2, ',', '.');

//
//    $export .= $data{$i}{"prod_kz"} . ";" . $data{$i}{"bezeichnung"} . ";" . $data{$i}{"brutto_preis1"} . ";" 
//            . $data{$i}{"brutto_preis2"} . ";" . $data{$i}{"mwst"} . "\r\n";


    $i++;

    // den n�chsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();

$header = array("Tedavi no", "Tedavi", "Fiyat 1", "Fiyat 2", "K.D.V.");

$excel = new EasyPHPExcel('Tedaviler');
$excel->setHeader($header)
      ->addRows($data)
      ->save('../Excel/Tedaviler.xlsx');

//file_put_contents("Tedaviler.csv", utf8_encode($export));
//file_put_contents("KundenListe.txt", ($export));



header("Location: ../Excel/Tedaviler.xlsx");
?>