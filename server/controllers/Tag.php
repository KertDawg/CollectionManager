<?php

namespace controllers;

require __DIR__ . "/../Configuration.php";


class Tag
{
	public function GetAllTags()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$Out = array();
		$Out["Tags"] = array();

		$bq = \utils\Database::GetDB()->prepare("
		SELECT TagID, TagName
		FROM Tag
		WHERE (UserID = ?)
		ORDER BY TagName;
		");
		$bq->execute([$User["UserID"]]);

		foreach($bq as $b)
		{
			$obj = array();
			$obj["TagID"] = $b["TagID"];
			$obj["TagName"] = $b["TagName"];

			array_push($Out["Tags"], $obj);
		}

		\utils\API::RespondSuccess($Out);
	}

	public function AddTag()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$POST = \utils\API::POST();
		$Tag = $POST->Tag;

		$bq = \utils\Database::GetDB()->prepare("
		INSERT INTO Tag
		(TagID, TagName, UserID)
		VALUES
		(?, ?, ?);
		");
		$bq->execute([\utils\Database::GUID(), $Tag->TagName, $User["UserID"]]);


		\utils\API::RespondSuccess("Success");
	}

	public function UpdateTag()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$POST = \utils\API::POST();
		$Tag = $POST->Tag;

		$bq = \utils\Database::GetDB()->prepare("
		UPDATE Tag
		SET TagName = ?
		WHERE (TagID = ?)
		AND (UserID = ?)
		");
		$bq->execute([$Tag->TagName, $Tag->TagID, $User["UserID"]]);


		\utils\API::RespondSuccess("Success");
	}

	public function DeleteTag($TagID)
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		//  Does the user own this tag?
		$UserOwns = $false;

		$bq = \utils\Database::GetDB()->prepare("
		SELECT COUNT(*) AS TagCount
		FROM Tag
		WHERE (UserID = ?)
		AND (TagID = ?);
		");
		$bq->execute([$User["UserID"], $TagID]);

		foreach($bq as $b)
		{
			if ($b["TagCount"] > 0)
			{
				$UserOwns = $true;
			}
		}

		if ($UserOwns)
		{
			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM Detail
			WHERE (TagID = ?);
			");
			$bq->execute([$TagID]);

			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM Tag
			WHERE (UserID = ?)
			AND (TagID = ?);
			");
			$bq->execute([$User["UserID"], $TagID]);
		}

		\utils\API::RespondSuccess("Success.");
	}
}
