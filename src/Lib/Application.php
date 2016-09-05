<?php

namespace TestTask\Lib;

class Application
{
    public static function redirect($uri = null)
    {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . HOST . $uri);
    }
}
