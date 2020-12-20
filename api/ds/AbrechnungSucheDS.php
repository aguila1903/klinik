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


    $out = array();

    $whereMenge = " Where menge > 0 ";

    if (isset($_REQUEST["verkauf_an"])) {
        $kunden_nr = $_REQUEST["verkauf_an"];
        $andVerkaufAn = " and verkauf_an = " . $kunden_nr;
        if ($kunden_nr != "null" && $kunden_nr != "") {
            if ((preg_match("/^[0-9]{1,11}?$/", trim($kunden_nr))) == 0) {

                $out['response']['status'] = -4;
                $out['response']['errors'] = array('verkauf_an' => "Bitte die Kunden-Nr prüfen");

                print json_encode($out);
                return;
            }
        } else {
            $andVerkaufAn = "";
        }
    } else {
        $andVerkaufAn = "";
    }

    if (isset($_REQUEST["prod_kz"])) {
        $prod_kz = $_REQUEST["prod_kz"];
        $andProdKz = " and prod_kz = " . $dbSyb->Quote(utf8_decode($prod_kz));
        if ($prod_kz != "null" && $prod_kz != "") {
            if (strlen($prod_kz) != 4) {
                $out['response']['status'] = -1;
                $out['response']['errors'] = array('prod_kz' => "Bitte einen Produkt-Kürzel mit 4 Zeichen eingeben.");

                print json_encode($out);

                return;
            }
        } else {
            $andProdKz = "";
        }
    } else {
        $andProdKz = "";
    }

    if (isset($_REQUEST["datum"])) {
        $datum = $_REQUEST["datum"];
        $andDatum = " and DATE_FORMAT(datum,GET_FORMAT(DATE,'EUR')) = " . $dbSyb->Quote($datum);
        if ($datum == "null" && $datum == "") {
            $andDatum = "";
        }
    }
 else {
    $andDatum = "";
}

if (isset($_REQUEST["beleg_nr"])) {
    $beleg_nr = $_REQUEST["beleg_nr"];
    $andBelegNr = " and beleg_nr = " . $dbSyb->Quote($beleg_nr);
    if ($beleg_nr != "null" && $beleg_nr != "") {
        if ((preg_match("/^[0-9\/]{1,45}?$/", trim($beleg_nr))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('beleg_nr' => "Bitte die Abrechnungsnr. prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $andBelegNr = "";
    }
} else {
    $andBelegNr = "";
}



if (isset($_REQUEST["freieSuche"])) {
    $freieSuche = $_REQUEST["freieSuche"];
    $likeFreieSuche = " and beleg_nr like '%" . $freieSuche . "%'";
    if ((preg_match("/^[a-zA-Z0-9.\/]{1,60}?$/", trim($freieSuche))) == 0) {

        $out['response']['data'] = array();
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('freieSuche' => "Bitte die Eingaben überprüfen!");

        print json_encode($out);
        return;
    }
} else {
    $likeFreieSuche = "";
}

$querySQL = "select
 distinct v.beleg_nr, v.verkauf_an, 
 concat(f.name,' (',k.stadtteil,')') as kunden_name, 
 DATE_FORMAT(v.datum,GET_FORMAT(DATE,'EUR')) as datum, 
 sum(v.gesamtpr_brutto+(v.gesamtpr_brutto*(v.mwst/100))) as gesamtpr_brutto,
count(v.beleg_nr) as anzPos
 from verkaeufe v, kunden k, filialen f"
        . $whereMenge
        . $andVerkaufAn
        . $andProdKz
        . $andDatum
        . $andBelegNr
        . $likeFreieSuche
        . " and v.verkauf_an = concat(k.filial_nr,plz,k.lfd_nr) and k.filial_nr = f.filial_nr and v.status = 'O' group by beleg_nr; ";

// file_put_contents('AbrechnungsSucheDS.txt', $querySQL);

$rs = $dbSyb->Execute($querySQL);

$data = array();

if (!$rs) {

    $out = array();

    $out['response']['data'] = $data;
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('freieSuche' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

$i = 0;

while (!$rs->EOF) {

    $data{$i}['beleg_nr'] = $rs->fields['beleg_nr'];
	$data{$i}['anzPos'] = $rs->fields['anzPos'];
	$data{$i}['gesamtpr_brutto'] =  number_format($rs->fields['gesamtpr_brutto'], 2, ',', '.'); 
	$data{$i}['datum'] = $rs->fields['datum'];
	$data{$i}['verkauf_an'] = $rs->fields['verkauf_an'];
	$data{$i}['kunden_name'] = utf8_encode($rs->fields['kunden_name']);

    $i++;
    $rs->MoveNext();
}

$rs->Close();

$out['response']['data'] = $data;
$out['response']['status'] = 0;
$out['response']['errors'] = array();

print json_encode($out);

} else {
    header("Location: http://$host$uri/noadmin.php");
}
?>