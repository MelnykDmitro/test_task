<?php

namespace TestTask\Model;

abstract class AbstractModel
{
    private static $db;
    private $_message;

    public function __construct($data = null)
    {
        if ($data) {
            $this->assign($data);
        }
    }

    public function assign($data)
    {
        $keys = array_keys(get_object_vars($this));

        $white_list = $this->getWhiteList();
        foreach ($white_list as $field) {
            unset($keys[$field]);
        }

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $this->{$key} = $data[$key];
            }
        }
    }

    protected static function getDb()
    {
        if (self::$db) {
            return self::$db;
        }

        $config = require APP_PATH . 'config.php';
        $db_config = $config['db'];

        try {
            $dsn = "mysql:host=" . $db_config['host'] . ";dbname=" . $db_config['db_name'] . ";charset=utf8";
            return new \PDO($dsn, $db_config['user'], $db_config['pass']);
        } catch (\PDOException $e) {
            die('Подключение не удалось: ' . $e->getMessage());
        }
    }

    public function getMessage()
    {
        return $this->_message;
    }

    public function appendMessage($message)
    {
        $this->_message = $message;
    }
}
