<?php

namespace controllers;

require __DIR__ . "/../Configuration.php";


class Maintenance
{
	public function GenerateNewAppVersionGUID()
	{
		$AppVersionGUID = \utils\Database::GUID();

		$uq = \utils\Database::GetDB()->prepare("
		DELETE FROM Setting
		WHERE (`Key` = 'AppVersion');
		");
		$uq->execute([]);

		$uq = \utils\Database::GetDB()->prepare("
		INSERT INTO Setting
		(`Key`, `Value`)
		VALUES
		('AppVersion', ?);
		");
		$uq->execute([$AppVersionGUID]);
	}

	public function FirstTimeSetup()
	{
		$Password = "abc123";
		$DoWeNeedToSetup = 0;

		//  Do we need to do this?
		$bq = \utils\Database::GetDB()->prepare("
		SELECT COUNT(*) AS UserCount
		FROM User;
		");
		$bq->execute([]);

		foreach($bq as $b)
		{
			if ($b["ItemID"] < 1)
			{
				$DoWeNeedToSetup = 1;
			}
		}

		if ($DoWeNeedToSetup)
		{
			//  Add an admin user.
			$bq = \utils\Database::GetDB()->prepare("
			INSERT INTO User 
			(UserID, UserName, Admin, Active, PasswordHash) 
			VALUES 
			(?, 'admin', 1, 1, ?);
			");
			$bq->execute([\utils\Database::GUID(), password_hash($Password, PASSWORD_DEFAULT)]);

			//  Add an app version.
			$bq = \utils\Database::GetDB()->prepare("
			INSERT INTO Setting 
			(Key, Value) 
			VALUES 
			('AppVersion', '');
			");
			$bq->execute([]);
		}
	}
}
