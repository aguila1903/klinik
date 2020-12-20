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


if (isset($_REQUEST["jahr"])){
$jahr = $_REQUEST["jahr"];
}else {
    $jahr = date("Y");
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
                $Where1 = " and year(datum) = " . $jahr;
            } else if ($auswahl == "2" && $kunden_nr == "TÜM") { // Komplett und alle Kunden
                $Where1 = "";
            } else if ($auswahl == "1" && $kunden_nr != "TÜM") { // zeitlich begrenzt aber nur Kunde x
                $Where1 =  " and year(datum) = " . $jahr . $Where2;
            } else if ($auswahl == "2" && $kunden_nr != "TÜM") { // Komplett aber nur Kunde x
                $Where1 = " and verkauf_an = " . $kunden_nr;
            }
        } else {
            $Where1 = "";
        }

$sqlQuery = "select distinct
    ifnull(Month(datum),Month(curdate())) as monat,
    case
        when ifnull(Month(datum),Month(curdate())) = 1 then 'Ocak'
        when ifnull(Month(datum),Month(curdate())) = 2 then 'Şubat'
        when ifnull(Month(datum),Month(curdate())) = 3 then 'Mart'
        when ifnull(Month(datum),Month(curdate())) = 4 then 'April'
        when ifnull(Month(datum),Month(curdate())) = 5 then 'Mayıs'
        when ifnull(Month(datum),Month(curdate())) = 6 then 'Haziran'
        when ifnull(Month(datum),Month(curdate())) = 7 then 'Temmuz'
        when ifnull(Month(datum),Month(curdate())) = 8 then 'Ağustos'
        when ifnull(Month(datum),Month(curdate())) = 9 then 'Eylül'
        when ifnull(Month(datum),Month(curdate())) = 10 then 'Ekim'
        when ifnull(Month(datum),Month(curdate())) = 11 then 'Kasım'
        when ifnull(Month(datum),Month(curdate())) = 12 then 'Aralık'
    end as monatsname
from
    verkaeufe
where
    status = 'B' ".$Where1
	. " order by datum asc;";
	
//file_put_contents("monatDS.txt",$sqlQuery );

$rs = $dbSyb->Execute($sqlQuery);

if (!$rs) {
    print $dbSyb->ErrorMsg() . "\n";
    return;
}
$i = 0;

$value = array();

while (!$rs->EOF) { 
	
    $value{$i}['monat'] = $rs->fields['monat'];
    $value{$i}['monatsname'] = ($rs->fields['monatsname']);
       
    $i++;
    $rs->MoveNext();
}

$rs->Close();

$output = json_encode($value);

print($output);
} else {
    header("Location: http://$host/klinik/noadmin.php");
}
