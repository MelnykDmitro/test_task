<?php

namespace TestTask;

use TestTask\Controller\AdminController;
use TestTask\Controller\ExceptionController;
use TestTask\Controller\CommentController;

class Bootstrap
{
    public static function run()
    {
        session_start();

        $uri = '/';
        if (isset($_GET['_url'])) {
            $uri = $_GET['_url'];
        }

        if ($uri == '/') {
            call_user_func([new CommentController(), 'indexAction']);
        } elseif ($uri == '/edit') {
            call_user_func([new CommentController(), 'editAction']);
        } elseif ($uri == '/approve') {
            call_user_func([new CommentController(), 'approveAction']);
        } elseif ($uri == '/refuse') {
            call_user_func([new CommentController(), 'refuseAction']);
        } elseif ($uri == '/admin/login') {
            call_user_func([new AdminController(), 'loginAction']);
        } elseif ($uri == '/admin/logout') {
            call_user_func([new AdminController(), 'logoutAction']);
        } else {
            call_user_func([new ExceptionController(), 'notFoundAction']);
        }
    }
}
