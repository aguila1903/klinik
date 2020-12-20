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


    if (isset($_REQUEST["beleg_nr"])) {
        $beleg_nr = $_REQUEST["beleg_nr"];
        if ($beleg_nr != "null" && $beleg_nr != "") {
            if ((preg_match("/^[0-9\/]{1,45}?$/", trim($beleg_nr))) == 0) {

                $out['response']['status'] = -4;
                $out['response']['errors'] = array('errors' => "Bitte die Abrechnungsnr. prüfen. " . $beleg_nr);

                print json_encode($out);
                return;
            }
        } else {
            $out['response']['status'] = -1;
            $out['response']['errors'] = array('errors' => "Die Abrechnungsnr. fehlt!");

            print json_encode($out);

            return;
        }
    } else {
        $out['response']['status'] = -1;
        $out['response']['errors'] = array('errors' => "Die Abrechnungsnr. fehlt!");

        print json_encode($out);

        return;
    }

    $sqlQuery = "Select v.lfd_nr, v.prod_kz, p.bezeichnung,   
menge, 
preis_kat, 
einzelpr_brutto as brutto_preis,
v.mwst,
beleg_nr
FROM klinikdb.verkaeufe v left join produkte p on v.prod_kz = p.prod_kz 
left join kunden k on v.verkauf_an = k.lfd_nr where v.beleg_nr = " . $dbSyb->Quote($beleg_nr) . ";";


    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $value{$i}['lfd_nr'] = $rs->fields['lfd_nr'];
        $value{$i}['prod_kz'] = ($rs->fields['prod_kz']);
        $value{$i}['bezeichnung'] = ($rs->fields['bezeichnung']);
        $value{$i}['menge'] = $rs->fields['menge'];
        $value{$i}['preis_kat'] = $rs->fields['preis_kat'];
        if ($value{$i}['preis_kat'] == "4") {
            $value{$i}['brutto_preis_'] = number_format($rs->fields['brutto_preis'], 2, ',', '.');
            $value{$i}['mwst_'] = number_format($rs->fields['mwst'], 2, ',', '.');
        } else {
            $value{$i}['brutto_preis_'] = "0,00";
            $value{$i}['mwst_'] = "0,00";
        }
        $value{$i}['brutto_preis'] = number_format($rs->fields['brutto_preis'], 2, ',', '.');
        $value{$i}['mwst'] = number_format($rs->fields['mwst'], 0, ',', '.');
        $value{$i}['beleg_nr'] = $rs->fields['beleg_nr'];

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
