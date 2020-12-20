<?php
session_start();

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');
header("Cache-Control: no-cache, must-revalidate");
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

if (isset($_SESSION["login"]) && $_SESSION["login"] == login && $_SESSION["admin"] == admin) {

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

if(isset($_REQUEST["plz"])){
    $plz = $_REQUEST["plz"];
    $Where = " where plz = ". $dbSyb->Quote($plz). " order by ort_stadtteil asc";
}else {
    $Where =  " order by ort_stadtteil asc";
}

if(isset($_REQUEST["anzeige"])){
    $anzeige = $_REQUEST["anzeige"];
}else {
    $anzeige = "ort";
}

$sqlQuery = "SELECT distinct ort_stadtteil from plz_tabelle ". $Where
;
// file_put_contents("plzDS.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);


if (!$rs) {
    print $dbSyb->ErrorMsg() . "\n";
    return;
}
$i = 0;

$value = array();

while (!$rs->EOF) {

    $value{$i}['nr'] = $i;
	if($anzeige == "ort"){
    $value{$i}['ort'] = utf8_encode($rs->fields['ort_stadtteil']);
     }
if($anzeige == "stadtteil"){
    $value{$i}['ort'] = str_replace("Hamburg ","",utf8_encode($rs->fields['ort_stadtteil']));
     }	 
    $i++;

    // den n�chsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();



$output = json_encode($value);

print($output);
} else {
    header("Location: http://$host$uri/noadmin.php");
}
