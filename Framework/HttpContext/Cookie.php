<?php

declare(strict_types = 1);

namespace WDB\HttpContext;

class Cookie
{
    /**
     * @var string
     */
    private $_name;

    public function __construct(string $name)
    {
        $this->_name = $name;
    }

    public function __set(string $name, string $value)
    {
        setcookie($name, $value);
    }

    /**
     * @return string
     */
    public function __toString() : string {
        if(isset($_COOKIE[$this->_name])) {
            return $_COOKIE[$this->_name];
        }

        return "";
    }

    public function delete(){
        if (isset($_COOKIE[$this->_name])) {
            unset($_COOKIE[$this->_name]);
        };
    }
}