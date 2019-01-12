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


if (isset($_REQUEST["attach_id"])) {
    $attach_id = $_REQUEST["attach_id"];
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('errors' => "Kunden-Nr fehlt!");

    print json_encode($out);
    return;
}

if (isset($_REQUEST["bezeichnung"])) {
    $bezeichnung = $_REQUEST["bezeichnung"];
    if ($bezeichnung != "null" && $bezeichnung != "") {
        if (strlen($bezeichnung) > 64 || strlen($bezeichnung) < 1) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('bezeichnung' => "Soyisim en az 64 harf'den olusmali");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('bezeichnung' => "Soyisim eksik");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('bezeichnung' => "Soyisim eksik");

    print json_encode($out);

    return;
}

$sqlQuery = "call deleteKundenAttach("
        . $attach_id .
        ", " . $dbSyb->quote(utf8_decode($_SESSION['benutzer'])) .
        ")";


//file_put_contents("addKunden.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

If (isset($rs->fields{'ergebnis'}) && $rs->fields{'ergebnis'} != -99) {
    if ($rs->fields{'ergebnis'} != 1 && $rs->fields{'ergebnis'} != 0) {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('errors' => "Es gab ein Problem beim Löschen aus der Datenbank! </br>" . utf8_encode($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    if (isset($rs->fields{'ergebnis'}) && $rs->fields{'ergebnis'} == -99) {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('errors' => "Sie versuchen einen Kunden zulöschen, welcher in Geschäftsvorfälle eingebunden ist. Dies ist nicht möglich. Bitte setzen Sie den Kunden auf inaktiv. Vorgang wird abgebrochen. </br>" . utf8_encode($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    } else {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('errors' => "Keine Ergebnis-Rückmeldung erhalten! Löschvorgang evtl. nicht erfolgreich. </br>" . utf8_encode($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
}

If (isset($rs->fields{'historie'})) {
    if ($rs->fields{'historie'} < 1) {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('errors' => "Es gab ein Problem beim Schreiben der Historie!</br>Vorgang wurde abgrebrochen</br>" . utf8_encode($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => "Keine Historie-Rückmeldung erhalten </br>" . utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}



$i = 0;

while (!$rs->EOF) {

    $value{$i}{"ergebnis"} = $rs->fields{'ergebnis'};

    $i++;

    // den n�chsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();

$path1 = getcwd() . "\attachments\\";

if (is_file($path1 . $bezeichnung) === TRUE) {
    unlink($path1 . $bezeichnung);
}

$out{'response'}{'status'} = 0;
$out{'response'}{'errors'} = array();
$out{'response'}{'data'} = $value;

print json_encode($out);

