<?php

namespace controllers;

require __DIR__ . "/../Configuration.php";


class Location
{
	public function GetAllLocations()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$Out = array();
		$Out["Locations"] = array();

		$bq = \utils\Database::GetDB()->prepare("
		SELECT l.LocationID, l.LocationName, l.ColorID, l.IconID, c.ColorCode, c.ColorName, c.TextCode,
		i.IconCode, i.IconName
		FROM Location l
		LEFT JOIN Icon i
		ON (l.IconID = i.IconID)
		LEFT JOIN Color c
		ON (l.ColorID = c.ColorID)
		WHERE (l.UserID = ?)
		ORDER BY l.LocationName;
		");
		$bq->execute([$User["UserID"]]);

		foreach($bq as $b)
		{
			$obj = array();
			$obj["LocationID"] = $b["LocationID"];
			$obj["LocationName"] = $b["LocationName"];
			$obj["IconID"] = $b["IconID"];
			$obj["ColorID"] = $b["ColorID"];
			$obj["ColorCode"] = $b["ColorCode"];
			$obj["ColorName"] = $b["ColorName"];
			$obj["TextCode"] = $b["TextCode"];
			$obj["IconCode"] = $b["IconCode"];
			$obj["IconName"] = $b["IconName"];

			array_push($Out["Locations"], $obj);
		}

		\utils\API::RespondSuccess($Out);
	}

	public function AddLocation()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$POST = \utils\API::POST();
		$Location = $POST->Location;

		$bq = \utils\Database::GetDB()->prepare("
		INSERT INTO Location
		(LocationID, LocationName, ColorID, IconID, UserID)
		VALUES
		(?, ?, ?, ?, ?);
		");
		$bq->execute([\utils\Database::GUID(), $Location->LocationName, $Location->ColorID, $Location->IconID, $User["UserID"]]);


		\utils\API::RespondSuccess("Success");
	}

	public function UpdateLocation()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$POST = \utils\API::POST();
		$Location = $POST->Location;

		$bq = \utils\Database::GetDB()->prepare("
		UPDATE Location
		SET LocationName = ?, ColorID = ?, IconID = ?
		WHERE (LocationID = ?)
		AND (UserID = ?)
		");
		$bq->execute([$Location->LocationName, $Location->ColorID, $Location->IconID, $Location->LocationID, $User["UserID"]]);


		\utils\API::RespondSuccess("Success");
	}

	public function DeleteLocation($LocationID)
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}


		//  Does the user own this location?
		$UserOwns = 0;

		$bq = \utils\Database::GetDB()->prepare("
		SELECT COUNT(*) AS LocationCount
		FROM Location
		WHERE (UserID = ?)
		AND (LocationID = ?);
		");
		$bq->execute([$User["UserID"], $LocationID]);

		foreach($bq as $b)
		{
			if ($b["LocationCount"] > 0)
			{
				$UserOwns = 1;
			}
		}

		if ($UserOwns)
		{
			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM ItemLocation
			WHERE (LocationID = ?);
			");
			$bq->execute([$LocationID]);

			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM Location
			WHERE (UserID = ?)
			AND (LocationID = ?);
			");
			$bq->execute([$User["UserID"], $LocationID]);
		}

		\utils\API::RespondSuccess("Success.");
	}
}
