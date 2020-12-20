
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


    $status = json_encode('stop');
    $bild = json_encode('');

    if (!$dbSyb->IsConnected()) {
        $result = json_encode('Datenbank-Verbindung konnte nicht hergestellt werden!');

        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

        return;
    }
    $dbSyb->debug = false;


    if (isset($_REQUEST["lfd_nr"])) {
        if ($_REQUEST["lfd_nr"] != "null" && $_REQUEST["lfd_nr"] != "") {
            $lfd_nr = $_REQUEST["lfd_nr"];
        } else {
            $result = json_encode('Die laufende Nr. fehlt!');

            echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

            return;
        }
    } else {
        $result = json_encode('Die laufende Nr. fehlt!');

        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

        return;
    }
    if (isset($_REQUEST["format"])) {
        if ($_REQUEST["format"] != "null" && $_REQUEST["format"] != "") {
            $format = $_REQUEST["format"];
        } else {
            $result = json_encode('Die Formatsangabe fehlt!');

            echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

            return;
        }
    } else {
        $result = json_encode('Die Formatsangabe fehlt!');

        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

        return;
    }
    
    if(isset($_FILES['datei'])){ 
$name_array = $_FILES['datei']['name']; 
$tmp_name_array = $_FILES['datei']['tmp_name']; 
$type_array = $_FILES['datei']['type']; 
//$size_array = $_FILES['datei']['size']; 
    $error_array = $_FILES['datei']['error']; 
    
    }else{
        $result = json_encode('Es wurde keine Datei hochladen.');

        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

        return;
    }

   for($i = 0; $i < count($tmp_name_array); $i++){
       
 // Prüfung ob es sich um eine Bild-Datei handelt  

    if ((($type_array[$i] != "image/gif" && ($type_array[$i] != "image/jpeg") && ($type_array[$i] != "image/jpg") && ($type_array[$i] != "image/pjpeg") && ($type_array[$i] != "image/x-png") && ($type_array[$i] != "image/png"))) && $format == "bild") {

        $result = json_encode('Sie haben zwar als Format Bild angegeben, haben jedoch keine Bild-Datei hochgeladen.</br></br>Bitte nur Grafiken mit dem Format jpg, jpeg, pjpeg, png oder x-png hochladen.');

        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

        return;
    } elseif (isset($_FILES['datei']) && (($type_array[$i] == "image/gif" && ($type_array[$i] == "image/jpeg") && ($type_array[$i] == "image/jpg") && ($type_array[$i] == "image/pjpeg") && ($type_array[$i] == "image/x-png") && ($type_array[$i] == "image/png"))) && $format != "bild") {

        $result = json_encode('Sie haben zwar eine Bild-Datei zum Upload ausgewählt, haben jedoch als Format etwas anderes eingestellt.');

        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

        return;
    }


// Prüfung ob es sich um eine PDF-Datei handelt  
    elseif (($type_array[$i] != 'application/pdf') && $format == "pdf") {

        $result = json_encode('Sie haben zwar als Format PDF angegeben, haben jedoch keine PDF-Datei hochgeladen');


        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

        return;
    } elseif (($type_array[$i] == 'application/pdf') && $format != "pdf") {

        $result = json_encode('Sie haben zwar eine PDF-Datei zum Upload ausgewählt, haben jedoch als Format etwas anderes eingestellt.');


        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

        return;
    } elseif (($type_array[$i] == 'application/pdf') && $format == "pdf") {

        $bild = json_encode('no_pic');
    }



    $path1 = getcwd() . "\Ausgaben\\";

    if (is_dir($path1) != 1) {
        mkdir($path1, 0777, true);
        chmod($path1, 0777);
    }


    if (!$error_array[$i]) {

        $beleg_name = $lfd_nr . '_' . basename(utf8_decode($name_array[$i]));

        if (@move_uploaded_file($tmp_name_array[$i], $path1 . $beleg_name)) {

            $querySQL = " Call prodBelegUpload (" . $dbSyb->Quote($beleg_name)
                    . ", " . $dbSyb->Quote($lfd_nr) . ")";

//            file_put_contents("bild_upload.txt", $querySQL);

            $rs = $dbSyb->Execute($querySQL);


            if (!$rs) {

                $result = json_encode('Datenbank-Fehler aufgetregen</br>' . $dbSyb->ErrorMsg());
                echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

                return;
            }

            $i = 0;
            $ergebnis = "";

            while (!$rs->EOF) {
                $ergebnis = $rs->fields['ergebnis'];

                $i++;

                $rs->MoveNext();
            }

            $rs->Close();

//            if(isset($rs->fields['ergebnis']) && isset($rs->fields['historie'])){
            if ($ergebnis == 1) {

                $result = json_encode(utf8_encode($beleg_name[$i]) . ' erfolgreich hochgeladen!');
                $status = json_encode("ok");
                $bild = json_encode(utf8_encode($beleg_name[$i]));
                echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";
            } else {

                $result = json_encode("Datei Upload hat versagt. (" . $ergebnis . ")");
                echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";
            }

//                }else {
//
//                $result = json_encode("Keine Rückmeldung erhalten.");
//                echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";
//            } 
        } else { //echo 'Datei Upload hat versagt.'; 
            $result = json_encode("Fehler beim verschieben der Datei!");
            echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";
        }
    } else {

        $fehlerText = "?";

        $fehler = $error_array[$i];

        if ($fehler == 0) {
            $fehlerText = "kein Datei-Fehler!";
        } else
        if ($fehler == 1) {
            $fehlerText = "Fehler 1 (" . $_FILES['datei']['size'] . ")";
        } else
        if ($fehler == 2) {
            $fehlerText = "Fehler 2(" . $_FILES['datei']['size'] . ")";
        } else
        if ($fehler == 3) {
            $fehlerText = "die Datei wurde nur teilweise übertragen";
        } else
        if ($fehler == 4) {
            $fehlerText = "es wurde keine Datei übertragen";
        }


        $result = json_encode($fehlerText);
        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";
    }

    
        }// ende for schleife
} else {
    header("Location: http://$host/klinik/noadmin.php");
}
?>
