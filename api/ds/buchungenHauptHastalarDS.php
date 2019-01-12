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
      [2] 27.09.2018 Skipt wird auch für die Buchungen im Terminkalender genutzt. Dort soll der gewählte Vorgang angezeigt werden, wobei erst die lfd_nr ermittelt werden muss.
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


    if (isset($_REQUEST["lfd_nr"])) {
        $lfd_nr = $_REQUEST["lfd_nr"];
        if ((preg_match("/^[-0-9]{1,20}$/", trim($lfd_nr))) == 0) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('errors' => "lfd_nr hatali!");

            print json_encode($out);
            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('errors' => "Laufende-Nr fehlt!");

        print json_encode($out);
        return;
    }
    
    if (isset($_REQUEST["beleg_nr"])) {
        $beleg_nr = $_REQUEST["beleg_nr"];
        if ((preg_match("/^[0-9]{1,20}$/", trim($beleg_nr))) == 0) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('errors' => "beleg_nr hatali!");

            print json_encode($out);
            return;
        }

        $sqlQuery = "Select distinct verkauf_an from verkaeufe Where  beleg_nr = " . $beleg_nr;

        $rs = $dbSyb->Execute($sqlQuery);
        if (!$rs) {
            print $dbSyb->ErrorMsg() . "\n";
            return;
        }
        
        $lfd_nr = $rs->fields{'verkauf_an'};
        
        $andWhere = " and beleg_nr = ".$beleg_nr;
    }else{
        $andWhere = "";
    }




    $sqlQuery = "select 
    beleg_nr,
    DATE_FORMAT(datum, GET_FORMAT(DATE, 'EUR')) as datum,
    verkauf_an,
    concat(vorname,' ',name) as name,
    concat(concat(k.vorname,' ',k.name) ,' (',v.verkauf_an,')') as name_mit_knd_nr,
    sum(v.menge * v.einzelpr_brutto) gesamtpr_brutto,
    m.mwst,
    DATE_FORMAT(v.datum, GET_FORMAT(DATE, 'EUR'))  as zahlfrist,    
    DATE_FORMAT(k.geburtstag, GET_FORMAT(DATE, 'EUR'))  as geburtstag,
    beleg_pfad
from
    verkaeufe v,
    kunden k,
    mwst_tab m,
    produkte p
where
    v.verkauf_an = k.lfd_nr and v.prod_kz = p.prod_kz and m.lfd_nr = p.mwst and status != 'P' and  v.verkauf_an = " . $lfd_nr .$andWhere. //[1][2]
            "  group by beleg_nr order by datum desc;";

// file_put_contents("buchungenHaupt.txt",$sqlQuery );

    $rs = $dbSyb->Execute($sqlQuery);


    if (!$rs) {
        print $dbSyb->ErrorMsg() . "\n";
        return;
    }
    $i = 0;

    $value = array();

    while (!$rs->EOF) {

        $mwst1 = 100 - $rs->fields{'mwst'};
        $mwst2 = ($mwst1 * $rs->fields{'gesamtpr_brutto'}) / 100;
        $mwst3 = $rs->fields{'gesamtpr_brutto'} - $mwst2;
        $value{$i}{"mwst_gesamtpr"} = number_format($mwst3, 4, '.', '');
        $value{$i}{"gesamtpr_brutto"} = number_format($rs->fields{'gesamtpr_brutto'}, 2, '.', '');
        $value{$i}{"name"} = ($rs->fields{'name'});
        $value{$i}{"name_mit_knd_nr"} = ($rs->fields{'name_mit_knd_nr'});
        $value{$i}{"verkauf_an"} = $rs->fields{'verkauf_an'};
        $value{$i}{"beleg_pfad"} = ($rs->fields{'beleg_pfad'});
        $value{$i}{"beleg_nr"} = $rs->fields{'beleg_nr'};
        $value{$i}{"datum"} = $rs->fields{'datum'};
        $value{$i}{"zahlfrist"} = $rs->fields{'zahlfrist'};
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
