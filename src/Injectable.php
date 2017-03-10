<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

/**
 * Interface Injectable
 *
 * 该接口是使用接口注入方式的默认父类接口，实现了它和它的所有子接口的类中的方法均会被注入依赖
 * 对象。可以通过对注入器的设置改变需要被注入的接口。
 *
 * @package Panlatent\Container
 */
interface Injectable
{

}