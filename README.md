[![Build Status](https://travis-ci.org/panlatent/container.svg)](https://travis-ci.org/panlatent/container)
[![Latest Stable Version](https://poser.pugx.org/panlatent/container/v/stable.svg)](https://packagist.org/packages/panlatent/container)
[![Total Downloads](https://poser.pugx.org/panlatent/container/downloads.svg)](https://packagist.org/packages/panlatent/container) 
[![Latest Unstable Version](https://poser.pugx.org/panlatent/container/v/unstable.svg)](https://packagist.org/packages/panlatent/container)
[![License](https://poser.pugx.org/panlatent/container/license.svg)](https://packagist.org/packages/panlatent/container)

Container is a depend inject container for PHP.

## Install

```bash
composer require panlatent/container
```

## Usage

0. Add depends

使用 ```Container::set($name, $builder, $singleton = false)``` 方法向容器添加一个依赖关系.

$name 是依赖关系名, 可以是一个别名字符串, 也可以是一个类名字符串
$builder 是要向容器提供的依赖对象, 可以使一个对象或者是一个类名或者是一个callable(必须返回一个对象)
$singleton 依赖是否为单例, 如果依赖对象实现了 Container\Injector 接口, 则容器无视该设置且生成对象一定为单例

1. Constructor Inject

可以使用 ```Container::injectNew($className)``` 方法实例化一个类, 该方法会从容器装配构造函数所需要的依赖.

2. Setter Inject

容器在实例化通过构造注入的类时, 会自动向实现了 Container\Injector 接口的类的对象注入容器对象.

3. Method/Function Inject

可以使用 ```Container::injectCall($callable, $params = []) ``` 方法调用一个方法/函数, 该方法会从容器装配调用时所需要的依赖.

$params 是附加给被调用的方法/函数的参数值数组, 该数组内的值会按顺序自动的提供给非类类型参数

## License

The Container is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).