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


if (isset($_REQUEST["ausg_art_kz"])) {
    $ausg_art_kz = $_REQUEST["ausg_art_kz"];
    if ($ausg_art_kz != "null" && $ausg_art_kz != "") {
        if ((preg_match("/^[0-9a-zA-Z]{4}?$/", trim($ausg_art_kz))) == 0) {
            $out['response']['status'] = -1;
            $out['response']['errors'] = array('ausg_art_kz' => "Bitte eine Ausgabe-Art entspricht nicht den Kriterien");

            print json_encode($out);

            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('ausg_art_kz' => "Ausgabe-Art fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('ausg_art_kz' => "Ausgabe-Art fehlt!");

    print json_encode($out);
    return;
}

$sqlQuery = "call deleteAusgabeArt("
        . $dbSyb->Quote(utf8_decode($ausg_art_kz)) .
")";


//file_put_contents("addKunden.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('errors' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

If (isset($rs->fields['ergebnis']) && $rs->fields['ergebnis'] != -99) {
    if ($rs->fields['ergebnis'] != 1 && $rs->fields['ergebnis'] != 0) {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('errors' => "Es gab ein Problem beim Löschen aus der Datenbank! </br>" . utf8_encode($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    if (isset($rs->fields['ergebnis']) && $rs->fields['ergebnis'] == -99) {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('errors' => "Aus dieser Ausgabe-Art wurde bereits eine Ausgabe erstellt. Bitte löschen Sie erst diese Ausgabe, sofern diese nicht in Geschäftsvorfälle intrigiert ist und versuchen Sie es erneut." );

        print json_encode($out);
        return;
    } else {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('errors' => "Keine Ergebnis-Rückmeldung erhalten! Löschvorgang evtl. nicht erfolgreich. </br>" . utf8_encode($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
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

