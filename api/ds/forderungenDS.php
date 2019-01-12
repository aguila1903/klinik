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

    if (isset($_REQUEST["freieSuche"])) {
        $freieSuche = $_REQUEST["freieSuche"];
        $Where1 = " Where beleg_nr like '%" . $freieSuche . "%' ";
    } else {

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

        if (isset($_REQUEST["kunden_nr"])) {
            $kunden_nr = $_REQUEST["kunden_nr"];
            if ($kunden_nr == "TÜM") {
                $Where2 = "";
            } else {
                $Where2 = " and kunden_nr = " . $kunden_nr;
            }
        } else {
            $Where2 = "";
        }

        if (isset($_REQUEST["auswahl"])) {
            $auswahl = $_REQUEST["auswahl"];
            if ($auswahl == "1" && $kunden_nr == "TÜM") { // Zeitlich begrenzt und alle Kunden
                $Where1 = " Where year(datum) = " . $jahr . " and month(datum) = " . $monat;
            } else if ($auswahl == "2" && $kunden_nr == "TÜM") { // Komplett und alle Kunden
                $Where1 = "";
            } else if ($auswahl == "1" && $kunden_nr != "TÜM") { // Zeitlich begrenzt aber nur Kunde x
                $Where1 = " Where year(datum) = " . $jahr . " and month(datum) = " . $monat . $Where2;
            } else if ($auswahl == "2" && $kunden_nr != "TÜM") { // Komplett aber nur Kunde x
                $Where1 = " Where kunden_nr = " . $kunden_nr;
            }
        } else {
            $Where1 = " Where year(datum) = " . $jahr . " and month(datum) = " . $monat;// Zeitlich begrenzt und alle Kunden
        }
    }



    $sqlQuery = "select 
    lfd_nr,
    beleg_nr,
    DATE_FORMAT(datum, GET_FORMAT(DATE, 'EUR')) as datum,
    kunden_nr,
    kundenname as kunden_name,
    sum(gesamtpr_brutto) as gesamtpr_brutto
from
    forderungen " . $Where1 .
            " group by  kunden_nr, beleg_nr order by beleg_nr, datum;";

    // file_put_contents("buchungenHaupt.txt",$sqlQuery );

    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $value{$i}{"lfd_nr"} = $rs->fields{'lfd_nr'};
        $value{$i}{"gesamtpr_brutto"} = number_format($rs->fields{'gesamtpr_brutto'}, 2, '.', '');
        $value{$i}{"kunden_name"} = utf8_encode($rs->fields{'kunden_name'});
        $value{$i}{"name_mit_kunden_nr"} = utf8_encode($rs->fields{'kunden_name'}) . " - Knd.-Nr.: " . $rs->fields{'kunden_nr'};
        $value{$i}{"kunden_nr"} = $rs->fields{'kunden_nr'};
        $value{$i}{"beleg_nr"} = $rs->fields{'beleg_nr'};
        $value{$i}{"datum"} = $rs->fields{'datum'};

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
