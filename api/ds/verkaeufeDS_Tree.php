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


    $sqlQuery = "SELECT distinct
    datum, zahlungsziel, verkauf_an, concat(vorname,' ',name) as name,
    DATE_FORMAT(a.datum, GET_FORMAT(DATE,'EUR')) as zahlDatum,
    DATE_FORMAT(b.geburtstag,GET_FORMAT(DATE,'EUR')) as geburtstag,
(Select distinct StartTime from jqcalendar where beleg_nr = a.beleg_nr) as startTime,
(Select distinct EndTime from jqcalendar where beleg_nr = a.beleg_nr) as endTime,
    beleg_nr, (select count(*) from verkaeufe c where a.beleg_nr = c.beleg_nr) as anzPos
from
    verkaeufe a,
    kunden b "
            . " Where a.verkauf_an = b.lfd_nr and status != 'B' "
            . ' and (Select distinct date_format(StartTime,"%Y%m%d") from jqcalendar where beleg_nr = a.beleg_nr) >= date_format(curdate(),"%Y%m%d");';
//Where a.verkauf_an = b.lfd_nr and status != 'B'

    // file_put_contents("verkaeufeDS.txt", $sqlQuery);
    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $value{$i}{"anzahl"} = $rs->fields{'anzPos'};
        $value{$i}{"name"} = ($rs->fields{'name'});
        $value{$i}{"verkauf_an"} = $rs->fields{'verkauf_an'};
        $value{$i}{"beleg_nr"} = $rs->fields{'beleg_nr'};
        $value{$i}{"datum"} = $rs->fields{'datum'};
        $value{$i}{"startTime"} = substr($rs->fields{'startTime'}, 11, 5);
        $value{$i}{"endTime"} = substr($rs->fields{'endTime'}, 11, 5);
        $value{$i}{"zahlungsziel"} = $rs->fields{'zahlungsziel'};
        $value{$i}{"geburtstag"} = $rs->fields{'geburtstag'};

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
