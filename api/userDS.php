<?php
session_start();

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');
date_default_timezone_set('europe/berlin');
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

function login($text, $ergebnis, $benutzer, $loginTxt, $admin, $status) {
    $data['text'] = $text;
    $data['ergebnis'] = $ergebnis;
    $data['status'] = $status;
    $_SESSION["benutzer"] = $benutzer;
    $_SESSION["login"] = $loginTxt;
    $_SESSION["admin"] = $admin;
    print(json_encode($data));
    die;
}

function createLog($meldung, $ip, $benutzer, $info, $browser, $os) {
    $log = $meldung . "  ; " . "IP: " . $ip . " ; " . date('d-m-Y H:i:s') . " ; " . "Kayitli Kullanıcı: " . $benutzer . " ; " . $info . " ; Browser: " . $browser . " ; OS: " . $os . "\n\n";
    file_put_contents(date('Ymd') . ".txt", $log, FILE_APPEND);
}

function errorMsg($errorMsg, $ergebnis) {
    $data['text'] = $errorMsg;
    $data['ergebnis'] = $ergebnis;
    $data['status'] = "";
    print(json_encode($data));
    die;
}

$data = array();
$meldung = "";
$info = "";
$ergebnis = 0;
$status = "";
$admin = "";
$ip = getenv("REMOTE_ADDR"); // get the ip number of the user  
$browser = $_SERVER['HTTP_USER_AGENT'];

$os = $_SERVER['HTTP_USER_AGENT'];

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
    errorMsg("Anmeldung: " . $dbSyb->ErrorMsg(),0);
}

$dbSyb->debug = false;


if (isset($_POST["benutzername"])) {
    if ($_POST["benutzername"] != "") {
        $benutzer = $_POST["benutzername"];
    } else {
        errorMsg("Kullanıcı gerekiyor",4);
    }
} else {
    errorMsg("Kullanıcı gerekiyor",4);
}

if (isset($_POST["passwort"])) {
    if ($_POST["passwort"] != "") {
        // $passwort = sha1($_POST["passwort"]);
        $passwort = $_POST["passwort"];
    } else {
        errorMsg("Sifre eksik",5);
    }
} else {
    errorMsg("Sifre eksik",5);
}

$session = session_id();
$querySQL = "
       call loginProc (" . $dbSyb->Quote($benutzer)        //=>>> SQL-Abfrage wird erstellt
        . "," . $dbSyb->Quote($passwort) . "," . $dbSyb->Quote($session) . ")";


$rs = $dbSyb->Execute($querySQL);


if (!$rs) {
    errorMsg("Query: " . $dbSyb->ErrorMsg(),0);
}
$ergebnis = $rs->fields['Ergebnis'];
$status = $rs->fields['status'];
$admin = $rs->fields['admin'];

if ($ergebnis == 1 && $status == 'B') { // Passwort OK und Kullanıcı ist freigeschaltet - Başarıyla giriş yaptınız
    login("Giriş başarılı", $ergebnis, $benutzer, 1, $admin, $status);
    createLog("[INFO]", $ip, $benutzer, "Giriş başarılı", $browser, $os);
} elseif ($ergebnis == 1 && $status == 'O') { // Passwort ist OK aber der Kullanıcı ist nicht freigeschaltet - Anmeldung nicht möglich
    login("Kullanıcı " . $benutzer . " daha aktive edilmedi!", $ergebnis, $benutzer, "falsch", $admin, $status);
    createLog("[ERROR]", $ip, $benutzer, "Kullanıcı '" . $benutzer . "' daha aktive edilmedi!", $browser, $os);
} elseif ($ergebnis == -99) { // Kullanıcı ist wegen 3 Login-Fehlversuchen 30 Minuten gesperrt
    login("Hesabınız " . $benutzer . " fazla yanliş giriş deneyişi nedeniyle  </br>5 dakika donduruldu.", $ergebnis, $benutzer, "falsch", $admin, $status);
    createLog("[ERROR]", $ip, $benutzer, "Kullanıcı '" . $benutzer . "' donduruldu.", $browser, $os);
} elseif ($ergebnis == -98) { // Kullanıcı hat seinen Passwort 3 mal falsch eingegeben und 5 dakika donduruldu  
    createLog("[ERROR]", $ip, $benutzer, "Kullanıcı '" . $benutzer . "' 5 dakika donduruldu.", $browser, $os);
    login("Üçten fazla hatalı giriş yaptınız. </br> Hesabiniz 5 dakika kilitlendi.", $ergebnis, $benutzer, "falsch", $admin, $status);
} else { // Anmeldung fehlgeschlagen - evtl. Passwort falsch oder Username falsch
    login("Giriş başarısız", $ergebnis, $benutzer, "falsch", $admin, $status);
    createLog("[ERROR]", $ip, $benutzer, "Giriş başarısız", $browser, $os);
}


//    header("Location: ".protokol."://$host$uri/$extra");
?>