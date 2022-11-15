<?php

namespace controllers;

require __DIR__ . "/../Configuration.php";


class Maintenance
{
	public function FirstTimeSetup()
	{
		$Password = "abc123";

		//  Add an admin user.
		$bq = \utils\Database::GetDB()->prepare("
		INSERT INTO User 
		(UserID, UserName, Admin, Active, PasswordHash) 
		VALUES 
		(?, 'admin', 1, 1, ?);
		");
		$bq->execute([\utils\Database::GUID(), password_hash($Password, PASSWORD_DEFAULT)]);
	}
}
