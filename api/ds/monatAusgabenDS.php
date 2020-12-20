<?php

session_start();

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');
header("Cache-Control: no-cache, must-revalidate");
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

if (isset($_SESSION["login"]) && $_SESSION["login"] == login) {

    /*     * *****************************************************************************
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


    if (isset($_REQUEST["ausg_art_kz"])) {
        $ausg_art_kz = $_REQUEST["ausg_art_kz"];
        if ($ausg_art_kz == "TÜM") {
            $Where2 = "";
        } else {
            $Where2 = " and ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz);
        }
    } else {
        $Where2 = "";
    }

    if (isset($_REQUEST["ausg_kz"])) {
        $ausg_kz = $_REQUEST["ausg_kz"];
        if ($ausg_kz == "TÜM") {
            $Where3 = "";
        } else {
            $Where3 = " and ausg_kz = " . $dbSyb->Quote($ausg_kz);
        }
    } else {
        $Where3 = "";
    }

    if (isset($_REQUEST["auswahl"])) {
        $auswahl = $_REQUEST["auswahl"];
        if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $ausg_kz == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art Alles, Ausgaben Alles
            $Where1 = " where year(datum) = " . $jahr;
        } else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art beschränkt, Ausgaben beschränkt
            $Where1 = " where year(datum) = " . $jahr . $Where2 . $Where3;
        } else if ($auswahl == "2" && $ausg_art_kz != "TÜM" && $ausg_kz != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art beschränkt, Ausgaben beschränkt
            $Where1 = " where year(datum) = " . $jahr . $Where2 . $Where3;
        }else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art beschränkt, Ausgaben Alles
            $Where1 = " where year(datum) = " . $jahr . $Where2;
        } else if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $ausg_kz == "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausg-Art Alles, Ausgaben Alles
            $Where1 = "";
        } else if ($auswahl == "2" && $ausg_art_kz != "TÜM" && $ausg_kz != "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausgabe-Art beschränkt, Ausgaben beschränkt
            $Where1 = " where ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz) . $Where3;
        }
        else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $ausg_kz == "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausgabe-Art Alles, Ausgabe Alles
            $Where1 = "";
        }
        else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz == "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausgabe-Art Alles, Ausgabe Alles
            $Where1 = " where ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz);
        }
        else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $ausg_kz != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alles, Ausgabe beschränkt
            $Where1 = " where ausg_kz = " . $dbSyb->Quote($ausg_kz). " and year(datum) = " . $jahr;
        }
        else if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $ausg_kz != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alles, Ausgabe beschränkt
            $Where1 = " where ausg_kz = " . $dbSyb->Quote($ausg_kz). " and year(datum) = " . $jahr;
        }
		else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz != "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausgabe-Art beschränkt, Ausgabe beschränkt
            $Where1 = " where ausg_kz = " . $dbSyb->Quote($ausg_kz). $Where2;
        }
    } else {
        $Where1 = "";
    }

    $sqlQuery = "
Select 'TÜM' as monat, 'TÜM' as monatsname, 0 as monat2 
union
select distinct
    ifnull(Month(datum),Month(curdate())) as monat, 
    case
        when ifnull(Month(datum),Month(curdate())) = 1 then 'Januar'
        when ifnull(Month(datum),Month(curdate())) = 2 then 'Februar'
        when ifnull(Month(datum),Month(curdate())) = 3 then 'März'
        when ifnull(Month(datum),Month(curdate())) = 4 then 'April'
        when ifnull(Month(datum),Month(curdate())) = 5 then 'Mai'
        when ifnull(Month(datum),Month(curdate())) = 6 then 'Juni'
        when ifnull(Month(datum),Month(curdate())) = 7 then 'Juli'
        when ifnull(Month(datum),Month(curdate())) = 8 then 'August'
        when ifnull(Month(datum),Month(curdate())) = 9 then 'September'
        when ifnull(Month(datum),Month(curdate())) = 10 then 'Oktober'
        when ifnull(Month(datum),Month(curdate())) = 11 then 'November'
        when ifnull(Month(datum),Month(curdate())) = 12 then 'Dezember'
    end as monatsname,
    case when
    length(ifnull(Month(datum),Month(curdate()))) = 1 then concat('0',ifnull(Month(datum),Month(curdate()))) 
    else ifnull(Month(datum),Month(curdate())) end as monat2
    
from
    laufende_ausgaben ".
$Where1
            . " order by monat2 asc;";

// file_put_contents("monatAusgabenDS.txt",$sqlQuery );

    $rs = $dbSyb->Execute($sqlQuery);

    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $value{$i}['monat'] = $rs->fields['monat'];
        $value{$i}['monatsname'] = $rs->fields['monatsname'];

        $i++;
        $rs->MoveNext();
    }

    $rs->Close();

    $output = json_encode($value);

    print($output);
} else {
    header("Location: http://$host/klinik/noadmin.php");
}
