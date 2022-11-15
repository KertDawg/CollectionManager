<?php

require_once __DIR__ . "/vendor/autoload.php";


date_default_timezone_set("America/New_York");

$Maintenance = new controllers\Maintenance();
$Maintenance->FirstTimeSetup();
