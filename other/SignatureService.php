<?php


namespace App\Common;


use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Redis;

class SignatureService
{
    /**
     * @notes:验证接口签名
     * @function: checkSignature
     * @param $params 目前请求参数包含：path=>客户端请求路径，timestamp=>客户端请求时间戳
     * @param $appKey 分配给客户端的app_key
     * @param $sing 客户端生成的签名
     * @return bool
     * @throws ApiException
     * @author: jingzhen
     * @date: 2022/11/02
     */
    public static function checkSignature($params, $appKey, $sign)
    {
        $config = config('signature');
        if (empty($sign)) {
            throw new ApiException('签名不能为空', $config['error_code']['STATUS_CODE_SIGN_EMPTY']);
        }
        $clientConfig = isset($config['client_secret'][$appKey]) ? $config['client_secret'][$appKey] : [];
        if (empty($clientConfig)) {
            throw new ApiException('没有访问权限', $config['error_code']['STATUS_CODE_NOT_AUTH']);
        }
        if (empty($params['timestamp'])) {
            throw new ApiException('缺少必填参数', $config['error_code']['STATUS_CODE_PARAMS_EMPTY']);
        }
        //签名校验
        $params['app_secret'] = $clientConfig['app_secret'];
        $serverSign = self::createSignature((array)$params);
        if ($sign !== $serverSign) {
            throw new ApiException('签名无效', $config['error_code']['STATUS_CODE_SIGN_INVALID']);
        }
        return true;
    }

    /**
     * @notes:生成接口签名
     * @function: createSignature
     * @param array $params
     * @return string
     * @author: jingzhen
     * @date: 2022/11/02
     */
    public static function createSignature(array $params)
    {
        unset($params['sign']);
        //过滤值为空的参数
        $data = [];
        array_walk($params, function ($v, $k) use (&$data) {
            if (!empty($v)) {
                $data[$k] = $v;
            }
        });
        //对过滤后的请求参数按照键名进行升序排序
        ksort($data);
        //将排序后的请求参数使用&拼接
        $dataStr = '';
        foreach ($data as $key => $value) {
            $dataStr .= $key . '=' . $value . '&';
        }
        $str = trim($dataStr, '&');
        //使用双重md5并转为大写
        return strtoupper(md5(md5($str)));
    }

    /**
     * @notes:检测客户端请求唯一ID
     * @function: checkRequestId
     * @param $requestId
     * @return bool
     * @throws ApiException
     * @author: jingzhen
     * @date: 2022/11/02
     */
    public static function checkRequestId($requestId)
    {
        $config = config('signature');
        if (empty($requestId)) {
            throw new ApiException("缺少必填参数", $config['error_code']['STATUS_CODE_PARAMS_EMPTY']);
        }
        $redis = Redis::connection('default');
        $key = 'interface-request-id:' . $requestId;
        $res = $redis->setnx($key, date('Y-m-d H:i:s'));
        if ($res === 1) {
            $redis->expire($key, 300);
        } else {
            throw new ApiException('重复请求', $config['error_code']['STATUS_CODE_REPEAT_REQUEST']);
        }
        return true;
    }
}
