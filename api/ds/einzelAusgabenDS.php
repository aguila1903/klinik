<?php
session_start();

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');
header("Cache-Control: no-cache, must-revalidate");
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

if (isset($_SESSION["login"]) && $_SESSION["login"] == login) {

/* * *****************************************************************************
  System: infotool - SVK-Versaende
  Funktion: Versandfehler anzeigen
  Autor: jra
  Datum: 04.12.2012

  Zusatzhinweise:

  �nderungen:

 * ***************************************************************************** */



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

if (isset($_REQUEST["ausg_art_kz"])) {
    $ausg_art_kz =  $_REQUEST["ausg_art_kz"];
    
    $ausg_art_kz = " Where ausg_art_kz = ". $dbSyb->Quote(utf8_decode($ausg_art_kz));
    
} else {
   $ausg_art_kz = "";
}


$sqlQuery = "select ausg_kz, ausg_art_kz, bezeichnung from ausgaben ".$ausg_art_kz;


$rs = $dbSyb->Execute($sqlQuery);


if (!$rs) {
    print $dbSyb->ErrorMsg() . "\n";
    return;
}
$i = 0;

$value = array();

while (!$rs->EOF) {

    $value{$i}['ausg_kz'] = utf8_encode($rs->fields['ausg_kz']);
    $value{$i}['ausg_art_kz'] = utf8_encode($rs->fields['ausg_art_kz']);
    $value{$i}['bezeichnung'] = utf8_encode($rs->fields['bezeichnung']);
       
    $i++;

    // den nächsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();



$output = json_encode($value);

print($output);
} else {
    header("Location: http://$host/klinik/noadmin.php");
}
