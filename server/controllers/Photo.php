<?php

namespace controllers;

require __DIR__ . "/../Configuration.php";


class Photo
{
	public function GetPhotosForItem($ItemID)
	{
		$Out = array();

		$bq = \utils\Database::GetDB()->prepare("
		SELECT p.PhotoID, p.ItemID, p.PhotoData
		FROM Photo p
		WHERE (p.ItemID = ?);
		");
		$bq->execute([$ItemID]);

		foreach($bq as $b)
		{
			$obj = array();
			$obj["PhotoID"] = $b["PhotoID"];
			$obj["ItemID"] = $b["ItemID"];
			$obj["PhotoData"] = $b["PhotoData"];

			array_push($Out, $obj);
		}

		return $Out;
	}

	public function AddPhoto()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$POST = \utils\API::POST();
		$Photo = $POST->Photo;

		$bq = \utils\Database::GetDB()->prepare("
		INSERT INTO Photo
		(PhotoID, ItemID, PhotoData)
		VALUES
		(?, ?, ?);
		");
		$bq->execute([\utils\Database::GUID(), $Photo->ItemID, \utils\Image::ResizeImageForDatabase($Photo->PhotoData)]);


		\utils\API::RespondSuccess("Success");
	}

	public function DeletePhoto($PhotoID)
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		//  Does the user own this photo?
		$UserOwns = 0;

		$bq = \utils\Database::GetDB()->prepare("
		SELECT COUNT(*) AS PhotoCount
		FROM Photo p
		INNER JOIN Item i
		ON (p.ItemID = i.ItemID)
		WHERE (i.UserID = ?)
		AND (p.PhotoID = ?);
		");
		$bq->execute([$User["UserID"], $PhotoID]);

		foreach($bq as $b)
		{
			if ($b["PhotoCount"] > 0)
			{
				$UserOwns = 1;
			}
		}

		if ($UserOwns)
		{
			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM Photo
			WHERE (PhotoID = ?);
			");
			$bq->execute([$PhotoID]);
		}

		\utils\API::RespondSuccess("Success.");
	}
}
