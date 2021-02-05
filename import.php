<?php
require_once "autoload.php";
use Modules\DataBase;
use Modules\Backup\Import;
$json = $_GET['json'];
$import = new Import($json);
$elements = $import->start();
$db = new DataBase('localhost', 'root', '', 'tests');
$connection = $db->connection;
foreach ($elements['tables'] as $value) {
    $connection->query($value);
}
foreach ($elements['rows'] as $value) {
    $connection->query($value);
}