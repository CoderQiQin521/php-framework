<?php

namespace bootstrap;
//use bootstrap\route;
use bootstrap\core\config;

/**
 *
 * User: Administrator
 * Date: 2021/11/20 21:08
 * Email: <coderqiqin@aliyun.com>
 **/
class App
{
    public static array $classMap = [];
    
    public static function run()
    {
        $appConfig = config::all('app');
        // 设置系统时区
        date_default_timezone_set($appConfig['timezone']);
        if ($appConfig['debug']){
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        
            ini_set('display_errors', 'On');
        }else {
            ini_set('display_errors', 'Off');
        }
        
        $route = new \bootstrap\core\route();
        $controller = $route->controller;
        $controller = ucfirst($controller);
        $action = $route->action;
        $controllerFile = APP . '/Controllers/' . $controller . 'Controller.php';
        $controllerClass = '\app\Controllers\\' . $controller . 'Controller';
        if (is_file($controllerFile)) {
            include $controllerFile;
            $controller = new $controllerClass();
            $controller->$action();
        }else {
           throw new \Exception('找不到控制器'.$controller);
        }
    }
    
    public static function load($class)
    {
        if (isset(self::$classMap[$class])) {
            return true;
        } else {
            $class = str_replace('\\', '/', $class);
            $file = BASE . '/' . $class . '.php';
            if (is_file($file)) {
                include $file;
                self::$classMap[$class] = $file;
                return true;
            } else {
                return false;
            }
        }
    }
}


/**
 *
 * User: Administrator
 * Date: 2021/11/20 21:08
 * Email: <coderqiqin@aliyun.com>
 **/
class App
{
    public static array $classMap = [];
    
    public static function run()
    {
        $appConfig = config::all('app');
        // 设置系统时区
        date_default_timezone_set($appConfig['timezone']);
        if ($appConfig['debug']){
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        
            ini_set('display_errors', 'On');
        }else {
            ini_set('display_errors', 'Off');
        }
        
        $route = new \bootstrap\core\route();
        $controller = $route->controller;
        $controller = ucfirst($controller);
        $action = $route->action;
        $controllerFile = APP . '/Controllers/' . $controller . 'Controller.php';
        $controllerClass = '\app\Controllers\\' . $controller . 'Controller';
        if (is_file($controllerFile)) {
            include $controllerFile;
            $controller = new $controllerClass();
            $controller->$action();
        }else {
           throw new \Exception('找不到控制器'.$controller);
        }
    }
    
    public static function load($class)
    {
        if (isset(self::$classMap[$class])) {
            return true;
        } else {
            $class = str_replace('\\', '/', $class);
            $file = BASE . '/' . $class . '.php';
            if (is_file($file)) {
                include $file;
                self::$classMap[$class] = $file;
                return true;
            } else {
                return false;
            }
        }
    }
}
