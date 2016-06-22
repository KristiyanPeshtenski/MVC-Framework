<?php

declare(strict_types = 1);

namespace WDB\Annotations;

use WDB\Helpers\Helpers;

class AuthorizeAnnotation extends Annotation
{

    public function __construct()
    {
        parent::__construct();
    }

    public function execute()
    {
        $this->beforeActionExecute();
    }

    private function beforeActionExecute(){
        if (!isset($_SESSION['userId'])) {
            Helpers::redirect(AppConfig::DEFAULT_CONTROLLER, AppConfig::DEFAULT_ACTION);
        }
    }
}