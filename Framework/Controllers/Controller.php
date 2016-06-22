<?php

declare(strict_types = 1);

namespace WDB\Controllers;

use WDB\Config\AppConfig;
use WDB\Helpers\Helpers;
use WDB\HttpContext\HttpContext;
use WDB\Models\ViewModel;
use WDB\View;

abstract class Controller
{
    /**
     * @var HttpContext
     */
    protected $context;

    /**
     * Controller constructor.
     * @param HttpContext $context
     * @NoAction
     */
    protected function __construct(HttpContext $context)
    {
        $this->context = $context;
    }

    /**
     * @param $model
     * @NoAction
     */
    protected function renderDefaultLayout($model = null)
    {
        $layout = AppConfig::DEFAULT_LAYOUT;
        if ($model === null)
        {
            $model = new ViewModel();
        }
        View::initView($model, $layout);
    }

    /**
     * @param string $layout
     * @param null $model
     * @NoAction
     */
    protected function renderLayout(string $layout, $model = null)
    {
        if ($model === null)
        {
            $model = new ViewModel();
        }
        View::initView($model, $layout);
    }

    /**
     * @NoAction
     * @param string $path
     */
    public function redirect(string $path = AppConfig::DEFAULT_REDIRECTION_PATH) {
        header("Location: " . Helpers::getBasePath() . $path);
        exit;
    }
}