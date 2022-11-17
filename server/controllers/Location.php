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
		SELECT LocationID, LocationName
		FROM Location
		WHERE (UserID = ?)
		ORDER BY LocationName;
		");
		$bq->execute([$User["UserID"]]);

		foreach($bq as $b)
		{
			$obj = array();
			$obj["LocationID"] = $b["LocationID"];
			$obj["LocationName"] = $b["LocationName"];

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
		(LocationID, LocationName, UserID)
		VALUES
		(?, ?, ?);
		");
		$bq->execute([\utils\Database::GUID(), $Location->LocationName, $User["UserID"]]);


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
		SET LocationName = ?
		WHERE (LocationID = ?)
		AND (UserID = ?)
		");
		$bq->execute([$Location->LocationName, $Location->LocationID, $User["UserID"]]);


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
		FROM Tag
		WHERE (UserID = ?)
		AND (TagID = ?);
		");
		$bq->execute([$User["UserID"], $TagID]);

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
			DELETE FROM Detail
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
