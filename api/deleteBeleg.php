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
    $out{'response'}{'errors'} = array('errors' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;


if (isset($_REQUEST["lfd_nr"])) {
    $lfd_nr = $_REQUEST["lfd_nr"];
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('errors' => "Laufende-Nr fehlt!");

    print json_encode($out);
    return;
}

if (isset($_REQUEST["beleg"])) {
    $name = $_REQUEST["beleg"];
    if ($name != "null" && $name != "") {
        if (strlen($name) > 200 || strlen($name) < 1) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('errors' => "Bitte einen Beleg-Namen mit max. 200 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('errors' => "Beleg-Name fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('errors' => "Beleg-Name fehlt!");

    print json_encode($out);

    return;
}



$sqlQuery = "Update laufende_ausgaben set beleg = NULL where lfd_nr = "       
        . $lfd_nr .";";


//file_put_contents("addKunden.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}


if(!unlink(getcwd() . "\Ausgaben\\" .utf8_decode($name))){
$fehlerMeldung = array();
$fehlerMeldung = error_get_last();

$out{'response'}{'status'} = -1;
$out{'response'}{'errors'} = array('errors' => $fehlerMeldung['message']);

    print json_encode($out);

    return;
}

while (!$rs->EOF) {

    $rs->MoveNext();
}

$rs->Close();

$out{'response'}{'status'} = 0;
$out{'response'}{'errors'} = array();
$out{'response'}{'data'} = $value;

print json_encode($out);

