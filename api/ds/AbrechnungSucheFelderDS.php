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


if (isset($_REQUEST["lookFor"])) {
    $lookFor = $_REQUEST["lookFor"];
} else {
    $lookFor = "";
}


if (isset($_REQUEST["verkauf_an"])) {
        $kunden_nr = $_REQUEST["verkauf_an"];
        $andVerkaufAn = " and v.verkauf_an = " . $kunden_nr;
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
        $andProdKz = " and v.prod_kz = " . $dbSyb->Quote(utf8_decode($prod_kz));
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
        $andDatum = " and DATE_FORMAT(v.datum,GET_FORMAT(DATE,'EUR')) = " . $dbSyb->Quote($datum);
        if ($datum == "null" && $datum == "") {
            $andDatum = "";
        }
    }
 else {
    $andDatum = "";
}

if (isset($_REQUEST["beleg_nr"])) {
    $beleg_nr = $_REQUEST["beleg_nr"];
    $andBelegNr = " and v.beleg_nr = " . $dbSyb->Quote($beleg_nr);
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




$querySQL = "select distinct ";

if ($lookFor == "v.verkauf_an") {
    $querySQL .= " verkauf_an, f.name as kunden_name ";
}
if ($lookFor == "v.prod_kz") {
    $querySQL .= "  v.prod_kz,  p.bezeichnung as produkt_bez ";
}
if ($lookFor == "v.datum") {
    $querySQL .= "  DATE_FORMAT(v.datum,GET_FORMAT(DATE,'EUR')) as datum";
}
if ($lookFor == "v.beleg_nr") {
    $querySQL .=  " beleg_nr ";
}

$querySQL .= " from verkaeufe v, kunden k, produkte p, filialen f where v.verkauf_an = concat(k.filial_nr,plz,k.lfd_nr) and v.prod_kz = p.prod_kz and k.filial_nr = f.filial_nr and v.status = 'O'  "
        . $andVerkaufAn
        . $andProdKz
        . $andDatum
        . $andBelegNr
        ." Order by $lookFor ";

// file_put_contents('AbrechnungsSucheFelderDS.txt', $querySQL);

$rs = $dbSyb->Execute($querySQL); //=>>> Abfrage wird an den Server übermittelt / ausgeführt?

$data = array();

if (!$rs) {
    // keine Query hat nicht funtioniert
    //print("Query 1: " . $dbSyb->ErrorMsg());
    //
    //>> [1] Fehlermeldung: INSERT-Fehler
    $out = array();

    $out['response']['data'] = $data;
    $out['response']['status'] = -4;
    $out['response']['errors'] = array('prod_kz' => utf8_encode($dbSyb->ErrorMsg()));

    print json_encode($out);
    return;
}

$i = 0;

while (!$rs->EOF) {
    if(isset($rs->fields['produkt_bez'])){
    $data{$i}['produkt_bez'] = utf8_encode($rs->fields['produkt_bez']);}
    
    if(isset($rs->fields['kunden_name'])){        
    $data{$i}['kunden_name'] = utf8_encode($rs->fields['kunden_name']);}
    
    if(isset($rs->fields['prod_kz'])){        
    $data{$i}['prod_kz'] = utf8_encode($rs->fields['prod_kz']);}
    
      
    if(isset($rs->fields['verkauf_an'])){
    $data{$i}['verkauf_an'] = $rs->fields['verkauf_an'];}
    
    if(isset($rs->fields['datum'])){
    $data{$i}['datum'] = $rs->fields['datum'];}
    
    if(isset($rs->fields['beleg_nr'])){
    $data{$i}['beleg_nr'] = $rs->fields['beleg_nr'];}
     
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