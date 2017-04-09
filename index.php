<html>
<head>
    <style>
        table {
            border-collapse: collapse;
        }
        table td {
            border: 1px solid #666;
            padding: 10px;
            vertical-align: top;
            word-break: break-all;
        }
        .double-table {
            margin-bottom: 40px;
        }
        .double-table td {
            width: 50%;
        }
        .triple-table {
            width: 33%;
        }
    </style>
</head>
<body>
<?php
error_reporting(-1);
ini_set('display_errors', 1);
include('vkim.class.php');

$vk = new Vkim();
$vk->setAccessToken($config['VK_ACCESS_TOKEN']);
$vk->setSecret($config['VK_SECRET']);

$vk->getDialogMessages();
//$vk->dumpDialogs();
echo $vk->PrintReport();
?>
</body>
</html>