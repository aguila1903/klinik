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


    if (isset($_REQUEST["lfd_nr"])) {
        $lfd_nr = $_REQUEST["lfd_nr"];
        if ($lfd_nr != "null" && $lfd_nr != "") {
            if ((preg_match("/^[0-9]{1,11}?$/", trim($lfd_nr))) == 0) {

                $out{'response'}{'status'} = -4;
                $out{'response'}{'errors'} = array('errors' => "Bitte die Abrechnungsnr. prüfen. " . $lfd_nr);

                print json_encode($out);
                return;
            }
        } else {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('errors' => "Die Abrechnungsnr. fehlt!");

            print json_encode($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('errors' => "Die Abrechnungsnr. fehlt!");

        print json_encode($out);

        return;
    }

    $sqlQuery = "Select bezeichnung, lfd_nr, attach_id from attachments where lfd_nr = " . $dbSyb->Quote($lfd_nr) . ";";


    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $value{$i}{"lfd_nr"} = $rs->fields{'lfd_nr'};
        $value{$i}{"attach_id"} = $rs->fields{'attach_id'};
        $value{$i}{"bezeichnung"} = ($rs->fields{'bezeichnung'});
        $value{$i}{"beleg_pfad"} = ($rs->fields{'bezeichnung'});

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
