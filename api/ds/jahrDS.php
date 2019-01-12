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


      /*  if (isset($_REQUEST["monat"])) {
            $monat = $_REQUEST["monat"];
        } else {
            $monat = date("m");
        }

        if (isset($_REQUEST["kunden_nr"])) {
            $kunden_nr = $_REQUEST["kunden_nr"];
            if ($kunden_nr == "TÜM") {
                $Where2 = "";
            } else {
                $Where2 = " and verkauf_an = " . $kunden_nr;
            }
        } else {
            $Where2 = "";
        }

        if (isset($_REQUEST["auswahl"])) {
            $auswahl = $_REQUEST["auswahl"];
            if ($auswahl == "1" && $kunden_nr == "TÜM") { // zeitlich begrenzt und alle Kunden
                $Where1 = " and month(datum) = " . $monat;
            } else if ($auswahl == "2" && $kunden_nr == "TÜM") { // Komplett und alle Kunden
                $Where1 = "";
            } else if ($auswahl == "1" && $kunden_nr != "TÜM") { // zeitlich begrenzt aber nur Kunde x
                $Where1 =  " and month(datum) = " . $monat . $Where2;
            } else if ($auswahl == "2" && $kunden_nr != "TÜM") { // Komplett aber nur Kunde x
                $Where1 = " and verkauf_an = " . $kunden_nr;
            }
        } else {
            $Where1 = "";
        }*/
    

if (isset($_REQUEST["vorgang"])) {
            $vorgang = $_REQUEST["vorgang"];
            
        }else{
            $vorgang = "";
        }

if($vorgang == "ausgaben"){
    $sqlQuery = "
select 'TÜM' as jahr
union
select distinct
    Year(datum) as jahr
from
    laufende_ausgaben;";
}else{
   $sqlQuery = "select distinct
    Year(datum) as jahr
from
    verkaeufe Where status = 'B';"; 
}

        
//    verkaeufe Where status = 'B'" .$Where1.";";

//file_put_contents("jahrDS.txt",$sqlQuery );

$rs = $dbSyb->Execute($sqlQuery);

if (!$rs) {
    print $dbSyb->ErrorMsg() . "\n";
    return;
}
$i = 0;

$value = array();

while (!$rs->EOF) { 
	
    $value{$i}{"jahr"} = $rs->fields{'jahr'};
       
    $i++;
    $rs->MoveNext();
}

$rs->Close();

$output = json_encode($value);

print($output);
} else {
    header("Location: http://$host/klinik/noadmin.php");
}
