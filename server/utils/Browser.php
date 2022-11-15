<?php

namespace utils;


class Browser
{
    public static function GetBrowserHash() 
    {        
        $Hash = md5(json_encode($_SERVER["HTTP_USER_AGENT"]));

        return $Hash;
	}
}
