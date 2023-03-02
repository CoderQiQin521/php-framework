<?php
/**
 *
 * User: Administrator
 * Date: 2023/03/02 20:38
 * Email: <coderqiqin@aliyun.com>
 **/
define('START', microtime(true));
defined('BASE') or define('BASE', realpath('../'));
defined('APP') or define('APP', BASE.'/app');
defined('PUBLICPATH') or define('PUBLICPATH', BASE.'/public');
defined('CONFIG') or define('CONFIG', BASE.'/config');
defined('BOOTSTRAP') or define('BOOTSTRAP', BASE.'/bootstrap');

require_once __DIR__ . '/../vendor/autoload.php';

require_once '../bootstrap/app.php';

require_once BOOTSTRAP.'/common/functions.php';

spl_autoload_register('\bootstrap\App::load');

\bootstrap\App::run();