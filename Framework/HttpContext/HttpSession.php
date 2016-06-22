<?php

declare(strict_types = 1);

namespace WDB\HttpContext;

class HttpSession
{
    /**
     * @var array
     */
    private $sessions = [];

    /**
     * @param string $key
     * @return Session
     */
    public function __get(string $key) : Session {
        if (array_key_exists($key, $this->sessions)) {
            return $this->sessions[$key];
        }
        $session = new Session($key);

        return $session;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function __set(string $key, string $value) {
        $session = new Session($key);
        $session->$key = $value;
        $this->sessions[$key] = $session;
    }
}