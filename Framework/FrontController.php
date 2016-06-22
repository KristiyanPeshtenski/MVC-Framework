<?php

declare(strict_types = 1);

namespace WDB;

use WDB\Annotations\AnnotationParser;
use WDB\Config\AppConfig;
use WDB\Helpers\Helpers;
use WDB\HttpContext\HttpContext;
use WDB\Routers\DefaultRouter;
use WDB\Routers\IRouter;

final class FrontController
{
    private static $_inst = null;
    /**
     * @var \WDB\Routers\IRouter
     */
    private $_customRoutes = array();
    private $_actions = array();

    /**
     * @var \WDB\Routers\IRouter
     */
    private $_router = null;
    private $_controllerName = null;
    private $_controller = null;
    private $_actionName = null;
    private $_requestParams = array();
    private $_area = null;

    //TODO: Fix Routers
    private function __construct()
    {
        $this->_router = new DefaultRouter();
        $this->initRoutes($this->getControllersNames());
    }

    public function getRouter() : IRouter
    {
        return $this->_router;
    }

    public function setRouter(IRouter $router)
    {
        $this->_router = $router;
    }

    public function dispatch()
    {
        if ($this->_router == null)
        {
            throw new \Exception('No valid router fount', 500);
        }

        $this->parseUri();
        $this->checkCustomRouteMatch();
        $this->initController();
        if(!$this->_actionName)
        {
            $this->_actionName = strtolower(AppConfig::DEFAULT_ACTION);
        }
        $actionDocs = $this->getActionDoc();
        AnnotationParser::getInstance()->processActionAnnotations($this->_actionName, $actionDocs);

        View::$controllerName = $this->_controllerName;
        View::$actionName = $this->_actionName;

        $this->actionNameAdjustment();

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->handleBindingModel();
        }

        call_user_func_array(
            [
                $this->_controller,
                $this->_actionName
            ],
            $this->_requestParams
        );
    }

    private function parseUri()
    {
        $uri = $this->_router->getUri();
        $this->_requestParams = explode('/', $uri);
        //$this->initArea();
        $this->_controllerName = ucfirst(array_shift($this->_requestParams));
        $this->_actionName = array_shift($this->_requestParams);
    }

    private function initArea(string $folder = AppConfig::AREAS_DEFAULT_FOLDER)
    {
        $areas = scandir($folder);
        if(in_array(ucfirst($this->_requestParams[0]), $areas))
        {
            $areaName = array_search(ucfirst($this->_requestParams[0]), $areas);
            $this->_area = $areas[$areaName];
            array_shift($this->_requestParams);
        }
    }

    private function initController()
    {
        $controllerFullName = AppConfig::CONTROLLERS_NAMESPACE;
        if($this->_controllerName != null && $this->_controllerName != '')
        {
            $controllerFullName .= ucfirst($this->_controllerName);
            // Check if controller name has Suffix
            if(strpos($this->_controllerName, AppConfig::CONTROLLER_SUFFIX) === false)
            {
                $controllerFullName .= AppConfig::CONTROLLER_SUFFIX;
            }
        }

        if(class_exists($controllerFullName, false))
        {
            $this->_controller = new $controllerFullName(HttpContext::getInstance());
        }else{
            $this->getDefaultController();
        }
    }

    private function getDefaultController()
    {
        $controllerFullName = AppConfig::CONTROLLERS_NAMESPACE . AppConfig::DEFAULT_CONTROLLER
            . AppConfig::CONTROLLER_SUFFIX;

        $this->_controllerName = ucfirst(AppConfig::DEFAULT_CONTROLLER);
        $this->_controller = new $controllerFullName(HttpContext::getInstance());
    }

    private function handleBindingModel()
    {
        Helpers::validateCSRTFToken();

        $errors = array();
        $refController = new \ReflectionClass($this->_controller);
        $refMethod = $refController->getMethod($this->_actionName);
        if(!$refMethod->getParameters())
        {
            return;
        }

        $params = $refMethod->getParameters();
        $count = 0;
        foreach ($params as $param) {
            if($param->getClass() !== null && class_exists($param->getClass()->getName(), false))
            {
                $paramClassName = $param->getClass()->getName();

                if(Helpers::endsWith($paramClassName, AppConfig::DEFAULT_BINDING_MODEL_SUFFIX))
                {
                    $paramRefClass = new \ReflectionClass($param->getClass()->getName());
                    $bindingModelName = $paramRefClass->getName();
                    $bindingModel = new $bindingModelName();
                    $modelProps = $paramRefClass->getProperties();
                    foreach ($modelProps as $prop) {
                        $propName = $prop->getName();
                        $setter = 'set' . ucfirst($propName);
                        $doc = $prop->getDocComment();
                        $annotations = $this->getBindingModelAnnotations($doc);
                        $displayName = array_key_exists('Display', $annotations) ? $annotations['Display'] : $propName;
                        if(array_key_exists('Required', $annotations) && !isset($_POST[$propName]))
                        {
                            $errors[] = $displayName . ' is required';
                        }
                        else if(
                            array_key_exists('MaxLength', $annotations)
                            && isset($_POST[$propName])
                            && strLen($_POST[$propName]) > (intval($annotations['MaxLength'])))
                        {
                            $errors[] = $displayName . ' Max length is ' . $annotations['MaxLength'];
                        }
                        else if(
                            array_key_exists('MinLength', $annotations)
                            && isset($_POST[$propName])
                            && strLen($_POST[$propName]) < (intval($annotations['MaxLength'])))
                        {
                            $errors[] = $displayName . ' Min length is ' . $annotations['MaxLength'];
                        }else {
                            $bindingModel->$setter($_POST[$propName]);
                        }
                    }

                    $this->_requestParams[] = $bindingModel;
                }
            }
            else if(count($this->_requestParams) < $count + 1)
            {
                throw new \Exception('Different parameters count.');
            }
            else if (preg_match('/@param ([^\s]+) \$' . $param->getName() . "/", $refMethod->getDocComment(), $parameterType))
            {
                if ($parameterType[1] === "int")
                {
                    $this->_requestParams[$count] = intval($this->_requestParams[$count]);
                }
            }

            $count++;
        }

        if(count($errors) > 0)
        {
            $_SESSION['binding-model-errors'] = $errors;
            // TODO: Handle Exceptions here default code is
            // throw new ApplicationException("", $this->requestStr);
        }
    }

    //TODO: HANDLE AREAS
    private function getControllersNames() : array
    {
        $controllerNames = array();
        $path = AppConfig::DEFAULT_CONTROLLERS_FOLDER;
        $files = array_diff(scandir($path), array('..', '.'));
        foreach ($files as $file) {
            $controllerName = substr($file, 0, strlen($file) - 4);
            $controllerNames[] = ucfirst($controllerName);
        }

        return $controllerNames;
    }

    /**
     * @return bool
     */
    private function checkCustomRouteMatch()
    :bool
    {

        if(is_array($this->_customRoutes))
        {
            foreach ($this->_customRoutes as $k => $v)
            {
                if(!in_array($_SERVER['REQUEST_METHOD'], $v['methods']))
                {
                    continue;
                }

                $hasMatch = false;
                $parsedParams = array();

                if(Helpers::startsWith($k, $this->_controllerName . '/' . $this->_actionName))
                {
                    if(count($this->_requestParams) === count($v['parameters']))
                    {
                        $hasMatch = true;
                        for($i = 0;$i < count($v['parameters']); $i++)
                        {
                            if($v['parameters'][$i] === '{int}' && Helpers::isInteger($this->_requestParams[$i]))
                            {
                                $parsedParams[] = (intval($this->_requestParams[$i]));
                            }
                            else if($v['parameters'][$i] === '{string}')
                            {
                                $parsedParams[] = $this->_requestParams[$i];
                            }
                            else if($v['parameters'][$i] !== $this->_requestParams[$i])
                            {
                                $hasMatch = false;
                                break;
                            }
                        }
                    }
                }

                if($hasMatch)
                {
                    $this->_controllerName = ucfirst($v['controller']);
                    $this->_actionName = $v['action'];
                    $this->_requestParams = $parsedParams;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $actionName
     * @return array
     * @throws \Exception
     */
    private function getActionDoc() : array
    {
        $key = $this->_controllerName . AppConfig::CONTROLLER_SUFFIX . '/' . $this->_actionName;

        if(!array_key_exists($key, $this->_actions))
        {
            throw new \Exception('No such action ' . $this->_actionName);
        }

        return $this->_actions[$key];
    }

    private function getBindingModelAnnotations(string $doc) : array{
        $annotations = [];
        if(preg_match_all('/@(\w+)\s*\(([^\)]*)\)\s*\n/', $doc, $matches)){
            for ($i = 0; $i < count($matches[0]); $i++) {
                $annotations[$matches[1][$i]] =  $matches[2][$i];
            }
        }

        return $annotations;
    }

    private  function actionNameAdjustment()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $this->_actionName .= "Post";
        } else if ($_SERVER['REQUEST_METHOD'] == 'Put'){
            $this->_actionName .= "Put";
        } else if ($_SERVER['REQUEST_METHOD'] == 'Delete'){
            $this->_actionName .= "Del";
        }
    }


    //TODO: add area property to actions
    private function initRoutes(array $controllersNames)
    {
        foreach($controllersNames as $controllersName)
        {
            $path = AppConfig::CONTROLLERS_NAMESPACE . $controllersName;
            $refController = new \ReflectionClass($path);
            $refMethods = $refController->getMethods();

            foreach ($refMethods as $refMethod) {
                $methodDoc = $refMethod->getDocComment();
                
                if($methodDoc && preg_match('/@NoAction/', $methodDoc, $isAction))
                {
                    continue;
                }

                $action = $controllersName . '/' . $refMethod->getName();
                $this->_actions[$action] = array(
                    "methods" => array(),
                    "annotations" => array(),
                    "params" => array(),
                    "arguments" => array()
                );

                $requestMethods = array("GET");
                if ($methodDoc && preg_match_all('/@(POST|PUT|DELETE|GET)/', $methodDoc, $requestMethodsAnnotations))
                {
                    $requestMethods = $requestMethodsAnnotations[1];
                }

                $this->_actions[$action]['methods'] = $requestMethods;
                if ($methodDoc && preg_match_all('/@Route\(([^\)]+)\)/', $methodDoc, $routeAnnotation))
                {
                    $params = explode('/', $routeAnnotation[1][0]);
                    array_shift($params);
                    array_shift($params);

                    $this->_customRoutes[$routeAnnotation[1][0]] = array(
                        "controller" => $controllersName,
                        "action" => $refMethod->getName(),
                        "parameters" => $params,
                        "methods" => $requestMethods
                    );
                }

                if ($methodDoc && preg_match_all('/@@(\w+)(?:\(([^)\s\n*]+)\))*/', $methodDoc, $match))
                {
                    for($i = 0; $i < count($match[0]); $i++)
                    {
                        $annotationName = AppConfig::ANNOTATIONS_NAMESPACE
                            . ucfirst($match[1][$i])
                            . AppConfig::ANNOTATION_SUFFIX;

                        $this->_actions[$action]["annotations"][$annotationName] = $match[2][$i];
                    }

                    if($methodDoc && preg_match_all('/@param\s+([^\s]+)\s+\$([^\s]+)/', $methodDoc, $paramType))
                    {
                        for($i = 0; $i < count($paramType[0]); $i++)
                        {
                            $this->_actions[$action]["params"][$paramType[2][$i]] = $paramType[2][$i];
                        }
                    }
                }
            }
        }
    }

    public static function getInstance() : FrontController
    {
        if(self::$_inst == null)
        {
            self::$_inst = new FrontController();
        }
        return self::$_inst;
    }
}