<?php

namespace utils;

require __DIR__ . "/../Configuration.php";


class Log
{
    public const SEVERITY_ERROR = 0;
    public const SEVERITY_WARNING = 1;
    public const SEVERITY_INFORMATION = 2;

    public const TYPE_LOGINSUCCESS = 0;
    public const TYPE_LOGINFAILURE = 1;
    public const TYPE_GOODTOKEN = 2;
    public const TYPE_TOKENEXPIRED = 3;
    public const TYPE_BADTOKEN = 4;

    public static function LogEvent($Severity, $Type, $UserID, $Message) 
    {
		$bq = \utils\Database::GetDB()->prepare("
        INSERT INTO Log
        (LogID, LogSeverity, LogType, LogUserID, LogDateTime, LogMessage)
        VALUES
        (?, ?, ?, ?, NOW(), ?);
		");
		$bq->execute([\utils\Database::GUID(), $Severity, $Type, $UserID, $Message]);
	}
}
