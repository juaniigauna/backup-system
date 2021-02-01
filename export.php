<?php
require_once "autoload.php";
use Modules\DataBase;
use Modules\Backup\Export;

if ($_POST) {
    $connection = new DataBase($_POST['host'], $_POST['user'], $_POST['password'], $_POST['db_name']);
    $collection = $connection->getCollection();
    $backup = new Export($collection);
    $export = $backup->start();
    if ($export) {
        echo "Backup completed: <a href='$export' download>Download</a> or <a href='view.php?json=$export' target='_blank'>View</a>";
    } else {
        echo "Has been occurred an error while backup database.";
    }
}