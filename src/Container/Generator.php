<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

class Generator {

    protected $container;

    protected $gen;

    protected $singleton;

    public function __construct(Container $container, $gen, $singleton = false)
    {
        $this->container = $container;
        $this->gen = $gen;
        $this->singleton = $singleton;
    }

    public function singleton()
    {
        return $this->singleton;
    }

    public function make()
    {
        if (is_string($this->gen)) {
            return $this->container->injectNew($this->gen);
        } elseif (is_callable($this->gen)) {
            if (is_object($object = call_user_func($this->gen))) {
                return $object;
            }
        }

        return false;
    }

    public function setSingleton($singleton)
    {
        $this->singleton = $singleton;
    }

    public function __invoke()
    {
        return $this->make();
    }

}