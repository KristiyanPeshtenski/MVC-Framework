<?php
declare(strict_types=1);

namespace WDB\Db\Drivers;

abstract class DriverAbstract
{
    protected $_user;
    protected $_pass;
    protected $_dbName;
    protected $_host;

    public function __construct(string $user, string $pass, string $dbName, string $host = null){
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_dbName = $dbName;
        $this->_host = $host;
    }

    /**
     * @return string
     */
    public abstract function getDsn();
}