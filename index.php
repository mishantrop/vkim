<?php
error_reporting(-1);
ini_set('display_errors', 1);
include('vkim.class.php');

$vkim = new Vkim();
$vkim->setAccessToken($config['VK_ACCESS_TOKEN']);
$vkim->setSecret($config['VK_SECRET']);

if (isset($_POST['run'])) {
	if (isset($_POST['interlocutor']) && !empty($_POST['interlocutor'])) {
		$vkim->setInterlocutor($_POST['interlocutor']);
	}
	if (isset($_POST['limit']) && !empty($_POST['limit'])) {
		$messagesLimit = (int)$_POST['limit'];
		$messagesLimit = ($messagesLimit >= 0 && $messagesLimit <= 2000) ? $messagesLimit : 256;
		$vkim->messagesLimit = $messagesLimit;
	}
	$vkim->getUsersInfo();
	$vkim->getDialogMessages();
	//$vkim->dumpDialogs();
	$output = $vkim->PrintReport();
} else {
	$output = file_get_contents('assets/templates/run.tpl');
}

$layout = file_get_contents('assets/templates/layout.tpl');
$layout = str_replace('{$output}', $output, $layout);

echo $layout;