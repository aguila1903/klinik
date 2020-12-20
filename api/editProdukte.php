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

    $out['response']['status'] = -1;
    $out['response']['errors'] = array('name' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;


if (isset($_REQUEST["prod_kz"])) {
    $prod_kz = $_REQUEST["prod_kz"];
    if ($prod_kz != "null" && $prod_kz != "") {
        if (strlen($prod_kz) != 4) {
            $out['response']['status'] = -1;
            $out['response']['errors'] = array('prod_kz' => "Bitte einen Produkt-Kürzel mit 4 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('prod_kz' => "Produkt-Kürzel fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('prod_kz' => "Produkt-Kürzel fehlt!");
    print json_encode($out);
    return;
}

if (isset($_REQUEST["bezeichnung"])) {
    $bezeichnung = $_REQUEST["bezeichnung"];
    if ($bezeichnung != "null" && $bezeichnung != "") {
        if (strlen($bezeichnung) > 250 || strlen($bezeichnung) < 1) {
            $out['response']['status'] = -1;
            $out['response']['errors'] = array('bezeichnung' => "Bitte eine Produkt-Bezeichnugn mit max. 250 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('bezeichnung' => "Bezeichnug fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('bezeichnung' => "Bezeichnug fehlt!");

    print json_encode($out);

    return;
}



if (isset($_REQUEST["brutto_preis1"])) {
    $brutto_preis1 = str_replace(".", "",$_REQUEST["brutto_preis1"]);
    if ($brutto_preis1 != "null" && $brutto_preis1 != "") {
        if ((preg_match("/^[0-9,]{1,10}?$/", trim($brutto_preis1))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('brutto_preis1' => "Bitte den Netto-Preis 1 prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('brutto_preis1' => "Der Netto-Preis 1 fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('brutto_preis1' => "Die Netto-Preis 1 fehlt!");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["brutto_preis2"])) {
    $brutto_preis2 = str_replace(".", "",$_REQUEST["brutto_preis2"]);
    if ($brutto_preis1 != "null" && $brutto_preis2 != "") {
        if ((preg_match("/^[0-9,]{1,10}?$/", trim($brutto_preis2))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('brutto_preis2' => "Bitte den Netto-Preis 2 prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('brutto_preis2' => "Der Netto-Preis 2 fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('brutto_preis2' => "Die Netto-Preis 2 fehlt!");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["mwst"])) {
    $mwst = $_REQUEST["mwst"];
    if ($mwst != "null" && $mwst != "") {
        if ((preg_match("/^[0-9.,]{1,6}?$/", trim($mwst))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('mwst' => "Bitte die MwSt prüfen");

            print json_encode($out);
            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('mwst' => "Die MwSt fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('mwst' => "Die MwSt fehlt!");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["aktiv"])) {
    $aktiv = $_REQUEST["aktiv"];

    if ($aktiv != "" && $aktiv != "null") {
        if ((preg_match("/^[0-1]{1}?$/", trim($aktiv))) == 0) {
            $out = array();

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('aktiv' => "Bitte den Produkt-Status prüfen");

            print json_encode($out);

            return;                                   // Der vertikale Strich '|' bedeuted oder.
        }
    } else {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('aktiv' => "Der Produkt-Status fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('aktiv' => "Der Produkt-Status fehlt!");

    print json_encode($out);

    return;
}




$sqlQuery = "call editProdukte("
        . $dbSyb->Quote(trim($prod_kz)) .
        ", " . $dbSyb->quote(trim($bezeichnung)) .
        ", " . str_replace(",", ".", $brutto_preis1) .
        ", " . str_replace(",", ".", $brutto_preis2) .
        ", " . str_replace(",", ".", $mwst) .
        ", " . $aktiv .
        ", " . $dbSyb->quote(trim($_SESSION['benutzer'])) .")";

file_put_contents("editProdukte.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('prod_kz' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

If (isset($rs->fields['ergebnis'])) {
    if ($rs->fields['ergebnis'] != 1 && $rs->fields['ergebnis'] != 0) {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('prod_kz' => "Es gab ein Problem beim Speichern in die Datenbank! </br>" . utf8_encode($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('prod_kz' => "Keine Ergebnis-Rückmeldung erhalten </br>" . utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

If (isset($rs->fields['historie'])) {
    if ($rs->fields['historie'] < 1) {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('prod_kz' => "Es gab ein Problem beim Schreiben der Historie!</br>Vorgang wurde abgrebrochen</br>" . utf8_encode($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('prod_kz' => "Keine Historie-Rückmeldung erhalten </br>" . utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

$i = 0;

while (!$rs->EOF) {

    $value{$i}['ergebnis'] = $rs->fields['ergebnis'];

    $i++;

    // den n�chsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();

$out['response']['status'] = 0;
$out['response']['errors'] = array();
$out['response']['data'] = $value;

print json_encode($out);

