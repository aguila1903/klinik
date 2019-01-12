<?php

session_start();

require_once('adodb5/adodb.inc.php');
require_once('PHPExcel\Classes\EasyPHPExcel.php');
require_once('PHPExcel\Classes\PHPExcel.php');


$header = array('Name', 'Sex', 'Job');

$data = array(
	array('Tobias Redmann', 'male', 'Freelance Software Developer'),
	array('Michael Schumacher', 'male', 'Formula One World Champion'),
	array('Michael Jackson', 'male', 'King Of Pop')
);

$excel = new EasyPHPExcel('Example 02');

$excel->setHeader($header)
      ->addRows($data)
      ->save('../Excel/hastalar.xlsx');

header("Location: ../Excel/hastalar.xlsx");
?>