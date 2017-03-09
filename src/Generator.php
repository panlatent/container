<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

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

    public function __construct(Container $container, $builder, $singleton = false)
    {
        $this->container = $container;
        $this->builder = $builder;
        $this->singleton = $singleton;
    }

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

    public function __invoke()
    {
        return $this->make();
    }
}