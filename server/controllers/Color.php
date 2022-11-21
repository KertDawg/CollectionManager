<?php

namespace controllers;

require __DIR__ . "/../Configuration.php";


class Color
{
	public function GetAllColors()
	{
		$Out = array();
		$Out["Colors"] = array();

		$bq = \utils\Database::GetDB()->prepare("
		SELECT c.ColorID, c.ColorCode, c.ColorName, c.TextCode
		FROM Color c
		ORDER BY c.ColorName;
		");
		$bq->execute([]);

		foreach($bq as $b) {
			$obj = array();
			$obj["ColorID"] = $b["ColorID"];
			$obj["ColorCode"] = $b["ColorCode"];
			$obj["ColorName"] = $b["ColorName"];
			$obj["TextCode"] = $b["TextCode"];

			array_push($Out["Colors"], $obj);
		}

		\utils\API::RespondSuccess($Out);
	}
}
