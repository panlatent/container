<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

class Injector
{
    /**
     * @var Containable
     */
    protected $container;

    /**
     * @var string|object|callable
     */
    protected $target;

    /**
     * @var bool
     */
    protected $isConstructor;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var array
     */
    protected $needParameterTypes = [];

    /**
     * @var array
     */
    protected $extraValues = [];

    /**
     * @var \ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @var \ReflectionParameter[]
     */
    protected $reflectionParameters;

    public function __construct(Containable $container, $target, $isConstructor = false)
    {
        $this->container = $container;
        $this->target = $target;
        $this->isConstructor = $isConstructor;

        if ($this->isConstructor) {
            $this->reflectionClass = new \ReflectionClass($this->target);
            if ($reflectionConstructor = $this->reflectionClass->getConstructor()) {
                $this->reflectionParameters = $reflectionConstructor->getParameters();
            }
        } else {
            $reflectionFunction = new \ReflectionFunction($this->target);
            $this->reflectionParameters = $reflectionFunction->getParameters();
        }

        $this->getNeedParameterTypes();
    }

    public function handle($extras = [])
    {
        $this->getParameters($extras);

        if ($this->isConstructor) {
            $object = $this->reflectionClass->newInstanceArgs($this->parameters);
            if ($object instanceof Injectable) {
                $object->setContainer($this->container);
            }
            return $object;
        } else {
            return call_user_func_array($this->target, $this->parameters);
        }
    }

    public function __invoke($extras = [])
    {
        return $this->handle($extras);
    }

    protected function getNeedParameterTypes()
    {
        /** @var \ReflectionParameter $parameter */
        foreach ($this->reflectionParameters as $parameter) {
            $pos = $parameter->getPosition();
            if (null !== ($class = $parameter->getClass())) {
                $this->needParameterTypes[$pos] = $class->getName();
            } else {
                $this->needParameterTypes[$pos] = false;
            }
        }
    }

    protected function getParameters($extras = [])
    {
        $this->extraValues = array_reverse($extras);
        /** @var \ReflectionParameter $parameter */
        foreach ($this->reflectionParameters as $parameter) {
            $pos = $parameter->getPosition();
            if (false === ($type = $this->needParameterTypes[$pos])) {
                $this->parameters[$pos] = $this->getParameterExtraValue($parameter);
            } else {
                $this->parameters[$pos] = $this->getParameterDependValue($type);
            }
        }
    }

    protected function getParameterExtraValue(\ReflectionParameter $parameter)
    {
        if (empty($this->extraValues)) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            } else {
                throw new Exception("");
            }
        } else {
            return array_pop($extras);
        }
    }

    protected function getParameterDependValue($className)
    {
        if (false === ($object = $this->container->get($className))) {
            throw new Exception("");
        }

        return $object;
    }
}