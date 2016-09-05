<?php

namespace TestTask\Model;

class Admin extends AbstractModel
{
    public $id;
    public $login;
    public $password;

    public static function isLogined()
    {
        return (isset($_SESSION['admin_id']));
    }

    public static function login($login, $password)
    {
        if (self::isLogined()) {
            return false;
        }

        $db = self::getDb();
        $stmt = $db->prepare('SELECT id FROM admin WHERE login = ? AND password = ? LIMIT 1');
        $stmt->execute([$login, sha1($password)]);
        $admins = $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);

        if (!$admins) {
            return false;
        }

        $admin = array_shift($admins);
        $_SESSION['admin_id'] = $admin->id;
        return true;
    }

    public static function logout()
    {
        if (!self::isLogined()) {
            return false;
        }

        $_SESSION['admin_id'] = null;
        return true;
    }
}
