<?php

session_start();

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');
header("Cache-Control: no-cache, must-revalidate");
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");



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


$sqlQuery = "Select "
        . " starttime as start "
        . " ,endtime as end "
        . " ,description "
        . " ,subject as title "
        . " ,beleg_nr as id "
        . " From jqcalendar "

;


$rs = $dbSyb->Execute($sqlQuery);


if (!$rs) {
    print $dbSyb->ErrorMsg() . "\n";
    return;
}
$i = 0;

$value = array();

while (!$rs->EOF) {

//       $value{$i}{"name"} = $rs->fields{'name'};
    $value{$i}{"title"} = $rs->fields{'title'};
//       $value{$i}{"description"} = $rs->fields{'description'};
    $value{$i}{"id"} = $rs->fields{'id'};
    // $value{$i}{"startDate"} = str_replace(' ', 'T', $rs->fields{'startDate'});
    // $value{$i}{"endDate"} = str_replace(' ', 'T', $rs->fields{'endDate'});
    $value{$i}{"start"} = $rs->fields{'start'};
    $value{$i}{"end"} = $rs->fields{'end'};
    $value{$i}{"description"} = "Test";
    if (date('Y-m-d') == substr($rs->fields{'start'}, 0, 10)) {
        $value{$i}{"color"} = 'green';
        $value{$i}{"textColor"} = 'white';
    } else {
        if (str_replace("-", "", substr($rs->fields{'start'}, 0, 10)) > date('Ymd')) {
            $value{$i}{"color"} = 'orange';
            $value{$i}{"textColor"} = 'black';
        } else {
            $value{$i}{"color"} = 'grey';
            $value{$i}{"textColor"} = 'white';
        }
    }
//    file_put_contents("datum.txt", str_replace("-", "", substr($rs->fields{'start'}, 0, 10))."\n", FILE_APPEND);
//       $value{$i}{"canEdit"} = "false";
    // $value{$i}{"eventWindowStyle"} = "testStyle";

    $i++;

    // den nächsten Datensatz lesen
    $rs->MoveNext();
}

$rs->Close();

$out = array();



// zentrale Anwortfunktion f�r REST-Datenquellen
// im Kern nicht anderes als print json_encode($value)
$out{'response'}{'status'} = 0;
$out{'response'}{'errors'} = array();
$out{'response'}{'data'} = $value;

print json_encode($value);
