<?php

namespace controllers;

require __DIR__ . "/../Configuration.php";


class Maintenance
{
	private $DB = "";


	public function GenerateNewAppVersionGUID()
	{
		$this->DB = \utils\Database::GetDB();
		$AppVersionGUID = \utils\Database::GUID();

		//  Check for existing app version.
		$eavq = $this->DB->query("
		SELECT Value
		FROM Setting
		WHERE (Key = 'AppVersion');
		");
		$meav = $eavq->fetchAll(\PDO::FETCH_ASSOC);
		$DoesRecordExist = false;

		foreach($meav as $v) {
			$DoesRecordExist = true;
		}

		if (!$DoesRecordExist)
		{
			//  Insert the record.
			$q2 = $this->DB->prepare("
			INSERT INTO Setting
			(Key, Value)
			VALUES 
			('AppVersion', ?);
			");
			$q2->execute([$AppVersionGUID]);
		}
		else
		{
			//  Update the existing record.
			$q2 = $this->DB->prepare("
			UPDATE Setting
			SET Value = ?
			WHERE (Key = 'AppVersion');
			");
			$q2->execute([$AppVersionGUID]);
		}
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
