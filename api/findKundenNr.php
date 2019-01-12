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

if (isset($_REQUEST["beleg_nr"])) {
    $beleg_nr =  $_REQUEST["beleg_nr"];
    if ($beleg_nr != "null" && $beleg_nr != "") {
        if ((preg_match("/^[0-9\/]{1,45}?$/", trim($beleg_nr))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('beleg_nr' => "Bitte die Abrechnungsnr. prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('beleg_nr' => "Die Abrechnungsnr. fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('beleg_nr' => "Die Abrechnungsnr. fehlt!");

    print json_encode($out);

    return;
}

$sqlQuery = "Select distinct verkauf_an, DATE_FORMAT(datum,GET_FORMAT(DATE,'EUR')) as datum from verkaeufe where beleg_nr = ". $dbSyb->Quote($beleg_nr) ;

//file_put_contents("addProdukte.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('beleg_nr' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
    $i = 0;

    while (!$rs->EOF) {

        $value{"kunden_nr"} = $rs->fields{'verkauf_an'};
        $value{"datum"} = $rs->fields{'datum'};

        $i++;

        // den n�chsten Datensatz lesen
        $rs->MoveNext();
    }

    $rs->Close();

    $out{'response'}{'status'} = 0;
    $out{'response'}{'errors'} = array();
    $out{'response'}{'data'} = $value;

    print json_encode($out);
