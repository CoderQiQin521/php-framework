<?php
/**
 *
 * User: Administrator
 * Date: 2021/11/21 12:26
 * Email: <coderqiqin@aliyun.com>
 **/

namespace bootstrap\core;

class view
{
    public function view($template, $data = [])
    {
        $file = BASE . '/views/' . $template;
        if (is_file($file)) {
            extract($data);
            include $file;
        }else {
            throw new \Exception('模版不存在'.$template);
        }
    }
}