<?php

namespace utils;

//require __DIR__ . "/../Configuration.php";


class Database extends \SQLite3
{
	function __construct()
    {
        //global $Configuration;


        $this->open("collections.db");

        //  Is this a blank database?
        if ($this->DoesDatabaseNeedInitialization())
        {
            $this->InitializeDatabase();
        }
    }

    private function DoesDatabaseNeedInitialization()
    {
        $Result = $this->query("SELECT COUNT(*) AS TableCount FROM sqlite_master WHERE ((type='table') AND (name='Setting'));");

        while ($row = $Result->fetchArray(SQLITE3_ASSOC))
        {
           if ($row["TableCount"] > 0)
           {
            return $true;
           }
           else
           {
            return $false;
           }
        }
    }

    private function InitializeDatabase()
    {
        //  Setting
        $this->exec("CREATE TABLE Setting (Key TEXT, Value	TEXT, PRIMARY KEY(Key));");

        //  Item
        $this->exec("CREATE TABLE Item (ID TEXT, Name TEXT, PRIMARY KEY(ID));");
    }

    public static function GUID()
    {
        return sprintf("%04X%04X-%04X-%04X-%04X-%04X%04X%04X", mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}