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
    $out{'response'}{'errors'} = array('prod_kz' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;



if (isset($_REQUEST["verkauf_an"])) {
    $kunden_nr = $_REQUEST["verkauf_an"];
    if ($kunden_nr != "null" && $kunden_nr != "") {
        if ((preg_match("/^[0-9]{1,11}?$/", trim($kunden_nr))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('verkauf_an' => "Bitte die Kunden-Nr prüfen");

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('verkauf_an' => "Kunden-Nr fehlt!");

        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('verkauf_an' => "Kunden-Nr fehlt!");

    print json_encode($out);
    return;
}


if (isset($_REQUEST["beleg_nr"])) {
    $beleg_nr = $_REQUEST["beleg_nr"];
    if ($beleg_nr != "null" && $beleg_nr != "") {
        if ((preg_match("/^[0-9\/]{1,45}?$/", trim($beleg_nr))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('beleg_nr' => "Bitte die Abrechnungsnr. prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('beleg_nr' => "Die Abrechnungsnr. fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('beleg_nr' => "Die Abrechnungsnr. fehlt!");

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

if (isset($_REQUEST["startTime"])) {
    $startTime = substr($_REQUEST["startTime"], 0, 5);
    if ($startTime != "null" && $startTime != "") {
        if ((preg_match("/^[0-9:]{5}?$/", trim($startTime))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('startTime' => "Baslangic zamani kontrol edin. " . $startTime);

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('startTime' => "Baslangic zamani eksik!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('startTime' => "Baslangic zamani eksik!");

    print json_encode($out);

    return;
}
if (isset($_REQUEST["endTime"])) {
    $endTime = substr($_REQUEST["endTime"], 0, 5);
    if ($endTime != "null" && $endTime != "") {
        if ((preg_match("/^[0-9:]{5}?$/", trim($endTime))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('endTime' => "Bitis zamani kontrol edin.");

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('endTime' => "Bitis zamani eksik!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('endTime' => "Bitis zamani eksik!");

    print json_encode($out);

    return;
}
if (str_replace(":", "", $endTime) < str_replace(":", "", $startTime)) {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('endTime' => "Bitis zamani baslangic zamanindan daha az!");
    print json_encode($out);
    return;
}


if (isset($_REQUEST["kunden_name"])) {
    $name = $_REQUEST["kunden_name"];
    if ($name != "null" && $name != "") {
        if (strlen($name) > 64 || strlen($name) < 1) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('verkauf_an' => "Bitte einen Kunden-Namen mit max. 64 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('verkauf_an' => "Kunden-Name fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('verkauf_an' => "Kunden-Name fehlt!");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["zahlungsziel"])) {
    $zahlungsziel = $_REQUEST["zahlungsziel"];
    if ($zahlungsziel != "null" && $zahlungsziel != "") {
        if ((preg_match("/^[sSzZ]{1}?$/", trim($zahlungsziel))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('zahlungsziel' => "Bitte das Zahlungsziel prüfen.");

            print json_encode($out);
            return;
        }
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('zahlungsziel' => "Zahlungsziel fehlt!");

    print json_encode($out);
    return;
}


$sqlQuery = "call editAbrechnung("
        . $kunden_nr .
        ", " . $dbSyb->Quote(($name)) .
        ", " . $dbSyb->quote($datum . " " . $startTime . ":00") .
        ", " . $dbSyb->Quote($datum . " " . $endTime . ":00") .
        ", " . $dbSyb->quote($beleg_nr);
$sqlQuery .= ", " . $dbSyb->quote($zahlungsziel) .
        ", " . $dbSyb->quote(utf8_decode($_SESSION['benutzer'])) . ")";

file_put_contents("editAbrechnung.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

If (isset($rs->fields{'ergebnis'})) {
    $ergebnis = $rs->fields{'ergebnis'};
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => "Keine Ergebnis-Rückmeldung erhalten! Evtl. war der Speichervorgang nicht erfolgreich.</br>" . ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

if ($ergebnis == -99) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => "Diese Abrechnungsnummer gibt es bereits für einen anderen Kunden. Bitte prüfen Sie Ihre Eingaben erneut!");

    print json_encode($out);
    return;
} elseif ($ergebnis == -98) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => "Bitte generieren Sie eine neue Abrechnungsnummer oder passen Sie das Rechnungsdatum an.");

    print json_encode($out);
    return;
} elseif ($ergebnis == -66) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('datum' => "Bu saatte bir randevu var!");

    print json_encode($out);
    return;
} elseif ($ergebnis > 0) {

    If (isset($rs->fields{'historie'})) {
        if ($rs->fields{'historie'} < 1) {
            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('errors' => "Es gab ein Problem beim Schreiben der Historie!</br>Vorgang wurde abgrebrochen</br>" . ($dbSyb->ErrorMsg()));

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('errors' => "Keine Historie-Rückmeldung erhalten! Evtl. wurden die Änderungen nicht protokolliert. </br>" . ($dbSyb->ErrorMsg()));

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

    $out{'response'}{'status'} = 0;
    $out{'response'}{'errors'} = array();
    $out{'response'}{'data'} = $value;

    print json_encode($out);
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => "Es ist ein unbekannter Fehler aufgetreten! Vorgang wird abgebrochen</br>" . ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
?>