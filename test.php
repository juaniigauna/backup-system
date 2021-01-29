<?php
require_once 'autoload.php';
use Modules\DataBase;

$db = new DataBase('localhost', 'root', '', 'pr');
$tables = $db->getTables();
header("Content-Type: application/json");
echo json_encode($tables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);