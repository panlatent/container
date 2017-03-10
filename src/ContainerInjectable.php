<?php
/**
 * Container - A depend inject container
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

use Psr\Container\ContainerInterface;

/**
 * Interface ContainerInjectable
 *
 * 实现了该接口的类可在实例化是自动注入容器组件对。该接口继承了Injectable接口，使用接口注入的
 * 方式获得依赖对象。
 *
 * @package Panlatent\Container
 */
interface ContainerInjectable extends Injectable
{
    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer();

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function setContainer(ContainerInterface $container);
}