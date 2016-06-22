<?php

declare(strict_types = 1);

namespace WDB\Identity\Managers;

use WDB\Config\AppConfig;
use WDB\Db\Database;

class DbManager implements IDbManager
{
    /**
     * @var IDbManager
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

    public function updateDatabase()
    {
        $tableClasses = $this->getIdentityClasses();

        foreach ($tableClasses as $tableClass) {
            $tableName = $this->getTableName($tableClass);
            $tableDocs = $this->getTableDocs($tableClass);
            $tableColumns = $this->getTableColumns($tableClass);

            if (!$this->tableExists($tableName))
            {
                $this->createTable($tableName, $tableColumns, $tableDocs);
            }
        }

        $this->seedRoles();
    }

    private function seedRoles()
    {
        if (!RoleManager::getInstance()->isExist(AppConfig::DEFAULT_ADMIN_ROLE_NAME)) {
            RoleManager::getInstance()->createRole(AppConfig::DEFAULT_ADMIN_ROLE_NAME);
        }

        if (!RoleManager::getInstance()->isExist(AppConfig::DEFAULT_REGISTRATION_ROLE)) {
            RoleManager::getInstance()->createRole(AppConfig::DEFAULT_REGISTRATION_ROLE);
        }
    }

    private function getIdentityClasses() :array
    {
        $identityClasses = array();
        $path = "../Framework/Identity/Tables";
        $classes = array_diff(scandir($path), array('..', '.'));

        foreach ($classes as $class) {
            $fullClassName = "WDB\\" . "Identity\\Tables\\" . substr($class, 0, strlen($class) - 4);
            $identityClasses[] = $fullClassName;
        }

        return $identityClasses;
    }

    private function getTableDocs(string $className) : array
    {
        $tableClassDoc = array();
        $refClass = new \ReflectionClass($className);

        if (preg_match_all('/@Primary\s*([^\s\n*]+)/', $refClass->getDocComment(), $fieldMatch)) {
            $str = "PRIMARY KEY (" . implode(", ", $fieldMatch[1]) . ")";
            $classDoc[] = $str;
        }

        if (preg_match_all('/@Foreign\s*([^\n*]+)/', $refClass->getDocComment(), $fieldMatch)) {
            foreach ($fieldMatch[1] as $item) {
                $classDoc[] = "FOREIGN KEY " . $item;
            }
        }

        return $tableClassDoc;
    }

    public function getTableName(string $class) : string {
        $refClass = new \ReflectionClass($class);
        if ($refClass->getDocComment() && preg_match('/@Table\s*([^\s\n*]+)/', $refClass->getDocComment(), $matches)) {
            $tableName = $matches[1];
        } else {
            throw new \Exception("No table name defined for this class!");
        }

        return $tableName;
    }

    /**
     * @param string $class
     * @return mixed
     * @Note This method is taken from ...
     */
    private function getTableColumns(string $class) :array
    {
        $properties = array();
        try {
            $rc = new \ReflectionClass($class);

            do {
                $rp = array();
                /* @var $p \ReflectionProperty */
                foreach ($rc->getProperties() as $p) {
                    $p->setAccessible(true);
                    preg_match('/@Column\s*([^\s\n*]+)/', $p->getDocComment(), $columnMatch);
                    $column = $columnMatch[1];
                    $rp[$column] = array();
                    $rp[$column]["Column"] = $column;

                    preg_match('/@Type\s*([^\s\n*]+)/', $p->getDocComment(), $typeMatch);
                    preg_match('/@Length\s*([^\s\n*]+)/', $p->getDocComment(), $lengthMatch);
                    $type = $typeMatch[1] . "(" . $lengthMatch[1] . ")";
                    $rp[$column]["Type"] = strtolower($type);
                    $rp[$column]["Null"] = preg_match('/@Null\s*/', $p->getDocComment(), $nullMatch) ? "YES" : "NO";

                    if (preg_match('/@Primary\s*/', $p->getDocComment(), $keyMatch)) {
                        $rp[$column]["Key"] = "PRI";
                    } else if (preg_match('/@Unique\s*/', $p->getDocComment(), $keyMatch)){
                        $rp[$column]["Key"] = "UNI";
                    } else {
                        $rp[$column]["Key"] = "";
                    }

                    $rp[$column]["Extra"] = preg_match('/@Increment\s*/', $p->getDocComment(), $incrementMatch) ? "auto_increment" : "";
                }

                $properties = array_merge($rp, $properties);
            } while ($rc = $rc->getParentClass());
        } catch (\ReflectionException $e) { }
        return $properties;
    }

    private function createTable(string $tableName, array $columns, array $classDoc) : bool
    {
        try {
            $sql = "CREATE table $tableName (";
            $col = array();

            foreach ($columns as $column) {
                $colStr = $column["Column"] . " " .
                    $column["Type"];

                if ($column["Null"] === "NO" && $column["Key"] !== "PRI") {
                    $colStr .= " NOT NULL";
                }

                if ($column["Extra"] === "auto_increment") {
                    $colStr .= " AUTO_INCREMENT";
                }

                if ($column["Key"] === "PRI") {
                    $colStr .= " PRIMARY KEY";
                }

                if ($column["Key"] === "UNI") {
                    $colStr .= " UNIQUE";
                }

                $col[] = $colStr;
            }

            $sql .= implode(",\n", $col);

            if (count($classDoc) > 0) {
                $sql .= ", " . implode(", ", $classDoc);
            }

            $sql .= ");";
            $this->_db->query($sql);
        } catch(\PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
            return false;
        }

        return true;
    }

    function createIdentityTables()
    {
        // TODO: Implement createIdentityTables() method.
    }

    public function tableExists(string $table) : bool
    {
        $response = $this->_db->prepare("SHOW TABLES LIKE $table")
            ->execute();

        return $response->rowCount() > 0;
    }

    static function getInstance() :IDbManager
    {
        if(self::$_inst == null)
        {
            self::$_inst = new DbManager();
        }

        return self::$_inst;
    }
}