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
    $out{'response'}{'errors'} = array('name' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;

if (isset($_REQUEST["kunden_nr"])) {
    $kunden_nr = $_REQUEST["kunden_nr"];
    if ($kunden_nr != "null" && $kunden_nr != "") {
        if ((preg_match("/^[0-9]{1,11}?$/", trim($kunden_nr))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('errors' => "Bitte die Menge prüfen. Maximal 11 Zeichen erlaubt");

            print json_encode($out);
            return;
        }
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('errors' => "Kunden-Nr fehlt!");

    print json_encode($out);
    return;
}

if (isset($_REQUEST["datum"])) {
    $datum = $_REQUEST["datum"];
    
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('datum' => "Datum fehlt!");

    print json_encode($out);

    return;
}

$sqlQuery = "Select zahlfrist, DATE_FORMAT(DATE_ADD(".$dbSyb->quote($datum).",INTERVAL zahlfrist DAY),GET_FORMAT(DATE, 'EUR')) as zahlDatum from kunden where concat(filial_nr,plz,lfd_nr) = ". $dbSyb->Quote($kunden_nr);

//file_put_contents("addProdukte.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
    $i = 0;

    while (!$rs->EOF) {

        $value{"zahlfrist"} = $rs->fields{'zahlfrist'};
        $value{"zahlDatum"} = $rs->fields{'zahlDatum'};

        $i++;

        // den n�chsten Datensatz lesen
        $rs->MoveNext();
    }

    $rs->Close();

    $out{'response'}{'status'} = 0;
    $out{'response'}{'errors'} = array();
    $out{'response'}{'data'} = $value;

    print json_encode($out);
