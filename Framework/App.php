<?php

declare(strict_types = 1);

namespace WDB;

use WDB\Config\AppConfig;
use WDB\Config\DbConfig;
use WDB\Db\Database;
use WDB\Helpers\Helpers;
use WDB\HttpContext\HttpContext;
use WDB\Identity\Managers\DbManager;
use WDB\Routers\DefaultRouter;
use WDB\Routers\IRouter;

final class App
{
    private static $_inst = null;
    /**
     * @var \WDB\Routers\IRouter
     */
    private $_router = null;
    private $_frontController = null;

    private function __construct() {}

    /**
     * @return Routers\IRouter
     */
    public function getRouter() : IRouter
    {
        return $this->_router;
    }

    /**
     * @param Routers\IRouter $router
     */
    public function setRouter(IRouter $router)
    {
        $this->_router = $router;
    }

    public function run()
    {
        try{
            Database::setInstance(
                DbConfig::DB_INSTANCE,
                DbConfig::DB_DRIVER,
                DbConfig::DB_USER,
                DbConfig::DB_PASSWORD,
                DbConfig::DB_NAME,
                DbConfig::DB_HOST
            );
        }catch (\Exception $e){
            echo 'Database Error';
        }

        DbManager::getInstance()->updateDatabase();
        HttpContext::getInstance()->getIdentity()->setCurrentUser();
        Helpers::setCSRFToken();
        $this->_frontController = FrontController::getInstance();
        $this->_frontController->dispatch();
    }

    public static function getInstance() : App
    {
        if(self::$_inst == null)
        {
            self::$_inst = new App();
        }
        return self::$_inst;
    }
}