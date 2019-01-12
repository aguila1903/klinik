<?php

session_start();

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');
header("Cache-Control: no-cache, must-revalidate");
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

if (isset($_SESSION["login"]) && $_SESSION["login"] == login) {

    /*     * *****************************************************************************
      System: infotool - SVK-Versaende
      Funktion: Versandfehler anzeigen
      Autor: jra
      Datum: 04.12.2012

      Zusatzhinweise:

      �nderungen:

     * ***************************************************************************** */



    $ADODB_CACHE_DIR = 'C:/php/cache';


    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC; // Liefert ein assoziatives Array, das der geholten Zeile entspricht 

    $ADODB_COUNTRECS = true;

    $dbSyb = ADONewConnection("mysqli");

// DB-Abfragen NICHT cachen
    $dbSyb->memCache = false;
    $dbSyb->memCacheHost = array('localhost'); /// $db->memCacheHost = $ip1; will work too
    $dbSyb->memCacheCompress = false; /// Use 'true' arbeitet unter Windows nicht
//$dsn = "'localhost','root',psw,'vitaldb'";
    $dbSyb->Connect('localhost', user, psw, db); //=>>> Verbindungsaufbau mit der DB


    if (!$dbSyb->IsConnected()) {


        print ("Anmeldung: " . $dbSyb->ErrorMsg());

        $data = array();

        return ($data);
    }

    $dbSyb->debug = false;
// Toplevel



    if (isset($_REQUEST["jahr"])) {
        $jahr = $_REQUEST["jahr"];
    } else {
        $jahr = date("Y");
    }

    if (isset($_REQUEST["monat"])) {
        $monat = $_REQUEST["monat"];
    } else {
        $monat = date("m");
    }
    if (isset($_REQUEST["ausg_art_kz"])) {
        $ausg_art_kz = $_REQUEST["ausg_art_kz"];
        if ($ausg_art_kz == "TÜM") {
            $Where2 = "";
        } else {
            $Where2 = " and a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz);
        }
    } else {
        $Where2 = "";
    }
    if (isset($_REQUEST["ausg_kz"])) {
        $ausg_kz = $_REQUEST["ausg_kz"];
        if ($ausg_kz == "TÜM") {
            $Where3 = "";
        } else {
            $Where3 = " and a.ausg_kz = " . $dbSyb->Quote($ausg_kz);
        }
    } else {
        $Where3 = "";
    }
	

	
	if (isset($_REQUEST["datum1"])) {
        $datum1 = $_REQUEST["datum1"];
        
    } else {
        $datum1 = "";
    }
	
	if (isset($_REQUEST["datum2"])) {
        $datum2 = $_REQUEST["datum2"];
        
    } else {
        $datum2 = "";
    }
	
		if (isset($_REQUEST["datumSuche"])) {
        $datumSuche = $_REQUEST["datumSuche"];
        if($datumSuche == "between"){
		$datum = " where a.datum between ".$dbSyb->Quote($datum1). " and ".$dbSyb->Quote($datum2);
		}else{$datum = " where a.datum = ".$dbSyb->Quote($datum1);}
    } else {
        $datumSuche = "";
    }


    if (isset($_REQUEST["auswahl"])) {
        $auswahl = $_REQUEST["auswahl"];
        if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $ausg_kz == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art Alles, Monat Alles, Ausg: Alles
            $Where1 = " where year(datum) = " . $jahr;
        } else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz == "TÜM" && $monat != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art beschränkt, Ausgabe: Alles, Monat beschränkt
            $Where1 = " where year(datum) = " . $jahr . $Where2 . " and month(datum) = " . $monat;
        } else if ($auswahl == "2" && $ausg_art_kz != "TÜM" && $ausg_kz != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausg-Art beschränkt, Ausgabe: beschränkt, Monat Alles
            $Where1 = " where year(datum) = " . $jahr . $Where2 . $Where3;
        } else if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $jahr == "TÜM" && $ausg_kz == "TÜM") { // Jahr Alles, Ausg-Art Alles,  Monat Alles
            $Where1 = "";
        } else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $monat != "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausgabe-Art beschränkt, Monat beschränkt, Ausgabe: Alles
            $Where1 = " where a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz) . " and month(datum) = " . $monat;
        } else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $monat != "TÜM" && $jahr == "TÜM") { // Jahr Alles, Ausgabe-Art beschränkt, Monat beschränkt, Ausgabe: beschränkt
            $Where1 = " where a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz) . " and month(datum) = " . $monat . $Where3;
        } else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz != "TÜM" && $monat != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art beschränkt, Monat beschränkt, Ausgabe: beschränkt
            $Where1 = " where a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz) . " and month(datum) = " . $monat . $Where3 . " and year(datum) = " . $jahr;
        } else if ($auswahl == "2" && $ausg_art_kz != "TÜM" && $ausg_kz == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art beschränkt, Monat beschränkt, Ausgabe: Alle
            $Where1 = " where a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz) . " and year(datum) = " . $jahr;
        } else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $ausg_kz == "TÜM" && $monat != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alle, Monat beschränkt, Ausgabe: Alle
            $Where1 = " where month(datum) = " . $monat . " and year(datum) = " . $jahr;
        } else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $ausg_kz == "TÜM" && $monat != "TÜM" && $jahr == "TÜM") { // Jahr Alle, Ausgabe-Art Alle, Monat beschränkt, Ausgabe: Alle
            $Where1 = " where month(datum) = " . $monat;
        } else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $ausg_kz == "TÜM" && $monat == "TÜM" && $jahr == "TÜM") { // Jahr Alle, Ausgabe-Art Alle, Monat Alle, Ausgabe: Alle
            $Where1 = "";
        } else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz == "TÜM" && $monat == "TÜM" && $jahr == "TÜM") { // Jahr Alle, Ausgabe-Art beschränkt, Monat Alle, Ausgabe: Alle
            $Where1 = " Where a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz);
        } else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $ausg_kz != "TÜM" && $monat == "TÜM" && $jahr == "TÜM") { // Jahr Alle, Ausgabe-Art Alle, Monat Alle, Ausgabe: beschränkt
            $Where1 = " Where a.ausg_kz = " . $dbSyb->Quote($ausg_kz);
        } else if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $ausg_kz != "TÜM" && $jahr == "TÜM") { // Jahr Alle, Ausgabe-Art Alle, Monat Alle, Ausgabe: beschränkt
            $Where1 = " Where a.ausg_kz = " . $dbSyb->Quote($ausg_kz);
        } else if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $ausg_kz != "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alle, Monat Alle, Ausgabe: beschränkt
            $Where1 = " Where a.ausg_kz = " . $dbSyb->Quote($ausg_kz);
        } else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $ausg_kz != "TÜM" && $monat == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alle, Monat Alle, Ausgabe: beschränkt
            $Where1 = " Where a.ausg_kz = " . $dbSyb->Quote($ausg_kz);
        } else if ($auswahl == "1" && $ausg_art_kz == "TÜM" && $ausg_kz == "TÜM" && $monat == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alle, Monat Alle, Ausgabe: Alle
            $Where1 = " Where year(datum) = " . $jahr;
        } else if ($auswahl == "2" && $ausg_art_kz == "TÜM" && $ausg_kz == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art Alle, Monat Alle, Ausgabe: Alle
            $Where1 = " Where year(datum) = " . $jahr;
        } else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz != "TÜM" && $monat == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art beschränkt, Monat Alle, Ausgabe: beschränkt
            $Where1 = " Where year(datum) = " . $jahr . $Where2 . $Where3;
        }
        else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz != "TÜM" && $monat == "TÜM" && $jahr == "TÜM") { // Jahr Alle, Ausgabe-Art beschränkt, Monat Alle, Ausgabe: beschränkt
            $Where1 = " Where a.ausg_kz = " . $dbSyb->Quote($ausg_kz). $Where2;
        }
        else if ($auswahl == "1" && $ausg_art_kz != "TÜM" && $ausg_kz == "TÜM" && $monat == "TÜM" && $jahr != "TÜM") { // Jahr beschränkt, Ausgabe-Art beschränkt, Monat Alle, Ausgabe: Alle
            $Where1 = " Where a.ausg_art_kz = " . $dbSyb->Quote($ausg_art_kz). " and year(datum) = " . $jahr;
        }
		else if ($auswahl == "2" && $ausg_art_kz != "TÜM" && $ausg_kz != "TÜM" && $monat == "TÜM" && $jahr == "TÜM") { // Jahr Alle, Ausgabe-Art beschränkt, Monat Alle, Ausgabe: beschränkt
            $Where1 = " Where a.ausg_kz = " . $dbSyb->Quote($ausg_kz).$Where2 ;
        }
    } else {
        $Where1 = " Where year(datum) = " . $jahr . " and month(datum) = " . $monat;
    }

	if ($datumSuche == "") {

    $sqlQuery = "SELECT lfd_nr,
a.ausg_art_kz,
b.bezeichnung as ausg_art_bez,
c.bezeichnung as ausg_bez,
a.ausg_kz, 
betrag_netto,
mwst_satz,
betrag_netto*(mwst_satz/100) as mwst,
(betrag_netto*(mwst_satz/100))+betrag_netto as betrag_brutto,
DATE_FORMAT(datum,GET_FORMAT(DATE,'EUR')) as datum, 
beleg
FROM klinikdb.laufende_ausgaben a left join ausgaben_arten b on a.ausg_art_kz = b.ausg_art_kz
left join ausgaben c on a.ausg_kz = c.ausg_kz" . $Where1 . " order by  a.datum, a.ausg_art_kz, a.ausg_kz;";
}
else{
    $sqlQuery = "SELECT lfd_nr,
a.ausg_art_kz,
b.bezeichnung as ausg_art_bez,
c.bezeichnung as ausg_bez,
a.ausg_kz, 
betrag_netto,
mwst_satz,
betrag_netto*(mwst_satz/100) as mwst,
(betrag_netto*(mwst_satz/100))+betrag_netto as betrag_brutto,
DATE_FORMAT(datum,GET_FORMAT(DATE,'EUR')) as datum, 
beleg
FROM klinikdb.laufende_ausgaben a left join ausgaben_arten b on a.ausg_art_kz = b.ausg_art_kz
left join ausgaben c on a.ausg_kz = c.ausg_kz" . $datum . " order by  a.datum, a.ausg_art_kz, a.ausg_kz;";
}

    // file_put_contents("ausgabenDS.txt", $sqlQuery);

    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $value{$i}{"lfd_nr"} = $rs->fields{'lfd_nr'};
        $value{$i}{"ausg_art_kz"} = utf8_encode($rs->fields{'ausg_art_kz'});
        $value{$i}{"ausg_art_bez"} = utf8_encode($rs->fields{'ausg_art_bez'});
        $value{$i}{"ausg_kz"} = utf8_encode($rs->fields{'ausg_kz'});
        $value{$i}{"ausg_bez"} = utf8_encode($rs->fields{'ausg_bez'});
        $value{$i}{"betrag_netto"} = number_format($rs->fields{'betrag_netto'}, 2, '.', '');
        $value{$i}{"betrag_brutto"} = number_format($rs->fields{'betrag_brutto'}, 2, '.', '');
        $value{$i}{"mwst"} = number_format($rs->fields{'mwst'}, 4, '.', '');
        $value{$i}{"mwst_satz"} = number_format($rs->fields{'mwst_satz'}, 2, '.', '');
        $value{$i}{"datum"} = $rs->fields{'datum'};
        $value{$i}{"beleg"} = utf8_encode($rs->fields{'beleg'});

        $i++;

        // den nächsten Datensatz lesen
        $rs->MoveNext();
    }

    $rs->Close();



    $output = json_encode($value);

    print($output);
} else {
    header("Location: http://$host/klinik/noadmin.php");
}
