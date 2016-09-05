<?php

define('HOST', 'http://test-task.dev/');
define('ROOT_PATH', realpath('..') . '/');
define('APP_PATH', ROOT_PATH . 'app/');

require ROOT_PATH . '/vendor/autoload.php';

try {
    \TestTask\Bootstrap::run();
} catch (Exception $e) {
    echo $e->getMessage();
}
