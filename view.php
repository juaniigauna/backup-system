<?php
header("Content-Type: application/json");
ob_start();
include $_GET['json'];
$json = json_decode(ob_get_contents());
ob_end_clean();
echo json_encode($json, JSON_PRETTY_PRINT);