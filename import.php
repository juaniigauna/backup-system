<?php
require_once "autoload.php";
use Modules\Backup\Import;
$json = $_GET['json'];
$import = new Import($json);
echo implode("<br><br>", $import->start());