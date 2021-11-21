<?php
/**
 *
 * User: Administrator
 * Date: 2021/11/21 6:35
 * Email: <coderqiqin@aliyun.com>
 **/

namespace bootstrap\core;

class model extends \Medoo\Medoo
{
    public function __construct()
    {
        $config = config::all('database');
        $dsn = "{$config['driver']}:host={$config['host']}:{$config['port']};dbname={$config['dbname']}";
        try {
//            parent::__construct($dsn, $config['username'], $config['password']);
            parent::__construct([
                'database_type' => $config['driver'],
                'database_name' => $config['dbname'],
                'server' => $config['host'],
                'username' => $config['username'],
                'password' => $config['password'],
                'charset' => $config['charset'],
                'port' => $config['port'],
                'prefix' => $config['prefix'],
            ]);
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}