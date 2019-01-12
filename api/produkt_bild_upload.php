
<?php
session_start();

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');
header("Cache-Control: no-cache, must-revalidate");
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

if (isset($_SESSION["login"]) && $_SESSION["login"] == login && $_SESSION["admin"] == admin) {

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


$status = json_encode('stop');
$bild = json_encode('');

if (!$dbSyb->IsConnected()) {
    $result = json_encode('Datenbank-Verbindung konnte nicht hergestellt werden!');
   
    echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

    return;
}
$dbSyb->debug = false;


if (isset($_REQUEST["prod_kz"])) {
    if($_REQUEST["prod_kz"] != "null" && $_REQUEST["prod_kz"] != ""){
    $prod_kz = $_REQUEST["prod_kz"];    
    }else {
    $result = json_encode('Das Produkt-Kürzel fehlt!');
   
    echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

    return;
}
} else {
    $result = json_encode('Das Produkt-Kürzel fehlt!');
   
    echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

    return;
}


   
if (isset($_FILES['datei']) && (($_FILES["datei"]["type"] != "image/gif"
&& ($_FILES["datei"]["type"] != "image/jpeg")
&& ($_FILES["datei"]["type"] != "image/jpg")
&& ($_FILES["datei"]["type"] != "image/pjpeg")
&& ($_FILES["datei"]["type"] != "image/x-png")
&& ($_FILES["datei"]["type"] != "image/png")))) {

        $result = json_encode('Bei der übergebenen Datei handelt es sich nicht um eine Bild-Datei!</br></br>Bitte nur Grafiken mit dem Format jpg, jpeg, pjpeg, png oder x-png hochladen.');
      
        echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";

        return;
    }
    
    $path1 = getcwd() . "\images\\";
    $path2 = getcwd() . "\images\produkt_bilder\\";

    if (is_dir($path1) != 1) {
        mkdir($path1, 0777, true);
        chmod($path1, 0777);
    }

    if (is_dir($path2) != 1) {
        mkdir($path2, 0777, true);
        chmod($path2, 0777);
    }

    if (!$_FILES['datei']['error']) {
        $bild_name = basename(utf8_decode($_FILES['datei']['name']));

        if (@move_uploaded_file($_FILES['datei']['tmp_name'], $path2 . $bild_name)) {

            $querySQL = " Call prodBildUpload (". $dbSyb->Quote($bild_name)
                     .", ". $dbSyb->Quote($prod_kz)
                     .", ". $dbSyb->quote(utf8_decode($_SESSION['benutzer'])) .")";
            
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
                $ergebnis = $rs->fields{'ergebnis'};
                $historie = $rs->fields{'historie'};

                $i++;

                $rs->MoveNext();
            }

            $rs->Close();
            
//            if(isset($rs->fields{'ergebnis'}) && isset($rs->fields{'historie'})){
            if (($ergebnis == 1 && $historie == 1) || ($ergebnis == 0 && $historie == 1)) {

                $result = json_encode(utf8_encode($bild_name) . ' erfolgreich hochgeladen!');
                $status = json_encode("ok");
                $bild = json_encode(utf8_encode($bild_name));
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
        
            }else { //echo 'Datei Upload hat versagt.'; 
            $result = json_encode("Fehler beim verschieben der Datei!");
            echo "<script type=\"text/javascript\">if(window && window.parent && window.parent['{$_POST['uploadFormID']}'] && window.parent['{$_POST['uploadFormID']}'].submitDone) { window.parent['{$_POST['uploadFormID']}'].submitDone($result, $status, $bild); } </script>";
        }
            
            
    } else {

        $fehlerText = "?";

        $fehler = $_FILES['datei']['error'];

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
} else {
    header("Location: http://$host/klinik/noadmin.php");
}
?>
