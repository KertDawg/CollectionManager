<?php

require_once __DIR__ . "/vendor/autoload.php";

$Maintenance = new controllers\Maintenance();
$Maintenance->GenerateNewAppVersionGUID();
