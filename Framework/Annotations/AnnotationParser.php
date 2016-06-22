<?php

declare(strict_types = 1);

namespace WDB\Annotations;

final class AnnotationParser
{

    private static $_inst = null;

    private function __construct()
    {
    }


    public function processActionAnnotations(string $actionName, array $actionDocs)
    {
        var_dump($actionDocs['methods']);
        if(!in_array($_SERVER['REQUEST_METHOD'], $actionDocs['methods']))
        {
            throw new \Exception($_SERVER['REQUEST_METHOD'] . 'not allowed for action ' . $actionName);
        }

        foreach ($actionDocs['annotations'] as $annotation => $params) {
            if($params === '')
            {
                $annotationClass = new $annotation();
            }else{
                $annotationClass = new $annotation($params);
            }

            call_user_func_array(
                [
                    $annotationClass,
                    "execute"
                ],
                array()
            );
        }
    }

    public static function getInstance()
    {
        if(self::$_inst == null)
        {
            self::$_inst = new AnnotationParser();
        }

        return self::$_inst;
    }

}