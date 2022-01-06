<?php
/**
 *
 * User: Administrator
 * Date: 2022/1/6 7:02
 * Email: <coderqiqin@aliyun.com>
 **/

namespace bootstrap\core;
use Exception;
use ReflectionClass;

class container
{
    protected array $defines = [];
    protected array $instances = [];
    public function __construct()
    {
    }
    
    /**
     * @param string $abstract 抽象
     * @param string $concrete 实现
     */
    public function set(string $abstract, object $concrete) {
        $this->defines[$abstract] = $concrete;
    }
    
    /**
     * @param string $abstract 抽象
     * @return object
     * @throws Exception
     */
    public function get(string $abstract):object {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
    
        if (isset($this->defines[$abstract])) {
            $concrete = $this->defines[$abstract];
            $object = $this->build($concrete);
        }else {
            $object = $this->build($abstract);
        }
        
        $this->instances[$abstract] = $object;
        return $object;
    }
    
    /**
     * @param $concrete
     * @return object
     * @throws Exception
     */
    private function build($concrete):object
    {
       
        // 反射class
        try {
            $reflectionClass = new ReflectionClass($concrete);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        // 如果不能实例化,抛出异常
        if (!$reflectionClass->isInstantiable()) {
            throw new Exception("class [$concrete] 不能初始化");
        }
        
        // 获取构造函数方法反射对象
        $constructor = $reflectionClass->getConstructor();
        
        // 类不存在构造函数, 直接实例化
        if (is_null($constructor)) {
            return new $concrete;
        }
    
        $dependencies = [];
        // 给每个签名参数赋值, 如果参数是对象则实例化, 如果参数有默认值则赋值
        foreach ($constructor->getParameters() as $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $dependencies[] = $parameter->getDefaultValue();
            }else {
                $dependClass = $parameter->getClass(); // 对象参数
                $isClass = !is_null($dependClass);
                $dependencies[] = $isClass ? $this->get($dependClass->getName()) : null;
            }
        }
        
        return $reflectionClass->newInstanceArgs($dependencies);
    }
}

interface Itest {
    public function hello();
}

class A implements Itest {
    public function hello(): string
    {
        return "hello";
    }
}

class B {
    private Itest $example;
    public function __construct(Itest $a)
    {
        $this->example = $a;
    }
    
    public function test() {
        return $this->example->hello();
    }
}

$container = new container();
$a = new A();  // 不应该是实现对象 而是 classname字符串反射
$container->set('Itest', $a);

try {
    $obj = $container->get('Itest');
    echo $obj->hello();
} catch (Exception $e) {
    throw new Exception($e->getMessage());
}

try {
    $b = new B();
    $obj1 = $container->get($b);
    echo $obj1->test();
} catch (Exception $e) {
    throw new Exception($e->getMessage());
}