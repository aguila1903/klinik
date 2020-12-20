<?php

session_start();

require_once('adodb5/adodb.inc.php');
require_once('PHPExcel\Classes\EasyPHPExcel.php');
require_once('PHPExcel\Classes\PHPExcel.php');

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
$data = array();

if (!$dbSyb->IsConnected()) {

    $out['response']['status'] = -1;
    $out['response']['errors'] = array('name' => ($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;


$querySQL = $sqlQuery = "SELECT "
        . "  lfd_nr"
        . ", kunden_nr "
        . ", vorname"
        . ", DATE_FORMAT(geburtstag,GET_FORMAT(DATE,'EUR')) as geburtstag"
        . ", name "
        . ", strasse"
        . ", trim(telefon) as telefon "
        . ", fax "
        . ", email "
        . ", kommentar "
        . ", ifnull(timestampdiff(year,geburtstag,CURDATE()),0) as yas  "
        . " from kunden ";


/* @var $rs string */
$rs = $dbSyb->Execute($querySQL); //=>>> Abfrage wird an den Server �bermittelt / ausgef�hrt?
// Ausgabe initialisieren



if (!$rs) {
    // keine Query hat nicht funtioniert

    print("Query 1: " . $dbSyb->ErrorMsg());

    return;
}
// das else MUSS nicht sein, da ein Fehler vorher Stoppt


$header = array("Hasta no", "T.C. Kimlik no", "Isim", "Soyisim", "Yas", "Dogum Tarih", "Telefon",  "e-Posta",  "Teshis");
$i = 0;
$adresse = "";

while (!$rs->EOF) { // =>>> End OF File
    $data{$i}{0} = $rs->fields['lfd_nr'];
    $data{$i}{1} = $rs->fields['kunden_nr'];
    $data{$i}{2} = $rs->fields['vorname'];
    $data{$i}{3} = $rs->fields['name'];
    $data{$i}{4} = number_format($rs->fields['yas'], 0);
    $data{$i}{5} = $rs->fields['geburtstag'];
    $data{$i}{6} = ($rs->fields['telefon']);
    $data{$i}{7} = ($rs->fields['email']);
    $data{$i}{8} = $rs->fields['kommentar'];
//    $data{$i}['fax'] = $rs->fields['fax'];
//    $data{$i}['strasse'] = ($rs->fields['strasse']);
//    $data{$i}['name_voll'] = $rs->fields['vorname'] . " " . $rs->fields['name'];

//    $adresse = str_replace("\n",", ", $data{$i}['strasse']);
//    
//    $export .= $data{$i}['lfd_nr'] . ";" . $data{$i}['kunden_nr'] . ";" . $data{$i}['vorname'] . ";" . $data{$i}['name'] . ";" .$adresse.";"
//            . $data{$i}['geburtstag'] . ";" . $data{$i}['telefon'] . ";" . $data{$i}['fax'] . ";"
//            . $data{$i}['email'] . "\r\n";

    $i++;
   
    $rs->MoveNext();
    
}

$rs->Close();

$excel = new EasyPHPExcel('Hastalar');
$excel->setHeader($header)
      ->addRows($data)
      ->save('../Excel/hastalar.xlsx');

//print_r($data);

//file_put_contents("Hastalar.csv", ($export));
//file_put_contents("KundenListe.txt", ($export));

header("Location: ../Excel/hastalar.xlsx");
