<?php
error_reporting(-1);
ini_set('display_errors', 1);
include 'Vkim.class.php';

try {
    $vkim = new Vkim();
    $vkim->setAccessToken($config['VK_ACCESS_TOKEN']);
    $vkim->setSecret($config['VK_SECRET']);

    if (isset($_POST['run'])) {
        if (isset($_POST['interlocutor']) && !empty($_POST['interlocutor'])) {
            $vkim->setInterlocutor($_POST['interlocutor']);
        }
        $vkim->messagesLimit = (isset($_POST['limit']) && !empty($_POST['limit'])) ? (int)$_POST['limit'] : 256;
        $vkim->getUsersInfo();
        $vkim->getDialogMessages();
        $output = $vkim->PrintReport();
    } else {
        $output = file_get_contents('assets/templates/run.tpl');
    }
    $layout = file_get_contents('assets/templates/layout.tpl');
    $layout = str_replace('{$output}', $output, $layout);
    echo $layout;
} catch (Exception $e) {
    echo '<h1>Exception!</h1>';
    echo $e->getMessage();
}
