<?php

declare(strict_types = 1);

namespace WDB\Annotations;

use WDB\Config\AppConfig;
use WDB\Helpers\Helpers;
use WDB\Identity\IdentityManagers\UserManager;

class AdminAnnotation extends Annotation
{

    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $this->beforeActionExecute();
    }

    private function beforeActionExecute()
    {
        if(!isset($_SESSION['user_id'])
            || !UserManager::getInstance()->isInRoleById($_SESSION['user_id'], AppConfig::DEFAULT_ADMIN_ROLE_NAME))
        {
            Helpers::redirect(AppConfig::DEFAULT_CONTROLLER, AppConfig::DEFAULT_ACTION);
        }
    }
}