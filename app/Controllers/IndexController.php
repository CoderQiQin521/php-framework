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
use bootstrap\core\controller;

class IndexController extends controller
{
    public function index()
    {
        $data = [
            'title' => 'PHP framework'
        ];
        $this->view('index.html', ['data' => $data]);
    }
    
    public function api()
    {
        $config = new config();
//        p($config->get('controller', 'route'));
//        p($config->get('action', 'route'));
//        p($config->all('route'));
//        p(\bootstrap\core\config::$cache);
        $memberModel = new MemberModel();
        p($memberModel->list());
    }
}