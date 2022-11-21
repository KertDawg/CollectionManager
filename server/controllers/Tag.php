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
		SELECT t.TagID, t.TagName, t.IconID, t.ColorID, c.ColorCode, c.ColorName, c.TextCode,
		i.IconCode, i.IconName
		FROM Tag t
		LEFT JOIN Icon i
		ON (t.IconID = i.IconID)
		LEFT JOIN Color c
		ON (t.ColorID = c.ColorID)
		WHERE (t.UserID = ?)
		ORDER BY t.TagName;
		");
		$bq->execute([$User["UserID"]]);

		foreach($bq as $b)
		{
			$obj = array();
			$obj["TagID"] = $b["TagID"];
			$obj["TagName"] = $b["TagName"];
			$obj["IconID"] = $b["IconID"];
			$obj["ColorID"] = $b["ColorID"];
			$obj["ColorCode"] = $b["ColorCode"];
			$obj["ColorName"] = $b["ColorName"];
			$obj["TextCode"] = $b["TextCode"];
			$obj["IconCode"] = $b["IconCode"];
			$obj["IconName"] = $b["IconName"];

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
		(TagID, TagName, IconID, ColorID, UserID)
		VALUES
		(?, ?, ?, ?, ?);
		");
		$bq->execute([\utils\Database::GUID(), $Tag->TagName, $Tag->IconID, $Tag->ColorID, $User["UserID"]]);


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
		SET TagName = ?, IconID = ?, ColorID = ?
		WHERE (TagID = ?)
		AND (UserID = ?)
		");
		$bq->execute([$Tag->TagName, $Tag->IconID, $Tag->ColorID, $Tag->TagID, $User["UserID"]]);


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
		$UserOwns = 0;

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
				$UserOwns = 1;
			}
		}

		if ($UserOwns)
		{
			$bq = \utils\Database::GetDB()->prepare("
			DELETE FROM ItemTag
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

	public function SyncItemTags($Item)
	{
		$bq = \utils\Database::GetDB()->prepare("
		DELETE FROM ItemTag
		WHERE (ItemID = ?);
		");
		$bq->execute([$Item->ItemID]);

		foreach($Item->Tags as $b)
		{
			$bq = \utils\Database::GetDB()->prepare("
			INSERT INTO ItemTag
			(ItemTagID, ItemID, TagID)
			VALUES
			(?, ?, ?);
			");
			$bq->execute([\utils\Database::GUID(), $Item->ItemID, $b->TagID]);
		}
	}

	public function GetTagsForItem($ItemID)
	{
		$out = array();

		$iq = \utils\Database::GetDB()->prepare("
		SELECT it.ItemTagID, t.TagID, t.TagName, t.IconID, t.ColorID, c.ColorCode, c.ColorName, c.TextCode,
		i.IconCode, i.IconName
		FROM ItemTag it
		INNER JOIN Tag t
		ON (it.TagID = t.TagID)
		LEFT JOIN Icon i
		ON (t.IconID = i.IconID)
		LEFT JOIN Color c
		ON (t.ColorID = c.ColorID)
		WHERE (it.ItemID = ?)
		ORDER BY t.TagName;
		");
		$iq->execute([$ItemID]);

		foreach($iq as $i)
		{
			$t = array();
			$t["ItemTagID"] = $i["ItemTagID"];
			$t["TagID"] = $i["TagID"];
			$t["TagName"] = $i["TagName"];
			$t["IconID"] = $i["IconID"];
			$t["ColorID"] = $i["ColorID"];
			$t["ColorCode"] = $i["ColorCode"];
			$t["ColorName"] = $i["ColorName"];
			$t["TextCode"] = $i["TextCode"];
			$t["IconCode"] = $i["IconCode"];
			$t["IconName"] = $i["IconName"];
			array_push($out, $t);
		}

		return $out;
	}
}
