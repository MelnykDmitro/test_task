<?php

namespace TestTask\Lib;

class View
{
    const VIEWS_FOLDER = APP_PATH . 'views/';

    public static function render($view, $vars = null)
    {
        $view_path = self::VIEWS_FOLDER . $view;

        ob_start();
        extract($vars, EXTR_PREFIX_SAME, "wddx");
        include($view_path);
        $view_content = ob_get_contents();
        ob_end_clean();

        include(self::VIEWS_FOLDER . 'layout.phtml');
    }
}
