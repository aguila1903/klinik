<?php

session_start();

require_once('db_psw_klinik.php');



$path =  __DIR__  . "\Backups\\";
$dateiname = date('Y-m-d');


if (is_dir($path) != 1) {
    mkdir($path);
}

$batch = "@echo off\n
cd c:\\xampp\\mysql\\bin\n


c:\\xampp\\mysql\\bin\\mysqldump.exe -uroot -p" . psw . " " . db . " > $path$dateiname.sql --routines\n

echo %errorlevel% ";

file_put_contents("$path$dateiname.bat", $batch);

$bathFileRun = "$path$dateiname.bat";


$output = exec("C:\\windows\\system32\\cmd.exe /c $bathFileRun");


$data = array();

if ($output == 0) {
    $data['rueckmeldung'] = utf8_decode($path) . $dateiname.".sql";
} else {
    
    unlink("$path$dateiname.sql");
    $out = array();

    $out['response']['data'] = array();
    $out['response']['status'] = -99;
    $out['response']['errors'] = "Fehler in der Matrix!";

    print json_encode($out);

    return;
}

unlink("$path$dateiname.bat");


$out = array();

$out['response']['status'] = 0;
$out['response']['errors'] = array();
$out['response']['data'] = $data;

print json_encode($out);
?>