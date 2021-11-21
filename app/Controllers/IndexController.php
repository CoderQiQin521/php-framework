<?php
/**
 *
 * User: Administrator
 * Date: 2021/11/20 23:56
 * Email: <coderqiqin@aliyun.com>
 **/

namespace app\Controllers;

use app\Models\MemberModel;

class IndexController
{
    public function index()
    {
//        p('你好');
        $memberModel = new MemberModel();
        p($memberModel->list());
    }
}