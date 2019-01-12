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
$bday = array();

if (!$dbSyb->IsConnected()) {

    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('errors' => trim($dbSyb->ErrorMsg()));

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

if (isset($_REQUEST["name"])) {
    $name = $_REQUEST["name"];
    if ($name != "null" && $name != "") {
        if (strlen($name) > 64 || strlen($name) < 1) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('name' => "Soyisim en az 64 harf'den olusmali");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('name' => "Soyisim eksik");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('name' => "Soyisim eksik");

    print json_encode($out);

    return;
}
if (isset($_REQUEST["vorname"])) {
    $vorname = $_REQUEST["vorname"];
    if ($vorname != "null" && $vorname != "") {
        if (strlen($vorname) > 64 || strlen($vorname) < 1) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('vorname' => "Isim en az 64 harf'den olusmali");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('vorname' => "Isim eksik");

        print json_encode($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('vorname' => "Isim eksik");

    print json_encode($out);

    return;
}

if (isset($_REQUEST["geburtstag"])) {
    $geburtstag = $_REQUEST["geburtstag"];
    if ($geburtstag != "null" && $geburtstag != "") {
        if ((preg_match("/^(([0-9]{2})+.(([0-9]{2})+.[0-9]{4}))|([ ])?$/", trim($geburtstag))) == 0) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('geburtstag' => "Doğum gününü lütfen su sekilde giriniz 24.08.1981");

            print json_encode($out);

            return;
        }
        $bday = explode(".", $geburtstag);
        if (checkdate($bday[1], $bday[0], $bday[2])) {

            $geburtstag = $bday[2] . "-" . $bday[1] . "-" . $bday[0];
        } else {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('geburtstag' => "Girilen Dogum günü gercek bir tarih degildir.");

            print json_encode($out);

            return;
        }
    } else {
        $geburtstag = null;
    }
} else {
    $geburtstag = null;
}

if (isset($_REQUEST["strasse"])) {
    $strasse = $_REQUEST["strasse"];
    if ($strasse != "null" && $strasse != "") {
        if (strlen($strasse) > 1000) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('strasse' => "Adres cok uzun lütfen 1000 harfi gecmeyiniz");

            print json_encode($out);

            return;
        }
    } else {
        $strasse = null;
    }
} else {
    $strasse = null;
}
if (isset($_REQUEST["kommentar"])) {
    $not = $_REQUEST["kommentar"];
    if ($not != "null" && $not != "") {
        if (strlen($not) > 2000) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('kommentar' => "Notunuz cok uzun lütfen 2000 harfi gecmeyiniz");

            print json_encode($out);

            return;
        }
    } else {
        $not = null;
    }
} else {
    $not = null;
}

if (isset($_REQUEST["kunden_nr"])) {
    $kunden_nr = $_REQUEST["kunden_nr"];
    if ($kunden_nr != "null" && $kunden_nr != "") {
        if ((preg_match("/^[0-9]{11}?$/", trim($kunden_nr))) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('kunden_nr' => "T.C. Kimlik numarasi yanlis, lütfen tekrar kontrol ediniz");

            print json_encode($out);
            return;
        }
    } else {
        $kunden_nr = null;
    }
} else {
    $kunden_nr = null;
}

if (isset($_REQUEST["telefon"])) {
    $telefon = $_REQUEST["telefon"];
    if ($telefon != "null" && $telefon != "") {

        if (preg_match("/(^[0-9\/\()\-\+\s]{0,45}?$)|([ ])/", $telefon) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('telefon' => "Bitte die Tel-Nr überprüfen!");

            print json_encode($out);
            return;
        }
    } else {
        $telefon = null;
    }
} else {
    $telefon = null;
}

if (isset($_REQUEST["fax"])) {
    $fax = $_REQUEST["fax"];
    if ($fax != "null" && $fax != "") {

        if (preg_match("/(^[0-9\/\()\-\+\s]{0,45}?$)|([ ])/", $fax) == 0) {

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('fax' => "Bitte die Fax-Nr überprüfen!");

            print json_encode($out);
            return;
        }
    } else {
        $fax = null;
    }
} else {
    $fax = null;
}

if (isset($_REQUEST["email"])) {
    $e_mail = $_REQUEST["email"];

    if ($e_mail != "" && $e_mail != "null") {
        if ((preg_match("/^(([a-zA-Z0-9_.\\-+])+@(([a-zA-Z0-9\\-])+\\.)+[a-zA-Z0-9]{2,4})|([ ])|([null])$/", trim(utf8_decode($e_mail)))) == 0) {
            $out = array();

            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('email' => "Lütfen e-Posta adresini tekrar kontrol ediniz");

            print json_encode($out);

            return;                                   // Der vertikale Strich '|' bedeuted oder.
        }
    } else {
        $e_mail = null;
    }
} else {
    $e_mail = null;
}


$sqlQuery = "call editKunden("
        . $lfd_nr;
if ($kunden_nr == null) {
    $sqlQuery .= ", NULL";
} else {
    $sqlQuery .= ", " . $dbSyb->Quote(trim($kunden_nr));
}
$sqlQuery .= ", " . $dbSyb->Quote(trim($name)) .
        ", " . $dbSyb->Quote(trim($vorname));

if ($strasse == null) {
    $sqlQuery .= ", NULL";
} else {
    $sqlQuery .= ", " . $dbSyb->Quote(trim($strasse));
}
if ($telefon == null) {
    $sqlQuery .= ", NULL";
} else {
    $sqlQuery .= ", " . $dbSyb->Quote(trim($telefon));
}
if ($fax == null) {
    $sqlQuery .= ", NULL";
} else {
    $sqlQuery .= ", " . $dbSyb->Quote(trim($fax));
}
if ($e_mail == null) {
    $sqlQuery .= ", NULL";
} else {
    $sqlQuery .=", " . $dbSyb->Quote(trim($e_mail));
}
if ($not == null) {
    $sqlQuery .= ", NULL";
} else {
    $sqlQuery .=", " . $dbSyb->Quote(trim($not));
}
if ($geburtstag == null) {
    $sqlQuery .= ", NULL";
} else {
    $sqlQuery .=", " . $dbSyb->Quote(trim($geburtstag));
}
$sqlQuery .= ", " . $dbSyb->Quote(trim($_SESSION['benutzer'])) . ")";


file_put_contents("editKunden.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => trim($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
If (isset($rs->fields{'ergebnis'})) {
    if ($rs->fields{'ergebnis'} != 1 && $rs->fields{'ergebnis'} != 0) {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('errors' => "Es gab ein Problem beim Speichern in die Datenbank! </br>" . trim($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => "Keine Ergebnis-Rückmeldung erhalten </br>" . trim($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
If (isset($rs->fields{'historie'})) {
    if ($rs->fields{'historie'} < 0) {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('errors' => "Es gab ein Problem beim Schreiben der Historie!</br>Vorgang wurde abgrebrochen</br>" . trim($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} If (isset($rs->fields{'historie'})) {
    if ($rs->fields{'historie'} == 0) {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('errors' => "Hic bir degisiklik yapilmadi</br>" . trim($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('errors' => "Keine Historie-Rückmeldung erhalten </br>" . trim($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

$i = 0;

while (!$rs->EOF) {

    $value{$i}{"kunden_nr"} = $rs->fields{'kunden_nr'};
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

