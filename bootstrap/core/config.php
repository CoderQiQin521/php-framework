<?php
/**
 *
 * User: Administrator
 * Date: 2021/11/21 5:48
 * Email: <coderqiqin@aliyun.com>
 **/

namespace bootstrap\core;

class config
{
    public static $cache = [];
    
    public static function get($name, $fileName)
    {
        if (self::$cache[$fileName]) {
            return self::$cache[$fileName][$name];
        } else {
            $file = CONFIG . '/' . $fileName . '.php';
            if (is_file($file)) {
                $config = include $file;
                if (isset($config[$name])) {
                    self::$cache[$fileName] = $config;
                    return $config[$name];
                }else {
                    throw new \Exception('没有这个配置项' . $name);
                }
            } else {
                throw new \Exception('配置文件不存在' . $file);
            }
        }
    }
    
    public static function all($fileName)
    {
        if (self::$cache[$fileName]) {
            return self::$cache[$fileName];
        } else {
            $file = CONFIG . '/' . $fileName . '.php';
            if (is_file($file)) {
                $config = include $file;
                self::$cache[$fileName] = $config;
                return $config[$fileName];
            } else {
                throw new \Exception('配置文件不存在' . $file);
            }
        }
    }
}