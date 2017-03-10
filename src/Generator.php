<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

/**
 * Class Generator
 *
 * 生成器是对依赖关系的一个包装类。根据不同结构的依赖关系，创建所需要的依赖对象。
 *
 * @package Panlatent\Container
 */
class Generator
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string|callable
     */
    protected $builder;

    /**
     * @var bool
     */
    protected $singleton;

    /**
     * Generator constructor.
     *
     * @param \Panlatent\Container\Container $container
     * @param                                $builder
     * @param bool                           $singleton
     */
    public function __construct(Container $container, $builder, $singleton = false)
    {
        $this->container = $container;
        $this->builder = $builder;
        $this->singleton = $singleton;
    }

    /**
     * @return callable|mixed|object|string
     * @throws \Panlatent\Container\Exception
     */
    public function make()
    {
        if (is_string($this->builder)) {
            return $this->container->injectClass($this->builder);
        }

        if (is_object($this->builder)) {
            return $this->singleton ? $this->builder : clone $this->builder;
        }

        if (is_callable($this->builder)) {
            $result = call_user_func($this->builder);
            if (is_object($result)) {
                return $result;
            } elseif (is_string($result)) {
                return $this->container->injectClass($result);
            }
        }

        throw new Exception();
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @return bool
     */
    public function isSingleton()
    {
        return $this->singleton;
    }

    /**
     * @return callable|mixed|object|string
     */
    public function __invoke()
    {
        return $this->make();
    }
}