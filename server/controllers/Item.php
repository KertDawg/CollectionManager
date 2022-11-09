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

		$Out = array();
		$Out["Items"] = array();

		$bq = \utils\Database::GetDB()->prepare("
		SELECT i.ItemID, i.ItemName
		FROM Item i
		WHERE (i.UserID = ?)
		ORDER BY i.ItemName;
		");
		$bq->execute([$User["UserID"]]);

		foreach($bq as $b) {
			$obj = array();
			$obj["ItemID"] = $b["ItemID"];
			$obj["ItemName"] = $b["ItemName"];

			array_push($Out["Items"], $obj);
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

		$bq = \utils\Database::GetDB()->prepare("
		INSERT INTO Item
		(ItemID, ItemName, UserID)
		VALUES
		(?, ?, ?);
		");
		$bq->execute([\utils\Database::GUID(), $Item->ItemName, $User["UserID"]]);


		\utils\API::RespondSuccess("Success")
	}
}
