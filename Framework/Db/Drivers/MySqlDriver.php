<?php
declare(strict_types=1);

namespace WDB\Db\Drivers;

class MySqlDriver extends DriverAbstract
{
    public function getDsn() : string
    {
        $dsn = 'mysql:host=' . $this->_host . ';dbname=' . $this->_dbName;

        return $dsn;
    }
}