<?php
/**
 *
 * User: Administrator
 * Date: 2021/11/20 20:38
 * Email: <coderqiqin@aliyun.com>
 **/
define('START', microtime(true));
defined('BASE') or define('BASE', realpath('../'));
defined('APP') or define('APP', BASE.'/app');
defined('CONFIG') or define('CONFIG', BASE.'/config');
defined('BOOTSTRAP') or define('BOOTSTRAP', BASE.'/bootstrap');
defined('DEBUG') or define('DEBUG', true);

require_once __DIR__ . '/../vendor/autoload.php';


if (DEBUG){
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
    
    ini_set('display_errors', 'On');
}else {
    ini_set('display_errors', 'Off');
}

require_once '../bootstrap/app.php';

require_once BOOTSTRAP.'/common/functions.php';

spl_autoload_register('\bootstrap\App::load');

\bootstrap\App::run();