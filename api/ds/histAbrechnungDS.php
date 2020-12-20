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
    $dbSyb->memCacheHost = array('localhost'); 
    $dbSyb->memCacheCompress = false; 
    $dbSyb->Connect('localhost', user, psw, db);


    if (!$dbSyb->IsConnected()) {


        print ("Anmeldung: " . $dbSyb->ErrorMsg());

        $data = array();

        return ($data);
    }

    $dbSyb->debug = false;

    if (isset($_REQUEST["lfd_nr"])) {
        $lfd_nr = $_REQUEST["lfd_nr"]." and ";
        $where = " Where lfd_nr = ".$lfd_nr;
    } else {
        $where = " Where ";
        
    }
   
    $sqlQuery = "SELECT "
   ." a.lfn,"
   ." a.lfd_nr, "
   ." a.kunden_nr, "    
   ." a.prod_bez, "
   ." a.prod_kz, "
   ." a.kunden_name, "
   ." a.user, "
   ." a.aenderdat, "
   ." a.feld, "
   ." a.a_inhalt, "
   ." a.n_inhalt, "
   ." b.codetext "
." from "
   ." hist_abrechnung a, "
   ." hist_bem b "
. $where .
    " a.codetext = b.code;";

//    file_put_contents("histAbrechnungDS.txt", $sqlQuery);

    $rs = $dbSyb->Execute($sqlQuery);
    
    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $data = array();

    while (!$rs->EOF) {
        $data{$i}['prod_kz'] = trim($rs->fields['prod_kz']);
        $data{$i}['kunden_name'] = trim($rs->fields['kunden_name']);
        $data{$i}['prod_bez'] = trim($rs->fields['prod_bez']);
        $data{$i}['lfn'] = $rs->fields['lfn'];
        $data{$i}['lfd_nr'] = $rs->fields['lfd_nr'];
        $data{$i}['kunden_nr'] = $rs->fields['kunden_nr'];
        $data{$i}['user'] = trim($rs->fields['user']);
        $data{$i}['aenderdat'] = $rs->fields['aenderdat'];
        $data{$i}['a_inhalt'] = trim($rs->fields['a_inhalt']);
        $data{$i}['n_inhalt'] = trim($rs->fields['n_inhalt']);
        $data{$i}['feld'] = trim($rs->fields['feld']);
        $data{$i}['codetext'] = trim($rs->fields['codetext']);

//    if ($value{$i}['aktiv'] == 0) {
//        $id = 0; 
//        $value{$i}['_hilite'] = $id;
//    }
 
        $i++;

        // den n�chsten Datensatz lesen
        $rs->MoveNext();
    }
    $rs->Close();

    $output = json_encode($data);

    print_r($output);
    
} else {
    header("Location: http://$host$uri/noadmin.php");
}

?>