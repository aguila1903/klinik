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

    if (isset($_REQUEST["name_voll"])) {
        $name_voll = $_REQUEST["name_voll"];
        $whereKunde = "Where (vorname like '%" . $name_voll . "%' or name like '%" . $name_voll . "%' or kunden_nr like '%" . $name_voll . "%' or concat(vorname,' ',name) like '%" . $name_voll . "%' or DATE_FORMAT(geburtstag,GET_FORMAT(DATE,'EUR')) like '%" . $name_voll . "%')";
    } else {
        $whereKunde = "";
    }

    $sqlQuery = "SELECT "
            . " lfd_nr"
            . ", kunden_nr "
            . ", vorname"
            . ", DATE_FORMAT(geburtstag,GET_FORMAT(DATE,'EUR')) as geburtstag"
            . ", name "
            . ", strasse"
            . ", trim(telefon) as telefon "
            . ", fax "
            . ", email "
            . ", kommentar "
            . ", ifnull(timestampdiff(year,geburtstag,CURDATE()),0) as yas "
            . " from kunden "
            . $whereKunde
            . " Order by vorname ";

//     file_put_contents("kundenDS", $sqlQuery);

    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $value{$i}['lfd_nr'] = $rs->fields['lfd_nr'];
        $value{$i}['kunden_nr'] = $rs->fields['kunden_nr'];
        $value{$i}['name'] = $rs->fields['name'];
        $value{$i}['vorname'] = $rs->fields['vorname'];
        $value{$i}['strasse'] = ($rs->fields['strasse']);
        $value{$i}['telefon'] = ($rs->fields['telefon']);
        $value{$i}['email'] = ($rs->fields['email']);
        $value{$i}['fax'] = $rs->fields['fax'];
        $value{$i}['yas'] = number_format($rs->fields['yas'],0);
        $value{$i}['geburtstag'] = $rs->fields['geburtstag'];
        $value{$i}['kommentar'] = $rs->fields['kommentar'];
        $value{$i}['name_voll'] = $rs->fields['vorname'] . " " . $rs->fields['name'];

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
