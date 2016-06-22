<?php

declare(strict_types = 1);

namespace WDB\Config;

abstract class AppConfig
{
    const DEFAULT_CONTROLLER = 'home';
    const DEFAULT_ACTION = 'index';

    const CONTROLLERS_NAMESPACE = 'WDB\\Controllers\\';
    const DEFAULT_CONTROLLERS_FOLDER = "Controllers";
    const CONTROLLER_SUFFIX = 'Controller';

    const VIEWS_NAMESPACE = 'WDB\\Views';
    const DEFAULT_VIEWS_FOLDER = "Views";
    const VIEW_EXTENSION = '.php';

    const LAYOUT_FOLDER = "Layouts";
    const DEFAULT_LAYOUT = 'default';

    const ANNOTATIONS_NAMESPACE = 'WDB\\Annotations\\';
    const ANNOTATION_SUFFIX = 'Annotation';

    const AREAS_NAMESPACE = 'WDB\\Areas\\';
    const AREAS_DEFAULT_FOLDER = "../ConferenceSchedulerApp/Areas";

    const DEFAULT_BINDING_MODEL_SUFFIX = 'BindingModel';

    const DEFAULT_ADMIN_ROLE_NAME = 'Admin';
    const DEFAULT_REGISTRATION_ROLE = 'User';

    const DEFAULT_REDIRECTION_PATH = '';
}