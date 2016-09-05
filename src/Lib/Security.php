<?php

namespace TestTask\Lib;

class Security
{
    public static function generateToken()
    {
        $token = sha1(uniqid());
        $_SESSION['token'] = $token;
        return $token;
    }

    public static function checkToken($token)
    {
        return $token == $_SESSION['token'];
    }
}
