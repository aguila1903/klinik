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
    $out['response']['errors'] = array('errors' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;

if (isset($_REQUEST["kunden_nr"])) {
    $kunden_nr = $_REQUEST["kunden_nr"];
    if ($kunden_nr != "null" && $kunden_nr != "") {
        if ((preg_match("/^[0-9]{1,11}?$/", trim($kunden_nr))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('errors' => "Bitte die Menge prüfen. Maximal 11 Zeichen erlaubt");

            print json_encode($out);
            return;
        }
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('errors' => "Kunden-Nr fehlt!");

    print json_encode($out);
    return;
}

if (isset($_REQUEST["beleg_nr"])) {
    $beleg_nr = $_REQUEST["beleg_nr"];
    if ($beleg_nr != "null" && $beleg_nr != "") {
        if ((preg_match("/^[0-9\/]{1,45}?$/", trim($beleg_nr))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('errors' => "Bitte die Abrechnungsnr. prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('errors' => "Die Abrechnungsnr. fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('errors' => "Die Abrechnungsnr. fehlt!");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["datum"])) {
    $datum = $_REQUEST["datum"];
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('errors' => "Datum fehlt!");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["kunden_name"])) {
    $name = $_REQUEST["kunden_name"];
    if ($name != "null" && $name != "") {
        if (strlen($name) > 64 || strlen($name) < 1) {
            $out['response']['status'] = -1;
            $out['response']['errors'] = array('errors' => "Bitte einen Kunden-Namen mit max. 64 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('errors' => "Kunden-Name fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('errors' => "Kunden-Name fehlt!");

    print json_encode($out);

    return;
}

$sqlQuery = "call abrechnungBuchen (" . $dbSyb->Quote($beleg_nr) .
        ", " . $kunden_nr .
        ", " . $dbSyb->quote($datum) .
        ", " . $dbSyb->Quote(($name)) .
        ");";

//file_put_contents("abrechnungBuchen.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('errors' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
If (isset($rs->fields['ergebnis'])) {
    $ergebnis = $rs->fields['ergebnis'];
} else {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('errors' => "Keine Ergebnis-Rückmeldung erhalten! Evtl. war die Buchung nicht erfolgreich.</br>" . ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}


if ($ergebnis > 0) {

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
} else {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('errors' => "Es ist ein unbekannter Fehler aufgetreten! Vorgang wird abgebrochen</br>" . ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
?>
