<?php

declare(strict_types = 1);

namespace WDB\Routers;

interface IRouter
{
    public function getUri() : string;

    public function  getPost() : array;
}