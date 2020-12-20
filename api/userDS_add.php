<?php

session_start();
$host = (htmlspecialchars($_SERVER["HTTP_HOST"]));
$uri = rtrim(dirname(htmlspecialchars($_SERVER["PHP_SELF"])), "/\\");

require_once('adodb5/adodb.inc.php');
require_once('db_psw_klinik.php');

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

function register($text, $ergebnis) {
    $data['text'] = $text;
    $data['ergebnis'] = $ergebnis;
    print(json_encode($data));
    die;
}

function errorMsg($errorMsg, $ergebnis) {
    $data['text'] = $errorMsg;
    $data['ergebnis'] = $ergebnis;
    print(json_encode($data));
    die;
}

$data = array();
$ergebnis = 0;
$status = "";



if (!$dbSyb->IsConnected()) {
    errorMsg("Anmeldung: " . $dbSyb->ErrorMsg(), 0);
}

$dbSyb->debug = false;


if (isset($_POST["benutzername"])) {
    $benutzer = ($_POST["benutzername"]);
    if (trim($benutzer) != "") {
        if ((preg_match("/^[0-9a-zA-Z-+*_.]{3,20}$/", trim($benutzer))) == 0) {
            errorMsg('Kullanıcı adı yalnızca 0-9 a-z A-Z - + * _ karakterlerinden oluşabilir</br> ve en azından 3 ve maks. 20 karakterden oluşabilir.', 4);
        }
    } else {
        errorMsg('Lütfen kullanıcı adını giriniz!', 4);
    }
} else {
    errorMsg('Lütfen kullanıcı adını giriniz!', 4);
}

if (isset($_POST["passwort"])) {
    if (trim($_POST["passwort"]) != "") {
        $_passwort = $_POST["passwort"];
        // if ((preg_match("/^[0-9a-zA-Z-+*_.]{6,128}$/", trim($_passwort))) == 0) {
            // errorMsg('Şifre yalnızca 0-9 a-z A-Z - + * _ karakterlerinden oluşabilir</ br> ve en az 6 ve maks. 12 karakterden oluşabilir.', 5);
        // }
    } else {
        errorMsg('Lütfen şifreyi giriniz!', 5);
    }
} else {

    errorMsg('Lütfen şifreyi giriniz!', 5);
}
//------------------------- Passwort 2 ------------------------------------------------------------------------------------------------------------------------------------------
if (isset($_POST["passwort2"])) {
    if (trim($_POST["passwort2"]) != "") {
        $_passwort2 = $_POST["passwort2"];
        if ($_passwort2 != $_passwort) {

            errorMsg('Parolalar eşleşmiyor!', 6);
        }
    } else {

        errorMsg('Lütfen şifrenizi onaylayın', 7);
    }
} else {
    errorMsg('Lütfen şifrenizi onaylayın', 7);
}

// $passwort = sha1($_passwort);


$querySQL = "call UserAddProc (" . $dbSyb->Quote($benutzer)
        . "," . $dbSyb->Quote($_passwort) . ")"
;
//file_put_contents("user_add.txt", $querySQL);

$rs = $dbSyb->Execute($querySQL);

$userID = 0;


if (!$rs) {
    errorMsg("Anmeldung: " . $dbSyb->ErrorMsg(), 0);
}

$i = 0;

while (!$rs->EOF) { // =>>> End OF File
    $ergebnis = $rs->fields['ergebnis'];
    $userID = $rs->fields['userID'];

    $i++;

    $rs->MoveNext();
}

$rs->Close();
if ($ergebnis == 1) {
    register('Kayıt başarılı oldu. </br> Birkaç saniye içinde oturum açma sayfasına yönlendirileceksiniz.', $ergebnis);
} elseif ($ergebnis == -1) {

    register('Bu kullanıcı adı zaten var!', $ergebnis);
} elseif ($ergebnis == -2) {

    register('Kayıt sırasında bir sistem hatası oluştu </br>' . $dbSyb->ErrorMsg(), $ergebnis);
} elseif ($ergebnis == 0) {

    egister('Kayıt başarısız oldu', $ergebnis);
}
?>