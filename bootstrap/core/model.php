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
        $config = config::all('database');
        $dsn = "{$config['driver']}:host={$config['host']}:{$config['port']};dbname={$config['dbname']}";
        try {
            parent::__construct($dsn, $config['username'], $config['password']);
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}