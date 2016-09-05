<?php

namespace TestTask\Controller;

use TestTask\Lib\Application;
use TestTask\Lib\Security;
use TestTask\Lib\View;
use TestTask\Model\Admin;

class AdminController
{
    public function loginAction()
    {
        if (Admin::isLogined()) {
            Application::redirect();
            return;
        }

        $login = null;
        $password = null;
        $message = null;

        if ($_POST && $_POST['token']) {
            if (isset($_POST['login'])) {
                $login = $_POST['login'];
            }

            if (isset($_POST['password'])) {
                $password = $_POST['password'];
            }

            if (Security::checkToken($_POST['token'])) {
                if (Admin::login($login, $password)) {
                    Application::redirect();
                    return;
                } else {
                    $message = 'Login or password is wrong';
                }
            } else {
                $message = 'Wrong token';
            }
        }

        $token = Security::generateToken();

        View::render('admin/login.phtml', [
            'login' => $login,
            'password' => $password,
            'message' => $message,
            'token' => $token
        ]);
    }

    public function logoutAction()
    {
        Admin::logout();
        Application::redirect();
    }
}
