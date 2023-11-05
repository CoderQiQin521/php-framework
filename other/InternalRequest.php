<?php
/**
 *
 * User: CoderQiQin
 * Date: 2022/11/9 19:36
 * Email: <coderqiqin@aliyun.com>
 **/

namespace App\Common;

use App\Exceptions\ApiException;
use App\Exceptions\ThirdPartyInternalException;
use App\Library\Helper\Request;

class InternalRequest implements InternalRequestContract
{
    public function send(string $url, array $param) {
        $param = $this->signatureParam($param);
        $res   = Request::requestJson($url, $param);
        $res = json_decode($res, true);
        if ($res['code'] != 0) {
            throw new ThirdPartyInternalException($res['message'] ?? $res['msg'], $res['code']);
        }
        return $res;
    }

    /**
     * 内部请求签名
     * @param array $param
     * @return array
     * @throws ApiException
     */
    private function signatureParam(array $param): array {
        $config = config('signature');
        if (!$config) {
            throw new ApiException('signature配置文件不存在');
        }
        $appKey              = $config['client_secret']['32282869174361']['app_key'];
        $appSecret           = $config['client_secret']['32282869174361']['app_secret'];
        $param['key']        = $appKey;
        $param['app_secret'] = $appSecret;
        $param['timestamp']  = time();
        $param['sign']       = SignatureService::createSignature($param);
        return $param;
    }

    public function hello() {
        return "hello";
    }
}
