<?php
session_start();

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');
header("Cache-Control: no-cache, must-revalidate");
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

if (isset($_SESSION["login"]) && $_SESSION["login"] == login) {

/* * *****************************************************************************
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


$out = array();

if (isset($_REQUEST["beleg_nr"])) {
    $beleg_nr =  $_REQUEST["beleg_nr"];
    if ($beleg_nr != "null" && $beleg_nr != "") {
        if ((preg_match("/^[0-9\/]{1,45}?$/", trim($beleg_nr))) == 0) {

            $out['response']['status'] = -4;
            $out['response']['errors'] = array('beleg_nr' => "Bitte die Abrechnungsnr. prüfen.");

            print json_encode($out);
            return;
        }
    } else {
        $beleg_nr = "";
    }
} else {
    $beleg_nr = "";
}

if(isset($_REQUEST["aktiv"])){
    $whereAktiv = " and aktiv = 1 and prod_kz not in (select prod_kz from verkaeufe where beleg_nr = ".$dbSyb->Quote($beleg_nr) .") order by bezeichnung; ";
}else{
    $whereAktiv = " order by bezeichnung;";
}

$sqlQuery = 
// " Select '0000' as prod_kz, 'Sonra secilecek' as bezeichnung, 0.00 as brutto_preis1, 0.00 as brutto_preis2, 0.00 as mwst, 0 as lfd_nr, 'J' as aktiv "
//. " Union "
 " SELECT "
."  prod_kz " 
.", bezeichnung " 
.", brutto_preis1 "
.", brutto_preis2 " 
//.", round(brutto_preis1+(brutto_preis1*(m.mwst/100)),4) as brutto_preis1 "
//.", round(brutto_preis2+(brutto_preis2*(m.mwst/100)),4) as brutto_preis2 " 
.", m.mwst "
.", lfd_nr "
.", prod_bild "
.", aktiv "
."  from "
."  produkte p, mwst_tab m where p.mwst = m.lfd_nr"
.$whereAktiv;

// file_put_contents("produkteDS.txt", $sqlQuery);

$rs = $dbSyb->Execute($sqlQuery);


if (!$rs) {
    print $dbSyb->ErrorMsg() . "\n";
    return;
}
$i = 0;

$value = array();

while (!$rs->EOF) {

    $value{$i}['prod_kz'] = ($rs->fields['prod_kz']);
    $value{$i}['aktiv'] = $rs->fields['aktiv'];
    $value{$i}['bezeichnung'] = ($rs->fields['bezeichnung']);
    $value{$i}['brutto_preis1'] = number_format($rs->fields['brutto_preis1'],2, ',', '.');
    $value{$i}['brutto_preis2'] = number_format($rs->fields['brutto_preis2'],2, ',', '.');
//    $value{$i}['brutto_preis1'] = number_format($rs->fields['brutto_preis1'],4, ',', '.');
//    $value{$i}['brutto_preis2'] = number_format($rs->fields['brutto_preis2'],4, ',', '.');
    $value{$i}['mwst2'] = number_format($rs->fields['mwst'],2, ',', '.');
    $value{$i}['mwst'] = ($rs->fields['lfd_nr']);
    $value{$i}['prod_bild'] = ($rs->fields['prod_bild']);
    
    if ($value{$i}['aktiv'] == 0) {
        $id = 0; 
        $value{$i}['_hilite'] = $id;
    }
   
    $i++;

    // den n�chsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();

$output = json_encode($value);

print($output);
} else {
    header("Location: http://$host/klinik/noadmin.php");
}
