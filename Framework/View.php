<?php

declare(strict_types = 1);

namespace WDB;

use WDB\Config\AppConfig;

class View
{
    public static $controllerName;
    public static $actionName;

    public static function initView($model, string $layout = 'default')
    {
        $viewPath = AppConfig::DEFAULT_VIEWS_FOLDER
            . DIRECTORY_SEPARATOR
            . self::$controllerName
            . DIRECTORY_SEPARATOR
            . self::$actionName
            . AppConfig::VIEW_EXTENSION;

        $model->view = $viewPath;
        self::ViewModelValidator($model, $viewPath);

        require AppConfig::DEFAULT_VIEWS_FOLDER
            . DIRECTORY_SEPARATOR
            . AppConfig::LAYOUT_FOLDER
            . DIRECTORY_SEPARATOR
            . strtolower($layout)
            . AppConfig::VIEW_EXTENSION;
    }



    public static function ViewModelValidator($model, string $filePath)
    {
        $file = fopen($filePath, 'r');
        $line = fgets($file);
        fclose($file);

        if (preg_match_all('/@var\s+([^\s]+)\s+(\$\w+).*\r?\n/', $line, $matches)) {
            if (!class_exists($matches[1][0], false)) {
                throw new \Exception ("Non existing model class! " . $matches[1][0]);
            }

            if (!($model instanceof $matches[1][0])) {
                throw new \Exception ("View model type not match.");
            }
        }
    }
}