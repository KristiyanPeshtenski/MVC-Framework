<?php

declare(strict_types = 1);

namespace WDB\HttpContext;

class HttpRequest
{
    /**
     * @var Get
     */
    private $_get = null;
    /**
     * @var Post
     */
    private $_post = null;

    public function __construct()
    {
        $this->_get = new Get();
        $this->_post = new Post();
    }

    /**
     * @return Get
     */
    public function getGet()
        :Get
    {
        return $this->_get;
    }

    /**
     * @return Post
     */
    public function getPost()
        :Post
    {
        return $this->_post;
    }
}