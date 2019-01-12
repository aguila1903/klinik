<?php

session_start();
require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');

$status = ('stop');


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
    $result = ('Datenbank-Verbindung konnte nicht hergestellt werden!');
    print ($result);
    return;
}


$dbSyb->debug = false;


if (isset($_FILES['datei'])) {

    $name_array = $_FILES['datei']['name'];
    $tmp_name_array = $_FILES['datei']['tmp_name'];
    $type_array = $_FILES['datei']['type'];
    $size_array = $_FILES['datei']['size'];
    $error_array = $_FILES['datei']['error'];

//    if (preg_match("/pdf/i", $type_array) != 1) {
//        print ("Datei ist keine PDF-Datei");
//        return;
//    }

    if ($error_array > 0) {
        $fehlerText = "Belirsiz bir Hata olustu";
        if ($fehler == 1) {
            $fehlerText = "Hata 1 (" . $size_array . ")";
        }
        if ($fehler == 2) {
            $fehlerText = "Hata 2(" . $size_array . ")";
        }
        if ($fehler == 3) {
            $fehlerText = "Veri sadece kismen yüklendi";
        }
        if ($fehler == 4) {
            $fehlerText = "Hic bir Veri yüklenmedi";
        }
        print ($fehlerText);
        return;
    }
} else {
    $result = ('Veri yüklenmedi');
    print ($result);
    return;
}




if (isset($_REQUEST["lfd_nr"])) {
    $lfd_nr = $_REQUEST["lfd_nr"];
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('errors' => "lfd_nr eksik");

    print ($out);
    return;
}

if (isset($_REQUEST["name"])) {
    $name = $_REQUEST["name"];
    if ($name != "null" && $name != "") {
        if (strlen($name) > 64 || strlen($name) < 1) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('name' => "Soyisim en az 64 harf'den olusmali");

            print ($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('name' => "Soyisim eksik");

        print ($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('name' => "Soyisim eksik");

    print ($out);

    return;
}
if (isset($_REQUEST["vorname"])) {
    $vorname = $_REQUEST["vorname"];
    if ($vorname != "null" && $vorname != "") {
        if (strlen($vorname) > 64 || strlen($vorname) < 1) {
            $out{'response'}{'status'} = -1;
            $out{'response'}{'errors'} = array('vorname' => "Isim en az 64 harf'den olusmali");

            print ($out);

            return;
        }
    } else {
        $out{'response'}{'status'} = -1;
        $out{'response'}{'errors'} = array('vorname' => "Isim eksik");

        print ($out);

        return;
    }
} else {
    $out{'response'}{'status'} = -1;
    $out{'response'}{'errors'} = array('vorname' => "Isim eksik");

    print ($out);

    return;
}


// $path = getcwd() . "\attachments\\";
$path = __DIR__ . "\attachments\\";

if (is_dir( $path) != 1) {
    mkdir( $path, 0777, true);
    chmod( $path, 0777);
}

$data = array();

$fileName = "ek_".$lfd_nr."_".$name_array;


if (move_uploaded_file(($tmp_name_array),  $path . ($fileName))) {

    $querySQL = " Insert into attachments (bezeichnung, lfd_nr) values(" . $dbSyb->Quote(($fileName)) . ",".$lfd_nr.")";


    $rs = $dbSyb->Execute($querySQL);

    if (!$rs) {
        $result = ('Veriyi yüklerken Veri Tabaninda bir hata olustu ' . $name_array . '</br> SQL-Hatasi: ' . $dbSyb->ErrorMsg());
        $status = ("ok");
        print ($result);
    } else {
        $result = 'Veri yüklendi!';
        print ($result);
    }
} else {
    $result = ("Veriyi kaydırıken bir Hata olustu" . $name_array);
    print ($result);
}
?>