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


if (isset($_REQUEST["lfd_nr"])) {
    $lfd_nr = $_REQUEST["lfd_nr"];
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('errors' => "Laufende-Nr fehlt!");

    print json_encode($out);
    return;
}

if (isset($_REQUEST["beleg"])) {
    $name = $_REQUEST["beleg"];
    if ($name != "null" && $name != "") {
        if (strlen($name) > 200 || strlen($name) < 1) {
            $out['response']['status'] = -1;
            $out['response']['errors'] = array('errors' => "Bitte einen Beleg-Namen mit max. 200 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $name = 'no_pic';
    }
} else {
    $name = 'no_pic';
}

$sqlQuery = "call deleteAusgabe (" . $lfd_nr . ")";


// file_put_contents("deleteAusgabe.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();




if (!$rs) {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('errors' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
if (isset($rs->fields['ergebnis'])) {
    $ergebnis = $rs->fields['ergebnis'];
    if ($ergebnis != 1) {

        if ($ergebnis == 0) {
            $out['response']['data'] = $ergebnis;
            $out['response']['status'] = -4;
            $out['response']['errors'] = array('errors' => "Es wurde kein Datensatz gelöscht. </br>" . utf8_encode($dbSyb->ErrorMsg()));

            print json_encode($out);
            return;
        } else {
            $out['response']['data'] = $ergebnis;
            $out['response']['status'] = -4;
            $out['response']['errors'] = array('errors' => "Es gab ein Problem beim Löschen aus der Datenbank! </br>" . utf8_encode($dbSyb->ErrorMsg()));

            print json_encode($out);
            return;
        }
    }
} else {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('errors' => "Keine Ergebnis-Rückmeldung erhalten! Löschvorgang evtl. nicht erfolgreich. </br>" . utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

while (!$rs->EOF) {

    $value['ergebnis'] = $rs->fields['ergebnis'];


    // den n�chsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();

if($name != 'no_pic'){
unlink(getcwd() . "\Ausgaben\\" .utf8_decode($name));
}


$out['response']['status'] = 0;
$out['response']['errors'] = array();
$out['response']['data'] = $value;

print json_encode($out);

