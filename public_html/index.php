<?php

header('Content-Type: application/json');

$ENABLE_ERRORS = TRUE;

if ($ENABLE_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function __autoload($classname) {
    $filename = $classname .".php";
    if (file_exists("./Controllers/".$filename)) {
        include_once("./Controllers/".$filename);
    } else if (file_exists("./Models/".$filename)) {
        include_once("./Models/".$filename);
    } else {
        include_once("./".$filename);
    }
}

$rootController = new RootController();
$rootController->delegateRequest();

?>