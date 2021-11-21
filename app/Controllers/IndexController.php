<?php
/**
 *
 * User: Administrator
 * Date: 2021/11/20 23:56
 * Email: <coderqiqin@aliyun.com>
 **/

namespace app\Controllers;

use app\Models\MemberModel;
use bootstrap\core\config;

class IndexController
{
    public function index()
    {
//        p('你好');
        $config = new config();
        p($config->get('controller', 'route'));
        p($config->all('route'));
        p(\bootstrap\core\config::$cache);
        $memberModel = new MemberModel();
        p($memberModel->list());
    }
}