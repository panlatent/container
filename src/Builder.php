<?php
/**
 * Container - A depend inject container
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

use Panlatent\Container\Resolve\ClassResolve;
use Panlatent\Container\Resolve\FunctionResolve;

class Builder
{
    const DEFINE_CALLABLE = 0;
    const DEFINE_CLASS = 1;
    const DEFINE_OBJECT = 2;
    const DEFINE_SCALAR = 3;
    /**
     * @var mixed
     */
    protected $definition;
    /**
     * @var bool
     */
    protected $singleton = false;
    /**
     * @var int
     */
    protected $type;
    /**
     * @var mixed
     */
    protected $object;

    public function __construct($definition)
    {
        $this->definition = $definition;

        if (is_string($definition)) {
            $this->type = static::DEFINE_CLASS;
        } elseif (is_callable($definition)) {
            $this->type = static::DEFINE_CALLABLE;
        } elseif (is_object($definition)) {
            $this->type = static::DEFINE_OBJECT;
        } else {
            $this->type = static::DEFINE_SCALAR;
        }
    }

    /**
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * @param bool $singleton
     */
    public function setSingleton(bool $singleton)
    {
        $this->singleton = $singleton;
    }

    public function build(Injector $injector)
    {
        if ($this->singleton && $this->object !== null) {
            return $this->object;
        }

        $object = $this->make($injector);
        if ($this->singleton) {
            $this->object = $object;
        }

        return $object;
    }

    protected function make(Injector $injector)
    {
        switch ($this->type) {
            case static::DEFINE_OBJECT:
            case static::DEFINE_SCALAR:
                return $this->definition;
            case static::DEFINE_CALLABLE:
                $resolve = new FunctionResolve($this->definition);
                $params = $injector->make($resolve);

                return $resolve->getReturn($params);
            case static::DEFINE_CLASS:
            default:
                // New an object with constructor parameters.
                $resolve = new ClassResolve($this->definition);
                $params = $injector->make($resolve);

                return $resolve->getInstance($params);
        }
    }
}