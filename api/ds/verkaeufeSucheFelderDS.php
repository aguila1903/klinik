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
      [1] 26.09.2018 Da bei den Rechnungen alle Termine angezeigt werden habe ich bei Status einen Fantasie-Wert gesetzt, damit alle angezeigt werden
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

    if (isset($_REQUEST["zahlungsziel"])) {
        $zahlungsziel_kz = $_REQUEST["zahlungsziel"];
        if ($zahlungsziel_kz == "null" && $zahlungsziel_kz == "") {
            $andZahlungsZiel = "";
        } else {
            $andZahlungsZiel = " and zahlungsziel = " . $dbSyb->Quote(trim($zahlungsziel_kz));
        }
    } else {
        $andZahlungsZiel = "";
    }


    if (isset($_REQUEST["freieSuche"])) {
        $freieSuche = $_REQUEST["freieSuche"];
        $likeFreieSuche = " and (beleg_nr like '%" . $freieSuche . "%' or vorname like '%" . $freieSuche . "%' or name like '%" . $freieSuche . "%' or kunden_nr like '%" . $freieSuche . "%' or concat(vorname,' ',name) like '%" . $freieSuche . "%' or DATE_FORMAT(geburtstag,GET_FORMAT(DATE,'EUR')) like '%" . $freieSuche . "%')";
    } else {
        $likeFreieSuche = "";
    }


    $querySQL = "SELECT distinct ";
    if ($lookFor == "prod_kz") {
        $querySQL .= " p.bezeichnung, v.prod_kz ";
    }
    if ($lookFor == "beleg_nr") {
        $querySQL .= " beleg_nr ";
    }
    if ($lookFor == "zahlungsziel") {
        $querySQL .= " zahlungsziel ";
    }
    if ($lookFor == "datum") {
        $querySQL .= " DATE_FORMAT(v.datum,GET_FORMAT(DATE,'EUR')) as datum ";
    }
    if ($lookFor == "geburtstag") {
        $querySQL .= " DATE_FORMAT(k.geburtstag,GET_FORMAT(DATE,'EUR')) as geburtstag ";
    }
    if ($lookFor == "verkauf_an") {
        $querySQL .= " concat(k.vorname,' ',k.name) as name, verkauf_an ";
    }
    if ($lookFor == "jahr") {
        $querySQL .= " year(v.datum) as jahr ";
    }
    if ($lookFor == "monat") {
        $querySQL .= " month(v.datum) as monat ";
    }
    $querySQL .= " FROM klinikdb.verkaeufe v left join produkte p on v.prod_kz = p.prod_kz 
left join kunden k on v.verkauf_an = k.lfd_nr Where status != 'P' " //[1]
//            . $andBezeichnung
            . $andProdKZ
//            . $andName
            . $andDatum
            . $andGeburtstag
            . $andVerkaufAn
            . $andBelegNr
            . $andZahlungsZiel
            . $andJahr
            . $andMonat
            . $likeFreieSuche;

    $querySQL .= ";";

    // file_put_contents("verkaeufeSucheFelderDS.txt", $querySQL);
    $rs = $dbSyb->Execute($querySQL);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {



        if (isset($rs->fields{'prod_kz'})) {
            $value{$i}{"prod_kz"} = ($rs->fields{'prod_kz'});
        }
        if (isset($rs->fields{'bezeichnung'})) {
            $value{$i}{"bezeichnung"} = ($rs->fields{'bezeichnung'});
        }
        if (isset($rs->fields{'name'})) {
            $value{$i}{"name"} = ($rs->fields{'name'});
        }
        if (isset($rs->fields{'verkauf_an'})) {
            $value{$i}{"verkauf_an"} = ($rs->fields{'verkauf_an'});
        }
        if (isset($rs->fields{'datum'})) {
            $value{$i}{"datum"} = ($rs->fields{'datum'});
        }
        if (isset($rs->fields{'beleg_nr'})) {
            $value{$i}{"beleg_nr"} = ($rs->fields{'beleg_nr'});
        }
        if (isset($rs->fields{'zahlungsziel'})) {
            $value{$i}{"zahlungsziel_kz"} = ($rs->fields{'zahlungsziel'});
            if ($rs->fields{'zahlungsziel'} == "S") {
                $value{$i}{"zahlungsziel"} = "Nakit";
            } else if ($rs->fields{'zahlungsziel'} == "Z") {
                $value{$i}{"zahlungsziel"} = "Kredi Kart";
            }
        }
        if (isset($rs->fields{'geburtstag'})) {
            $value{$i}{"geburtstag"} = ($rs->fields{'geburtstag'});
        }
        if (isset($rs->fields{'jahr'})) {
            $value{$i}{"jahr"} = ($rs->fields{'jahr'});
        }
        if (isset($rs->fields{'monat'})) {
            $value{$i}{"monat"} = ($rs->fields{'monat'});
            switch ($value{$i}{"monat"}) {
                case 1:
                    $value{$i}{"monatAusg"} = "Ocak";
                    break;
                case 2:
                    $value{$i}{"monatAusg"} = "Şubat";
                    break;
                case 3:
                    $value{$i}{"monatAusg"} = "Mart";
                    break;
                case 4:
                    $value{$i}{"monatAusg"} = "Nisan";
                    break;
                case 5:
                    $value{$i}{"monatAusg"} = "Mayıs";
                    break;
                case 6:
                    $value{$i}{"monatAusg"} = "Haziran";
                    break;
                case 7:
                    $value{$i}{"monatAusg"} = "Temmuz";
                    break;
                case 8:
                    $value{$i}{"monatAusg"} = "Ağustos";
                    break;
                case 9:
                    $value{$i}{"monatAusg"} = "Eylül";
                    break;
                case 10:
                    $value{$i}{"monatAusg"} = "Ekim";
                    break;
                case 11:
                    $value{$i}{"monatAusg"} = "Kasım";
                    break;
                case 12:
                    $value{$i}{"monatAusg"} = "Aralık";
                    break;
            }
        }

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
