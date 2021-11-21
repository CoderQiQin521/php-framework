<?php
/**
 *
 * User: Administrator
 * Date: 2021/11/20 23:13
 * Email: <coderqiqin@aliyun.com>
 **/

function p($value)
{
    if (is_bool($value)) {
        var_dump($value);
    } else if (is_null($value)) {
        var_dump(NULL);
    } else {
        echo "<pre style='font-size: 12px;background-color: #303030; color: #f08d49;padding: 10px;border-radius: 4px;font-family: Consolas;line-height:1.5'>" . print_r($value, true) . "</pre>";
    }
}

function returnJson($data = [], $code = 200, $message = 'success')
{
    if (!is_numeric($code)) {
        throw new Exception('code必须为数字');
    }
    
    $result = [
        'code' => $code,
        'data' => $data,
        'message' => $message
    ];
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    die();
}