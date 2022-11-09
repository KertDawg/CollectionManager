<?php

require_once __DIR__ . "/vendor/autoload.php";


date_default_timezone_set("America/New_York");
$router = new \Bramus\Router\Router();

$APIBase = "/api";
$LocalHostList = array(
    '127.0.0.1',
    '::1'
);

if(!in_array($_SERVER['REMOTE_ADDR'], $LocalHostList)) {
    $APIBase = "";
}

$router->mount($APIBase, function() use ($router) {
	$router->options(".*", function () {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization, content-type, Token");
		header("Access-Control-Allow-Methods: GET,HEAD,PUT,PATCH,POST,DELETE");
	});

	$router->get("/", function() {
		echo "The API lives here.";
	});

	$router->mount("/item", function () use ($router) {
		$router->get("/", function () {
			$Item = new controllers\Item();
			$Item->GetAllItems();
		});

		$router->post("/add", function () {
			$Item = new controllers\Item();
			$Item->AddItem();
		});
	});
});

$router->run();
