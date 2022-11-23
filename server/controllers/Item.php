<?php

namespace controllers;

require __DIR__ . "/../Configuration.php";


class Item
{
	public function GetAllItems()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$Tag = new Tag();
		$Location = new Location();
		$Photo = new Photo();
		$Out = array();
		$Out["Items"] = array();

		$bq = \utils\Database::GetDB()->prepare("
		SELECT i.ItemID, i.ItemName, i.ItemDescription, i.ItemCost
		FROM Item i
		WHERE (i.UserID = ?)
		ORDER BY i.ItemName;
		");
		$bq->execute([$User["UserID"]]);

		foreach($bq as $b)
		{
			$obj = array();
			$obj["ItemID"] = $b["ItemID"];
			$obj["ItemName"] = $b["ItemName"];
			$obj["ItemDescription"] = $b["ItemDescription"];
			$obj["ItemCost"] = $b["ItemCost"];

			$obj["Tags"] = $Tag->GetTagsForItem($b["ItemID"]);
			$obj["Locations"] = $Location->GetLocationsForItem($b["ItemID"]);
			$obj["Photos"] = $Photo->GetPhotosForItem($b["ItemID"]);

			array_push($Out["Items"], $obj);
		}

		\utils\API::RespondSuccess($Out);
	}

	public function GetOneItem($ItemID)
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$Tag = new Tag();
		$Location = new Location();
		$Photo = new Photo();
		$Out = array();

		$bq = \utils\Database::GetDB()->prepare("
		SELECT i.ItemID, i.ItemName, i.ItemDescription, i.ItemCost
		FROM Item i
		WHERE (i.UserID = ?)
		AND (i.ItemID = ?)
		ORDER BY i.ItemName;
		");
		$bq->execute([$User["UserID"], $ItemID]);

		foreach($bq as $b)
		{
			$obj = array();
			$obj["ItemID"] = $b["ItemID"];
			$obj["ItemName"] = $b["ItemName"];
			$obj["ItemDescription"] = $b["ItemDescription"];
			$obj["ItemCost"] = $b["ItemCost"];

			$obj["Tags"] = $Tag->GetTagsForItem($b["ItemID"]);
			$obj["Locations"] = $Location->GetLocationsForItem($b["ItemID"]);
			$obj["Photos"] = $Photo->GetPhotosForItem($b["ItemID"]);

			$Out["Item"] = $obj;
		}

		\utils\API::RespondSuccess($Out);
	}

	public function AddItem()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$POST = \utils\API::POST();
		$Item = $POST->Item;
		$Item->ItemID = \utils\Database::GUID();

		$bq = \utils\Database::GetDB()->prepare("
		INSERT INTO Item
		(ItemID, ItemName, ItemDescription, ItemCost, UserID)
		VALUES
		(?, ?, ?, ?, ?);
		");
		$bq->execute([$Item->ItemID, $Item->ItemName, $Item->ItemDescription, $Item->ItemCost, $User["UserID"]]);

		$Location = new Location();
		$Location->SyncItemLocations($Item);

		$Tag = new Tag();
		$Tag->SyncItemTags($Item);

		\utils\API::RespondSuccess("Success");
	}

	public function UpdateItem()
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		$POST = \utils\API::POST();
		$Item = $POST->Item;

		$bq = \utils\Database::GetDB()->prepare("
		UPDATE Item
		SET ItemName = ?, ItemDescription = ?, ItemCost = ?
		WHERE (ItemID = ?)
		AND (UserID = ?)
		");
		$bq->execute([$Item->ItemName, $Item->ItemDescription, $Item->ItemCost, $Item->ItemID, $User["UserID"]]);

		$Location = new Location();
		$Location->SyncItemLocations($Item);

		$Tag = new Tag();
		$Tag->SyncItemTags($Item);

		\utils\API::RespondSuccess("Success");
	}

	public function DeleteItem($ItemID)
	{
		//  $User["UserID"] will be -1 if they're not logged in.
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondError("Invalid user");
			return;
		}

		//  Does the user own this item?
		$UserOwns = 0;

		$bq = \utils\Database::GetDB()->prepare("
		SELECT COUNT(*) AS ItemCount
		FROM Item
		WHERE (UserID = ?)
		AND (ItemID = ?);
		");
		$bq->execute([$User["UserID"], $ItemID]);

		foreach($bq as $b)
		{
			if ($b["ItemCount"] > 0)
			{
				$UserOwns = 1;
			}
		}

		if ($UserOwns)
		{
			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM ItemLocation
			WHERE (ItemID = ?);
			");
			$bq->execute([$ItemID]);

			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM ItemTag
			WHERE (ItemID = ?);
			");
			$bq->execute([$ItemID]);

			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM Photo
			WHERE (ItemID = ?);
			");
			$bq->execute([$ItemID]);

			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM Item
			WHERE (UserID = ?)
			AND (ItemID = ?);
			");
			$bq->execute([$User["UserID"], $ItemID]);
			\utils\API::RespondSuccess("Success.");
		}
		else
		{
			\utils\API::RespondSuccess($f);
		}

	}
}
