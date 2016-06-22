<?php

declare(strict_types = 1);

namespace WDB\Annotations;

abstract class Annotation
{
    protected function __construct() {

    }

    public abstract function execute();
}