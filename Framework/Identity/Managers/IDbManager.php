<?php

declare(strict_types = 1);

namespace WDB\Identity\Managers;

interface IDbManager
{
    static function getInstance() :IDbManager;

    function createIdentityTables();

    function updateDatabase();

}