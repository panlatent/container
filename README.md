Container
=========
[![Build Status](https://travis-ci.org/panlatent/container.svg)](https://travis-ci.org/panlatent/container)
[![Latest Stable Version](https://poser.pugx.org/panlatent/container/v/stable.svg)](https://packagist.org/packages/panlatent/container)
[![Total Downloads](https://poser.pugx.org/panlatent/container/downloads.svg)](https://packagist.org/packages/panlatent/container) 
[![Latest Unstable Version](https://poser.pugx.org/panlatent/container/v/unstable.svg)](https://packagist.org/packages/panlatent/container)
[![License](https://poser.pugx.org/panlatent/container/license.svg)](https://packagist.org/packages/panlatent/container)

Container is a depend inject container for PHP.

Panlatent\Container是一个Ioc容器组件，使用依赖注入的方式解决依赖问题。它提供了构造注入、接口
注入和Setter注入三种方式。可以使用预先设置的依赖关系去创建对象，调用方法及函数。它的目的是成为
一个灵活壮健的Ioc容器组件，并且支持了Psr-11容器接口，为其他项目提供面向对象编程中的基础服务。

## Install

```bash
composer require panlatent/container
```

Or，Add to composer.json and run ```composer install``` or ```composer update```.

## Usage

可以使用选项定制或者关闭一些特性。

0.Container

使用 ```Container::set($name, $builder, $singleton = false)``` 方法向容器添加一个依赖关系。
使用 ```Container::get($name)``` 方法从容器中获取一个对象，它的依赖关系必须已经被添加到容器中。

1.Constructor Inject

可以使用 ```Container::injectClass($className)``` 方法手动实例化一个类，该方法会从容器装
配构造函数所需要的依赖。

2.Interface Inject

在从容器创建时，容器会自动向实现了Injectable及其子接口的类中注入依赖对象。

3.Setter Inject

在从容器创建时，容器会自动向实现用户指定的setter方法注入依赖对象，也可以自动注入使用PHPDoc注释
标注的方法。

4.Method/Function Inject

可以使用 ```Container::injectMethod($object, $method, $params = []) ``` 方法调用一个
方法, 该方法会从容器装配调用时所需要的依赖。
$object 是一个对象
$method 是方法名
$params 是附加给被调用的方法/函数的参数值数组, 该数组内的值会按顺序自动的提供给非类类型参数

可以使用 ```Container::injectFunction($callable, $params = []) ``` 方法调用一个函数,
该方法会从容器装配调用时所需要的依赖。
$params 是附加给被调用的方法/函数的参数值数组, 该数组内的值会按顺序自动的提供给非类类型参数

## License

The Container is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).