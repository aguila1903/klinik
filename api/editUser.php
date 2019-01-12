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
    $out{'response'}{'errors'} = array('kunden_nr' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

$dbSyb->debug = false;


if (isset($_POST["UserID"])) {
    $UserID = $_POST["UserID"];
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('UserID' => "UserID eksik!");

    print json_encode($out);
    return;
}

if (isset($_POST["benutzer"])) {
    $benutzer = $_POST["benutzer"];
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('benutzer' => "Kullanici eksik!");

    print json_encode($out);
    return;
}

if (isset($_POST["passwort"])) {
    if (trim($_POST["passwort"]) != "") {
        $_passwort = $_POST["passwort"];
//        if ((preg_match("/^[0-9a-zA-Z-+*_.]{6,12}$/", trim($_passwort))) == 0) {
//
//            $out{'response'}{'status'} = -4;
//            $out{'response'}{'errors'} = array('passwort' => "Şifre yalnızca 0-9 a-z A-Z - + * _ karakterlerinden oluşabilir</ br> ve en az 6 ve maks. 12 karakterden oluşabilir.");
//
//            print json_encode($out);
//
//            return;
//        }
    } else {

        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('passwort' => "Lütfen şifreyi giriniz!");

        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('passwort' => "Lütfen şifreyi giriniz!");

    print json_encode($out);
    return;
}
//------------------------- Passwort 2 ------------------------------------------------------------------------------------------------------------------------------------------
if (isset($_POST["passwort2"])) {
    if (trim($_POST["passwort2"]) != "") {
        $_passwort2 = $_POST["passwort2"];
        if ($_passwort2 != $_passwort) {
            $out{'response'}{'status'} = -4;
            $out{'response'}{'errors'} = array('passwort2' => "Parolalar eşleşmiyor!");

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('passwort2' => "Lütfen şifrenizi onaylayın");

        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('passwort2' => "Lütfen şifreyi giriniz!");

    print json_encode($out);
    return;
}





if (isset($_POST["orig_passwort"])) {
    if (trim($_POST["orig_passwort"]) != "") {
        $orig_passwort = $_POST["orig_passwort"];
//        if ((preg_match("/^[0-9a-zA-Z-+*_.]{6,12}$/", trim($orig_passwort))) == 0) {
//
//            $out{'response'}{'status'} = -4;
//            $out{'response'}{'errors'} = array('orig_passwort' => "Şifre yalnızca 0-9 a-z A-Z - + * _ karakterlerinden oluşabilir</ br> ve en az 6 ve maks. 12 karakterden oluşabilir.");
//
//            print json_encode($out);
//            return;
//        }
    } else {

        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('orig_passwort' => "Lütfen geçerli şifrenizi giriniz!");

        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('orig_passwort' => "Lütfen geçerli şifrenizi girinizz!");

    print json_encode($out);
    return;
}
//$origpasswort = sha1($orig_passwort);
$querySQL1 = "
       call loginProc (" . $dbSyb->quote(trim($benutzer))
        . "," . $dbSyb->Quote($orig_passwort).", " .$dbSyb->Quote(session_id()) . ")";

$rs1 = $dbSyb->Execute($querySQL1);


if (!$rs1) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('orig_passwort' => $dbSyb->ErrorMsg());

    print json_encode($out);
    return;
}

while (!$rs1->EOF) {
    $orig_passwort_ = $rs1->fields{'Ergebnis'};

    $rs1->MoveNext();
}
$rs1->Close();

if ($orig_passwort_ != 1) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('orig_passwort' => "Geçerli şifrenizi yanlış girdiniz");

    print json_encode($out);
    return;
}



/*
 * **************** Datenverbindung muss erneuert werden, da sonst Fehler auftreten ***********************
 */
$dbSyb->nConnect('localhost', user, psw, db); //=>>> Verbindungsaufbau mit der DB


if (!$dbSyb->IsConnected()) {

    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('errors' => ($dbSyb->ErrorMsg()));

    print json_encode($out);

    return;
}

//$passwort = sha1($_passwort);
$sqlQuery = "call editUser("
        . $UserID .
        ", " . $dbSyb->Quote(trim($_passwort)) .
		", " . $dbSyb->quote(trim($_SESSION['benutzer'])) . ")";


//file_put_contents("editKunden.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);

$value = array();

if (!$rs) {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('UserID' => ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}
If (isset($rs->fields{'ergebnis'})) {
    if ($rs->fields{'ergebnis'} != 1 && $rs->fields{'ergebnis'} != 0) {
        $out{'response'}{'status'} = -4;
        $out{'response'}{'errors'} = array('UserID' => "Veritabanına kaydedilirken bir sorun oluştu! </br>" . ($dbSyb->ErrorMsg()));

        print json_encode($out);
        return;
    }
} else {
    $out{'response'}{'status'} = -4;
    $out{'response'}{'errors'} = array('UserID' => "Sonuçta geri bildirim alınamadı </br>" . ($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

$i = 0;

while (!$rs->EOF) {

    $value{$i}{"UserID"} = $rs->fields{'UserID'};
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

