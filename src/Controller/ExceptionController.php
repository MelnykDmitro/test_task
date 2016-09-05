<?php

namespace TestTask\Controller;

use TestTask\Lib\View;

class ExceptionController
{
    public function notFoundAction()
    {
        header("HTTP/1.1 404 Not found");
        View::render('exception/not_found.phtml');
    }

    public function accessDeniedAction()
    {
        header("HTTP/1.1 403 Forbidden");
        View::render('exception/access_denied.phtml');
    }
}
