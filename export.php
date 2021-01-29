<?php
include "autoload.php";
use Modules\DataBase;
use Modules\Backup;

if ($_POST) {
    $connection = new DataBase($_POST['host'], $_POST['user'], $_POST['password'], $_POST['db_name']);
    $collection = $connection->getCollection();
    $backup = new Backup($collection);
    $export = $backup->export();
    if ($export) {
        $url = $export['path'];
        echo "Backup completed: <a href='$url' download>Download</a> or <a href='$url' target='_blank'>View</a>";
    } else {
        echo "Has been occurred an error while backup database.";
    }
}