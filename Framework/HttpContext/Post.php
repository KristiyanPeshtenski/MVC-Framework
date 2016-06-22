<?php

declare(strict_types = 1);

namespace WDB\HttpContext;


class Post
{
    public function __get(string $name)
    {
        if(isset($_POST[$name]))
        {
            return $_POST[$name];
        }
        return '';
    }

    public function all()
        :array
    {
        return $_POST;
    }

    public function hasPost()
        :bool
    {
        return count($_POST) > 0;
    }
}