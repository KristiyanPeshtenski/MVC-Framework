<?php

declare(strict_types = 1);

namespace WDB\Routers;

class DefaultRouter implements IRouter
{

    public function getUri() : string
    {
        $uri = $_SERVER['REQUEST_URI'];
        $self = $_SERVER['PHP_SELF'];
        $index = basename($self);
        $directories = str_replace($index, '', $self);
        $requestString = str_replace($directories, '', $uri);
        return $requestString;
    }

    public function  getPost() : array
    {
        return $_POST;
    }
}