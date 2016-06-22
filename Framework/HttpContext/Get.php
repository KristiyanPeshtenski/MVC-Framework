<?php

declare(strict_types = 1);

namespace WDB\HttpContext;

class Get
{
    public function __get(string $name)
        :string
    {
        if(isset($_GET[$name]))
        {
            return $_GET[$name];
        }

        return '';
    }

    public function all()
        :array
    {
        return $_GET;
    }

    public function hasGet()
        :bool
    {
        return count($_GET) > 0;
    }
}