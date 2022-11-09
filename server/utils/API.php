<?php

namespace utils;

require __DIR__ . "/../Configuration.php";


class API {
    const BLOCK_SIZE = 8;
    const IV_LENGTH = 16;
	const CIPHER = "AES256";


	public static function RespondSuccess($Data) {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization, Token");
		header("Access-Control-Allow-Methods: GET,HEAD,PUT,PATCH,POST,DELETE");
		echo json_encode($Data, JSON_UNESCAPED_SLASHES);
	}

	public static function RespondError($Message) {
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Authorization, Token");
		header("Access-Control-Allow-Methods: GET,HEAD,PUT,PATCH,POST,DELETE");
		http_response_code(401);
		echo json_encode($Message);
	}

	public static function POST()
	{
		$RawData = file_get_contents("php://input");
		return json_decode($RawData);
	}

	public static function EncryptCookie($Data)
	{
        global $Configuration;

        $IV = static::GenerateIV(true);
        $PlainText = json_encode($Data);
        $CookieText = static::EncryptString($PlainText, $Configuration["TokenKey"], $IV);

        return base64_encode($IV) . "*****" . $CookieText;
 	}

	public static function DecryptCookie($CookieText)
	{
        global $Configuration;

        $IV = base64_decode(explode("*****", $CookieText)[0]);
        $CypherText = explode("*****", $CookieText)[1];
        $PlainText = static::DecryptString($CypherText, $Configuration["TokenKey"], $IV);
        $Data = json_decode(rtrim($PlainText, "\0"));

        return $Data;
 	}

    public static function GenerateIV(bool $allowLessSecure = false): string
    {
        $success = false;
        $random = openssl_random_pseudo_bytes(openssl_cipher_iv_length(static::CIPHER));
        if (!$success) {
            if (function_exists("sodium_randombytes_random16")) {
                $random = sodium_randombytes_random16();
            } else {
                try {
                    $random = random_bytes(static::IV_LENGTH);
                }
                catch (Exception $e) {
                    if ($allowLessSecure) {
                        $permitted_chars = implode(
                            '',
                            array_merge(
                                range('A', 'z'),
                                range(0, 9),
                                str_split('~!@#$%&*()-=+{};:"<>,.?/\'')
                            )
                        );
                        $random = '';
                        for ($i = 0; $i < static::IV_LENGTH; $i++) {
                            $random .= $permitted_chars[mt_rand(0, (static::IV_LENGTH) - 1)];
                        }
                    }
                    else {
                        throw new RuntimeException("Unable to generate initialization vector (IV)");
                    }
                }
            }
        }

        return $random;
    }

    public static function GetPaddedText(string $plainText): string
    {
        $stringLength = strlen($plainText);
        if ($stringLength % static::BLOCK_SIZE) {
            $plainText = str_pad($plainText, $stringLength + static::BLOCK_SIZE - $stringLength % static::BLOCK_SIZE, "\0");
        }
        return $plainText;
    }

    public static function EncryptString(string $plainText, string $key, string $iv): string
    {
        $plainText = static::getPaddedText($plainText);
        return base64_encode(openssl_encrypt($plainText, static::CIPHER, $key, OPENSSL_RAW_DATA, $iv));
    }

    public static function DecryptString(string $encryptedText, string $key, string $iv): string
    {
        return openssl_decrypt(base64_decode($encryptedText), static::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
    }
}
