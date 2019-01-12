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

    if (isset($_REQUEST["monat"])) {
        $monat = $_REQUEST["monat"];
    } else {
        $monat = date("m");
    }

    if (isset($_REQUEST["auswahl"])) {
        $auswahl = $_REQUEST["auswahl"];
        if ($auswahl == "1") { // Monatlich
            $Where1 = " Where year(datum) = " . $jahr . " and month(datum) = " . $monat;
        } else if ($auswahl == "2") { // Komplett 
            $Where1 = "";
        }
    } else {
        $Where1 = "";
    }

    if (isset($_REQUEST["auswahl"])) {
        $auswahl = $_REQUEST["auswahl"];
        if ($auswahl == "1") { // Monatlich
            $Where2 = " and year(datum) = " . $jahr . " and month(datum) = " . $monat;
        } else if ($auswahl == "2") { // Komplett 
            $Where2 = "";
        }
    } else {
        $Where2 = "";
    }

    if (isset($_REQUEST["auswahl"])) {
        $auswahl = $_REQUEST["auswahl"];
        if ($auswahl == "1") { // Monatlich
            $Where3 = " Where year(datum) = " . $jahr . " and month(datum) = " . $monat . " and verkauf_an = k.lfd_nr and status = 'B'";
        } else if ($auswahl == "2") { // Komplett 
            $Where3 = "";
        }
    } else {
        $Where3 = "";
    }

    $sqlQuery = "SELECT distinct"
            . "  k.lfd_nr "
            . " ,k.kunden_nr "
            . " ,concat(vorname,' ',name) as name"
            . ", DATE_FORMAT(k.geburtstag,GET_FORMAT(DATE,'EUR')) as geburtstag"
            . ", (select count(*) from verkaeufe " . $Where3 . ") as anzVorg "
            . ", ifnull(timestampdiff(year,geburtstag,CURDATE()),0) as yas "
            . " from kunden k, verkaeufe f "
            . " Where k.lfd_nr = f.verkauf_an and status = 'B'"
            . $Where2
            . " Order by vorname ";
    ;
//    file_put_contents("kundenDS_Union_All.txt", $sqlQuery . "\n", FILE_APPEND);
    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $value{$i}{"kunden_nr"} = $rs->fields{'kunden_nr'};
        $value{$i}{"lfd_nr"} = $rs->fields{'lfd_nr'};
        $value{$i}{"name"} = $rs->fields{'name'};
        $value{$i}{"yas"} = number_format($rs->fields{'yas'},0);
        $value{$i}{"geburtstag"} = $rs->fields{'geburtstag'};
        $value{$i}{"anzVorg"} = $rs->fields{'anzVorg'};

        $i++;

        // den n�chsten Datensatz lesen
        $rs->MoveNext();
    }

    $rs->Close();



    $output = json_encode($value);

    print($output);
} else {
    header("Location: http://$host/klinik/noadmin.php");
}
