<?php

/* * *****************************************************************************
  System: infotool - SVK-Versaende
  Funktion: Versandfehler anzeigen
  Autor: jra
  Datum: 04.12.2012

  Zusatzhinweise:

  �nderungen:

 * ***************************************************************************** */

session_start();

require_once('adodb5/adodb.inc.php');

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

if (!$dbSyb->IsConnected()) {

    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('name' => ($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;


if (isset($_REQUEST["prod_kz"])) {
    $prod_kz = $_REQUEST["prod_kz"];
    if ($prod_kz != "null" && $prod_kz != "") {
        if (strlen($prod_kz) != 4) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('prod_kz' => "Bitte einen Produkt-Kürzel mit 4 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('prod_kz' => "Produkt-Kürzel fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('prod_kz' => "Produkt-Kürzel fehlt!");
    print json_encode($out);
    return;
}


if (isset($_REQUEST["preis_kat"])) {
    $preis_kat = $_REQUEST["preis_kat"];
    if ($preis_kat != "null" && $preis_kat != "") {
        if ((preg_match("/^[1-9,]{1}?$/", trim($preis_kat))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('preis_kat' => "Bitte die Preis-Kategorie prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('preis_kat' => "Die Preis-Kategorie fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('preis_kat' => "Die Preis-Kategorie fehlt!");

    print json_encode($out);

    return;
}


$sqlQuery = "call selectPreisKat("
        . $dbSyb->Quote(utf8_decode($prod_kz)) .
        ", " .$preis_kat . ")";

//file_put_contents("addProdukte.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('prod_kz' => ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
    $i = 0;

    while (!$rs->EOF) {

        $value{"mwst"} = $rs->fields{'mwst'};
        $value{"brutto_preis"} = $rs->fields{'brutto_preis'};

        $i++;

        // den n�chsten Datensatz lesen
        $rs->MoveNext();
    }

    $rs->Close();

    $out{'response'}{'status'} = 0;
    $out{'response'}{'errors'} = array();
    $out{'response'}{'data'} = $value;

    print json_encode($out);
