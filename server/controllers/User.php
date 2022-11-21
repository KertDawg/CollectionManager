<?php

namespace controllers;

class User
{
	public static function LogIn() 
	{
		$POST = \utils\API::POST();
		$UserName = $POST->UserName;
		$Password = $POST->Password;

		if (strlen($UserName) < 1)
		{
			$UserData = array(
				"UserID" => -1,
				"UserName" => "Login Not Found",
				"Token" => ""
			);

			\utils\Log::LogEvent(\utils\Log::SEVERITY_WARNING, \utils\Log::TYPE_LOGINFAILURE, "", "Attempted login with a non-exisitent user name.");
			\utils\API::RespondSuccess($UserData);
			return;
		}

		$uq = \utils\Database::GetDB()->prepare("
		SELECT u.UserID, u.UserName, u.PasswordHash, u.Admin
		FROM User u 
		WHERE (u.UserName = ?)
		AND (u.Active <> 0);");
		$uq->execute([$UserName]);

		foreach($uq->fetchAll() as $user)
		{
			if (password_verify($Password, $user["PasswordHash"]))
			{
				// This user matches.

				//  Build the token cookie.
				$UserData = array(
					"UserID" => $user["UserID"],
					"UserName" => $user["UserName"],
					"Token" => static::BuildCookie($user),
					"Admin" => $user["Admin"]
				);

				\utils\Log::LogEvent(\utils\Log::SEVERITY_INFORMATION, \utils\Log::TYPE_LOGINSUCCESS, $user["UserID"], "Successful login.");
				\utils\API::RespondSuccess($UserData);
				return;
			}
		}

		$UserData = array(
			"UserID" => -1,
			"UserName" => "Login Not Found",
			"Token" => "",
			"Admin" => 0
		);

		\utils\Log::LogEvent(\utils\Log::SEVERITY_INFORMATION, \utils\Log::SEVERITY_WARNING, "", "Attempted login with username \"" . $UserName . "\".");
		\utils\API::RespondSuccess($UserData);
	}

	public static function BuildCookie($User)
	{
		$Token = array(
			"UserID" => $User["UserID"],
			"Issued" => date("Y-m-d H:i:s"),
			"UserName" => $User["UserName"],
			"IPAddress" => $_SERVER["REMOTE_ADDR"],
			"Browser" => \utils\Browser::GetBrowserHash()
		);

		$Output = \utils\API::EncryptCookie($Token);

		return $Output;
	}

	public static function ValidateToken()
	{
		$Token = trim($_SERVER["HTTP_TOKEN"]);

		if (is_null($Token) || !isset($Token) || (strlen($Token) < 1))
		{
			return array("UserID" => -1);
		}

		$Object = \utils\API::DecryptCookie($Token);
		$Issued = new \DateTime($Object->Issued);
		$Expires = $Issued->Add(new \DateInterval("P7D"));
		$Now = new \DateTime();
		$IPAddress = $Object->IPAddress;
		$Browser = $Object->Browser;

		if ($Expires < $Now)
		{
			\utils\Log::LogEvent(\utils\Log::SEVERITY_WARNING, \utils\Log::TYPE_TOKENEXPIRED, $Object->UserID, "Attempted use of expired token.");
			return array("UserID" => -1);
		}

		if ($Browser != \utils\Browser::GetBrowserHash())
		{
			\utils\Log::LogEvent(\utils\Log::SEVERITY_WARNING, \utils\Log::TYPE_BADTOKEN, $Object->UserID, "The token was from another browser.  Got " . $Browser . " in the token, and the browser is " . \utils\Browser::GetBrowserHash());
			return array("UserID" => -1);
		}

		//  Read the user record.
		$uq = \utils\Database::GetDB()->prepare("
		SELECT u.UserID, u.UserName, u.Admin
		FROM User u
		WHERE (u.UserID = ?);
		");
		$uq->execute([$Object->UserID]);
		$UserData = array("UserID" => -1);

		foreach($uq->fetchAll() as $user)
		{
			//  Make a new token to keep the user logged in.
			$UserData = array(
				"UserID" => $user["UserID"],
				"UserName" => $user["UserName"],
				"Token" => static::BuildCookie($user),
				"Admin" => $user["Admin"],
			);
		}

		if ($UserData["UserID"] == -1)
		{
			\utils\Log::LogEvent(\utils\Log::SEVERITY_INFORMATION, \utils\Log::TYPE_GOODTOKEN, $UserData["UserID"], "Bad token or something.  Browser token: " . $Browser);
		}
		else
		{
			\utils\Log::LogEvent(\utils\Log::SEVERITY_INFORMATION, \utils\Log::TYPE_GOODTOKEN, $UserData["UserID"], "The token was valid.  Browser token: " . $Browser);
		}

		return $UserData;
	}

	public static function CheckTokenStatus()
	{
		$User = \controllers\User::ValidateToken();

		if ($User["UserID"] == "-1")
		{
			\utils\API::RespondSuccess(0);
			return;
		}
		else
		{
			\utils\API::RespondSuccess($User);
			return;
		}
	}

	public static function RegisterUser()
	{
		$POST = \utils\API::POST();
		$UserName = $POST->UserName;
		$Password = $POST->Password;

		//  Does user exist in the database?
		$bq = \utils\Database::GetDB()->prepare("
		SELECT UserName
		FROM User u
		WHERE (UserName = ?);
		");
		$bq->execute([$UserName]);

		foreach($bq->fetchAll() as $b) {
			//  This email is already in use.
			\utils\API::RespondSuccess("Username is already in use.");
			return;
		}

		$Token = \utils\Database::GUID();

		//  Insert the record.
		$bq = \utils\Database::GetDB()->prepare("
		INSERT INTO User 
		(UserID, UserName, Admin, Active, PasswordHash) 
		VALUES 
		(?, ?, 0, 1, ?);
		");
		$bq->execute([\utils\Database::GUID(), $UserName, password_hash($Password, PASSWORD_DEFAULT)]);

		\utils\API::RespondSuccess("Success.");
	}
}
