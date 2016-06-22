<?php

declare(strict_types = 1);

namespace WDB\Identity\Managers;

use WDB\Db\Database;

class RoleManager implements IRoleManager
{
    /**
     * @var IRoleManager
     */
    private static $_inst = null;

    /**
     * @var Database
     */
    private $_db;

    private function __construct()
    {
        $this->_db = Database::getInstance('default');
    }

    static function getInstance()
        :IRoleManager
    {
        if(self::$_inst == null)
        {
            self::$_inst = new RoleManager();
        }

        return self::$_inst;
    }

    function createRole(string $name) : bool
    {
        if($this->isExist($name))
        {
            throw new \Exception ('role already exist ' . $name);
        }

        $response = $this->_db->prepare("INSERT INTO roles(name) VALUES (?)")
            ->execute([$name]);

        return $response->rowCount() > 0;
    }

    function isExist(string $name) : bool
    {
        $response = $this->_db->prepare("SELECT id FROM roles WHERE name = ?")
            ->execute([$name]);
        return $response->rowCount() > 0;
    }

    function getId(string $name) : int
    {
        $response = $this->_db->prepare("SELECT id FROM roles WHERE name = ?")
            ->execute([$name]);

        if($response->rowCount() === 0)
        {
            throw new \Exception('Role with name ' . $name . 'doesnt exist');
        }
        return intval($response->fetch()["id"]);
    }
}