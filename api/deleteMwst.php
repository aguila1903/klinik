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
    $out['response']['errors'] = array('errors' => trim($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;


if (isset($_REQUEST["lfd_nr"])) {
    $lfd_nr = $_REQUEST["lfd_nr"];
    if ($lfd_nr != "null" && $lfd_nr != "") {
        if ((preg_match("/^[0-9]{1,11}?$/", trim($lfd_nr))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('lfd_nr' => "Bitte die LfdNr prüfen. Maximal 11 Zeichen erlaubt");

            print json_encode($out);
            return;
        }
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('lfd_nr' => "Lfd-Nr fehlt!");

    print json_encode($out);
    return;
}



$sqlQuery = "call deleteMwst("
        . $lfd_nr .
        ")";


file_put_contents("addKunden.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

if (!$rs) {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('errors' => trim($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}


$value = array();



If ($rs->fields['ergebnis'] != -99 && $rs->fields['ergebnis'] != -98) {
    if ($rs->fields['ergebnis'] != 1) {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('errors' => "Es gab ein Problem beim Löschen aus der Datenbank! </br>" . trim($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }else{
	$out['response']['status'] = 0;
    $out['response']['errors'] = array();
    $out['response']['data'] = $value;

    print json_encode($out);
	}
} else {
    if ($rs->fields['ergebnis'] == -99) {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('errors' => "Sie versuchen einen MwSt-Satz zu löschen, welches in Geschäftsvorfälle eingebunden ist. Dies ist nicht möglich. </br>" . trim($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    } else if($rs->fields['ergebnis'] == -98){
		$out['response']['status'] = -4;
        $out['response']['errors'] = array('errors' => "Der MwSt-Satz wurde nicht gelöscht</br>" . trim($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
	}	
	else {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('errors' => "Keine Ergebnis-Rückmeldung erhalten! Löschvorgang evtl. nicht erfolgreich. </br> ".$rs->fields['ergebnis'] . trim($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
}




