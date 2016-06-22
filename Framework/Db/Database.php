<?php

declare(strict_types = 1);

namespace WDB\Db;

use WDB\Db\Drivers\DriverFactory;

class Database
{
    private static $_inst = array();
    /**
     * @var \PDO
     */
    private $_db = null;
    /**
     * @var \PDOStatement
     */
    private $_stmt = null;
    private $_params = array();
    private $_sql = null;

    public function __construct($dbInstance)
    {
        $this->_db = $dbInstance;
    }

    /**
     * @param string $instanceName
     * @param string $driver
     * @param string $user
     * @param string $pass
     * @param string $dbname
     * @param null $host
     */
    public static function setInstance(
        string $instanceName,
        string $driver,
        string $user,
        string $pass,
        string $dbname,
        $host = null)
    {
        $driver = DriverFactory::Create($driver, $user, $pass, $dbname, $host);
        $pdo = new \PDO(
            $driver->getDsn(),
            $user,
            $pass
        );

        self::$_inst[$instanceName] = new self($pdo);
    }

    /**
     * @param string $instanceName
     * @return Database
     * @throws \Exception
     */
    public static function getInstance(string $instanceName)
    {
        if(self::$_inst[$instanceName] == null)
        {
            throw new \Exception('invalid instance name ' . $instanceName, 500);
        }
        return self::$_inst[$instanceName];
    }



    public function prepare($sql, array $params = array(), array $pdoOptions = array())
    : Database
    {
        $this->_stmt = $this->_db->prepare($sql, $pdoOptions);
        $this->_sql = $sql;
        $this->_params = $params;
        return $this;
    }

    public function execute(array $params = array()) : Database
    {
        if($params)
        {
            $this->_params = $params;
        }

        $this->_stmt->execute($this->_params);
        return $this;
    }

    public function query($sql){
        return $this->_db->query($sql);
    }

    public function fetchAllAssoc()
    : array
    {
        return $this->_stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchRowAssoc()
    : array
    {
        return $this->_stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAllNum()
    {
        return $this->_stmt->fetchall(\PDO::FETCH_NUM);
    }

    public function fetchRowNum()
    {
        return $this->_stmt->fetch(\PDO::FETCH_NUM);
    }

    public function fetchAllObj()
    {
        return $this->_stmt->fetchall(\PDO::FETCH_OBJ);
    }

    public function fetchRowObj()
    {
        return $this->_stmt->fetch(\PDO::FETCH_OBJ);
    }

    public function fetchAllColumn($column)
    {
        return $this->_stmt->fetchall(\PDO::FETCH_COLUMN, $column);
    }

    public function fetch($fetchStyle = \PDO::FETCH_ASSOC){
        return $this->_stmt->fetch($fetchStyle);
    }

    public function fetchAllClass($class)
    {
        return $this->_stmt->fetchall(\PDO::FETCH_CLASS, $class);
    }

    public function fetchRowClass($class)
    {
        return $this->_stmt->fetch(\PDO::FETCH_CLASS, $class);
    }

    public function getLastInsertId()
    :int
    {
        return $this->_db->lastInsertId();
    }

    public function rowCount()
    :int
    {
        return $this->_stmt->rowCount();
    }

    public function getStmt()
    : \PDOStatement
    {
        return $this->_stmt;
    }


}