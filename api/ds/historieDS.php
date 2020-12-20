<?php

session_start();

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');
header("Cache-Control: no-cache, must-revalidate");
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

if (isset($_SESSION["login"]) && $_SESSION["login"] == login && $_SESSION["admin"] == admin) {

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

    if (isset($_REQUEST["tab"])) {
        $tab = $_REQUEST["tab"];
    } else {
        print ("Tab fehlt!");

        $data = array();

        return ($data);
    }

    if ($tab == "kunden") {
        if (isset($_REQUEST["lfd_nr"])) {
            $lfd_nr = $_REQUEST["lfd_nr"];
            $where = " Where a.schluessel = " . $lfd_nr . " and ";

            $sqlQuery = "SELECT a.lfn, a.schluessel, a.name, a.user, a.aenderdat, a.feld, a.a_inhalt, a.n_inhalt, b.codetext from hist_kunden a, hist_bem b " . $where
                    . " a.codetext = b.code";
        } else {

            $where = "";
            $sqlQuery = "SELECT lfn, schluessel, hist_kunden.name, user, aenderdat, feld, a_inhalt, n_inhalt, hist_bem.codetext 
                        from hist_kunden 
                        join hist_bem on hist_kunden.codetext = hist_bem.code
                        left outer join kunden on hist_kunden.schluessel = lfd_nr;";
        }
    } elseif ($tab == "produkte") {
        if (isset($_REQUEST["prod_kz"])) {
            $prod_kz = $_REQUEST["prod_kz"];
            $where = " Where a.schluessel = " . trim($dbSyb->Quote($prod_kz)) . " and ";

            $sqlQuery = "SELECT a.lfn, a.schluessel, a.name, a.user, a.aenderdat, a.feld, a.a_inhalt, a.n_inhalt, b.codetext from hist_produkte a, hist_bem b " . $where
                    . " a.codetext = b.code";
        } else {

            $where = " Where ";
            $sqlQuery = "SELECT a.lfn, a.schluessel, a.name, a.user, a.aenderdat, a.feld, a.a_inhalt, a.n_inhalt, b.codetext from hist_produkte a, hist_bem b " . $where
                    . " a.codetext = b.code";
        }
    }



    // file_put_contents("kundenHistDS.txt", $sqlQuery);

    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {


        $value{$i}['name'] = trim($rs->fields['name']);
        $value{$i}['schluessel'] = $rs->fields['schluessel'];
        $value{$i}['lfn'] = $rs->fields['lfn'];
        $value{$i}['user'] = trim($rs->fields['user']);
        $value{$i}['aenderdat'] = $rs->fields['aenderdat'];
        $value{$i}['a_inhalt'] = trim($rs->fields['a_inhalt']);
        $value{$i}['n_inhalt'] = trim($rs->fields['n_inhalt']);
        $value{$i}['feld'] = trim($rs->fields['feld']);
        $value{$i}['codetext'] = trim($rs->fields['codetext']);

//    if ($value{$i}['aktiv'] == 0) {
//        $id = 0; 
//        $value{$i}['_hilite'] = $id;
//    }

        $i++;

        // den n�chsten Datensatz lesen
        $rs->MoveNext();
    }

    $rs->Close();

    $output = json_encode($value);
    print($output);
} else {
    header("Location: http://$host$uri/noadmin.php");
}
?>