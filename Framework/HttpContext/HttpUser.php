<?php

declare(strict_types = 1);

namespace WDB\HttpContext;
// TODO Add get user profile
use Framework\Models\ViewModels\UserProfileViewModel;
use WDB\Identity\Managers\UserManager;
use WDB\Identity\Tables\User;

class HttpUser
{
    private $_currentUser;

    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function isLogged() : bool {
        return (string) HttpContext::getInstance()->getSession()->userId !== "";
    }

    /**
     * @return bool
     */
    public function isAdmin() : bool {
        if ($this->isLogged()) {
            $userId =(string) HttpContext::getInstance()->getSession()->userId;
            return UserManager::getInstance()->isInRoleById($userId, AppConfig::DEFAULT_ADMIN_ROLE);
        }

        return false;
    }

    /**
     * @return UserProfileViewModel
     */
    public function getCurrentUser() : UserProfileViewModel
    {
        if ($this->isLogged()) {
            return $this->_currentUser;
        }

        return new UserProfileViewModel();
    }

    public function setCurrentUser() {
        if ($this->isLogged()) {
            $userId = (string) HttpContext::getInstance()->getSession()->userId;
            $this->currentUser = UserManager::getInstance()->getInfo($userId);
        }
    }

    public function logout(){
        HttpContext::getInstance()->getSession()->userId->delete();
    }
}