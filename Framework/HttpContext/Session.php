<?php

declare(strict_types = 1);

namespace WDB\HttpContext;

class Session
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $sessionName) {
        $this->name = $sessionName;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set(string $name, string $value) {
        $_SESSION[$name] = $value;
    }

    /**
     * @return string
     */
    public function __toString() : string {
        if(isset($_SESSION[$this->name])) {
            return $_SESSION[$this->name];
        }

        return "";
    }

    public function delete(){
        if (isset($_SESSION[$this->name])) {
            unset($_SESSION[$this->name]);
        }
    }
}