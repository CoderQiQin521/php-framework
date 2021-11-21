<?php
/**
 *
 * User: Administrator
 * Date: 2021/11/21 6:35
 * Email: <coderqiqin@aliyun.com>
 **/

namespace bootstrap\core;

class model extends \PDO
{
    public function __construct()
    {
        $dsn = 'mysql:host=localhost;dbname=test';
        $username = 'root';
        $password = 'root';
        try {
            parent::__construct($dsn,$username, $password);
        }catch (\PDOException $e) {
            throw $e;
        }
    }
}