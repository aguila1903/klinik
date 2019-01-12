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

if (isset($_REQUEST["kunden_name"])) {
    $name = $_REQUEST["kunden_name"];
    if ($name != "null" && $name != "") {
        if (strlen($name) > 64 || strlen($name) < 1) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('kunden_nr' => "Bitte einen Kunden-Namen mit max. 64 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('kunden_nr' => "Kunden-Name fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('kunden_nr' => "Kunden-Name fehlt!");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["prod_bez"])) {
    $bezeichnung = $_REQUEST["prod_bez"];
    if ($bezeichnung != "null" && $bezeichnung != "") {
        if (strlen($bezeichnung) > 250 || strlen($bezeichnung) < 1) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('prod_kz' => "Bitte eine Produkt-Bezeichnung mit max. 250 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('prod_kz' => "Produkt-Bezeichnung fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('prod_kz' => "Produkt-Bezeichnung fehlt!");

    print json_encode($out);

    return;
}
if (isset($_REQUEST["prod_kz"])) {
    $prod_kz = $_REQUEST["prod_kz"];
    if ($prod_kz != "null" && $prod_kz != "") {
        if (strlen($prod_kz) != 4) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('prod_kz' => "Bitte einen Produkt-Kürzel mit 4 Zeichen eingeben.");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('prod_kz' => "Produkt-Kürzel fehlt!");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('prod_kz' => "Produkt-Kürzel fehlt!");
    print json_encode($out);
    return;
}

if (isset($_REQUEST["verkauf_an"])) {
    $kunden_nr = $_REQUEST["verkauf_an"];
    if ($kunden_nr != "null" && $kunden_nr != "") {
        if ((preg_match("/^[0-9]{1,11}?$/", trim($kunden_nr))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('menge' => "Bitte die Menge prüfen. Maximal 11 Zeichen erlaubt");

            print json_encode($out);
            return;
        }
}} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('kunden_nr' => "Kunden-Nr fehlt!");

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


$sqlQuery = "call deletePosition("       
        . $lfd_nr .
        ", " . $dbSyb->Quote(trim($prod_kz)) .
        ", " . $dbSyb->Quote(trim($bezeichnung)) .
        ", " . $kunden_nr .
        ", " . $dbSyb->Quote(trim($name)) .
        ", " . $dbSyb->Quote(trim($beleg_nr)) .
        ", " . $dbSyb->quote(trim($_SESSION['benutzer'])) .
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
        $out{'response'}{'errors'} = array('errors' => "Sie versuchen ein Produkt zu löschen, welches in Geschäftsvorfälle verwickelt ist. Dies ist nicht möglich. Vorgang wird abgrebrochen</br>" . utf8_encode($dbSyb->ErrorMsg()));

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

$out{'response'}{'status'} = 0;
$out{'response'}{'errors'} = array();
$out{'response'}{'data'} = $value;

print json_encode($out);

