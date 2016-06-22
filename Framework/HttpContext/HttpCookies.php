<?php

declare(strict_types = 1);

namespace WDB\HttpContext;

class HttpCookies
{
    /**
     * @var array
     */
    private $cookies = [];

    /**
     * @param string $key
     * @return Cookie
     */
    public function __get(string $key) : Cookie
    {
        if (array_key_exists($key, $this->cookies)) {
            return $this->cookies[$key];
        }
        $cookie = new Cookie($key);

        return $cookie;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function __set(string $key, string $value)
    {
        $cookie = new Cookie($key);
        $cookie->$key = $value;
        $this->cookies[$key] = $cookie;
    }

}