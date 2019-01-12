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
        $out{'response'}{'status'} = 99;
        $out{'response'}{'errors'} = array();
        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = 99;
    $out{'response'}{'errors'} = array();
    print json_encode($out);
    return;
}

$sqlQuery = "select sum(menge) as menge, 
sum(einzelpr_netto) as einzelpr_netto, 
sum(einzelpr_netto*(mwst/100)) as mwst_einzelpr,
sum((einzelpr_netto*(mwst/100))+einzelpr_netto) as einzelpr_brutto,
sum(gesamtpr_brutto) as gesamtpr_brutto,
sum(gesamtpr_brutto*(mwst/100)) as mwst_gesamtpr,
sum((gesamtpr_brutto*(mwst/100))+gesamtpr_brutto) as gesamtpr_brutto
from verkaeufe where beleg_nr = ". $dbSyb->Quote($beleg_nr) ;

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

    $value{"menge"} = $rs->fields{'menge'};
    $value{"einzelpr_netto"} = number_format($rs->fields{'einzelpr_netto'}, 2, ',', '.');
    $value{"mwst_einzelpr"} = number_format($rs->fields{'mwst_einzelpr'}, 4, ',', '.');
    $value{"einzelpr_brutto"} = number_format($rs->fields{'einzelpr_brutto'}, 2, ',', '.');
    $value{"gesamtpr_brutto"} = number_format($rs->fields{'gesamtpr_brutto'}, 2, ',', '.');
    $value{"mwst_gesamtpr"} = number_format($rs->fields{'mwst_gesamtpr'}, 4, ',', '.');
    $value{"gesamtpr_brutto"} = number_format($rs->fields{'gesamtpr_brutto'}, 2, ',', '.');

        $i++;

        // den n�chsten Datensatz lesen
        $rs->MoveNext();
    }

    $rs->Close();

    $out{'response'}{'status'} = 0;
    $out{'response'}{'errors'} = array();
    $out{'response'}{'data'} = $value;

    print json_encode($out);
