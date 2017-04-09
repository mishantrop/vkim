<?php
error_reporting(-1);
ini_set('display_errors', 1);
include('vkim.class.php');

$vkim = new Vkim();
$vkim->setAccessToken($config['VK_ACCESS_TOKEN']);
$vkim->setSecret($config['VK_SECRET']);

$vkim->getUsersInfo();
$vkim->getDialogMessages();
//$vkim->dumpDialogs();
$output = $vkim->PrintReport();

$layout = file_get_contents('assets/templates/layout.tpl');
$layout = str_replace('{$output}', $output, $layout);

echo $layout;