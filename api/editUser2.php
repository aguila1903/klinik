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
    $out['response']['errors'] = array('kunden_nr' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;


if (isset($_REQUEST["UserID"])) {
    $UserID = $_REQUEST["UserID"];
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('UserID' => "UserID fehlt!");

    print json_encode($out);
    return;
}



if (isset($_REQUEST["admin"])) {
    $admin = $_REQUEST["admin"];
    if ($admin != "null" && $admin != "") {
        if ((preg_match("/^[JN]{1}?$/", trim($admin))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('admin' => "Bitte das Feld Admin prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('admin' => "Admin fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('admin' => "Admin fehlt!");

    print json_encode($out);

    return;
}
if (isset($_REQUEST["status"])) {
    $status = $_REQUEST["status"];
    if ($status != "null" && $status != "") {
        if ((preg_match("/^[OB]{1}?$/", trim($status))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('status' => "Bitte das Feld Status prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('status' => "Status fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out['response']['status'] = -1;
    $out['response']['errors'] = array('status' => "Status fehlt!");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["email"])) {
    $e_mail = $_REQUEST["email"];

    if ($e_mail != "" && $e_mail != "null") {
        if ((preg_match("/^(([a-zA-Z0-9_.\\-+])+@(([a-zA-Z0-9\\-])+\\.)+[a-zA-Z0-9]{2,4})|([ ])|([null])$/", trim(trim($e_mail)))) == 0) {


            $out['response']['status'] = -4;
            $out['response']['errors'] = array('email' => "Bitte eine korrekte eMail-Adresse eingeben.");

            print json_encode($out);

            return;                                   // Der vertikale Strich '|' bedeuted oder.
        }
    } else {
        $e_mail = null;
    }
} else {
    $e_mail = null;
}




$sqlQuery = "call editUser2("
        . $UserID .
        ", " . $dbSyb->Quote(trim($admin)) .
        ", " . $dbSyb->quote(trim($status));
if ($e_mail == null) {
    $sqlQuery .= ", NULL";
} else {
    $sqlQuery .= ", " . $dbSyb->quote(trim($e_mail));
}
$sqlQuery .= ", " . $dbSyb->quote(trim($_SESSION['benutzer'])) . ")";


//file_put_contents("editKunden.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('UserID' => ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
If (isset($rs->fields['ergebnis'])) {
    if ($rs->fields['ergebnis'] != 1 && $rs->fields['ergebnis'] != 0) {
        $out['response']['status'] = -4;
        $out['response']['errors'] = array('UserID' => "Veritabanına kaydedilirken bir sorun oluştu! </br>" . ($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('UserID' => "Sonuçta geri bildirim alınamadı </br>" . ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
//If (isset($rs->fields['historie'])) {
//    if ($rs->fields['historie'] < 1) {
//        $out['response']['status'] = -4;
//        $out['response']['errors'] = array('UserID' => "Es gab ein Problem beim Schreiben der Historie!</br>Vorgang wurde abgrebrochen</br>" . utf8_encode($dbSyb->ErrorMsg()));
//
//        print json_encode($out);
//        return;
//    }
//} else {
//    $out['response']['status'] = -4;
//    $out['response']['errors'] = array('UserID' => "Keine Historie-Rückmeldung erhalten </br>" . utf8_encode($dbSyb->ErrorMsg()));
//
//    print json_encode($out);
//    return;
//}

$i = 0;

while (!$rs->EOF) {

    $value{$i}['UserID'] = $rs->fields['UserID'];
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

