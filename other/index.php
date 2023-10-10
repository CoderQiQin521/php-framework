<?php

// $name = 'this is a name';

// print(123 . "\t");

// $age = 20;

// print($name . "\t" . $age);

// $japanese = 80;

$name = 'd';
$res = match($name) {
    'a' => 'aaaa',
    'b' => 'bbbbb',
    'c','d' => (function() {
        return 'cdcdcdcd';
    })(),
    default => 'none'
};
print_r($res. "\n");

// 定义一个注解
#[Attribute]
class PrintSomeThing
{
  public function __construct($str = '')
  {
     echo sprintf("打印字符串 %s \n", $str);
  }
}

// 使用注解
#[PrintSomeThing("hello world")]
class AnotherThing{}

// 使用反射读取注解
$reflectionClass = new ReflectionClass(AnotherThing::class);
$attributes = $reflectionClass->getAttributes();
foreach($attributes as $attribute) {
  $attribute->newInstance(); //获取注解实例的时候，会输出 ‘打印字符串 Hello world’
}

$res = match($name) {
    'a' => 'aaaa',
    'b' => 'bbbbb',
    'c','d' => (function() {
        return 'cdcdcdcd';
    })(),
    default => 'none'
};
print_r($res. "\n");
print_r(2023);
<?php

// $name = 'this is a name';

// print(123 . "\t");

// $age = 20;

// print($name . "\t" . $age);

// $japanese = 80;

$name = 'd';
$res = match($name) {
    'a' => 'aaaa',
    'b' => 'bbbbb',
    'c','d' => (function() {
        return 'cdcdcdcd';
    })(),
    default => 'none'
};
print_r($res. "\n");

// 定义一个注解
#[Attribute]
class PrintSomeThing
{
  public function __construct($str = '')
  {
     echo sprintf("打印字符串 %s \n", $str);
  }
}

// 使用注解
#[PrintSomeThing("hello world")]
class AnotherThing{}

// 使用反射读取注解
$reflectionClass = new ReflectionClass(AnotherThing::class);
$attributes = $reflectionClass->getAttributes();
foreach($attributes as $attribute) {
  $attribute->newInstance(); //获取注解实例的时候，会输出 ‘打印字符串 Hello world’
}

$res = match($name) {
    'a' => 'aaaa',
    'b' => 'bbbbb',
    'c','d' => (function() {
        return 'cdcdcdcd';
    })(),
    default => 'none'
};
print_r($res. "\n");
print_r(2023);
