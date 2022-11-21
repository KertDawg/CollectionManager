<?php

namespace controllers;

require __DIR__ . "/../Configuration.php";


class Icon
{
	public function GetAllIcons()
	{
		$Out = array();
		$Out["Icons"] = array();

		$bq = \utils\Database::GetDB()->prepare("
		SELECT IconID, IconCode, IconName
		FROM Icon
		ORDER BY IconName;
		");
		$bq->execute([]);

		foreach($bq as $b)
		{
			$obj = array();
			$obj["IconID"] = $b["IconID"];
			$obj["IconCode"] = $b["IconCode"];
			$obj["IconName"] = $b["IconName"];

			array_push($Out["Icons"], $obj);
		}

		\utils\API::RespondSuccess($Out);
	}
}
