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

      Änderungen:
[1] 26.09.2018 Da bei den Rechnungen alle Termine angezeigt werden habe ich den Status auskommentiert
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

    if (isset($_REQUEST["lookFor"])) {
        $lookFor = $_REQUEST["lookFor"];
    } else {
        $lookFor = "";
    }


    if (isset($_REQUEST["prod_kz"])) {
        $prod_kz = $_REQUEST["prod_kz"];
        if ($prod_kz == "null" && $prod_kz == "") {
            $andProdKZ = "";
        } else {
            $andProdKZ = " and v.prod_kz = " . $dbSyb->Quote(trim($prod_kz));
        }
    } else {
        $andProdKZ = "";
    }


    if (isset($_REQUEST["datum"])) {
        $datum = $_REQUEST["datum"];
        if ($datum == "null" && $datum == "") {
            $andDatum = "";
        } else {
            $andDatum = " and DATE_FORMAT(v.datum,GET_FORMAT(DATE,'EUR')) = " . $dbSyb->Quote(trim($datum));
        }
    } else {
        $andDatum = "";
    }

    if (isset($_REQUEST["geburtstag"])) {
        $geburtstag = $_REQUEST["geburtstag"];
        if ($geburtstag == "null" && $geburtstag == "") {
            $andGeburtstag = "";
        } else {
            $andGeburtstag = " and DATE_FORMAT(k.geburtstag,GET_FORMAT(DATE,'EUR')) = " . $dbSyb->Quote(trim($geburtstag));
        }
    } else {
        $andGeburtstag = "";
    }

    if (isset($_REQUEST["verkauf_an"])) {
        $verkauf_an = $_REQUEST["verkauf_an"];
        if ($verkauf_an == "null" && $verkauf_an == "") {
            $andVerkaufAn = "";
        } else {
            $andVerkaufAn = " and verkauf_an = " . $dbSyb->Quote(trim($verkauf_an));
        }
    } else {
        $andVerkaufAn = "";
    }


    if (isset($_REQUEST["beleg_nr"])) {
        $beleg_nr = $_REQUEST["beleg_nr"];
        if ($beleg_nr == "null" && $beleg_nr == "") {
            $andBelegNr = "";
        } else {
            $andBelegNr = " and beleg_nr = " . $dbSyb->Quote(trim($beleg_nr));
        }
    } else {
        $andBelegNr = "";
    }

    if (isset($_REQUEST["zahlungsziel_kz"])) {
        $zahlungsziel_kz = $_REQUEST["zahlungsziel_kz"];
        if ($zahlungsziel_kz == "null" && $zahlungsziel_kz == "") {
            $andZahlungsZiel = "";
        } else {
            $andZahlungsZiel = " and zahlungsziel = " . $dbSyb->Quote(trim($zahlungsziel_kz));
        }
    } else {
        $andZahlungsZiel = "";
    }
    if (isset($_REQUEST["monat"])) {
        $monat = $_REQUEST["monat"];
        if ($monat == "null" && $monat == "") {
            $andMonat = "";
        } else {
            $andMonat = " and month(v.datum) = " . $dbSyb->Quote(trim($monat));
        }
    } else {
        $andMonat = "";
    }
    if (isset($_REQUEST["jahr"])) {
        $jahr = $_REQUEST["jahr"];
        if ($jahr == "null" && $jahr == "") {
            $andJahr = "";
        } else {
            $andJahr = " and year(v.datum) = " . $dbSyb->Quote(trim($jahr));
        }
    } else {
        $andJahr = "";
    }


    if (isset($_REQUEST["freieSuche"])) {
        $freieSuche = $_REQUEST["freieSuche"];
        $likeFreieSuche = " and (beleg_nr like '%" . $freieSuche . "%' or vorname like '%" . $freieSuche . "%' or name like '%" . $freieSuche . "%' or kunden_nr like '%" . $freieSuche . "%' or concat(vorname,' ',name) like '%" . $freieSuche . "%' or DATE_FORMAT(geburtstag,GET_FORMAT(DATE,'EUR')) like '%" . $freieSuche . "%')";
    } else {
        $likeFreieSuche = "";
    }


    $sqlQuery = "select beleg_nr "
            . ", DATE_FORMAT(datum, GET_FORMAT(DATE, 'EUR')) as datum "
            . ", verkauf_an "
            . ", concat(vorname,' ',name) as name"
            . ", concat(concat(k.vorname,' ',k.name) ,' (',v.verkauf_an,')') as name_mit_knd_nr"
            . ", sum(v.menge * v.einzelpr_brutto) gesamtpr_brutto "
            . ", v.mwst "
            . ", DATE_FORMAT(v.datum, GET_FORMAT(DATE, 'EUR')) as zahlfrist"
            . ", DATE_FORMAT(k.geburtstag, GET_FORMAT(DATE, 'EUR'))  as geburtstag"
            . ", beleg_pfad "
            . " from verkaeufe v, kunden k, produkte p, mwst_tab m where v.verkauf_an = k.lfd_nr and v.prod_kz = p.prod_kz and m.lfd_nr = p.mwst  /*and status = 'B'*/ " //[1]
            . $andProdKZ
            . $andDatum
            . $andGeburtstag
            . $andVerkaufAn
            . $andBelegNr
            . $andZahlungsZiel
            . $andJahr
            . $andMonat
            . $likeFreieSuche .
            " group by beleg_nr order by datum desc;";

//    file_put_contents("buchungenHaupt.txt", $sqlQuery);

    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $mwst1 = 100 - $rs->fields['mwst'];
        $mwst2 = ($mwst1 * $rs->fields['gesamtpr_brutto']) / 100;
        $mwst3 = $rs->fields['gesamtpr_brutto'] - $mwst2;
        $value{$i}['mwst_gesamtpr'] = number_format($mwst3, 4, '.', '');
        $value{$i}['gesamtpr_brutto'] = number_format($rs->fields['gesamtpr_brutto'], 2, '.', '');
        $value{$i}['name'] = ($rs->fields['name']);
        $value{$i}['name_mit_knd_nr'] = ($rs->fields['name_mit_knd_nr']);
        $value{$i}['verkauf_an'] = $rs->fields['verkauf_an'];
        $value{$i}['beleg_pfad'] = ($rs->fields['beleg_pfad']);
        $value{$i}['beleg_nr'] = $rs->fields['beleg_nr'];
        $value{$i}['datum'] = $rs->fields['datum'];
        $value{$i}['zahlfrist'] = $rs->fields['zahlfrist'];
        $value{$i}['geburtstag'] = $rs->fields['geburtstag'];

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