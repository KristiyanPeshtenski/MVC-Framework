<?php

declare(strict_types = 1);

namespace WDB\Helpers;

use WDB\Config\AppConfig;
use WDB\Exceptions\ApplicationException;

class Helpers
{
    public static function setCSRFToken() : string
    {
        $_SESSION['formToken'] = uniqid(mt_rand(), true);
        return $_SESSION['formToken'];
    }

    public static function getCSRFToken() : string
    {
        if(!isset($_SESSION['formToken'])) {
            self::setCSRFToken();
        }
        return $_SESSION['formToken'];
    }

    public static function validateCSRTFToken()
    {
        if($_POST['formToken'] != self::getCSRFToken()) {
            unset($_POST['formToken']);
            throw new ApplicationException("Invalid Token");
        }
    }

    public static function getBasePath() {
        $phpSelf = $_SERVER['PHP_SELF'];
        $index = basename($phpSelf);

        $basePath = str_replace($index, '', $phpSelf);
        return $basePath;
    }

    public static function redirect (string $controller, string $action)
    {
        $location = self::getBasePath()
            . $controller
            . DIRECTORY_SEPARATOR
            . $action;

        header("Location: $location");
        exit;
    }

    public static function escapeAll($toEscape) {
        if(is_array($toEscape)) {
            foreach ($toEscape as $key => &$value) {
                if(is_object($value)) {
                    $reflection = new \ReflectionClass($value);
                    $properties = $reflection->getProperties();

                    foreach ($properties as &$property) {
                        $property->setAccessible(true);
                        $property->setValue($value, self::escapeAll($property->getValue($value)));
                    }
                }elseif(is_array($value)) {
                    self::escapeAll($value);
                }else {
                    $value = htmlspecialchars($value);
                }
            }
        }elseif(is_object($toEscape)) {
            $reflection = new \ReflectionClass($toEscape);
            $properties = $reflection->getProperties();

            foreach ($properties as &$property) {
                $property->setAccessible(true);
                $property->setValue($toEscape, self::escapeAll($property->getValue($toEscape)));
            }
        }else {
            $toEscape = htmlspecialchars($toEscape);
        }

        return $toEscape;
    }

    public static function writeInFile(string $path, string $content)
    {
        $f = fopen($path, 'w+');
        fwrite($f, $content);
        fclose($f);
    }

    public static function startsWith($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    public static function endsWith($haystack, $needle) {
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    public static function isInteger($input){
        return(ctype_digit(strval($input)));
    }
}