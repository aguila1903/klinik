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


if (isset($_REQUEST["ausg_art_kz"])) {
    $ausg_art_kz = $_REQUEST["ausg_art_kz"];
    if ($ausg_art_kz != "null" && $ausg_art_kz != "") {
        if ((preg_match("/^[0-9a-zA-Z]{4}?$/", trim($ausg_art_kz))) == 0) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('ausg_art_kz' => "Bitte eine Ausgabe-Art entspricht nicht den Kriterien");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('ausg_art_kz' => "Ausgabe-Art fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('ausg_art_kz' => "Ausgabe-Art fehlt!");

    print json_encode($out);
    return;
}
if (isset($_REQUEST["ausg_kz"])) {
    $ausg_kz = $_REQUEST["ausg_kz"];
    if ($ausg_kz != "null" && $ausg_kz != "") {
        if ((preg_match("/^[0-9a-zA-Z]{4}?$/", trim($ausg_kz))) == 0) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('ausg_kz' => "Bitte eine Ausgabe entspricht nicht den Kriterien");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('ausg_kz' => "Ausgabe fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('ausg_kz' => "Ausgabe fehlt!");

    print json_encode($out);
    return;
}



if (isset($_REQUEST["betrag_netto"])) {
    $betrag_netto = str_replace(".", "", $_REQUEST["betrag_netto"]);
    if ($betrag_netto != "null" && $betrag_netto != "") {
        if ((preg_match("/^[0-9,]{1,10}?$/", trim($betrag_netto))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('betrag_netto' => "Bitte den Netto-Betrag prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('betrag_netto' => "Der Netto-Betrag fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('betrag_netto' => "Der Netto-Betrag fehlt!");

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

if (isset($_REQUEST["mwst_satz"])) {
    $mwst_satz = $_REQUEST["mwst_satz"];
    if ($mwst_satz != "null" && $mwst_satz != "") {
        if ((preg_match("/^[0-9.,]{1,6}?$/", trim($mwst_satz))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('mwst_satz' => "Bitte die MwSt prüfen");

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('mwst_satz' => "Die MwSt fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('mwst_satz' => "Die MwSt fehlt!");

    print json_encode($out);

    return;
}



$sqlQuery = "call addAusgabe("
        . $dbSyb->Quote(utf8_decode($ausg_art_kz)) .
        ", " . $dbSyb->quote(utf8_decode($ausg_kz)) .
        ", " . str_replace(",", ".",$betrag_netto) .
        ", " . str_replace(",", ".",$mwst_satz) .
        ", " . $dbSyb->quote($datum) . ")";

// file_put_contents("addAusgabe.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('ausg_art_kz' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

If (isset($rs->fields{'ergebnis'})) {
    if ($rs->fields{'ergebnis'} != 1 && $rs->fields{'ergebnis'} != 0) {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('ausg_art_kz' => "Es gab ein Problem beim Speichern in die Datenbank! </br>" . utf8_encode($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('ausg_art_kz' => "Keine Ergebnis-Rückmeldung erhalten </br>" . utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

$i = 0;

while (!$rs->EOF) {

    $value{$i}{"lfd_nr"} = $rs->fields{'lfd_nr'};
    $value{$i}{"ergebnis"} = $rs->fields{'ergebnis'};

    $i++;

    // den n�chsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();

$out{'response'}{'status'} = 0;
$out{'response'}{'errors'} = array();
$out{'response'}{'data'} = $value;

print json_encode($out);

