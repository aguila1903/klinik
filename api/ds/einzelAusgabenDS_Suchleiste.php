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


    if (isset($_REQUEST["jahr"])) {
        $jahr = $_REQUEST["jahr"];
    } else {
        $jahr = date("Y");
    }

    if (isset($_REQUEST["monat"])) {
        $monat = $_REQUEST["monat"];
    } else {
        $monat = date("m");
    }   
 if (isset($_REQUEST["ausg_art_kz"])) {
        $ausg_art_kz = $_REQUEST["ausg_art_kz"];
        if ($ausg_art_kz == "TÜM") {
            $Where2 = "";
        } else {
            $Where2 = " and a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz);
        }
    } else {
        $Where2 = "";
    }


    if (isset($_REQUEST["auswahl"])) {
        $auswahl = $_REQUEST["auswahl"];
        if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $monat == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art Alles, Monat Alles
            $Where1 = " and year(datum) = " . $jahr;
        } else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $monat != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art beschränkt, Monat beschränkt
            $Where1 = " and year(datum) = " . $jahr . $Where2 . " and month(datum) = ". $monat;
        } else if ($auswahl == "2" && $ausg_art_kz != "TÜM" && $monat == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art beschränkt, Monat Alles
            $Where1 = " and year(datum) = " . $jahr . $Where2;
        } else if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $monat == "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausg-Art Alles, Monat Alles
            $Where1 = "";
        } else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $monat != "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausgabe-Art beschränkt, Monat beschränkt
            $Where1 = " and a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz) . " and month(datum) = ". $monat;
        }else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $monat != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alles, Monat beschränkt
            $Where1 = " and a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz) . " and month(datum) = ". $monat;
        }
		else if ($auswahl == "2" && $ausg_art_kz != "TÜM" && $monat != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art beschränkt, Monat beschränkt
            $Where1 = " and a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz) . " and month(datum) = ". $monat;
        }
		else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $monat == "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausgabe-Art Alles, Monat Alles
            $Where1 = "";
        }
		else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $monat == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alles, Monat Alles
            $Where1 = " and year(datum) = " . $jahr;
        }
		else if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $monat == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alles, Monat Alles
            $Where1 = " and year(datum) = " . $jahr;
        }
		else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $monat == "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausgabe-Art beschränkt, Monat Alles
            $Where1 =  " and a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz);
        }
		else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $monat == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art beschränkt, Monat Alles
            $Where1 =  " and a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz)." and year(datum) = " . $jahr;
        }
				
    } else {
        $Where1 = "";
    }



$sqlQuery = " Select 'TÜM' as ausg_kz, 'TÜM' as bezeichnung
Union
select a.ausg_kz, b.bezeichnung from laufende_ausgaben a, ausgaben b Where a.ausg_kz = b.ausg_kz and a.ausg_art_kz = b.ausg_art_kz ".$Where1;

// file_put_contents("einzelAusgabenDS.txt",$sqlQuery );

$rs = $dbSyb->Execute($sqlQuery);


if (!$rs) {
    print $dbSyb->ErrorMsg() . "\n";
    return;
}
$i = 0;

$value = array();

while (!$rs->EOF) {

    $value{$i}['ausg_kz'] = utf8_encode($rs->fields['ausg_kz']);
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
