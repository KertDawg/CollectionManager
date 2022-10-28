<?php

namespace utils;

require __DIR__ . "/../Configuration.php";


class Database extends
{
    private static $DB = "";


	public static function GetDB()
    {
        global $Configuration;


        if (self::$DB == "")
        {
            $host = $Configuration["DBHost"];
            $db   = $Configuration["DBSchema"];
            $user = $Configuration["DBUser"];
            $pass = $Configuration["DBPassword"];
            $now = new \DateTime();
            $mins = $now->getOffset() / 60;
            $sgn = ($mins < 0 ? -1 : 1);
            $mins = abs($mins);
            $hrs = floor($mins / 60);
            $mins -= $hrs * 60;
            $offset = sprintf("%+d:%02d", $hrs*$sgn, $mins);
            
            $dsn = "mysql:host=$host;dbname=$db";
            $options = [
                \PDO::ATTR_PERSISTENT         => true,
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try
            {
                 self::$DB = new \PDO($dsn, $user, $pass, $options);
                 self::$DB->exec("SET time_zone='$offset';");
            } catch (\PDOException $e) {
                 throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$DB;
    }

    public static function GUID()
    {
        return sprintf("%04X%04X-%04X-%04X-%04X-%04X%04X%04X", mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}