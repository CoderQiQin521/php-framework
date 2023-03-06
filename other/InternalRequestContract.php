<?php
/**
 *
 * User: Administrator
 * Date: 2022/11/29 7:50
 * Email: <coderqiqin@aliyun.com>
 **/

namespace App\Common;


use App\Exceptions\ApiException;
use App\Exceptions\ThirdPartyInternalException;

/**
 * 内部服务请求
 */
interface InternalRequestContract
{
    /**
     * @param string $url
     * @param array  $param
     * @return mixed
     * @throws ThirdPartyInternalException|ApiException
     */
    public function send(string $url, array $param);
}