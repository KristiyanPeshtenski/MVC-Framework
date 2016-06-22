<?php

declare(strict_types = 1);

namespace WDB\Config;

abstract class DbConfig
{
    const DB_DRIVER = 'mysql';
    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PASSWORD = '';
    const DB_NAME = 'conference_scheduler_db';
    const DB_INSTANCE = 'default';
    public static $PDO_OPTIONS = array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
}