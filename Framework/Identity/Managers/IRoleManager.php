<?php

declare(strict_types = 1);

namespace WDB\Identity\Managers;

interface IRoleManager
{
    static function getInstance() : IRoleManager;

    function createRole(string $name) : bool;

    function isExist(string $name) : bool;

    function getId (string $name) : int;
}