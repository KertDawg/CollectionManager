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

	$router->mount("/tag", function () use ($router) {
		$router->get("/", function () {
			$Tag = new controllers\Tag();
			$Tag->GetAllTags();
		});

		$router->post("/add", function () {
			$Tag = new controllers\Tag();
			$Tag->AddTag();
		});

		$router->post("/update", function () {
			$Tag = new controllers\Tag();
			$Tag->UpdateTag();
		});
	});

	$router->mount("/location", function () use ($router) {
		$router->get("/", function () {
			$Location = new controllers\Location();
			$Location->GetAllLocations();
		});

		$router->post("/add", function () {
			$Location = new controllers\Location();
			$Location->AddLocation();
		});

		$router->post("/aupdatedd", function () {
			$Location = new controllers\Location();
			$Location->UpdateLocation();
		});
	});

	$router->mount("/user", function () use ($router) {
		$router->post("/login", function () {
			$User = new controllers\User();
			$User->LogIn();
		});

		$router->post("/check", function () {
			$User = new controllers\User();
			$User->CheckTokenStatus();
		});

		$router->get("/validate/{Token}", function ($Token) {
			$User = new controllers\User();
			$User->ValidateUser($Token);
		});

		$router->get("/", function () {
			$User = new controllers\User();
			$User->GetUserNames();
		});
	});
});

$router->run();
